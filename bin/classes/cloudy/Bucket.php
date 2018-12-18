<?php namespace cloudy;

use CURLFile;
use PHPUnit\Runner\Exception;
use function mime;

class Bucket
{
	
	private $ctx;
	
	private $master;
	
	private $uniqid;
	
	public function __construct($uniqid, $master, $ctx) {
		$this->ctx = $ctx;
		$this->master = $master;
		$this->uniqid = $uniqid;
	}
	
	public function upload($file, $name = null) {
		
		if (filesize($file) === 0) {
			throw new \spitfire\exceptions\PrivateException('File is empty', 1811091157);
		}
		
		$r = $this->master->request('/media/create.json');
		$r->get('signature', (string)$this->ctx->signature());
		$r->post('bucket', $this->uniqid);
		$r->post('name', $name? : basename($file));
		$r->post('file', new CURLFile($file));
		$r->post('mime', mime($file));
		
		try {
			$response = $r->send()->expect(200)->json();
		} catch (\Exception $ex) {
			var_dump($ex->getTraceAsString());
			die($r->send()->html());
		}
		
		$media = new Media($this, $response->name, $response->uniqid);
		$media->setLinks([$response->link]);
		$media->setMime(mime($file));
		return $media;
	}
	
	public function getMedia($name) {
		
		$r = $this->master->request(sprintf('/media/read/%s/%s.json', $this->uniqid, urlencode($name)));
		$r->get('signature', (string)$this->ctx->signature());
		$response = $r->send()->expect(200)->json();
		
		$servers = $links = [];
		
		foreach ($response->servers as $server) {
			$servers[] = new Server($server->hostname);
		}
		
		foreach ($response->links as $link) {
			$links[] = $link->uniqid;
		}
		
		
		$file = new Media($this, $name, $response->uniqid);
		$file->setMime($response->mime);
		$file->setLinks($links);
		$file->setServers($servers);
		
		return $file;
	}
	
	public function remove($filename) {
		$r = $this->master->request(sprintf('/media/delete/%s/%s.json', $this->uniqid, urlencode($filename)));
		$r->get('signature', (string)$this->ctx->signature());
		$r->send()->expect(200)->json();
		
		return true;
	}
	
	public function getCloudy() {
		return $this->ctx;
	}
	
	public function uniqid() {
		return $this->uniqid;
	}
	
}
