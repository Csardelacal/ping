<?php namespace auth;

use Exception;

class Token
{
	
	private $sso;
	private $token;
	private $expires;
	private $redirect;
	
	public function __construct($sso, $token, $expires, $redirect) {
		$this->sso = $sso;
		$this->token = $token;
		$this->expires = $expires;
		$this->redirect = $redirect;
	}
	
	public function getId() {
		return $this->token;
	}
	
	public function getRedirect($successURI, $failureURI = null) {
		return $this->redirect . '?' . http_build_query(Array('returnurl' => strval($successURI), 'cancelurl' => strval($failureURI)));
	}
	
	public function getTokenInfo() {
		static $cache = null;
		
		if ($cache !== null) { return $cache; }
		
		$response = file_get_contents($this->sso->getEndpoint() . '/auth/index/' . $this->token . '.json');
		
		if (!isset($http_response_header))            { throw new Exception('SSO connection failed'); }
		if (!strstr($http_response_header[0], '200')) { throw new Exception('SSO error'); }
		
		return $cache = json_decode($response);
	}
	
	public function isAuthenticated() {
		return $this->getTokenInfo()->authenticated;
	}
}