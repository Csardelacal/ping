<?php namespace spitfire\io\beans;

use spitfire\exceptions\PrivateException;
use spitfire\io\renderers\RenderableFieldFile;

class FileField extends BasicField implements RenderableFieldFile
{
	
	public function getRequestValue() {
		$file = parent::getRequestValue();
		
		if ($file instanceof \spitfire\io\Upload) return $file->validate()->store();
		else throw new PrivateException('Not an upload');
	}
	
	public function __toString() {
		$id = "field_{$this->getName()}";
		return sprintf('<div class="field"><label for="%s">%s</label><input type="%s" id="%s" name="%s" ><small>%s</small></div>',
			$id, $this->getCaption(), $this->type, $id, $this->getName(), __(end(explode('/', $this->getValue()))) 
			);
	}

	public function getEnforcedFieldRenderer() {
		return null;
	}

	public function getMaxFileSize() {
		return null;
	}

	public function getSupportedFileFormats() {
		return null;
	}

}
