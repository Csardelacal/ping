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
		$schema->type = new EnumField('image', 'video', 'external');
		$schema->file = new FileField();
		
		$schema->secret = new StringField(100);
		
		/*
		 * This field allows the system to keep track of where external sources 
		 * originated from.
		 */
		$schema->source = new StringField(1024);
	}
	
	public function preview($size = 700) {
		
		$file = $this->getTable()->getDb()->table('media\thumb')->get('media', $this)->where('width', $size)->where('aspect', 'original')->first();
		
		if (!$file) {
			$original = storage($this->file);
			$target   = storage(Environment::get('uploads.thumbs')?: 'app://bin/usr/thumbs/');
			
			$media    = media()->load($original)->scale($size);
			$poster   = $media->poster();
			$stored   = $media->store($target->make($size . '_original_' . $original->basename()));
			
			$file = $this->getTable()->getDb()->table('media\thumb')->newRecord();
			$file->media = $this;
			$file->width = $size;
			$file->aspect= 'original';
			$file->mime  = storage($this->file)->mime();
			$file->file  = $stored->uri();
			$file->poster = $media !== $poster? $poster->store($target->make($size . '_po_' . $original->basename()))->uri() : null;
			$file->store();
		}
		
		return $file;
	}
	
	public function square($size = 512) {
		
		$file = $this->getTable()->getDb()->table('media\thumb')->get('media', $this)->where('width', $size)->where('aspect', 'square')->first();
		
		if (!$file) {
			$original = storage($this->file);
			$target   = storage(Environment::get('uploads.thumbs')?: 'app://bin/usr/thumbs/');
			
			$media    = media()->load($original)->fit($size, $size);
			$poster   = $media->poster();
			$stored   = $media->store($target->make($size . '_square_' . $original->basename()));
			
			$file = $this->getTable()->getDb()->table('media\thumb')->newRecord();
			$file->media = $this;
			$file->width = $size;
			$file->mime  = storage($this->file)->mime();
			$file->file  = $stored->uri();
			$file->aspect= 'square';
			$file->poster = $media !== $poster? $poster->store($target->make($size . '_ps_' . $original->basename()))->uri() : null;
			$file->store();
		}
		
		return $file;
	}
	
	public function double($size = 512) {
		
		$file = $this->getTable()->getDb()->table('media\thumb')->get('media', $this)->where('width', $size)->where('aspect', 'double')->first();
		
		if (!$file) {
			$original = storage($this->file);
			$target   = storage(Environment::get('uploads.thumbs')?: 'app://bin/usr/thumbs/');
			
			$media    = media()->load($original)->fit($size, $size * 2);
			$poster   = $media->poster();
			$stored   = $media->store($target->make($size . '_double_' . $original->basename()));
			
			$file = $this->getTable()->getDb()->table('media\thumb')->newRecord();
			$file->media = $this;
			$file->width = $size;
			$file->mime  = storage($this->file)->mime();
			$file->file  = $stored->uri();
			$file->aspect= 'double';
			$file->poster = $media !== $poster? $poster->store($target->make($size . '_pd_' . $original->basename()))->uri() : null;
			$file->store();
		}
		
		return $file;
	}

}