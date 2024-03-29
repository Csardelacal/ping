<?php namespace spitfire\mvc;

use spitfire\mvc\MVC;

class ViewElement extends MVC
{
	private $file;
	private $data;
	
	public function __construct($file, $data) {
		$this->file = $file;
		$this->data = $data;
	}
	
	public function set ($key, $value) {
		$this->data[$key] = $value;
		return $this;
	}
	
	public function render () {
		ob_start();
		foreach ($this->data as $k => $v) $$k = $v;
		echo '<!-- Started: ' . $this->file .' -->' . "\n";
		include $this->file;
		echo "\n" . '<!-- Ended: ' . $this->file .' -->';
		return ob_get_clean();
		
	}
	
	public function __toString() {
		return $this->render();
	}
}