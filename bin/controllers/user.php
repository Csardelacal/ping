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
			->getHeaders()->redirect($token->getRedirect((string)new absoluteURL('user', 'login')));
	}
	
	public function logout() {
		
		#If there is a session for this user, we destroy it
		Session::getInstance()->destroy();
		
		return $this->response->setBody('Redirecting...')
				  ->getHeaders()->redirect(new URL());
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
		
		$feed = db()->table('notifications')
			->get('src', $dbu)
			->addRestriction('target', null)
			->setResultsPerPage(10)
			->setOrder('created', 'DESC');
		
		if (isset($_GET['until'])) {
			$feed->addRestriction('_id', $_GET['until'], '<');
		}
		
		$this->view->set('user', $user);
		$this->view->set('notifications', $feed->fetchAll());
	}
	
}
