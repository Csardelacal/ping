<?php namespace settings;

use IntegerField;
use Reference;
use spitfire\Model;
use spitfire\storage\database\Schema;

class NotificationModel extends Model
{
	
	const NOTIFY_DEFAULT   = 0;
	const NOTIFY_EMAIL     = 0;
	const NOTIFY_IMMEDIATE = 0;
	
	const NOTIFY_DIGEST    = 1;
	const NOTIFY_NONE      = 2;
	
	/**
	 * 
	 * @param Schema $schema
	 */
	public function definitions(Schema $schema) {
		$schema->user    = new Reference('user');
		$schema->type    = new IntegerField(true);
		$schema->setting = new IntegerField(true);
	}

}