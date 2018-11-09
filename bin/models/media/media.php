<?php namespace media;

use EnumField;
use FileField;
use Reference;
use spitfire\core\Environment;
use spitfire\Model;
use spitfire\storage\database\Schema;
use StringField;
use function media;
use function storage;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class MediaModel extends Model
{
	
	/**
	 * 
	 * @param Schema $schema
	 */
	public function definitions(Schema $schema) {
		$schema->ping = new Reference('ping');
		$schema->type = new EnumField('image', 'video');
		$schema->file = new FileField();
		
		$schema->secret = new StringField(100);
		
		/*
		 * This field allows the system to keep track of where external sources 
		 * originated from.
		 */
		$schema->source = new StringField(1024);
	}
	
	public function preview($size = 'm') {
		
		return $this->getTable()->getDb()->table('media\thumb')->get('media', $this)->where('aspect', $size)->first(true);
		
	}

}