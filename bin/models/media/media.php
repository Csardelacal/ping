<?php namespace media;

use EnumField;
use Exception;
use FileField;
use Reference;
use spitfire\exceptions\PublicException;
use spitfire\Model;
use spitfire\storage\database\Schema;
use StringField;

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
		
		try {
			return $this->getTable()->getDb()->table('media\thumb')->get('media__id', $this->_id)->where('aspect', $size)->first(true);
		}
		catch (Exception$e) {
			$this->ping->processed = false;
			$this->ping->store();
			trigger_error(sprintf('Found unprocessed media in ping #%s', $this->ping->_id), E_USER_WARNING);
			throw new PublicException('Media error', 500, $e);
		}
		
	}

}