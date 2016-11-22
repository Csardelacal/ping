<?php

class NotificationController extends AppController
{
	
	public function index() {
		
	}
	
	public function push() {
		
		#Get the applications credentials
		$appId  = isset($_GET['appId']) ? $_GET['appId']  : null;
		$appSec = isset($_GET['appSec'])? $_GET['appSec'] : null;
		
		#Check the application's credentials
		
		#Read POST data
		$src     = db()->table('user')->get('authId', isset($this->user)? $this->user->id : _def($_POST['src'], null))->fetch();
		$target  = db()->table('user')->get('authId', $_POST['target'])->fetch();
		$content = $_POST['content'];
		$url     = $_POST['url'];
		$media   = $_POST['media'];
		
		#Check if the source is valid
		if (!$src) {
			$u = $this->sso->getUser(isset($this->user)? $this->user->id : _def($_POST['src'], null));
			
			if ($u) { 
				$src = db()->table('user')->newRecord(); 
				$src->authId = $u->getId();
				$src->store();
			} else {
				throw new \spitfire\exceptions\PublicException('No user found', 400);
			}
		}
		
		#Repeat with the target
		if (!$target && isset($_POST['target'])) {
			$u = $this->sso->getUser($_POST['target']);
			
			if ($u) { 
				$target = db()->table('user')->newRecord(); 
				$target->authId = $u->getId();
				$target->store();
			} else {
				throw new \spitfire\exceptions\PublicException('No user found', 400);
			}
		}
		
		#Make it a record
		$notification = db()->table('notification')->newRecord();
		$notification->src = $src;
		$notification->target = $target;
		$notification->content = $content;
		$notification->url     = $url;
		$notification->media   = $media;
		$notification->store();
		
	}
	
}