<?php

use spitfire\io\session\Session;

/*
 * The MIT License
 *
 * Copyright 2019 César de la Cal Bretschneider <cesar@magic3w.com>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

class AccountController extends AppController
{
	
	
	public function login()
	{
		
		#If the user is already logged in we do not re-login him.
		if ($this->user) {
			return $this->response->setBody('Redirecting...')
					  ->getHeaders()->redirect(url('feed'));
		}
		
		#Create and keep the token that we'll need to maintain for the app to work
		$token = $this->sso->createToken();
		Session::getInstance()->lock($token);
		
		#Send the user to the login server
		$this->response->setBody('Redirecting...')
			->getHeaders()->redirect($token->getRedirect((string)url('account', 'login')->absolute()));
	}
	
	public function authorize($token)
	{
		$t = $this->sso->makeToken($token);
		Session::getInstance()->lock($t);
		
		return $this->response->setBody('Redirecting...')
				  ->getHeaders()->redirect(url('feed'));
	}
	
	public function logout()
	{
		
		#If there is a session for this user, we destroy it
		Session::getInstance()->destroy();
		
		return $this->response->setBody('Redirecting...')
				  ->getHeaders()->redirect(url());
	}
}
