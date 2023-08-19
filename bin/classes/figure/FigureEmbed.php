<?php namespace figure;

use commishes\figureSdk\Client;
use media\MediaModel;
use spitfire\exceptions\PublicException;

class FigureEmbed
{
	
	private MediaModel $media;
	private string $aspect;
	
	public function __construct(MediaModel $media, string $aspect)
	{
		$this->media = $media;
		$this->aspect = $aspect;
	}
	
	public function getURI()
	{
		switch ($this->aspect) {
			case 't': 
				return container()->get(Client::class)->url(
					$this->media->figure,
					['w' => 512, 'h' => 512, 'fit' => 'crop']
				);
			case 's': 
				if ($this->media->animated) {
					return container()->get(Client::class)->video(
						$this->media->figure,
						['w' => 512]
					);
				}
				
				return container()->get(Client::class)->url(
					$this->media->figure,
					['w' => 512]
				);
			case 'm': 
				if ($this->media->animated) {
					return container()->get(Client::class)->video(
						$this->media->figure,
						['w' => 1280]
					);
				}
				
				return container()->get(Client::class)->url(
					$this->media->figure,
					['w' => 1280]
				);
			case 'l': 
				if ($this->media->animated) {
					return container()->get(Client::class)->video(
						$this->media->figure,
						['w' => 1920]
					);
				}
				
				return container()->get(Client::class)->url(
					$this->media->figure,
					['w' => 1920]
				);
		}
			
		throw new PublicException('Invalid image aspect');
	}
	
	public function getEmbed()
	{
		$uri = $this->getURI('s');
		$medium = $this->getURI('m');
		
		switch($this->media->animated) {
			case 'video/mp4':
			case 'video/quicktime':
			case 'image/gif':
				return sprintf(
					'<video muted playsinline preload="none" loop src="%s" data-large="%s" poster="%s" style="width: 100%%" onmouseover="this.play()" onmouseout="this.pause()"></video>',
					$uri,
					$medium,
					$this->media->lqip
				);
			default:
				return sprintf('<img src="%s" data-large="%s" style="width: 100%%">', $uri, $medium);
		}
	}
	
}
