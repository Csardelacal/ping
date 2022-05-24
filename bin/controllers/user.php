<?php

class UserController extends AppController
{
	
	public function index()
	{
	}
	
	
	/**
	 *
	 * @template user/show
	 * @param type $username
	 * @param type $args
	 * @throws \spitfire\exceptions\PublicException
	 */
	public function show($username)
	{
		$author = AuthorModel::find($username);
		
		if (!$author) {
			throw new \spitfire\exceptions\PublicException('No author found', 404);
		}
		
		$feed = db()->table('ping')
			->getAll()
			->addRestriction('src__id', $author->_id)
			->addRestriction('target', null, 'IS')
			->addRestriction('deleted', null, 'IS')
			->setOrder('created', 'DESC');
		
		
		if (isset($_GET['until'])) {
			$feed->addRestriction('_id', $_GET['until'], '<');
		}
		
		$pings = $feed->range(0, 10);
		
		if ($this->user) {
			$dbuser  = db()->table('user')->get('_id', $this->user->id)->fetch();
			
			if (!$dbuser) {
				$dbuser = UserModel::makeFromSSO($this->sso->getUser($this->user->id));
			}
			
			$me      = AuthorModel::find($dbuser->_id);
			
			$mine = db()->table('ping')->get('src__id', $me->_id)
					->where('target__id', $author->_id)
					->where('processed', true)
					->where('deleted', null)
					->where('created', '>', $pings->last()->created)
					->setOrder('created', 'DESC');
			
			if (isset($_GET['until'])) {
				$mine->addRestriction('_id', $_GET['until'], '<');
			}
			
			$pings = $pings->add($mine->range(0, 100)->toArray())->sort(function ($a, $b) {
				return $a->created < $b->created? 1 : -1;
			});
		}
		
		$this->view->setFile('user/show');
		$this->view->set('author', $author);
		$this->view->set('user', $author);
		$this->view->set('notifications', $pings);
		$this->view->set('me', $me);
	}
	
	public function __call($name, $arguments)
	{
		return $this->response->setBody('Redirecting...')->getHeaders()->redirect(url('user', 'show', $name));
	}
}
