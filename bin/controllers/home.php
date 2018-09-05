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
		
		$response = $r->send()->json();
		$server   = $response->payload->master->hostname;
		
		$r = request($server . '/media/create.json');
		$r->get('signature', (string)$this->sso->makeSignature('1488571465'));
		$r->post('bucket', 'i353k45j');
		$r->post('name', base_convert(time(), 10, 32) . '.jpg');
		$r->post('file', new CURLFile('/home/cesar/Pictures/1472761257.crow3000_img_0043.jpg'));
		$r->post('mime', mime('/home/cesar/Pictures/1472761257.crow3000_img_0043.jpg'));
		$response = $r->send()->html();
		die($response);
	}
}