<?php

class FollowModel extends spitfire\Model
{
	
	public function definitions(\spitfire\storage\database\Schema $schema) {
		$schema->follower = new Reference('author');
		$schema->prey     = new Reference('author');
		$schema->created  = new IntegerField();
	}
	
	public function onbeforesave() {
		if ($this->created === null) { $this->created = time(); }
	}

}
