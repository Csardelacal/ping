<?php

class NotificationModel extends spitfire\Model
{
	
	public function definitions(\spitfire\storage\database\Schema $schema) {
		$schema->src     = new Reference('user'); # User originating the action
		$schema->target  = new Reference('user'); # If a notification is not a broadcast
		$schema->content = new StringField(255);  # A ping can contain up to 255 characters
		$schema->url     = new StringField(255);  # Source URL for the notification
		$schema->media   = new StringField(255);  # URL with the content. Media should be cached
		$schema->explicit= new BooleanField();    # Indicates whether the user should have to opt in to see the content
		$schema->deleted = new IntegerField(true);# Null if it was not deleted, timestamp of deletion
		$schema->created = new IntegerField(true);
	}
	
	public function onbeforesave() {
		if (!$this->created) { $this->created = time(); }
	}

}
