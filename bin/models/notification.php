<?php

class NotificationModel extends spitfire\Model
{
	
	public function definitions(\spitfire\storage\database\Schema $schema) {
		$schema->src     = new Reference('user'); # User originating the action
		$schema->target  = new Reference('user'); # If a notification is not a broadcast
		$schema->content = new StringField(255);  # A ping can contain up to 255 characters
		$schema->url     = new StringField(255);  # Source URL for the notification
		$schema->created = new IntegerField(true);
	}
	
	public function onbeforesave() {
		if (!$this->created) { $this->created = time(); }
	}

}
