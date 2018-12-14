<?php namespace media;

/* 
 * The MIT License
 *
 * Copyright 2018 César de la Cal Bretschneider <cesar@magic3w.com>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

class Compressor
{
	
	private $sizes = [
		 't'   => [ 512,  512, 'jpeg'],
		 's'   => [ 512, null, 'jpeg'],
		 'm'   => [1280, null, 'jpeg'],
		 //'l'   => [1920, null,  'png'],
		 //'xl'  => [3840, null,  'png'],
		 //'src' => [null, null,  'png']
	];
	
	/**
	 *
	 * @var MediaModel
	 */
	private $media;
	
	public function __construct(MediaModel$media) {
		$this->media = $media;
	}
	
	public function process() {
		
		try {
			$original = storage()->get($this->media->file);
			$manipulator = media()->load($original);
		} 
		catch (\Exception $ex) {
			console()->error('Error loading media. ' . $ex->getMessage())->ln();
			return;
		}
		
		/**
		 * Loop over the formats we need to generate from the file.
		 */
		foreach ($this->sizes as $name => $target) {
			
			$width  = $target[0];
			$height = $target[1];
			$format = $target[2];
			
			if ($this->media->getTable()->getDb()->table('media\thumb')->get('media', $this->media )->where('aspect', $name)->first()) {
				continue;
			}
			
			/*
			 * Prepare a new model to write the data to. We also fill in some basic 
			 * metadata required to retrieve the file later.
			 */
			$thumb = $this->media->getTable()->getDb()->table('media\thumb')->newRecord();
			$thumb->media  = $this->media;
			$thumb->aspect = $name;
			
			
			/*
			 * Resize the media appropriately
			 */
			if ($width && $height) {
				$manipulator->fit($width, $height);
			}
			elseif ($width && $manipulator instanceof \spitfire\io\media\FFMPEGManipulator) {
				$manipulator->downscale($width);
			}
			elseif ($width) {
				$manipulator->scale($width);
			}
			else {
				//Do not scale or resize
			}
			
			$poster = $manipulator->poster();
			$location = storage()->dir(\spitfire\core\Environment::get('uploads.thumbs')? : \spitfire\core\Environment::get('uploads.directory'));
			
			try {
				/*
				 * If the poster is not the same manipulator as the file's, it implies
				 * that the file was animated / video and therefore has a different still
				 * frame.
				 */
				if ($poster !== $manipulator) {

					$export = $manipulator->store($location->make(uniqid() . $name . '_' . $original->basename()));

					$thumb->file   = $export->uri();
					$thumb->mime   = $export->mime();

					$thumb->poster = $poster->store($location->make(uniqid() . $name . '-poster_' . $original->filename() . '.' . $format))->uri();
				}
				else {
					$export = $manipulator->store($location->make(uniqid() . $name . '_' . $original->filename() . '.' . $format));

					$thumb->file   = $export->uri();
					$thumb->mime   = $export->mime();
				}

				$thumb->store();
			} 
			catch (\Exception $ex) {
				console()->error('Error storing media. ' . $ex->getMessage())->ln();
				return;
			}
		}
	}
	
}