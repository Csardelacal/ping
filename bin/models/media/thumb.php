<?php namespace media;

use spitfire\Model;
use spitfire\storage\database\Schema;


class ThumbModel extends Model
{
	
	public function definitions(Schema $schema) {
		$schema->ping  = new \Reference('ping');
		$schema->width = new \IntegerField(true);
		$schema->mime  = new \StringField(20);
		$schema->file  = new \FileField();
	}
	
	
	public function getMediaEmbed() {
		try {
			if (empty($this->file)) { throw new spitfire\exceptions\PrivateException(); }
			
			$file = storage($this->file);
			$uri  = $file instanceof \spitfire\storage\objectStorage\EmbedInterface? $file->publicURI() : $this->file;
			$mime = $file instanceof \spitfire\storage\objectStorage\NodeInterface? $file->mime() : 'image/png';


			switch($mime) {
				case 'video/mp4':
				case 'image/gif':
					return sprintf('<video muted autoPlay loop src="%s" style="width: 100%%"></video>', $uri);
				default:
					return sprintf('<img src="%s"  style="width: 100%%">', $uri);
			}
		} 
		catch (Exception $ex) {
			return sprintf('<img src="%s"  style="width: 100%%">', $this->file);
		}
	}

}
