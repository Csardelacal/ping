<?php

class EmailSender
{
	
	private $sso;
	
	public function __construct(\auth\SSO$sso) {
		$this->sso = $sso;
	}
	
	public function push($email, $src, $content, $url, $media) {
		$a = Array('src' => $src, 'content' => $content, 'url' => $url, 'media' => $media);
		$v = new _SF_ViewElement('bin/templates/email/notification.php', $a);
		$t = $v->render();
		
		$this->sso->sendEmail($email, sprintf('[%s] %s', spitfire\core\Environment::get('site.name')? : 'Ping', Strings::ellipsis($content, 50)), $t);
	}
	
}
