<?php 

use spitfire\io\session\Session;

class UserController extends AppController
{
	
	public function index() {
		
	}
	
	public function login() {
		
		#If the user is already logged in we do not re-login him.
		if ($this->user) {
			return $this->response->setBody('Redirecting...')
					  ->getHeaders()->redirect(url('feed'));
		}
		
		#Create and keep the token that we'll need to maintain for the app to work
		$token = $this->sso->createToken();
		Session::getInstance()->lock($token);
		
		#Send the user to the login server
		$this->response->setBody('Redirecting...')
			->getHeaders()->redirect($token->getRedirect((string)url('user', 'login')->absolute()));
	}
	
	public function authorize($token) {
		$t = $this->sso->makeToken($token);
		Session::getInstance()->lock($t);
		
		return $this->response->setBody('Redirecting...')
				  ->getHeaders()->redirect(url('feed'));
	}
	
	public function logout() {
		
		#If there is a session for this user, we destroy it
		Session::getInstance()->destroy();
		
		return $this->response->setBody('Redirecting...')
				  ->getHeaders()->redirect(url());
	}
	
	public function follows($username) {
		$user = $this->sso->getUser($username);
		
		$query     = db()->table('follow')->get('follower__id', db()->table('user')->get('authId', $user->getId())->fetch()->_id);
		$followers = db()->table('user')->get('followers', $query);
		
		$paginator = new \spitfire\storage\database\pagination\Paginator($followers);
		
		$this->view->set('pagination', $paginator);
		$this->view->set('followers',  $paginator->records());
		
		$this->view->set('user', $user);
	}
	
	/**
	 * 
	 * @template user/follows
	 * @param type $username
	 */
	public function following($username) {
		$user = $this->sso->getUser($username);
		
		$query     = db()->table('follow')->get('prey__id', db()->table('user')->get('authId', $user->getId())->fetch()->_id);
		$followers = db()->table('user')->get('following', $query);
		
		$paginator = new \spitfire\storage\database\pagination\Paginator($followers);
		
		$this->view->set('pagination', $paginator);
		$this->view->set('followers',  $paginator->records());
		
		$this->view->set('user', $user);
	}
	
	
	/**
	 * 
	 * @template user/show
	 * @param type $username
	 * @param type $args
	 * @throws \spitfire\exceptions\PublicException
	 */
	public function __call($username, $args) {
		$user = $this->sso->getUser($username);
		$dbu  = db()->table('user')->get('authId', $user->getId())->first(true);
		$author = db()->table('author')->get('user', $dbu)->first();
		
		if (!$dbu || !$user) { throw new \spitfire\exceptions\PublicException('No user found', 404); }
		
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
		$this->view->set('user', $user);
		$this->view->set('notifications', $feed->range(0, 10));
		
	}
	
}
