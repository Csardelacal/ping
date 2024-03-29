<?php

class UserModel extends spitfire\Model
{
	
	public function definitions(\spitfire\storage\database\Schema $schema)
	{
		$schema->authId           = new IntegerField(); # The ID the user has assigned on the auth server
		$schema->lastSeen         = new IntegerField(); # We use this to see how many unread pings the user has
		$schema->lastSeenActivity = new IntegerField(); # We use this to see how many unread notifications the user has
		$schema->digested         = new IntegerField();
		
		$schema->followers = new ChildrenField('follow', 'prey');
		$schema->following = new ChildrenField('follow', 'follower');
	}
	
	public function notify($type, $interval)
	{
		$db = $this->getTable()->getDb();
		$setting  = $db->table('settings\notification')->get('user', $this)->addRestriction('type', $type)->fetch();
		
		if (!$setting) {
			$notify = settings\NotificationModel::NOTIFY_DEFAULT;
		}
		else {
			$notify = $setting->setting;
		}
		
		return ((int)$notify) === ((int)$interval);
	}
	
	public static function makeFromSSO($u)
	{
		
		if (!$u) {
			throw new \spitfire\exceptions\PrivateException('Expected user', 1611231500);
		}
		
		/**
		 * We want to double check that there is no user already registered
		 */
		$dbuser = db()->table('user')->get('_id', $u->getId)->first();
		
		if ($dbuser) {
			trigger_error('Attempted to make a user that already exists', E_USER_WARNING);
			return $dbuser;
		}
		
		$target = db()->table('user')->newRecord();
		$target->authId = $u->getId();
		$target->_id    = $u->getId();
		$target->store();
		
		return $target;
	}
}
