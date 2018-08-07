<?php

use auth\SSO;
use auth\SSOCache;
use spitfire\core\Environment;

class EmailSender
{
	
	private $sso;
	
	public function __construct($sso) {
		
		if (!$sso instanceof SSO && !$sso instanceof SSOCache) {
			throw new BadMethodCallException('Invalid arguments', 701101433);
		}
		
		$this->sso = $sso;
	}
	
	public function push($email, $src, $content, $url, $media) {
		$a = Array('src' => $src, 'content' => $content, 'url' => $url, 'media' => $media);
		$v = new \spitfire\io\template\Template('bin/templates/email/notification.php');
		$t = $v->render($a);
		
		$this->sso->sendEmail($email, sprintf('[%s] %s: %s', Environment::get('site.name')? : 'Ping', $src->getUsername(), Strings::ellipsis($content, 50)), $t);
	}
	
	public function queue($notification) {
		$r = db()->table('email\digestqueue')->newRecord();
		$r->user         = $notification->target;
		$r->type         = $notification->type;
		$r->notification = $notification;
		$r->store();
	}
	
	public function sendDigest($tgt) {
		$a = Array('tgt' => $tgt, 'sso' => $this->sso);
		$v = new \spitfire\io\template\Template('bin/templates/email/digest.php');
		$t = $v->render($a);
		
		$this->sso->sendEmail(strval($tgt->getId()), sprintf('[%s] %s', Environment::get('site.name')? : 'Ping', 'Your daily digest'), $t);
	}
	
}
