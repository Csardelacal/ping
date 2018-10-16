<?php

class PingModel extends spitfire\Model
{
	
	public function definitions(\spitfire\storage\database\Schema $schema) {
		$schema->src     = new Reference('user'); # User originating the action
		$schema->target  = new Reference('user'); # If a notification is not a broadcast
		$schema->content = new StringField(300);  # A ping can contain up to 255 characters - but user id's get expanded
		$schema->url     = new StringField(255);  # Source URL for the notification
		$schema->media   = new StringField(255);  # URL with the content. Media should be cached. @deprecated
		$schema->explicit= new BooleanField();    # Indicates whether the user should have to opt in to see the content
		$schema->deleted = new IntegerField(true);# Null if it was not deleted, timestamp of deletion
		$schema->created = new IntegerField(true);
		$schema->irt     = new Reference('ping');
		$schema->share   = new Reference('ping');
		
		/*
		 * This should be the actual media field, but since the old media field is
		 * still out there in the wild we'll have to deprecate it first.
		 */
		$schema->attached= new ChildrenField('media\media', 'ping');
		$schema->processed = new BooleanField();
		
		$schema->replies = new ChildrenField('ping', 'irt');
		$schema->shared  = new ChildrenField('ping', 'share');
	}
	
	public function onbeforesave() {
		if (!$this->created) { $this->created = time(); }
	}
	
	/**
	 * 
	 * @return type
	 * @deprecated since version 20181008
	 */
	public function getMediaURI() {
		return in_array(parse_url($this->media, PHP_URL_SCHEME), ['file', 'app'])? strval(url('image', 'preview', $this->_id)->absolute()) : $this->media; 
	}
	
	/**
	 * 
	 * @return type
	 * @throws spitfire\exceptions\PrivateException
	 * @deprecated since version 20181008
	 */
	public function getMediaEmbed() {
		try {
			if (empty($this->media)) { throw new spitfire\exceptions\PrivateException(); }
			
			$file = storage($this->media);
			$uri  = $file instanceof \spitfire\storage\objectStorage\EmbedInterface? $file->publicURI() : $this->getMediaURI();


			switch($file->mime()) {
				case 'video/mp4':
				case 'image/gif':
					return sprintf('<video muted autoPlay loop src="%s" style="width: 100%%"></video>', $uri);
				default:
					return sprintf('<img src="%s"  style="width: 100%%">', $uri);
			}
		} 
		catch (Exception $ex) {
			return sprintf('<img src="%s"  style="width: 100%%">', $this->getMediaURI());
		}
		return in_array(parse_url($this->media, PHP_URL_SCHEME), ['file', 'app'])? strval(url('image', 'preview', $this->_id)->absolute()) : $this->media; 
	}
	
	public function original() {
		return $this->share? $this->share->original() : $this;
	}
	
	/**
	 * 
	 * @param type $size
	 * @return type
	 * @deprecated since version 20181008
	 */
	public function preview($size = 700) {
		if (!$this->media) {
			return null;
		}
		
		$file = $this->getTable()->getDb()->table('media\thumb')->get('ping', $this)->where('width', $size)->first();
		
		if (!$file) {
			$original = storage($this->media);
			$target   = storage(spitfire\core\Environment::get('uploads.thumbs')?: 'app://bin/usr/thumbs/');
			
			$media    = media()->load($original)->scale($size);
			$poster   = $media->poster();
			$stored   = $media->store($target->make($size . '_' . $original->basename()));
			
			$file = $this->getTable()->getDb()->table('media\thumb')->newRecord();
			$file->ping  = $this;
			$file->width = $size;
			$file->mime  = storage($this->media)->mime();
			$file->file  = $stored->uri();
			$file->poster = $media !== $poster? $poster->store($target->make($size . '_p_' . $original->basename()))->uri() : null;
			$file->store();
		}
		
		return $file;
	}

}
