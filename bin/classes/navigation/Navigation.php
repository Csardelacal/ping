<?php namespace navigation;

class Navigation
{
	
	private $entries = Array();
	
	public function add($url, $caption, $name = null) {
		return $this->entries[$name] = new Entry($name, $url, $caption);
	}
	
	public function __toString() {
		return sprintf('<ul class="navigation">%s</ul>', implode(PHP_EOL, $this->entries));
	}
	
}