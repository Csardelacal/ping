<?php 

class UserController extends AppController
{
	
	public function index() {
		
	}
	
	
	/**
	 * 
	 * @template user/show
	 * @param type $username
	 * @param type $args
	 * @throws \spitfire\exceptions\PublicException
	 */
	public function show($username) {
		$author = AuthorModel::find($username);
		
		if (!$author) { throw new \spitfire\exceptions\PublicException('No author found', 404); }
		
		$feed = db()->table('ping')
			->getAll()
			->group()
				->group(spitfire\storage\database\RestrictionGroup::TYPE_AND)
					->addRestriction('src', $author)
					->addRestriction('target', null, 'IS')
				->endGroup()
				->group(spitfire\storage\database\RestrictionGroup::TYPE_AND)
					->addRestriction('src', AuthorModel::get(db()->table('user')->get('_id', $this->user? $this->user->id : null)->fetch()))
					->addRestriction('target', $author)
				->endGroup()
			->endGroup()
			->addRestriction('deleted', null, 'IS')
			->setOrder('created', 'DESC');
		
		if (isset($_GET['until'])) {
			$feed->addRestriction('_id', $_GET['until'], '<');
		}
		
		$this->view->setFile('user/show');
		$this->view->set('author', $author);
		$this->view->set('user', $author);
		$this->view->set('notifications', $feed->range(0, 10));
		
		$dbuser  = db()->table('user')->get('authId', $this->user->id)->fetch()? : UserModel::makeFromSSO($this->sso->getUser($this->user->id));
		$me      = AuthorModel::get($dbuser);
		$this->view->set('me', $me);
	}
	
	public function __call($name, $arguments) {
		return $this->response->setBody('Redirecting...')->getHeaders()->redirect(url('user', 'show', $name));
	}
	
}
