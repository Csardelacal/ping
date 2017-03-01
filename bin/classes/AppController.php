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
	
	protected $secondaryNav;
	
	public function _onload() {
		$session     = Session::getInstance();
		
		#Create a user
		$this->sso   = new SSOCache(Environment::get('sso.endpoint'), Environment::get('sso.appId'), Environment::get('sso.appSec'));
		$this->token = isset($_GET['token'])? $this->sso->makeToken($_GET['token']) : $session->getUser();
		$this->user  = $this->token && $this->token instanceof auth\Token? $this->token->getTokenInfo()->user : null;
		
		#Maintain the user in the view. This way we can draw an interface for them
		$this->view->set('authUser', $this->user);
		$this->view->set('sso', $this->sso);
		
		#Create a sidebar navigation
		$this->secondaryNav = new Navigation();
		$this->view->set('secondary_navigation', $this->secondaryNav);
		
		_t(new ping\Locale());
	}
	
}
