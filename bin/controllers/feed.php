<?php

class FeedController extends AppController
{
	
	public function index() {
		
		/*
		 * The feed only works with a logged in user. This is due to the fact that
		 * there is no point in having a public feed that displays contents 
		 */
		if (!$this->user) { 
			return $this->response->getHeaders()->redirect(new URL('user', 'login')); 
		}
		
		$this->secondaryNav->add(url('feed'), 'Feed')->setActive(true);
		$this->secondaryNav->add(url('activity'), 'Activity <span class="badge" data-ping-activity data-ping-amt="0">?</span>');
		$this->secondaryNav->add(url('people', 'followingMe'), 'Followers');
		$this->secondaryNav->add(url('people', 'iFollow'), 'Following');
		
		/*
		 * Read the notifications for the user and send them to the view
		 */
		
		$dbuser  = db()->table('user')->get('authId', $this->user->id)->fetch()? : UserModel::makeFromSSO($this->sso->getUser($this->user->id));
		$follows = db()->table('follow')->get('follower__id', $dbuser->_id);
		$users   = db()->table('user')->getAll()->group(\spitfire\storage\database\RestrictionGroup::TYPE_OR)->where('followers', $follows)->where('_id', $dbuser->_id)->endGroup();
		
		$query = db()->table('ping')->getAll()
				->group()
				  ->addRestriction('target__id', $dbuser->_id)
				  ->group(spitfire\storage\database\RestrictionGroup::TYPE_AND)
					->addRestriction('src', $users)
				   ->addRestriction('target', null, 'IS')
				  ->endGroup()
				->endGroup()
				->addRestriction('created', time() - 720 * 3600, '>')
				->addRestriction('deleted', null, 'IS')
				->setResultsPerPage(10)
				->setOrder('created', 'DESC');
		
		if (isset($_GET['until'])) {
			$query->addRestriction('_id', $_GET['until'], '<');
		}
		
		$notifications = $query->fetchAll();
		
		#Set the notifications that were unseen as seen
		$dbuser->lastSeen = time();
		$dbuser->store();
		
		$this->view->set('notifications', $notifications);
	}
	
	public function counter() {
		
		$memcached = new \spitfire\cache\MemcachedAdapter(); 
		$memcached->setTimeout(20);
		
		$dbuser = db()->table('user')->get('authId', $this->user->id)->fetch();
		
		
		if (!$dbuser) {
			$this->view->set('count', 0)->set('samples', []);
			return;
		}
		
		$follows = db()->table('follow')->get('follower__id', $dbuser->_id);
		$users   = db()->table('user')->get('followers', $follows);
		
		$query = db()->table('ping')->getAll()
				->group()
				  ->addRestriction('target__id', $dbuser->_id)
				  ->group(spitfire\storage\database\RestrictionGroup::TYPE_AND)
				   ->addRestriction('src__id',    $dbuser->_id)
				   ->addRestriction('target__id', null, 'IS')
				  ->endGroup()
				  ->group(spitfire\storage\database\RestrictionGroup::TYPE_AND)
					->addRestriction('src', $users)
				   ->addRestriction('target', null, 'IS')
				  ->endGroup()
				->endGroup()
				->addRestriction('created', max($dbuser->lastSeen, time() - 168 * 3600) , '>')
				->setResultsPerPage(10)
				->setOrder('created', 'DESC');
		
		$activity = db()->table('notification')->getAll()
				->group()
				  ->addRestriction('target__id', $dbuser->_id)
				->endGroup()
				->addRestriction('created', max($dbuser->lastSeenActivity, time() - 720 * 3600) , '>')
				->setResultsPerPage(10)
				->setOrder('created', 'DESC');
		
		
		$this->view->set('count', $memcached->get('ping.notifications.' . $dbuser->_id, function () use($query) { return $query->count(); }));
		$this->view->set('activity', $memcached->get('ping.activity.' . $dbuser->_id, function () use($activity) { return $activity->count(); }));
	}
	
}

