<?php

use auth\SSO;
use spitfire\core\Environment;

abstract class AppController extends Controller
{
	
	protected $sso;
	protected $user;
	
	public function _onload() {
		$session    = \spitfire\io\session\Session::getInstance();
		
		$this->sso  = new SSO(Environment::get('sso.endpoint'), Environment::get('sso.appId'), Environment::get('sso.appSec'));
		$this->user = $session->getUser();
	}
	
}
