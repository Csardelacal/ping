<?php

class FollowModel extends spitfire\Model
{
	
	public function definitions(\spitfire\storage\database\Schema $schema) {
		$schema->follower = new Reference('user');
		$schema->prey     = new Reference('user');
		$schema->created  = new IntegerField();
	}

}
