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
		
		$upload = db()->table('notification')->get('_id', $id)->fetch();
		
		if (!in_array($size, self::$acceptablePreviewSizes)) { throw new PublicException('Invalid dimensions', 400); }
		
		if (!$upload)        { throw new Exception('Auction not found', 404); }
		if (!$upload->media) { throw new Exception('Image   not found', 404); }
		
		#Check if the file is locally stored
		$info = parse_url($upload->media);
		
		if ($info['scheme'] !== 'file') { throw new Exception('Auction not found', 404); }
		
		$targetfile = realpath(pathinfo($info['path'], PATHINFO_DIRNAME)) . '/resized_' . $size . '_' . pathinfo($info['path'], PATHINFO_FILENAME) . '.png';
		
		if ($upload->deleted) {
			$targetfile = realpath('assets/img/deleted.png');
			$this->response->getHeaders()->status(404);
		}
		
		if (!file_exists($targetfile)) {
			$img = new Image($info['path']);
			$img->resize($size);
			$img->setCompression(5);
			PNGQuant::compress($img->store($targetfile));
		}
		
		if (ob_get_length()) {
			die();
		}
		
		header('Content-type: image/png');
		header('Cache-Control: no-transform,public,max-age=3600');
		header('Expires: ' . date('r', time() + 3600));
		
		readfile($targetfile);
		die();
	}
}