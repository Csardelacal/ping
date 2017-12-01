<?php

class AuthUtil
{
	
	private $sso;
	
	public function __construct($sso) {
		$this->sso = $sso;
	}
	
	public function checkAppCredentials($appId, $appSec) {
		
		$sso = new \auth\SSO('http://' . $appId . ':' . $appSec . '@localhost/auth');
		
		#Check the application's credentials
		if (!$this->sso->authApp($sso->makeSignature())) {
			throw new PublicException('Aunthentication error', 403);
		}
		
	}
	
}
