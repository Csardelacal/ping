<?php namespace auth;

class Token
{
	
	private $sso;
	private $token;
	private $redirect;
	
	public function __construct($sso, $token, $redirect) {
		$this->sso = $sso;
		$this->token = $token;
		$this->redirect = $redirect;
	}
	
	public function getRedirect($successURI, $failureURI = null) {
		return $this->redirect . '?' . http_build_query(Array('returnurl' => $successURI));
	}
	
	public function getTokenInfo() {
		static $cache = null;
		
		if ($cache !== null) { return $cache; }
		
		$response = file_get_contents($this->sso->getEndpoint() . '/auth/index/' . $this->token . '.json');
		
		if (!strstr($http_response_header[0], '200')) { throw new \Exception('SSO error'); }
		
		return $cache = json_decode($response);
	}
	
	public function isAuthenticated() {
		return $this->getTokenInfo()->authenticated;
	}
}