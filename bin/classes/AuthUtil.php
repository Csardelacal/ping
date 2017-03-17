<?php

class AuthUtil
{
	
	private $sso;
	
	public function __construct($sso) {
		$this->sso = $sso;
	}
	
	public function checkAppCredentials($appId, $appSec) {
		
		#Check the application's credentials
		if (!$this->sso->authApp($appId, $appSec)) {
			throw new PublicException('Aunthentication error', 403);
		}
		
	}
	
}
