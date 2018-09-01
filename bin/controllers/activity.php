<?php

use spitfire\exceptions\PublicException;
use settings\NotificationModel as NotificationSetting;

class ActivityController extends AppController
{
	
	public function index() {
		if (!$this->user) { throw new PublicException('Auth error: Login required', 403); }
		
		$this->secondaryNav->add(url('feed'), 'Feed');
		$this->secondaryNav->add(url('activity'), 'Activity')->setActive(true);
		$this->secondaryNav->add(url('people', 'followingMe'), 'Followers');
		$this->secondaryNav->add(url('people', 'iFollow'), 'Following');
		
		if (isset($_GET['until'])) {
			$notifications = db()->table('notification')->get('target__id', $this->user->id)->addRestriction('_id', $_GET['until'], '<')->setOrder('_id', 'DESC')->range(0, 50);
		} else {
			$notifications = db()->table('notification')->get('target__id', $this->user->id)->setOrder('_id', 'DESC')->range(0, 50);
		}
		
		$user = db()->table('user')->get('_id', $this->user->id)->fetch();
		$user->lastSeenActivity = time();
		$user->store();
		
		$this->view->set('notifications', $notifications);
	}
	
	/**
	 * 
	 * @request-method POST
	 */
	public function push() {
		
		#Get the applications credentials
		$appId  = isset($_GET['appId']) ? $_GET['appId']  : null;
		$appSec = isset($_GET['appSec'])? $_GET['appSec'] : null;
		
		#Validate the app
		if (isset($_GET['signature'])) {
			if(!$this->sso->authApp($_GET['signature'])->isAuthenticated()) {
				throw new PublicException('Invalid signature', 403);
			}
		}
		else {
			$authUtil = new AuthUtil($this->sso);
			$authUtil->checkAppCredentials($appId, $appSec);
		}
		
		
		#Read POST data
		$srcid    = _def($_POST['src'], null);
		$tgtid    = (array)_def($_POST['target'], null);
		$content  = str_replace("\r", '',_def($_POST['content'], null));
		$url      = _def($_POST['url'], null);
		$type     = _def($_POST['type'], 0);
		
		
		#Validation
		$v = Array();
		$v['msg']   = validate($content)->minLength(1, 'Content cannot be empty')->maxLength(250, 'Ping is too long');
		$v['url']   = $url   === null? null : validate($url)->asURL('URL needs to be a URL');
		validate($v['msg'], $v['url']);
		
		#There needs to be a src user. That means that somebody is originating the
		#notification. There has to be one, and no more than one.
		$src = db()->table('user')->get('authId', $srcid)->fetch()? : UserModel::makeFromSSO($this->sso->getUser($srcid));
		
		$targets = array_filter(array_map(function ($tgtid) use ($srcid) {
			
			#If sourceID and target are identical, we skip the sending of the notification
			#This requires the application to check whether the user is visiting his own profile
			if ($srcid == $tgtid) { return null; }
			
			#If there is no user specified we do skip them
			try { return db()->table('user')->get('authId', $tgtid)->fetch()? : UserModel::makeFromSSO($this->sso->getUser($tgtid)); } 
			catch (Exception$e) { return $tgtid; }
			
		}, $tgtid));
		
		
		#Prepare an email sender to push emails to whoever needs them
		$email   = new EmailSender($this->sso);
		
		#It could happen that the target is established as an email and therefore
		#receives notifications directly as emails
		foreach ($targets as $target) {
			if ($target instanceof UserModel) {
				
				#Make it a record
				$notification = db()->table('notification')->newRecord();
				$notification->src = $src;
				$notification->target = $target;
				$notification->content = Mention::mentionsToId($content);
				$notification->url     = $url;
				$notification->type    = $type;
				$notification->store();

				#Check the user's preferences and send an email
				if ($target->notify($notification->type, NotificationSetting::NOTIFY_EMAIL)) {
					$email->push($notification->target->_id, $this->sso->getUser($src->authId), $content, $url);
				}
				elseif ($target->notify($notification->type, NotificationSetting::NOTIFY_DIGEST)) {
					$email->queue($notification);
				}
			}
			# Notify the user via mail.
			elseif (filter_var($_POST['target'], FILTER_VALIDATE_EMAIL)) {
				$email->push($_POST['target'], $this->sso->getUser($src->authId), $content, $url);
			}
		}
		
		#This happens if the user defined no targets (this would imply that the ping 
		#they sent out was public.
		if (empty($tgtid))  {
			throw new PublicException('Notifications require a target', 400);
		}
		
	}
	
}