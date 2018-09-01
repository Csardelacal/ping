<?php

class AuthUtil
{
	
	/**
	 *
	 * @var auth\SSO
	 */
	private $sso;
	
	public function __construct($sso) {
		$this->sso = $sso;
	}
	
	public function checkAppCredentials($appId, $appSec) {
		
		$sso = new \auth\SSO('https://' . $appId . ':' . $appSec . '@' . substr($this->sso->getEndpoint(), 0, strlen('https://')));
		
		#Check the application's credentials
		if (!$this->sso->authApp($sso->makeSignature())->isAuthenticated()) {
			throw new PublicException('Aunthentication error', 403);
		}
		
	}
	
}
