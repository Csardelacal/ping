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
			->addRestriction('src__id', $author->_id)
			->addRestriction('target', null, 'IS')
			->addRestriction('deleted', null, 'IS')
			->setOrder('created', 'DESC');
		
		if (isset($_GET['until'])) {
			$feed->addRestriction('_id', $_GET['until'], '<');
		}
		
		$this->view->setFile('user/show');
		$this->view->set('author', $author);
		$this->view->set('user', $author);
		$this->view->set('notifications', $feed->range(0, 10));
		
		$me      = AuthorModel::find($this->user->id);
		$this->view->set('me', $me);
		
	}
	
	public function __call($name, $arguments) {
		return $this->response->setBody('Redirecting...')->getHeaders()->redirect(url('user', 'show', $name));
	}
	
}
