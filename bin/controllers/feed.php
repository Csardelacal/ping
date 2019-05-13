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

		/*
		 * Select the current user from the database, we need this user since we'll
		 * be displaying all their content mixed with the content addressed to them 
		 * and the content they have subscribed to.
		 * 
		 * The variable $me represents the current user's author. The author is the
		 * entity that creates posts and can be followed. Authors can be normalized
		 * across servers in a process called federation.
		 */
		$dbuser  = db()->table('user')->get('authId', $this->user->id)->fetch()? : UserModel::makeFromSSO($this->sso->getUser($this->user->id));
		$me      = AuthorModel::get($dbuser);
		
		/*
		 * Find all the authors and sources the user has subscribed to. These will
		 * generally make up the bulk of the data that the user wishes to see.
		 */
		$follows = db()->table('follow')->get('follower__id', $me->_id);
		
		/*
		 * Once we have the users, and in order to accelerate the queries, we will
		 * denormalize the users and keep only the ID of the authors we're subscribed
		 * to.
		 */
		$authors   = db()->table('author')->getAll()->where('followers', $follows)->all()->each(function($e) {
			return $e->_id;
		})->toArray();
		
		/*
		 * Assemble the query to find all the notifications for the user. Including:
		 * * Pings I sent
		 * * Pings that were addressed at me
		 * * Pings from people I subscribed to
		 * 
		 * All the pings shown must be processed and not deleted.
		 */
		$query = db()->table('ping')->getAll()
				->group()
				  ->where('target__id', $me->_id)
				  ->where('src__id', $me->_id)
				  ->group(spitfire\storage\database\RestrictionGroup::TYPE_AND)
					->where('src__id', $authors)
				   ->where('target', null)
				  ->endGroup()
				->endGroup()
				->where('processed', true)
				->where('deleted', null)
				->setOrder('created', 'DESC');

		if (isset($_GET['until'])) {
			$query->where('_id', '<', $_GET['until']);
		}

		$notifications = $query->range(0, 2);

		#Set the notifications that were unseen as seen
		$dbuser->lastSeen = time();
		$dbuser->store();

		$this->view->set('notifications', $notifications);
	}
	
	/**
	 * Unlike the feed, the counter will exclude the pings we sent ourselves, and
	 * those directed at ourselves. This is intended to accelerate the response time
	 * of the counter which gets invoked far more often than the feed itself.
	 */
	public function counter() {
		/*
		 * Start memcached. In case a user is hammering the server with requests,
		 * it will be able to answer the requests really quickly without getting
		 * itself clogged with it's own queries.
		 */
		$memcached = new \spitfire\cache\MemcachedAdapter();
		$memcached->setTimeout(20);
		
		/*
		 * Get the current user from the SSO server and extract the author from it
		 */
		$dbuser = db()->table('user')->get('authId', $this->user? $this->user->id : 0)->fetch();
		$me     = AuthorModel::get($dbuser);

		/*
		 * If the user is not registered, then we simply display no notifications
		 * for them.
		 */
		if (!$dbuser) {
			$this->view->set('count', 0);
			$this->view->set('activity', 0);
			return;
		}
		
		/*
		 * Extract the user's following list. This will be used to assemble the 
		 * query that counts the open notifications.
		 */
		$follows = db()->table('follow')->get('follower__id', $me->_id);
		$users   = db()->table('author')->get('followers', $follows)->all()->each(function($e) {
			return $e->_id;
		})->toArray()? : null;
		
		/*
		 * Create the query to find all the posts from authors that we subscribed 
		 * to that occurred since we last checked our feed.
		 */
		$query = db()->table('ping')->getAll()
				->where('src__id', $users)
				->where('target', null)
				->where('created', '>', max($dbuser->lastSeen, time() - 168 * 3600));

		/*
		 * Find all the activity we haven't collected since we last checked it. Please
		 * note that the last seen flag for the activity is separate, this is bound
		 * to change if the activity is merged into the main feed or at least shown
		 * on it.
		 */
		$activity = db()->table('notification')->getAll()
				->addRestriction('target__id', $dbuser->_id)
				->addRestriction('created', max($dbuser->lastSeenActivity, time() - 720 * 3600) , '>');
		
		
		$this->view->set('count', (int)$memcached->get('ping.notifications.' . $dbuser->_id, function () use($query) { return $query->count(); }));
		$this->view->set('activity', (int)$memcached->get('ping.activity.' . $dbuser->_id, function () use($activity) { return $activity->count(); }));
	}

}
