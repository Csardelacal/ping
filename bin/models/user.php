<?php

class UserModel extends spitfire\Model 
{
	
	public function definitions(\spitfire\storage\database\Schema $schema) {
		$schema->authId = new IntegerField(); # The ID the user has assigned on the auth server
	}

}
