<?php

use spitfire\exceptions\PublicException;
use spitfire\io\Upload;

class MediaController extends AppController
{
	
	/**
	 * 
	 * @throws PublicException
	 */
	public function upload() {
		
		current_context()->response->getHeaders()->set('Access-Control-Allow-Origin', '*');
		current_context()->response->getHeaders()->set('Access-Control-Allow-Headers', 'Content-Type');
		
		if (!$this->request->isPost()) {
			$this->response->setBody('This endpoint accepts only POST requests');
			return;
		}
		
		if (isset($_POST['file']) && $_POST['file'] instanceof Upload) {
			#Store the file and process thumbs
			$local = $_POST['file']->store();
			$source = null;
		}
		else {
			throw new PublicException('File requires an upload', 400);
		}
		
		$media = media()->load($local);
		
		$record = db()->table('media\media')->newRecord();
		$record->file = $local->uri();
		$record->source = $source;
		$record->type   = $media instanceof \spitfire\io\media\FFMPEGManipulator && $media->hasAudio()? 'video' : 'image';
		$record->secret = base64_encode(random_bytes(50));
		$record->store();
		
		
		$this->view->set('record', $record);
		
	}
	
}