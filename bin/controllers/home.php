<?php

/**
 * Prebuilt test controller. Use this to test all the components built into
 * for right operation. This should be deleted whe using Spitfire.
 */

class HomeController extends AppController
{
	public function index() {
		if ($this->user) {
			$this->response->setBody('Redirecting...')->getHeaders()->redirect(url('feed'));
		}
	}
	
	public function test() {
		$r = request('http://localhost/cloudy/pool1/bucket/read/1.json');
		$r->get('signature', (string)$this->sso->makeSignature('1488571465'));
		die($r->send()->html());
	}
}