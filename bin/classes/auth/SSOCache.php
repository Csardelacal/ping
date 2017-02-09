<?php namespace auth;

class SSOCache
{
	
	private $sso;
	
	private $cache;
	
	public function __construct($endpoint, $appId, $appSecret) {
		$this->sso   = new SSO($endpoint, $appId, $appSecret);
		$this->cache = new \spitfire\cache\MemcachedAdapter();
		$this->cache->setTimeout(3600*24);
	}
	
	public function getUser($id, $token = null) {
		
		if ($token) { return $this->sso->getUser($id, $token); }
		
		return $this->cache->get('sso_user_' . $id, function () use ($id) {
			return $this->sso->getUser($id);
		});
	}
	
	public function getSSO() {
		return $this->sso;
	}
	
	public function __call($name, $arguments) {
		return call_user_func_array(Array($this->sso, $name), $arguments);
	}
}