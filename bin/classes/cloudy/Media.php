<?php namespace cloudy;

class Media
{
	
	private $bucket;
	
	private $filename;
	
	private $uniqid;
	
	private $mime;
	
	private $links;
	
	private $servers;
	
	public function __construct($bucket, $filename, $uniqid = null) {
		$this->bucket = $bucket;
		$this->filename = $filename;
		$this->uniqid = $uniqid;
	}
	
	public function getBucket() {
		return $this->bucket;
	}
		
	public function getUniqid() {
		return $this->uniqid;
	}
	
	public function getName() {
		return $this->filename;
	}
	
	public function getMime() {
		return $this->mime;
	}

	public function setMime($mime) {
		$this->mime = $mime;
		return $this;
	}
		
	public function getLinks() {
		return $this->links;
	}
	
	public function setLinks($links) {
		$this->links = $links;
		return $this;
	}
	
	public function getServers() {
		return $this->servers;
	}
	
	public function setServers($servers) {
		$this->servers = $servers;
		return $this;
	}
	
	public function delete() {
		$this->bucket->remove($this->filename);
	}
	
}
