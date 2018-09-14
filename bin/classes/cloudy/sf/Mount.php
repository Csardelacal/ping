<?php namespace cloudy\sf;

use cloudy\Cloudy;
use spitfire\core\CollectionInterface;
use spitfire\exceptions\PrivateException;
use spitfire\storage\objectStorage\FileInterface;
use spitfire\storage\objectStorage\MountPointInterface;
use spitfire\storage\objectStorage\NodeInterface;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Mount implements MountPointInterface
{
	
	/**
	 *
	 * @var Cloudy
	 */
	private $cloudy;
	
	private $scheme;
	
	public function __construct($scheme, $cloudy) {
		$this->cloudy = $cloudy;
		$this->scheme = $scheme;
	}
	
	public function all() : CollectionInterface {
		throw new PrivateException('Invalid endpoint', 1809141212);
	}

	public function contains($name) : int {
		throw new PrivateException('Invalid endpoint', 1809141212);
	}

	public function delete() : bool {
		throw new PrivateException('Invalid endpoint', 1809141212);
	}

	public function isWritable() : bool {
		return false;
	}

	public function make($name) : FileInterface {
		//TODO: Allow creating buckets?
		return false;
	}

	public function mkdir($name) : NodeInterface {
		return false;
	}

	public function open($name) : NodeInterface {
		return new Bucket($this, $this->cloudy->bucket($name));
	}

	public function scheme() {
		return rtrim($this->scheme, ':/');
	}

	public function up() : NodeInterface {
		throw new PrivateException('You are up already', 1809141214);
	}

	public function uri() : string {
		return trim($this->scheme, ':/') . '://';
	}

}
