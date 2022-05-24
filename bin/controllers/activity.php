<?php

use spitfire\exceptions\PublicException;

/**
 * Activity refers to anything that happens on the network ping is directly connected
 * to. This means that, unlike pings or authors, the activity is not able to be
 * federated. The server can determine to push activity to the user if there's an
 * event on the federated network that is considered worthy of attention.
 *
 * Whenever a user receives activity, they will be notified about it. Either via
 * email or push notification.
 *
 * @author César de la Cal Bretschneider <cesar@magic3w.com>
 */
class ActivityController extends AppController
{
	
	public function index()
	{
		/*
		 * A user can only get their activity feed when they are logged into the
		 * application.
		 */
		if (!$this->user) {
			$this->response->setBody('Redirect...')->getHeaders()->redirect(url('account', 'login'));
			return;
		}
		
		if (isset($_GET['until'])) {
			$notifications = db()->table('notification')->get('target__id', $this->user->id)->addRestriction('_id', $_GET['until'], '<')->setOrder('_id', 'DESC')->range(0, 20);
		} else {
			$notifications = db()->table('notification')->get('target__id', $this->user->id)->setOrder('_id', 'DESC')->range(0, 20);
		}
		
		$user = db()->table('user')->get('_id', $this->user->id)->fetch();
		$user->lastSeenActivity = time();
		$user->store();
		
		$this->view->set('notifications', $notifications);
	}
	
	/**
	 *
	 * @validate GET#signature (required)
	 * @validate POST#target (required)
	 * @validate POST#content(required string length[1, 250])
	 * @validate POST#url(required string url)
	 * @request-method POST
	 */
	public function push()
	{
		
		#Validate the app
		if (!$this->sso->authApp($_GET['signature'])) {
			throw new PublicException('Invalid signature', 403);
		}
		
		
		#Read POST data
		$srcid    = $_POST['src'];
		$tgtid    = (array)$_POST['target'];
		$content  = str_replace("\r", '', $_POST['content']);
		$url      = $_POST['url'];
		$type     = _def($_POST['type'], 0);
		
		#There needs to be a src user. That means that somebody is originating the
		#notification. There has to be one, and no more than one.
		try {
			$src = AuthorModel::get(db()->table('user')->get('_id', $srcid)->fetch()? : UserModel::makeFromSSO($this->sso->getUser($srcid)));
		}
		catch (\Exception$e) {
			trigger_error(sprintf('User with id %s was not found', $srcid), E_USER_NOTICE);
			$src = null;
		}
		
		$targets = collect($tgtid)->filter(function ($tgtid) use ($src, $content, $url) {
			
			#In the event the user is not registered, and the application is notifying
			#a guest with just an email address.
			if (filter_var($tgtid, FILTER_VALIDATE_EMAIL)) {
				$email   = new \EmailSender($this->sso);
				$email->push($tgtid, $src, $content, $url);
				return false;
			}
			
			return true;
		})
		->each(function ($tgtid) use ($srcid) {
			
			#If sourceID and target are identical, we skip the sending of the notification
			#This requires the application to check whether the user is visiting his own profile
			if ($srcid == $tgtid) {
				return null;
			}
			
			#If there is no user specified we do skip them
			try {
				return db()->table('user')->get('_id', $tgtid)->fetch()? : UserModel::makeFromSSO($this->sso->getUser($tgtid));
			}
			catch (\Exception$e) {
				return null;
			}
		})->filter()->toArray();
		
		
		#It could happen that the target is established as an email and therefore
		#receives notifications directly as emails
		foreach ($targets as $target) {
			#Make it a record
			$notification = db()->table('notification')->newRecord();
			$notification->src     = $src;
			$notification->target  = $target;
			$notification->content = $content;
			$notification->url     = $url;
			$notification->type    = $type;
			
			$this->core->activity->push->do(function ($notification) {
				$notification->store();
			}, $notification);
		}
		
		#This happens if the user defined no targets (this would imply that the ping
		#they sent out was public.
		if (empty($tgtid)) {
			throw new PublicException('Notifications require a target', 400);
		}
	}
}
