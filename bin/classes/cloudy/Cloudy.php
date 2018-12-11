<?php namespace cloudy;

class Cloudy 
{
	
	private $sso;
	
	private $endpoint;
	
	private $appId;
	
	
	public function __construct($endpoint, $sso) {
		$reflection = URLReflection::fromURL($endpoint);
		
		$this->endpoint  = rtrim($reflection->getProtocol() . '://' . $reflection->getServer() . ':' . $reflection->getPort() . $reflection->getPath(), '/');
		$this->appId     = $reflection->getUser();
		
		$this->sso = $sso instanceof \auth\SSO || $sso instanceof \auth\SSOCache? $sso : new \auth\SSOCache($sso);
	}
	
	public function bucket($uniqid) {
		$cache = new \spitfire\cache\MemcachedAdapter();
		
		$response = $cache->get('cloudy_bucket_' . $uniqid, function() use ($uniqid) {
			$r = request(sprintf('%s/bucket/read/%s.json', $this->endpoint, $uniqid));
			$r->get('signature', (string)$this->sso->makeSignature($this->appId));

			try {
				$response = $r->send()->expect(200)->json();
			} 
			catch (\Exception $ex) {
				die($r->send()->html());
			}

			if (!isset($response->payload)) {
				var_dump($response);
				die();
			}
			
			return $response;
		});
		
		$server   = $response->payload->master->hostname;
		
		return new Bucket($uniqid, new Server($server), $this);
	}
	
	public function sso() {
		return $this->sso;
	}
	
	public function signature() {
		return (string)$this->sso->makeSignature($this->appId);
	}
	
}
