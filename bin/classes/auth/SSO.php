<?php namespace auth;

use Exception;
use spitfire\exceptions\PrivateException;

class SSO
{
	
	private $endpoint;
	private $appId;
	private $appSecret;
	
	public function __construct($endpoint, $appId, $appSecret) {
		$this->endpoint  = rtrim($endpoint, '/');
		$this->appId     = $appId;
		$this->appSecret = $appSecret;
	}
	
	/**
	 * Creates a new SSO Token. This allows your application to request a single
	 * user's token and manage it.
	 */
	public function createToken() {
		/*
		 * Fetch the JSON message from the endpoint. This should tell us whether 
		 * the request was a success.
		 */
		$response = file_get_contents($this->endpoint . '/token/create.json?' . 
				  http_build_query(Array('appID' => $this->appId, 'appSecret' => $this->appSecret)));
		
		if (!strstr($http_response_header[0], '200')) { throw new Exception('SSO rejected the token with ' . $http_response_header[0], 1605201109); }

		$data = json_decode($response);

		if (json_last_error() !== JSON_ERROR_NONE) { throw new Exception('SSO sent invalid json response - ' . json_last_error_msg(), 1608012100); }
		
		return new Token($this, $data->token, $data->location);
	}
	
	/**
	 * Instances a token. As opposed to the createToken method, this token cannot
	 * be authorized afterwards. 
	 * 
	 * @param string $token
	 * @return \auth\Token
	 */
	public function makeToken($token) {
		return new Token($this, $token, null);
	}
	
	public function getUser($username, Token$token = null) {
		
		if (!$username) { throw new Exception('Valid user id needed'); }
		
		$url = $this->endpoint . '/user/detail/' . $username . '.json';
		if ($token && $token->isAuthenticated()) { $url.= '?' . http_build_query(Array('token' => $token->getTokenInfo()->token)); }
		
		/*
		 * Fetch the JSON message from the endpoint. This should tell us whether 
		 * the request was a success.
		 */
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		$response = curl_exec($ch);

		$http_response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if ($http_response_code !== 200) {
			throw new Exception('SSO rejected the request' . $response, 1605141533);
		}
		
		$data = json_decode($response)->payload;
		
		return new User($data->id, $data->username, $data->aliases, $data->groups, $data->verified, $data->registered_unix, $data->attributes, $data->avatar);
	}
	
	public function sendEmail($userid, $subject, $body) {
		
		$url = $this->endpoint . '/email/send/' . $userid . '.json';
		$url.= '?' . http_build_query(Array('appId' => $this->appId, 'appSecret' => $this->appSecret));
		
		/*
		 * Fetch the JSON message from the endpoint. This should tell us whether 
		 * the request was a success.
		 */
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, Array('body' => $body, 'subject' => $subject));
		
		$response = curl_exec($ch);
		
		if ( curl_getinfo($ch, CURLINFO_HTTP_CODE) !== 200) { throw new Exception('SSO rejected the request' . $response, 1605141533); }
		
		$data = json_decode($response)->payload;
		
		return $data;
	}
	
	public function getEndpoint() {
		return $this->endpoint;
	}
	
	public function getAppId() {
		return $this->appId;
	}
	
	public function getAppSecret() {
		return $this->appSecret;
	}
	
}

