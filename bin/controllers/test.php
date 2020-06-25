<?php

class TestController extends AppController
{
	
	public function test() {
		var_dump($this->sso->sendEmail(1, 'Test email', 'Test body'));
		die(($this->sso->makeSignature('1768757879')));
	}
	
	public function socket() {
		$signature = $this->sso->makeSignature('1768757879');
		$user = $_GET['user']?? 'Csharp';
		
		for ($i = 0; $i < 10; $i++) {
			$ch = curl_init('https://localhost:8081/' . $user);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_VERBOSE, true);
			curl_setopt($ch, CURLOPT_HEADER, true);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['signature' => (string)$signature, 'body' => md5(time())]));

			echo curl_exec($ch);
			echo curl_error($ch);
		}
		die();
	}
	
}