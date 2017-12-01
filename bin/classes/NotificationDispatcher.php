<?php 

use NotificationModel;
use settings\NotificationModel as NotificationSetting;

/**
 * 
 * @author César de la Cal Bretschneider <cesar@magic3w.com>
 */
class NotificationDispatcher
{
	
	private $sso;
	
	private $db;
	
	private $email;
	
	public function __construct($sso, $db) {
		$this->sso = $sso;
		$this->db = $db;
		$this->email = new EmailSender($this->sso);
	}
	
	public function push($src, $target, $content, $url, $type = NotificationModel::TYPE_OTHER) {
		
		$notification = db()->table('notification')->newRecord();
		$notification->src     = $src;
		$notification->target  = $target;
		$notification->content = $content;
		$notification->url     = $url;
		$notification->type    = $type;
		$notification->store();
		
		
		#Check the user's preferences and send an email
		if ($target->notify($notification->type, NotificationSetting::NOTIFY_EMAIL)) {
			$this->email->push($notification->target->_id, $this->sso->getUser($src->authId), $content, $url, null);
		}
		elseif ($target->notify($notification->type, NotificationSetting::NOTIFY_DIGEST)) {
			$this->email->queue($notification);
		}
	}
	
}