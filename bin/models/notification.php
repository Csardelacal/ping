<?php

class NotificationModel extends spitfire\Model
{
	
	const TYPE_OTHER   = 0;
	const TYPE_FOLLOW  = 1;
	const TYPE_LIKE    = 2;
	const TYPE_SHARE   = 3;
	const TYPE_COMMENT = 4;
	const TYPE_ALERT   = 5;
	const TYPE_PAYMENT = 6;
	const TYPE_MENTION = 7;
	const TYPE_MESSAGE = 8;
	
	public function definitions(\spitfire\storage\database\Schema $schema) {
		$schema->src     = new Reference('user'); # User originating the action
		$schema->target  = new Reference('user'); # If a notification is not a broadcast
		$schema->content = new StringField(255);  # A ping can contain up to 255 characters
		$schema->url     = new StringField(255);  # Source URL for the notification
		$schema->created = new IntegerField(true);
		$schema->type    = new IntegerField(true);
		
		$schema->type->setNullable(false);
	}
	
	public function onbeforesave() {
		if (!$this->created) { $this->created = time(); }
	}
	
	public static function getTypesAvailable() {
		return [
			'other'   => self::TYPE_OTHER,
			'follow'  => self::TYPE_FOLLOW,
			'like'    => self::TYPE_LIKE,
			'share'   => self::TYPE_SHARE,
			'comment' => self::TYPE_COMMENT,
			'alert'   => self::TYPE_ALERT,
			'payment' => self::TYPE_PAYMENT,
			'mention' => self::TYPE_MENTION,
			'message' => self::TYPE_MESSAGE
		];
	}

}
