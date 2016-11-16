<?php namespace auth;

class User
{
	
	private $id;
	private $username;
	private $aliases;
	private $groups;
	private $verified;
	private $registered;
	private $attributes;
	private $avatar;
	
	public function __construct($id, $username, $aliases, $groups, $verified, $registered, $attributes, $avatar) {
		$this->id = $id;
		$this->username = $username;
		$this->aliases = $aliases;
		$this->groups = $groups;
		$this->verified = $verified;
		$this->registered = $registered;
		$this->attributes = $attributes;
		$this->avatar = $avatar;
	}
	
	public function getId() {
		return $this->id;
	}
	
	public function getUsername() {
		return $this->username;
	}
	
	public function getAvatar($size) {
		return $this->avatar->{$size};
	}
	
}

