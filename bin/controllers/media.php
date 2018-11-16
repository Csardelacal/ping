<?php 

use spitfire\core\Environment;
use spitfire\exceptions\PublicException;
use spitfire\io\Upload;

class MediaController extends AppController
{
	
	/**
	 * 
	 * @validate POST#type(string required)
	 */
	public function upload() {
		
		if ($_POST['file'] instanceof Upload) {
			#Store the file and process thumbs
			$local = $_POST['file']->store();
			$source = null;
		}
		else {
			throw new PublicException('File requires an upload', 400);
		}
		
		$mime = $local->mime();
		
		switch($_POST['type']) {
			case 'video':
				if (!Strings::startsWith($mime, 'video/')) {
					$local->delete();
					throw new PublicException('Invalid type', 400);
				}
				break;
			
			case 'image':
				if (!Strings::startsWith($mime, 'image/')) {
					$local->delete();
					throw new PublicException('Invalid type', 400);
				}
				break;
			
			default:
				throw new PublicException('Invalid type', 400);
		}
		
		$record = db()->table('media\media')->newRecord();
		$record->file = $local->uri();
		$record->source = $source;
		$record->type = $_POST['type'];
		$record->secret = base64_encode(random_bytes(50));
		$record->store();
		
		
		$this->view->set('record', $record);
		
	}
	
}