<?php namespace media;

use spitfire\Model;
use spitfire\storage\database\Schema;


class ThumbModel extends Model
{
	
	public function definitions(Schema $schema) {
		$schema->media  = new \Reference('media\media');
		$schema->aspect = new \EnumField('t', 's', 'm', 'l', 'xl', 'src');
		$schema->mime   = new \StringField(20);
		$schema->file   = new \FileField();
		$schema->poster = new \FileField();
	}
	
	public function getEmbed() {
		if (!$this->media) {
			return null;
		}
		
		$memcached = new \spitfire\cache\MemcachedAdapter();
		$memcached->setTimeout(86400);
		
		list($uri, $mime, $post) = unserialize($memcached->get('embed_for_' . $this->media->_id . '_' . $this->aspect, function () {
			$file = storage($this->file);
			$post = $this->poster? storage()->get($this->poster) : null;

			$uri  = $file instanceof \spitfire\storage\objectStorage\EmbedInterface? $file->publicURI() : url('image', 'preview', $this->_id);
			$mime = $file instanceof \spitfire\storage\objectStorage\NodeInterface? $file->mime() : 'image/png';
			
			return serialize([$uri, $mime, $post]);
		}));
		
		if ($this->media->type === 'video') {
			return sprintf('<video controls preload="none" src="%s" poster="%s" style="width: 100%%"></video>', $uri, $post instanceof \spitfire\storage\objectStorage\EmbedInterface? $post->publicURI() : $post);
		}
		else {
			
			switch($mime) {
				case 'video/mp4':
				case 'video/quicktime':
				case 'image/gif':
					return sprintf('<video muted playsinline preload="none" loop src="%s" poster="%s" style="width: 100%%" onmouseover="this.play()" onmouseout="this.pause()"></video>', $uri, $post instanceof \spitfire\storage\objectStorage\EmbedInterface? $post->publicURI() : $post->uri());
				default:
					return sprintf('<img src="%s"  style="width: 100%%">', $uri);
			}
		}
	}
	
	public function getMediaEmbed() {
		try {
			if (empty($this->file)) { throw new spitfire\exceptions\PrivateException(); }
			
			$file = storage($this->file);
			$post = $this->poster? storage()->get($this->poster) : null;
			
			$uri  = $file instanceof \spitfire\storage\objectStorage\EmbedInterface? $file->publicURI() : $this->file;
			$mime = $file instanceof \spitfire\storage\objectStorage\NodeInterface? $file->mime() : 'image/png';
			

			switch($mime) {
				case 'video/mp4':
				case 'video/quicktime':
				case 'image/gif':
					return sprintf('<video muted playsinline preload="none" loop src="%s" poster="%s" style="width: 100%%" onmouseover="this.play()" onmouseout="this.pause()"></video>', $uri, $post instanceof \spitfire\storage\objectStorage\EmbedInterface? $post->publicURI() : $post);
				default:
					return sprintf('<img src="%s"  style="width: 100%%">', $uri);
			}
		} 
		catch (Exception $ex) {
			return sprintf('<img src="%s"  style="width: 100%%">', $this->file);
		}
	}

}
