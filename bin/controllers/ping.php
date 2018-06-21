<?php

use spitfire\exceptions\PublicException;
use spitfire\io\Upload;
use spitfire\validation\PositiveNumberValidationRule;
use settings\NotificationModel as NotificationSetting;

class PingController extends AppController
{
	
	public function index() {
		
	}
	
	/**
	 * 
	 * @todo This should allow for multiple targets to be defined at once.
	 * @request-method POST
	 */
	public function push() {
		
		#Get the applications credentials
		$appId  = isset($_GET['appId']) ? $_GET['appId']  : null;
		$appSec = isset($_GET['appSec'])? $_GET['appSec'] : null;
		
		#Validate the app
		if (isset($_GET['signature'])) {
			$this->user || $this->sso->authApp($_GET['signature']);
		}
		else {
			$authUtil = new AuthUtil($this->sso);
			$this->user || $authUtil->checkAppCredentials($appId, $appSec);
		}
		
		
		#Read POST data
		$srcid    = isset($this->user)? $this->user->id : _def($_POST['src'], null);
		$tgtid    = (array)_def($_POST['target'], null);
		$content  = str_replace("\r", '',_def($_POST['content'], null));
		$url      = _def($_POST['url'], null);
		$media    = _def($_POST['media'], null);
		$irt      = _def($_POST['irt'], null);
		$explicit = !!_def($_POST['explicit'], false);
		
		#If the media is a file, we will store it
		if ($media instanceof Upload) {
			$media = 'file:' . $media->store();
		}
		
		#Validation
		$v = Array();
		$v['msg']   = validate($content)->minLength(1, 'Content cannot be empty')->maxLength(250, 'Ping is too long');
		$v['url']   = $url   === null? null : validate($url)->asURL('URL needs to be a URL');
		$v['media'] = $media === null? null : validate($media)->asURL('Media needs to be a file or URL');
		$v['irt']   = $irt   === null? null : validate($irt)->addRule(new PositiveNumberValidationRule('IRT id is invalid'))->addRule(new ClosureValidationRule(function ($e) { return db()->table('ping')->get('_id', $e)->fetch()? false : 'Invalid ping ID'; }));
		validate($v['msg'], $v['url'], $v['media'], $v['irt']);
		
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
				$notification = db()->table('ping')->newRecord();
				$notification->src = $src;
				$notification->target = $target;
				$notification->content = Mention::mentionsToId($content);
				$notification->url     = $url;
				$notification->media   = $media;
				$notification->explicit= $explicit;
				$notification->irt     = $irt? db()->table('ping')->get('_id', $irt)->fetch() : null;
				$notification->store();

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
			}
			# Notify the user via mail.
			elseif (filter_var($_POST['target'], FILTER_VALIDATE_EMAIL)) {
				$email->push($_POST['target'], $this->sso->getUser($src->authId), $content, $url, $media);
			}
		}
		
		#This happens if the user defined no targets (this would imply that the ping 
		#they sent out was public.
		if (empty($tgtid))  {
			#Make it a record
			$notification = db()->table('ping')->newRecord();
			$notification->src = $src;
			$notification->target = null;
			$notification->content = Mention::mentionsToId($content);
			$notification->url     = $url;
			$notification->media   = $media;
			$notification->explicit= $explicit;
			$notification->irt     = $irt? db()->table('ping')->get('_id', $irt)->fetch() : null;
			$notification->store();
			
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
		}
		
	}
	
	public function delete($id, $confirm = null) {
		$notification = db()->table('ping')->get('_id', $id)->fetch();
		$salt = sha1('somethingrandom' . $id . (int)(time() / 86400));
		
		if (!$notification) { throw new PublicException('No notification found', 404); }
		
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
		$g->addRestriction('target', db()->table('user')->get('authId', $this->user->id));
		$g->addRestriction('src', db()->table('user')->get('authId', $this->user->id));
		
		$query->setResultsPerPage(20);
		$query->setOrder('_id', 'desc');
		
		if (isset($_GET['until'])) { $query->addRestriction('_id', $_GET['until'], '<'); }
		
		$replies = $query->fetchAll();
		
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