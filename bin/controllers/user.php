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
					  ->getHeaders()->redirect(new URL('feed'));
		}
		
		#Create and keep the token that we'll need to maintain for the app to work
		$token = $this->sso->createToken();
		Session::getInstance()->lock($token);
		
		#Send the user to the login server
		$this->response->setBody('Redirecting...')
			->getHeaders()->redirect($token->getRedirect((string)new \AbsoluteURL('user', 'login')));
	}
	
	public function authorize($token) {
		$t = $this->sso->makeToken($token);
		Session::getInstance()->lock($t);
		
		return $this->response->setBody('Redirecting...')
				  ->getHeaders()->redirect(new URL('feed'));
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
		$followers = db()->table('user')->get('followers', $query)->setResultsPerPage(21);
		
		$paginator = new Pagination($followers);
		
		$this->view->set('user', $user);
		$this->view->set('pagination', $paginator);
		$this->view->set('followers',  $followers->fetchAll());
	}
	
	/**
	 * 
	 * @template user/follows
	 * @param type $username
	 */
	public function following($username) {
		$user = $this->sso->getUser($username);
		
		$query     = db()->table('follow')->get('prey__id', db()->table('user')->get('authId', $user->getId())->fetch()->_id);
		$followers = db()->table('user')->get('following', $query)->setResultsPerPage(21);
		
		$paginator = new Pagination($followers);
		
		$this->view->set('user', $user);
		$this->view->set('pagination', $paginator);
		$this->view->set('followers',  $followers->fetchAll());
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
		$dbu  = db()->table('user')->get('authId', $user->getId())->fetch();
		
		if (!$dbu || !$user) { throw new \spitfire\exceptions\PublicException('No user found', 404); }
		
		$this->secondaryNav->add(new URL('feed'), 'Feed');
		$this->secondaryNav->add(new URL('people', 'followingMe'), 'Followers');
		$this->secondaryNav->add(new URL('people', 'iFollow'), 'Following');
		
		$feed = db()->table('ping')
			->getAll()
			->group()
				->group(spitfire\storage\database\RestrictionGroup::TYPE_AND)
					->addRestriction('src', $dbu)
					->addRestriction('target', null, 'IS')
				->endGroup()
				->group(spitfire\storage\database\RestrictionGroup::TYPE_AND)
					->addRestriction('src', db()->table('user')->get('_id', $this->user->id)->fetch())
					->addRestriction('target', $dbu)
				->endGroup()
			->endGroup()
			->addRestriction('deleted', null, 'IS')
			->setResultsPerPage(10)
			->setOrder('created', 'DESC');
		
		if (isset($_GET['until'])) {
			$feed->addRestriction('_id', $_GET['until'], '<');
		}
		
		$this->view->set('user', $user);
		$this->view->set('notifications', $feed->fetchAll());
		
	}
	
}
