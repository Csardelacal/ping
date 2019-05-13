<?php

use auth\SSO;
use auth\SSOCache;
use auth\Token;
use ping\core\Ping;
use ping\Locale;
use spitfire\cache\MemcachedAdapter;
use spitfire\core\Environment;
use spitfire\io\session\Session;

abstract class AppController extends Controller
{
	
	/**
	 *
	 * @var SSO
	 */
	public $sso;
	protected $user;
	
	
	protected $core;
	
	/**
	 *
	 * @var Token
	 */
	protected $token;
	
	protected $secondaryNav;
	
	public function _onload() {
		$session     = Session::getInstance();
		
		#Create a brief cache for the sessions.
		$cache       = new MemcachedAdapter();
		$cache->setTimeout(120);
		
		#Create a user
		$this->sso     = new SSOCache(Environment::get('SSO'));
		$this->token   = isset($_GET['token'])? $this->sso->makeToken($_GET['token']) : $session->getUser();
		$this->authapp = isset($_GET['signature'])? $this->sso->authApp($_GET['signature']) : null;
		
		#Fetch the user from the cache if necessary
		$this->user  = $this->token && $this->token instanceof Token? $cache->get('ping_token_' . $this->token->getId(), function () { 
			return $this->token->isAuthenticated()? $this->token->getTokenInfo()->user : null; 
		}) : null;
		
		$this->authapp = isset($_GET['signature'])? $this->sso->authApp($_GET['signature']) : 
			($this->user? $cache->get('ping_token_' . $this->token->getId(), function () { 
				return $this->token->getTokenInfo()->app->id; 
			}) : null);
		
		#Maintain the user in the view. This way we can draw an interface for them
		$this->view->set('authUser', $this->user);
		$this->view->set('sso', $this->sso);
		
		#Create the core, so the application can reliably and consistently handle events
		$this->core = Ping::instance();
		
		_t(new Locale());
	}
	
}
