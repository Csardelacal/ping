<?php

class UserModel extends spitfire\Model 
{
	
	public function definitions(\spitfire\storage\database\Schema $schema) {
		$schema->authId    = new IntegerField(); # The ID the user has assigned on the auth server
		$schema->lastSeen  = new IntegerField(); # We use this to see how many unread notifications the user has
		
		$schema->followers = new ChildrenField('follow', 'prey');
		$schema->following = new ChildrenField('follow', 'follower');
	}
	
	public static function makeFromSSO($u) {
		
		if ($u) { 
			$target = db()->table('user')->newRecord(); 
			$target->authId = $u->getId();
			$target->_id    = $u->getId();
			$target->store();
			
			return $target;
		}
		
		throw new \spitfire\exceptions\PrivateException('Expected user', 1611231500);
	}

}
