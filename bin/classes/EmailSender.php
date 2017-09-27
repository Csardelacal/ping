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
		$v = new _SF_ViewElement('bin/templates/email/notification.php', $a);
		$t = $v->render();
		
		$this->sso->sendEmail($email, sprintf('[%s] %s: %s', Environment::get('site.name')? : 'Ping', $src->getUsername(), Strings::ellipsis($content, 50)), $t);
	}
	
}
