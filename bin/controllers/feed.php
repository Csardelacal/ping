<?php

class FeedController extends AppController
{
	
	public function index() {
		
		/*
		 * The feed only works with a logged in user. This is due to the fact that
		 * there is no point in having a public feed that displays contents 
		 */
		if (!$this->user) { 
			return $this->response->getHeaders()->redirect(new URL('user', 'login')); 
		}
		
		$this->secondaryNav->add(new URL('feed'), 'Feed')->setActive(true);
	}
	
}
