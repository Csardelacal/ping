<?php

class AuthorModel extends spitfire\Model 
{
	
	public function definitions(\spitfire\storage\database\Schema $schema) {
		$schema->user             = new Reference('user'); # The ID the user has assigned on the auth server
		$schema->public           = new BooleanField();
		
		/*
		 * The following variables are used for federating Ping with external sources,
		 * allowing users to interconnect their profiles with diaspora or activityPub
		 * enabled services - making it easier to share with and receive content from,
		 * sources that aren't ping directly.
		 * 
		 * You MUST not use any of these variables to determine whether an author 
		 * is federated. Ping users will have own GUID and the other variables may
		 * be used for caching information.
		 */
		$schema->displayName      = new StringField(100); # In case the author is an external author, we present the displayname
		$schema->fqun             = new StringField(255); # Allows the author to have a fully qualified username - this allows federation
		$schema->guid             = new StringField(255); 
		$schema->avatar           = new StringField(255); # The URL to the author's avatar. this is not necessary if the user field is populated
		
		/*
		 * Users can freely follow authors and be followed by authors that are not
		 * immediately registered on the same server.
		 */
		$schema->followers = new ChildrenField('follow', 'prey');
		$schema->following = new ChildrenField('follow', 'follower');
	}
	
	/**
	 * Retrieves the author from a post
	 */
	public static function get(UserModel$user = null) {
		if ($user === null) {
			return null;
		}
		
		try {
			return db()->table('author')->get('user', $user)->first(true);
		}
		catch (\spitfire\exceptions\PrivateException$e) {
			$author = db()->table('author')->newRecord();
			$author->guid = substr(base_convert(bin2hex(random_bytes(100)), 16, 36), 0, 150);
			$author->user = $user;
			$author->store();
			
			return $author;
		}
	}
	
}
