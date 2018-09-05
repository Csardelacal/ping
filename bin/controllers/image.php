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
	
	public static $acceptablePreviewSizes = [700, 960, 1280, 1920];
	public static $acceptableThumbSizes   = [32, 64, 128, 256, 512];
	
	
	public function preview($id, $size = 700) {
		
		$upload = db()->table('ping')->get('_id', $id)->fetch();
		
		if (!in_array($size, self::$acceptablePreviewSizes)) { throw new PublicException('Invalid dimensions', 400); }
		
		if (!$upload)        { throw new Exception('Auction not found', 404); }
		if (!$upload->media) { throw new Exception('Image   not found', 404); }
		
		#Check if the file is locally stored
		$info = parse_url($upload->media);
		
		if ($info['scheme'] === 'file') { $storage = storage('app' . substr($upload->media, 4)); }
		else                            { $storage = storage($upload->media); }
		
		if ($upload->deleted) {
			$targetfile = realpath('assets/img/deleted.png');
			$this->response->getHeaders()->status(404);
		}
		
		try {
			$targetfile = storage(\spitfire\core\Environment::get('uploads.directory'))->open('resized_' . $size . '_' . $storage->basename() . '.png');
		}
		catch (\Exception$ex) {
			$img = media()->load($storage);
			$img->scale($size);
			$targetfile = $img->store(storage(\spitfire\core\Environment::get('uploads.directory'))->make('resized_' . $size . '_' . $storage->basename() . '.png'));
			
			PNGQuant::compress($targetfile->getPath());
		}
		
		if (ob_get_length()) {
			die();
		}
		
		$this->response->getHeaders()->set('Content-type', $targetfile->mime());
		$this->response->getHeaders()->set('Cache-Control', 'no-transform,public,max-age=3600');
		$this->response->getHeaders()->set('Expires', date('r', time() + 3600));
		
		$this->response->setBody($targetfile->read());
	}
}