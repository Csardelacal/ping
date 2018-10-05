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
		$r = request('http://localhost/cloudy/pool1/bucket/read/i353k45j.json');
		$r->get('signature', (string)$this->sso->makeSignature('1488571465'));
		
		$response = $r->send()->json();
		$server   = $response->payload->master->hostname;
		
		$r = request($server . '/media/create.json');
		$r->get('signature', (string)$this->sso->makeSignature('1488571465'));
		$r->post('bucket', 'i353k45j');
		$r->post('name', base_convert(time(), 10, 32) . '.jpg');
		$r->post('file', new CURLFile('/home/cesar/Pictures/1472761257.crow3000_img_0043.jpg'));
		$r->post('mime', mime('/home/cesar/Pictures/1472761257.crow3000_img_0043.jpg'));
		
		$response = $r->send()->json();
		$mediauuid = $response->uniqid;
		
		var_dump($response);
		sleep(1);
		
		$r = request($server . '/media/read/' . $mediauuid . '.json');
		$r->get('signature', (string)$this->sso->makeSignature('1488571465'));
		$response = $r->send()->json();
		
		
		foreach ($response->servers as $server) {
			echo sprintf('<img src="%s/file/retrieve/link/%s" width="100" height="100">', $server->hostname, reset($response->links)->uniqid);
		}
		
		die(sprintf('<a href="%s">Delete</a>', url('home', 'testDelete', $mediauuid)));
	}
	
	public function testDelete($uniqid) {
		
		$r = request('http://localhost/cloudy/pool1/bucket/read/i353k45j.json');
		$r->get('signature', (string)$this->sso->makeSignature('1488571465'));
		
		$response = $r->send()->json();
		$server   = $response->payload->master->hostname;
		
		
		$r = request($server . '/media/delete/' . $uniqid . '.json');
		$r->get('signature', (string)$this->sso->makeSignature('1488571465'));
		$r->send();
		
		die();
	}
}