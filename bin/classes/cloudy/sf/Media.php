<?php namespace cloudy\sf;

use cloudy\Media as ParentClass;
use spitfire\storage\objectStorage\DirectoryInterface;
use spitfire\storage\objectStorage\EmbedInterface;
use spitfire\storage\objectStorage\FileInterface;
use spitfire\storage\objectStorage\NodeInterface;

class Media implements EmbedInterface
{
	
	private $parent;
	
	private $media;
	
	private $body = null;
	
	public function __construct($parent, $media) {
		$this->parent = $parent;
		$this->media = $media;
	}
	
	public function basename(): string {
		$_ret = $this->media instanceof ParentClass? $this->media->getName() : $this->media;
		
		if ($_ret === null) {
			throw new \spitfire\exceptions\PrivateException('Bug detected', 1809141314);
		}
		
		return $_ret;
	}

	public function delete(): bool {
		//TODO Implement
		return false;
	}

	public function isWritable(): bool {
		return true;
	}

	public function mime(): string {
		return $this->media instanceof ParentClass? $this->media->getMime() : null;
	}

	public function move(DirectoryInterface $to, string $name): FileInterface {
		//TODO: Implement
	}

	public function read(): string {
		if ($this->body !== null) { return $this->body; }
		
		$servers = $this->media->getServers();
		
		if (!count($servers)) {
			throw new \spitfire\exceptions\PrivateException('No servers for ' . $this->media->getName());
		}
		
		$server  = $servers[rand(0, count($servers) - 1)]->getEndpoint();
		$links   = $this->media->getLinks();
		
		$r = request($server . '/file/retrieve/link/' . reset($links));
		$r->get('signature', (string)$this->media->getBucket()->getCloudy()->signature());
		
		spitfire()->log(sprintf('Trying to fetch file %s from %s', $this->media->getUniqid(), $server));
		
		return $this->body = $r->send()->expect(200)->html();
	}

	public function up(): NodeInterface {
		return $this->parent;
	}

	public function uri(): string {
		return $this->up()->uri() . $this->basename();
	}

	public function write(string $data): bool {
		$tmp  = tmpfile();
		fwrite($tmp, $data);
		
		$meta_data = stream_get_meta_data($tmp);
		$filename = $meta_data["uri"];
		
		$this->media = $this->parent->getBucket()->upload($filename, $this->basename());
		$this->body = $data;
		unlink($tmp);
		return true;
	}

	public function publicURI() {
		$servers = $this->media->getServers();
		$links   = $this->media->getLinks();
		
		if (!count($servers)) {
			throw new \spitfire\exceptions\PrivateException('No servers for ' . $this->media->getName());
		}
		
		return $servers[rand(0, count($servers) - 1)]->getEndpoint() . '/file/retrieve/link/' . reset($links);
	}

	public function filename(): string {
		
		$_ret = $this->media instanceof ParentClass? $this->media->getName() : $this->media;
		
		if ($_ret === null) {
			throw new \spitfire\exceptions\PrivateException('Bug detected', 1809141314);
		}
		
		$pieces = explode('.', $_ret);
		
		if (count($pieces) > 1) {
			array_pop($pieces);
		}
		
		return implode('.', $pieces);
	}

}
