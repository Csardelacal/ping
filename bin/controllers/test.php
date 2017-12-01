<?php

class TestController extends AppController
{
	
	public function test() {
		die($this->sso->makeSignature('1211271296'));
	}
	
}