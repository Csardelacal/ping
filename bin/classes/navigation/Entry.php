<?php namespace navigation;

class Entry extends Navigation
{
	
	private $name;
	
	private $url;
	
	private $caption;
	
	private $active = false;
	
	public function __construct($name, $url, $caption) {
		$this->name = $name;
		$this->url = $url;
		$this->caption = $caption;
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function getUrl() {
		return $this->url;
	}
	
	public function getCaption() {
		return $this->caption;
	}
	
	public function getActive() {
		return $this->active;
	}
	
	public function setActive($active) {
		$this->active = $active;
		return $this;
	}
	
	public function __toString() {
		return sprintf(
			'<li class="%s"><a href="%s">%s</a>%s</li>', 
			$this->active? 'active' : 'inactive',
			$this->url,
			$this->caption,
			parent::__toString()
		);
	}
	
}
