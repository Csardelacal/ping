<?php

/**
 * The home controller doesn't do much really. It just displays the welcome page
 * if the user is not logged in and redirects to the feed if the user is logged
 * in.
 * 
 * @author César de la Cal Bretschneider <cesar@magic3w.com>
 */
class HomeController extends AppController
{
	
	/**
	 * Home just needs to redirect to the feed itself or display a splash screen
	 * if the user is not yet logged in.
	 */
	public function index() {
		if ($this->user) {
			$this->response->setBody('Redirecting...')->getHeaders()->redirect(url('feed'));
		}
	}
	
}