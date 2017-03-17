<?php

/**
 * Prebuilt test controller. Use this to test all the components built into
 * for right operation. This should be deleted whe using Spitfire.
 */

class HomeController extends AppController
{
	public function index() {
		if ($this->user) {
			$this->response->setBody('Redirecting...')->getHeaders()->redirect(new URL('feed'));
		}
	}
}