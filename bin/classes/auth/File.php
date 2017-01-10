<?php namespace auth;

class File
{
	
	private $previewURL;
	private $downloadURL;
	
	public function __construct($previewURL, $downloadURL) {
		$this->previewURL = $previewURL;
		$this->downloadURL = $downloadURL;
	}
	
	public function getPreviewURL($w = null, $h = null) {
		return implode('/', Array(trim($this->previewURL, '/'), $w, $h)) . '/';
	}

	
}