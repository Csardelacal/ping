<?php namespace tests\spitfire\core;

use BadMethodCallException;
use PHPUnit\Framework\TestCase;
use spitfire\core\Headers;

class HeadersTest extends TestCase
{
	
	public function testContentType() {
		
		$t = new Headers();
		
		$t->contentType('php');
		$this->assertEquals('text/html;charset=utf-8', $t->get('Content-type'));
		
		$t->contentType('html');
		$this->assertEquals('text/html;charset=utf-8', $t->get('Content-type'));
		
		$t->contentType('json');
		$this->assertEquals('application/json;charset=utf-8', $t->get('Content-type'));
		
		$t->contentType('xml');
		$this->assertEquals('application/xml;charset=utf-8', $t->get('Content-type'));
		
	}
	
	/**
	 * Test whether the state shorthand function rewrites the states properly. If
	 * it does, the app should be returning 200 OK as status when you pass 200 to
	 * it.
	 */
	public function testStatus() {
		$t = new Headers();
		$t->status('200');
		$this->assertEquals('200', $t->get('Status'));
	}
	
	/**
	 */
	public function testInvalidStatus() {
		$t = new Headers();
		
		$this->expectException(BadMethodCallException::class);
		$t->status('22');
	}
	
}

