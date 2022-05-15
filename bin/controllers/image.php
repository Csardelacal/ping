<?php

use spitfire\exceptions\PublicException;
use spitfire\io\Image;
use spitfire\io\image\PNGQuant;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ImageController extends AppController
{
	public function preview($id, $size = 700) {

		$upload = db()->table('media\thumb')->get('_id', $id)->fetch();
		
		if (!$upload)        { throw new Exception('Media not found', 404); }
		if (!$upload->file) { throw new Exception('Image   not found', 404); }

		#Check if the file is locally stored
		$info = parse_url($upload->file);

		if ($info['scheme'] === 'file') { $storage = storage('app' . substr($upload->file, 4)); }
		else                            { $storage = storage($upload->file); }

		/*if ($upload->deleted) {
			$targetfile = realpath('assets/img/deleted.png');
			$this->response->getHeaders()->status(404);
		}*/
		
		if (ob_get_length() || !($storage instanceof \spitfire\storage\drive\File)) {
			die();
		}

		$this->response->getHeaders()->set('Content-type', $upload->mime);
		$this->response->getHeaders()->set('Cache-Control', 'no-transform,public,max-age=3600');
		$this->response->getHeaders()->set('Expires', date('r', time() + 3600));

		$this->response->setBody($storage->read());
	}
}
