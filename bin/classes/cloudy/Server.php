<?php namespace cloudy;

class Server
{
	
	private $endpoint;
	
	public function __construct($endpoint) {
		$this->endpoint = $endpoint;
	}
	
	public function getEndpoint() {
		return $this->endpoint;
	}
		
	public function request($url) {
		return request($this->endpoint . $url);
	}
	
}
