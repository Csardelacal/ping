<?php

use spitfire\exceptions\PublicException;
use spitfire\io\Upload;
use settings\NotificationModel as NotificationSetting;

class PingController extends AppController
{
	
	public function index() {
		
	}
	
	/**
	 * 
	 * @validate >> POST#src (positive number)
	 * @validate >> POST#target (positive number)
	 * @validate >> POST#msg (required string length[0, 250])
	 * @validate >> POST#url (string url)
	 * @validate >> POST#irt (positive number)
	 * 
	 * @request-method POST
	 */
	public function push() {
		
		#Get the applications credentials
		$appId  = isset($_GET['appId']) ? $_GET['appId']  : null;
		$appSec = isset($_GET['appSec'])? $_GET['appSec'] : null;
		
		if ($this->user) {
			$srcid = $this->user->id;
		}
		#Validate the app
		elseif (isset($_GET['signature']) && $this->sso->authApp($_GET['signature'])) {
			$srcid = $_POST['src']?? null;
		}
		/**
		 * This block refers to the old mechanism of authenticating apps against
		 * PHPAS. It would require the app to send both the secret and the id to 
		 * identify itself.
		 * 
		 * @deprecated since version 20181003
		 */
		elseif(isset($_GET['appId'])) {
			trigger_error('Authenticating apps with plaintext secrets is deprecated. Offending app is ' . $_GET['appId'], E_USER_DEPRECATED);
			$authUtil = new AuthUtil($this->sso);
			$authUtil->checkAppCredentials($appId, $appSec);
		}
		else {
			throw new PublicException('Authentication required', 403);
		}
		
		
		#Read POST data
		$tgtid    = $_POST['target']?? null;
		$content  = str_replace("\r", '', $_POST['content']);
		$url      = $_POST['url']?? null;
		$media    = $_POST['media']?? null;
		$irt      = $_POST['irt']?? null;
		$explicit = !!($_POST['explicit']?? false);
		
		#There needs to be a src user. That means that somebody is originating the
		#notification. There has to be one, and no more than one.
		$src = AuthorModel::get(db()->table('user')->get('authId', $srcid)->first()? : UserModel::makeFromSSO($this->sso->getUser($srcid)));
		
		#If a source is sent
		$target = $tgtid === null? null : (db()->table('user')->get('authId', $tgtid)->fetch()? : AuthorModel::get(UserModel::makeFromSSO($this->sso->getUser($tgtid))));
		
		#Prepare an email sender to push emails to whoever needs them
		$email   = new EmailSender($this->sso);
		
		#It could happen that the target is established as an email and therefore
		#receives notifications directly as emails
		if (!($target instanceof UserModel || $target === null)) {
			throw new PublicException('Invalid target', 400);
		}

		#Make it a record
		$notification = db()->table('ping')->newRecord();
		$notification->src = $src;
		$notification->target = $target;
		$notification->content = Mention::mentionsToId($content);
		$notification->url     = $url;
		$notification->explicit= $explicit;
		$notification->irt     = $irt? db()->table('ping')->get('_id', $irt)->first(true) : null;
		$notification->processed = false;
		$notification->locked = false;
		$notification->store();
		
		#If the media is a file, we will store it
		/**
		 * @todo This method should be deprecated in favor of batch processing the
		 * files
		 */
		if ($media instanceof Upload) {
			$media = $media->store()->uri();
		}
		elseif (is_string($media)) {
			$notification->media = $media;
			$notification->store();
			
			$media = [];
		}
		
		#Attach the media
		foreach (array_filter($media) as $file) {
			list($id, $secret) = explode(':', $file);
			$record = db()->table('media\media')->get('_id', $id)->where('secret', $secret)->first(true);
			$record->ping = $notification;
			$record->store();
		}

		#Check the user's preferences and send an email
		$email->push($_POST['target'], $this->sso->getUser($src->authId), $content, $url, null);

		if ($irt) {
			$tgt = db()->table('ping')->get('_id', $irt)->fetch()->src;
			$n = db()->table('notification')->newRecord();
			$n->src     = $src;
			$n->target  = $tgt;
			$n->content = 'Replied to your ping';
			$n->type    = NotificationModel::TYPE_COMMENT;
			$n->url     = strval(url('ping', 'detail', $notification->_id)->absolute());
			$n->store();
		}

		$mentioned = Mention::getMentionedUsers($notification->content);
		foreach ($mentioned as $u) {
			$n = db()->table('notification')->newRecord();
			$n->src     = $src;
			$n->target  = $u;
			$n->content = 'Mentioned you';
			$n->type    = NotificationModel::TYPE_MENTION;
			$n->store();

			#Check the user's preferences and send an email
			if ($u->notify($n->type, NotificationSetting::NOTIFY_EMAIL)) {
				$email->push($n->target->_id, $this->sso->getUser($src->authId), 'Mentioned you', null);
			}
			elseif ($u->notify($n->type, NotificationSetting::NOTIFY_DIGEST)) {
				$email->queue($n);
			}
		}
		
		try {
			$sem = new cron\FlipFlop(spitfire()->getCWD() . '/bin/usr/.media.cron.lock');
			$sem->notify();
		}
		catch (Exception $ex) {
			#Notifying the cron-job failed. Gracefully recover.
		}
		
		$this->view->set('ping', $notification);
		
	}
	
	public function delete($id, $confirm = null) {
		$notification = db()->table('ping')->get('_id', $id)->fetch();
		$salt = sha1('somethingrandom' . $id . (int)(time() / 86400));
		
		if (!$notification) { throw new PublicException('No notification found', 404); }
		
		if (!$this->user) {
			throw new PublicException('Login required', 403);
		}
		
		if ($notification->target === null && $notification->src->_id !== $this->user->id)  
			{ throw new PublicException('No notification found', 404); }
		
		if ($notification->target !== null && $notification->target->_id !== $this->user->id)  
			{ throw new PublicException('No notification found', 404); }
		
		if ($confirm === $salt) {
			$notification->deleted = time();
			$notification->store();
			
			return $this->response->setBody('OK')->getHeaders()->redirect(url('feed'));
		}
		
		$this->view->set('id', $id);
		$this->view->set('salt', $salt);
	}
	
	public function detail($pingid) {
		$ping = db()->table('ping')->get('_id', $pingid)->fetch();
		
		if (!$ping || $ping->deleted) { throw new PublicException('Ping does not exist', 404);}
		
		$this->view->set('user', $this->sso->getUser($ping->src->_id));
		$this->view->set('ping', $ping);
	}
	
	public function replies($pingid) {
		$ping = db()->table('ping')->get('_id', $pingid)->fetch();
		
		if (!$ping || $ping->deleted) { throw new PublicException('Ping does not exist', 404); }
		if ($ping->target && $ping->src->authId !== $this->user->id && $ping->target->authId !== $this->user->id) { throw new PublicException('Ping does not exist', 404); }
		
		$query = $ping->replies->getQuery();
		$g = $query->group();
		$g->addRestriction('target', null, 'IS');
		$g->addRestriction('target', AuthorModel::get(db()->table('user')->get('authId', $this->user? $this->user->id : null)->first()));
		$g->addRestriction('src', AuthorModel::get(db()->table('user')->get('authId', $this->user->id)->first()));
		
		$query->setOrder('_id', 'desc');
		
		if (isset($_GET['until'])) { $query->addRestriction('_id', $_GET['until'], '<'); }
		
		$replies = $query->range(0, 20);
		
		//$this->view->set('user',          $this->sso->getUser($ping->src->_id));
		//$this->view->set('ping',          $ping);
		$this->view->set('notifications', $replies);
	}
	
	public function share($pingid) {
		$original = db()->table('ping')->get('_id', $pingid)->fetch();
		$dispatcher = new NotificationDispatcher($this->sso, db());
		
		if (!$this->user)                    { throw new PublicException('Log in required', 403); }
		if (!$original || $original->target) { throw new PublicException('Ping cannot be shared', 403); }
		
		$src = db()->table('user')->get('authId', $this->user->id)->fetch();
		$count = 0;
		
		do {
			$dispatcher->push($src, $original->src, 'Shared your ping', strval(url('ping', 'detail', $original->_id)->absolute()), NotificationModel::TYPE_SHARE);
			$original = $original->share? : $original;
			$count++;
		} 
		while ($count < 10 && $original->share);
		
		$shared = db()->table('ping')->newRecord();
		$shared->_id     = null;
		$shared->src     = $src;
		$shared->target  = null;
		$shared->content = $original->content;
		$shared->url     = $original->url;
		$shared->media   = $original->media;
		$shared->explicit= $original->explicit;
		$shared->deleted = $original->deleted;
		$shared->created = time();
		$shared->irt     = $original->irt;
		$shared->share   = $original;
		$shared->store();
		
		$this->view->set('shared', $shared);
	}
}