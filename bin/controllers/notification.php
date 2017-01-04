<?php

use spitfire\exceptions\PublicException;

class NotificationController extends AppController
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
		
		#Check the application's credentials
		if (!$this->user && !$this->sso->authApp($appId, $appSec)) {
			throw new PublicException('Aunthentication error', 403);
		}
		
		#Read POST data
		$srcid   = isset($this->user)? $this->user->id : _def($_POST['src'], null);
		$tgtid   = _def($_POST['target'], null);
		$email   = new EmailSender($this->sso);
		
		#Construct the required data
		$src = db()->table('user')->get('authId', $srcid)->fetch()? : UserModel::makeFromSSO($this->sso->getUser($srcid));
		
		#If sourceID and target are identical, we skip the sending of the notification
		#This requires the application to check whether the user is visiting his own profile
		if ($srcid == $tgtid) { 
			return; //The user should be aware he did the action that would send himself a notification.
		}
		
		try {
			$target = db()->table('user')->get('authId', $tgtid)->fetch()? : UserModel::makeFromSSO($this->sso->getUser($tgtid));
		} catch (Exception$e) {
			$target = null;
		}
		
		$content = $_POST['content'];
		$url     = $_POST['url'];
		$media   = $_POST['media'];
		
		#It could happen that the target is established as an email and therefore
		#receives notifications directly as emails
		if (!$target && isset($_POST['target']) && filter_var($_POST['target'], FILTER_VALIDATE_EMAIL)) {
			# Notify the user via mail.
			$email->push($_POST['target'], $this->sso->getUser($src->authId), $content, $url, $media);
		}
		else {
			#Make it a record
			$notification = db()->table('notification')->newRecord();
			$notification->src = $src;
			$notification->target = $target;
			$notification->content = $content;
			$notification->url     = $url;
			$notification->media   = $media;
			$notification->store();
			
			#Check the user's preferences and send an email
			//TODO: Add check to verify the user has chosen to receive notifications
			if ($target) {
				$email->push($_POST['target'], $this->sso->getUser($src->authId), $content, $url, $media);
			}
			
		}
		
	}
	
}