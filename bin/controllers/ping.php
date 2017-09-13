<?php

use spitfire\exceptions\PublicException;

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
		$authUtil = new AuthUtil($this->sso);
		$this->user || $authUtil->checkAppCredentials($appId, $appSec);
		
		
		#Read POST data
		$srcid    = isset($this->user)? $this->user->id : _def($_POST['src'], null);
		$tgtid    = (array)_def($_POST['target'], null);
		$content  = str_replace("\r", '',_def($_POST['content'], null));
		$url      = _def($_POST['url'], null);
		$media    = _def($_POST['media'], null);
		$explicit = !!_def($_POST['explicit'], false);
		
		#If the media is a file, we will store it
		if ($media instanceof spitfire\io\Upload) {
			$media = 'file:' . $media->store();
		}
		
		#Validation
		$v = Array();
		$v['msg']   = validate($content)->minLength(1, 'Content cannot be empty')->maxLength(250, 'Ping is too long');
		$v['url']   = $url   === null? null : validate($url)->asURL('URL needs to be a URL');
		$v['media'] = $media === null? null : validate($media)->asURL('Media needs to be a file or URL');
		validate($v['msg'], $v['url'], $v['media']);
		
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
				$notification->store();

				#Check the user's preferences and send an email
				$email->push($_POST['target'], $this->sso->getUser($src->authId), $content, $url, $media);
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
			$notification->store();
			
			$mentioned = Mention::getMentionedUsers($notification->content);
			foreach ($mentioned as $u) {
				$n = db()->table('notification')->newRecord();
				$n->src     = $src;
				$n->target  = $u;
				$n->content = 'Mentioned you';
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
	
}