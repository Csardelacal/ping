<?php

use spitfire\storage\objectStorage\EmbedInterface;

class PingModel extends spitfire\Model
{
	
	public function definitions(\spitfire\storage\database\Schema $schema)
	{
		$schema->user    = new Reference('user');
		$schema->ping    = new Reference('ping');
		
		/**
		 * A comma separated list of the users:pings that shared this 
		 */ 
		$schema->shared  = new StringField(255);
		
		$schema->created = new IntegerField(true);
		$schema->updated = new IntegerField(true);
	}
	
	public function onbeforesave()
	{
		if (!$this->created) {
			$this->created = time();
		}
		
		$this->updated = time();
	}
}
