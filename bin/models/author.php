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
		$schema->server           = new Reference('server');
		$schema->displayName      = new StringField(100);  # In case the author is an external author, otherwise the system should fetch that from PHPAS
		$schema->userName         = new StringField(100);  # In case the author is an external author, we present the displayname
		$schema->bio              = new StringField(255);  # In case the author is an external author, otherwise the system should fetch that from PHPAS
		$schema->guid             = new StringField(255);  # The GUID will allow the server to identify the user across the world
		$schema->avatar           = new StringField(4096); # The author's avatar. this is not necessary if the user field is populated
		$schema->banner           = new StringField(4096); # The author's banner. this is not necessary if the user field is populated
		
		/*
		 * Users can freely follow authors and be followed by authors that are not
		 * immediately registered on the same server.
		 */
		$schema->followers = new ChildrenField('follow', 'prey');
		$schema->following = new ChildrenField('follow', 'follower');
	}
	
	public function onbeforesave() {
		if (!$this->guid) {
			$this->guid = 'a' . strtolower(substr(str_replace(['+', '/', '='], '', base64_encode(random_bytes(100))), 0, 100));
		}
	}
	
	public static function find($identifier) {
		
		if ($identifier === null) {
			return null;
		}
		
		if ($identifier instanceof UserModel) {
			return self::get($identifier);
		}
		
		if (substr_count($identifier, '@') == 2 && Strings::startsWith($identifier, '@')) {
			#Search for an author on a different server
			#TODO: Implement
		}
		
		if (substr_count($identifier, '@') == 1 && Strings::startsWith($identifier, '@')) {
			#Search for a local user
			$sso = current_context()->controller->sso;
			$usr = $sso->getUser(trim($identifier, '@'));
			return db()->table('author')->get('user__id', $usr->getId())->first();
		}
		
		if (Strings::startsWith($identifier, ':')) {
			#Search by GUID
			return db()->table('author')->get('guid', trim($identifier, ':'))->first();
		}
		
		if (intval($identifier)) {
			#Search for a local user
			#Please note that a user may not have been created if they didn't enable ping
			return db()->table('author')->get('user__id', $identifier)->first();
		}
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
		catch (\spitfire\exceptions\PublicException$e) {
			$author = db()->table('author')->newRecord();
			$author->guid = substr(bin2hex(random_bytes(100)), 0, 150);
			$author->user = $user;
			$author->store();
			
			return $author;
		}
	}
	
	public function getBanner() {
		if ($this->banner) {
			return $this->banner;
		}
		else {
			try {
				$sso = current_context()->controller->sso;
				$usr = $sso->getUser($this->user->_id);
				return $usr->getAttribute('banner')->getPreviewURL(1280, 300);
			}
			catch (\Exception$e) {
				return null;
			}
		}
		
	}
	
	public function getAvatar() {
		if ($this->avatar) {
			return $this->avatar;
		}
		else {
			$sso = current_context()->controller->sso;
			$usr = $sso->getUser($this->user->_id);
			return $usr->getAvatar(128);
		}
		
	}
	
}
