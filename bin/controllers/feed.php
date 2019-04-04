<?php

class FeedController extends AppController
{

	public function index() {

		/*
		 * The feed only works with a logged in user. This is due to the fact that
		 * there is no point in having a public feed that displays contents
		 */
		if (!$this->user) {
			return $this->response->setBody('Redirecting...')->getHeaders()->redirect(url('user', 'login'));
		}

		$this->secondaryNav->add(url('feed'), 'Feed')->setActive(true);
		$this->secondaryNav->add(url('activity'), 'Activity <span class="badge" data-ping-activity data-ping-amt="0">?</span>');
		$this->secondaryNav->add(url('people', 'followingMe'), 'Followers');
		$this->secondaryNav->add(url('people', 'iFollow'), 'Following');

		/*
		 * Read the notifications for the user and send them to the view
		 */

		$dbuser  = db()->table('user')->get('authId', $this->user->id)->fetch()? : UserModel::makeFromSSO($this->sso->getUser($this->user->id));
		$follows = db()->table('follow')->get('follower__id', AuthorModel::get($dbuser)->_id);
		$users   = db()->table('author')->getAll()->where('followers', $follows)->all()->each(function($e) {
			return $e->_id;
		})->toArray();
		
		$users[] = $dbuser->_id;

		$query = db()->table('ping')->getAll()
				->group()
				  ->addRestriction('target__id', $dbuser->_id)
				  ->group(spitfire\storage\database\RestrictionGroup::TYPE_AND)
					->where('src__id', $users)
				   ->addRestriction('target', null, 'IS')
				  ->endGroup()
				->endGroup()
				->addRestriction('created', time() - 720 * 3600, '>')
				->where('processed', true)
				->addRestriction('deleted', null, 'IS')
				->setOrder('created', 'DESC');

		if (isset($_GET['until'])) {
			$query->addRestriction('_id', $_GET['until'], '<');
		}

		$notifications = $query->range(0, 2);

		#Set the notifications that were unseen as seen
		$dbuser->lastSeen = time();
		$dbuser->store();

		$this->view->set('notifications', $notifications);
	}

	public function counter() {

		$memcached = new \spitfire\cache\MemcachedAdapter();
		$memcached->setTimeout(20);

		$dbuser = db()->table('user')->get('authId', $this->user? $this->user->id : 0)->fetch();


		if (!$dbuser) {
			$this->view->set('count', 0);
			$this->view->set('activity', 0);
			return;
		}

		$follows = db()->table('follow')->get('follower__id', AuthorModel::get($dbuser)->_id);
		$users   = db()->table('user')->get('followers', $follows)->all()->each(function($e) {
			return $e->_id;
		})->toArray();

		$query = db()->table('ping')->getAll()
				->where('src__id', $users)
				->where('target', null)
				->where('created', '>', max($dbuser->lastSeen, time() - 168 * 3600));

		$activity = db()->table('notification')->getAll()
				->addRestriction('target__id', $dbuser->_id)
				->addRestriction('created', max($dbuser->lastSeenActivity, time() - 720 * 3600) , '>');


		$this->view->set('count', (int)$memcached->get('ping.notifications.' . $dbuser->_id, function () use($query) { return $query->count(); }));
		$this->view->set('activity', (int)$memcached->get('ping.activity.' . $dbuser->_id, function () use($activity) { return $activity->count(); }));
	}

}
