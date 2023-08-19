<?php

use commishes\figureSdk\Client as FigureClient;
use media\MediaModel;
use media\ThumbModel;
use spitfire\mvc\Director;

class UpgradeDirector extends Director
{
	
	
	
	public function images()
	{
		db()
		->table('media\media')
		->get('figure', null)
		->where('type', 'image')
		->range(0, 5)->each(function (MediaModel $upload) {
			try { $contents = storage()->get($upload->file)->read(); } 
			catch (\Exception $ex) { $contents = file_get_contents($upload->file); }
			
			$file = tempnam('/tmp/', 'ping_');
			file_put_contents($file, $contents);
			
			try {
				$figure = container()->get(FigureClient::class);
				$result = $figure->upload($file);
				
				$figure->claim($result->getId(), $result->getSecret(), sprintf('Ping #%s', $upload->ping->_id));
				
				[$width, $height] = (function () use ($file) {
					$dimensions = getimagesize($file);
					if ($dimensions === false) { return [1, 1]; }
					return $dimensions;
				})();
				
				$upload->figure = $result->getId();
				$upload->lqip   = $result->getLqip();
				$upload->ratio = $width / $height;
				$upload->animated = $result->getAnimated();
				$upload->contentType = $result->getContentType();
				$upload->store();
			}
			catch (\Exception $e) {
				echo 'Could not upgrade ', $upload->_id, PHP_EOL;
				throw $e;
			}
			finally {
				unlink($file);
			}
			
			return $upload;
		})
		->each(function (MediaModel $upload) {
			
			echo 'Deleting ', $upload->_id, ': ', $upload->file, PHP_EOL;
			
			/**
			 * Find the upload's upload and delete that too.
			 */
			try { storage()->get($upload->file)->delete(); } 
			catch (\Exception $ex) { unlink($upload->file); }
			
			
			$upload->file = null;
			$upload->store();
			
			db()->table('media\thumb')->get('media', $upload)->all()->each(function (ThumbModel $thumb) {
				echo 'Deleting thumb ', $thumb->_id, ': ', $thumb->file, PHP_EOL;
			
				/**
				 * Find the thumb's thumb and delete that too.
				 */
				try { storage()->get($thumb->file)->delete(); } 
				catch (\Exception $ex) { unlink($thumb->file); }
				
				echo 'Deleting thumb ', $thumb->_id, ': ', $thumb->poster, PHP_EOL;
				
				/**
				 * Find the thumb's poster and delete that too.
				 */
				try { storage()->get($thumb->poster)->delete(); } 
				catch (\Exception $ex) { unlink($thumb->poster); }
				
				$thumb->delete();
			});
		});
	}
}
