<?php

use spitfire\storage\objectStorage\EmbedInterface;

class PingModel extends spitfire\Model
{
	
	public function definitions(\spitfire\storage\database\Schema $schema)
	{
		$schema->guid    = new StringField(150);
		$schema->authapp = new StringField(50);
		$schema->src     = new Reference('author'); # User originating the action
		$schema->target  = new Reference('author'); # If a notification is not a broadcast
		$schema->content = new StringField(999);  # A ping can contain up to 255 characters - but user id's get expanded
		$schema->url     = new StringField(255);  # Source URL for the notification
		$schema->media   = new StringField(255);  # URL with the content. Media should be cached. @deprecated
		$schema->explicit= new BooleanField();    # Indicates whether the user should have to opt in to see the content
		$schema->deleted = new IntegerField(true);# Null if it was not deleted, timestamp of deletion
		$schema->created = new IntegerField(true);
		$schema->removed = new IntegerField(true);# Null if it was not deleted, timestamp of deletion
		$schema->staff   = new IntegerField(true);# Null if no staff action taken, user ID of last staff to modify
		$schema->irt     = new Reference('ping');
		$schema->share   = new Reference('ping');
		
		/*
		 * This should be the actual media field, but since the old media field is
		 * still out there in the wild we'll have to deprecate it first.
		 */
		$schema->attached= new ChildrenField('media\media', 'ping');
		
		/*
		 * This is required for the polls to work properly and be able to be closed
		 * after the user determines they're no longer interesting
		 */
		$schema->pollEnd   = new IntegerField(true);
		
		$schema->processed = new BooleanField();
		$schema->locked    = new BooleanField();
		
		$schema->replies = new ChildrenField('ping', 'irt');
		$schema->shared  = new ChildrenField('ping', 'share');
	}
	
	public function onbeforesave()
	{
		if (!$this->created) {
			$this->created = time();
		}
		if (!$this->guid) {
			$random = base64_encode(random_bytes(100));
			$this->guid = 'p' . strtolower(substr(str_replace(['+', '/', '='], '', $random), 0, 100));
		}
	}
	
	/**
	 *
	 * @return type
	 * @deprecated since version 20181008
	 */
	public function getMediaURI()
	{
		if (in_array(parse_url($this->media, PHP_URL_SCHEME), ['file', 'app'])) {
			return strval(url('image', 'preview', $this->_id)->absolute());
		}
		else {
			return $this->media;
		}
	}
	
	/**
	 *
	 * @return type
	 * @throws spitfire\exceptions\PrivateException
	 * @deprecated since version 20181008
	 */
	public function getMediaEmbed()
	{
		try {
			if (empty($this->media)) {
				throw new spitfire\exceptions\PrivateException();
			}
			
			$file = storage($this->media);
			$uri  = $file instanceof EmbedInterface? $file->publicURI() : $this->getMediaURI();
			
			
			switch ($file->mime()) {
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
		
		if (in_array(parse_url($this->media, PHP_URL_SCHEME), ['file', 'app'])) {
			return strval(url('image', 'preview', $this->_id)->absolute());
		}
		else {
			return $this->media;
		}
	}
	
	public function original()
	{
		return $this->share? $this->share->original() : $this;
	}
	
	public function attachmentsPreview()
	{
		$ret = [];
		$cnt = 0;
		
		$attached = $this->attached->toArray();
		$length = count($attached);
		
		foreach ($attached as $attachment) {
			$ret[] = [
				'idx' => $cnt++,
				'type' => $attachment->type,
				'url' => $attachment->preview($length > 1? 't' : 'm')->getURI(),
				'embed' => $attachment->preview($length > 1? 't' : 'm')->getEmbed()
			];
		}
		
		return $ret;
	}
}
