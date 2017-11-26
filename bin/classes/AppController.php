<?php

use auth\SSOCache;
use navigation\Navigation;
use spitfire\core\Environment;
use spitfire\io\session\Session;

abstract class AppController extends Controller
{
	
	/**
	 *
	 * @var auth\SSO
	 */
	protected $sso;
	protected $user;
	
	/**
	 *
	 * @var auth\Token
	 */
	protected $token;
	
	protected $secondaryNav;
	
	public function _onload() {
		$session     = Session::getInstance();
		
		#Create a brief cache for the sessions.
		$cache       = new spitfire\cache\MemcachedAdapter();
		$cache->setTimeout(120);
		
		#Create a user
		$this->sso   = new SSOCache(Environment::get('SSO'));
		$this->token = isset($_GET['token'])? $this->sso->makeToken($_GET['token']) : $session->getUser();
		
		#Fetch the user from the cache if necessary
		$this->user  = $this->token && $this->token instanceof auth\Token? $cache->get('token_' . $this->token->getId(), function () { 
			return $this->token->getTokenInfo()->user; 
		}) : null;
		
		#Maintain the user in the view. This way we can draw an interface for them
		$this->view->set('authUser', $this->user);
		$this->view->set('sso', $this->sso);
		
		#Create a sidebar navigation
		$this->secondaryNav = new Navigation();
		$this->view->set('secondary_navigation', $this->secondaryNav);
		
		_t(new ping\Locale());
	}
	
}
