<?php

class TestController extends AppController
{
	
	public function test() {
		die(urlencode($this->sso->makeSignature()) . '<br>' . urlencode($this->sso->makeSignature('390035134')));
	}
	
}