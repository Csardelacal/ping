<?php

use auth\SSOCache;
use spitfire\core\Environment;
use spitfire\mvc\Director;

class FeedDirector extends Director
{
	
	public function user(string $username) : int
	{
		
		$sso = new SSOCache(Environment::get('SSO'));
		$user = $sso->getUser($username);
		
		/*
		 * Select the current user from the database, we need this user since we'll
		 * be displaying all their content mixed with the content addressed to them
		 * and the content they have subscribed to.
		 *
		 * The variable $me represents the current user's author. The author is the
		 * entity that creates posts and can be followed. Authors can be normalized
		 * across servers in a process called federation.
		 */
		$dbuser = db()->table('user')->get('authId', $user->getId())->first(true);
		
		$me = AuthorModel::find($dbuser);
		
		/*
		 * Find all the authors and sources the user has subscribed to. These will
		 * generally make up the bulk of the data that the user wishes to see.
		 */
		$follows = db()->table('follow')->get('follower__id', $me->_id);
		
		/*
		 * Assemble the query to find all the notifications for the user. Including:
		 * * Pings I sent
		 * * Pings that were addressed at me
		 * * Pings from people I subscribed to
		 *
		 * All the pings shown must be processed and not deleted.
		 * 
		 * @todo The feed retrieval logic should be moved to an external method so it
		 * can be invoked from the controller and director alike
		 */
		$query = db()->table('ping')->getAll()
				->where('src', db()->table('author')->getAll()->where('followers', $follows))
				->where('target', null)
				->where('processed', true)
				->where('deleted', null)
				->setOrder('created', 'DESC');
		
		$mine = db()->table('ping')->get('src__id', $me->_id)
				->where('processed', true)
				->where('deleted', null)
				->setOrder('created', 'DESC');
		
		$atme = db()->table('ping')->get('target__id', $me->_id)
				->where('processed', true)
				->where('deleted', null)
				->setOrder('created', 'DESC');
		
		if (isset($_GET['until'])) {
			$query->where('_id', '<', $_GET['until']);
			$mine->where('_id', '<', $_GET['until']);
			$atme->where('_id', '<', $_GET['until']);
		}
		
		$notifications = $query->range(0, 3);
		$mine->where('created', '>', $notifications->last()->created);
		$atme->where('created', '>', $notifications->last()->created);
		
		$pings = $notifications
			->add($mine->range(0, 100)->toArray())
			->add($atme->range(0, 100)->toArray())
			->sort(function ($a, $b) {
				return $a->created < $b->created? 1 : -1;
			});
		
		console()->success(str_repeat('-', 99))->ln();
		
		foreach($pings as $ping) {
			console()->success(sprintf(
				'|%10d | %10d | %-15s | %-30s | %20s|',
				$ping->_id,
				$ping->src->_id,
				$sso->getUser($ping->src->user->_id)->getUsername(),
				substr(str_replace('\n', '', $ping->content), 0, 30),
				date('Y-m-d H:i:s', $ping->created)
			))->ln();
		}
		
		console()->success(str_repeat('-', 99))->ln();
		
		return 0;
	}
}
