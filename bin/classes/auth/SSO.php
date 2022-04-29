<?php namespace auth;

use Exception;
use signature\Hash;
use signature\Signature;

class SSO
{
	
	private $endpoint;
	private $appId;
	private $appSecret;
	
	public function __construct($credentials) {
		$reflection = URLReflection::fromURL($credentials);
		
		$this->endpoint  = rtrim($reflection->getProtocol() . '://' . $reflection->getServer() . ':' . $reflection->getPort() . $reflection->getPath(), '/');
		$this->appId     = $reflection->getUser();
		$this->appSecret = $reflection->getPassword();
		
		if (!$this->appSecret) {
			throw new Exception('App Secret is missing', 1807021658);
		}
	}
	
	/**
	 * Creates a new SSO Token. This allows your application to request a single
	 * user's token and manage it.
	 */
	public function createToken($expires = null) {
		/*
		 * Fetch the JSON message from the endpoint. This should tell us whether 
		 * the request was a success.
		 */
		$get = Array('appID' => $this->appId, 'appSecret' => $this->appSecret);
		if ($expires !== null) { $get['expires'] = $expires; }
		
		$response = file_get_contents($this->endpoint . '/token/create.json?' . 
				  http_build_query($get));
		
		if (!strstr($http_response_header[0], '200')) { throw new Exception('SSO rejected the token with ' . $http_response_header[0], 1605201109); }

		$data = json_decode($response);

		if (json_last_error() !== JSON_ERROR_NONE) { throw new Exception('SSO sent invalid json response - ' . json_last_error_msg(), 1608012100); }
		
		return new Token($this, $data->token, $data->expires, $data->location);
	}
	
	/**
	 * Instances a token. As opposed to the createToken method, this token cannot
	 * be authorized afterwards. 
	 * 
	 * @param string $token
	 * @return Token
	 */
	public function makeToken($token) {
		return new Token($this, $token, null, null);
	}
	
	public function getUser($username, Token$token = null) {
		
		if (!$username) { throw new Exception('Valid user id needed'); }
		
		/*
		 * Assemble the request we need to retrieve the data. Please note that if
		 * there is no token we pass no parameters.
		 */
		$request = new Request(
			$this->endpoint . '/user/detail/' . $username . '.json',
			$token && $token->isAuthenticated()? Array('token' => $token->getTokenInfo()->token, 'signature' => (string)$this->makeSignature()) : Array('signature' => (string)$this->makeSignature())
		);
		
		/*
		 * Fetch the JSON message from the endpoint. This should tell us whether 
		 * the request was a success.
		 */
		$resp = $request->send();
		$data = json_decode($resp)->payload;
		
		return new User($data->id, $data->username, $data->aliases, $data->groups, $data->verified, $data->registered_unix, $data->attributes, $data->avatar);
	}
	
	/**
	 * 
	 * @param string $signature
	 * @param string $token
	 * @param string $context
	 * @return AppAuthentication
	 */
	public function authApp($signature, $token = null, $context = null) {		
		if ($token instanceof Token) {
			$token = $token->getId();
		}
		
		$request = new Request(
			$this->endpoint . '/auth/app.json',
			array_filter(Array('token' => $token, 'signature' => (string)$this->makeSignature(), 'remote' => $signature, 'context' => $context))
		);
		
		$response = $request->send();
		
		$json = json_decode($response);
		$src  = new App($json->local->id, $this->appSecret, $json->local->name);
		
		if (isset($json->remote)) {
			$app = new App($json->remote->id, null, $json->remote->name);
		}
		else {
			$app = null;
		}
		
		if ($json->context) {
			$contexts = [];
			foreach ($json->context as $jsctx) {
				$ctx = new Context($this, $app, $jsctx->id);
				$ctx->setExists(!$jsctx->undefined);
				$ctx->setGranted($jsctx->granted);
				$contexts[$jsctx->id] = $ctx;
			}
		}
		else {
			$contexts = [];
		}
		
		$res  = new AppAuthentication($this, $src, $app, $contexts, $json->token);
		
		return $res;
	}
	
	public function sendEmail($userid, $subject, $body) {
		
		$request = new Request(
			$this->endpoint . '/email/send/' . $userid . '.json',
			Array('appId' => $this->appId, 'appSecret' => $this->appSecret)
		);
		
		$response = $request->send(Array('body' => $body, 'subject' => $subject));
		$data = json_decode($response)->payload;
		
		return $data;
	}
	
	public function getEndpoint() {
		return $this->endpoint;
	}
	
	public function getAppId() {
		return $this->appId;
	}
	
	public function makeSignature($target = null, $contexts = []) {
		$signature = new Signature(Hash::ALGO_DEFAULT, $this->appId, $this->appSecret, $target, $contexts);
		return $signature;
	}
	
	public function getAppList() {
		$url      = $this->endpoint . '/appdrawer/index.json';
		$request  = new Request($url, ['signature' => (string)$this->makeSignature(), 'all' => 'yes']);
		
		$response = $request->send();
		$data     = JSON::decode($response);
		
		return $data;
	}
	
	public function getAppDrawer() {
		$url = $this->endpoint . '/appdrawer/index.json';
		$request  = new Request($url, []);
		
		$response = $request->send();
		$data     = JSON::decode($response);
		
		return $data;
	}
	
	public function getAppDrawerJS() {
		return $this->endpoint . '/appdrawer/index.js';
	}
	
	public function getGroupList() {
		$url  = $this->endpoint . '/group/index.json';
		$resp = file_get_contents($url);
		
		if (!strstr($http_response_header[0], '200')) { 
			throw new Exception('SSO rejected the request with ' . $http_response_header[0], 201605201109);
		}
		
		$data = json_decode($resp);
		return $data->payload;
	}
	
	public function getGroup($id) {
		$url  = $this->endpoint . '/group/detail/' . $id . '.json';
		$resp = file_get_contents($url);
		
		if (!strstr($http_response_header[0], '200')) { 
			throw new Exception('SSO rejected the request with ' . $http_response_header[0], 201605201109);
		}
		
		$data = json_decode($resp);
		return $data->payload;
	}
	
}

