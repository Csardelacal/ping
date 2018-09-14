<?php namespace cloudy\sf;

use Exception;
use spitfire\core\CollectionInterface;
use spitfire\exceptions\PrivateException;
use spitfire\storage\objectStorage\DirectoryInterface;
use spitfire\storage\objectStorage\FileInterface;
use spitfire\storage\objectStorage\NodeInterface;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Bucket implements DirectoryInterface
{
	
	private $parent;
	
	private $bucket;
	
	public function __construct($parent, $bucket) {
		$this->parent = $parent;
		$this->bucket = $bucket;
	}

	public function all() : CollectionInterface {
		return [];
	}

	public function contains($name) : int {
		try {
			$this->bucket->getMedia($name);
			return DirectoryInterface::CONTAINS_FILE;
		}
		catch (Exception$e) {
			return DirectoryInterface::CONTAINS_NONX;
		}
	}

	public function delete() : bool {
		return false;
	}

	public function isWritable() :bool {
		return true;
	}

	public function make($name) : FileInterface {
		return new Media($this, $name);
	}

	public function mkdir($name) : NodeInterface {
		throw new PrivateException('Unsupported operation', 1809141221);
	}

	public function open($name) : NodeInterface {
		return new Media($this, $this->bucket->getMedia($name));
	}

	public function up() : NodeInterface {
		return $this->parent;
	}

	public function uri() : string {
		return $this->parent->uri() . $this->bucket->uniqid() . '/';
	}
	
	public function getBucket() {
		return $this->bucket;
	}

}
