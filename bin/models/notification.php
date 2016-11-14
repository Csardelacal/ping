<?php

class NotificationModel extends spitfire\Model
{
	
	public function definitions(\spitfire\storage\database\Schema $schema) {
		$schema->src     = new Reference('user'); # User originating the action
		$schema->target  = new Reference('user'); # If a notification is not a broadcast
		$schema->content = new StringField(255);  # A ping can contain up to 255 characters
		$schema->url     = new StringField(255);  # Source URL for the notification
		$schema->media   = new StringField(255);  # URL with the content. Media should be cached
	}

}
