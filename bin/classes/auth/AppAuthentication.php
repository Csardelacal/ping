<?php namespace auth;

/* 
 * The MIT License
 *
 * Copyright 2017 César de la Cal Bretschneider <cesar@magic3w.com>.
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

class AppAuthentication
{
	
	/**
	 *
	 * @var SSO
	 */
	private $sso;
	private $local;
	private $remote;
	
	/**
	 *
	 * @var Context[]
	 */
	private $contexts;
	private $token;
	
	public function __construct($sso, $src, $remote, $context, $token) {
		$this->sso = $sso;
		$this->local = $src;
		$this->remote = $remote;
		$this->contexts = $context;
		$this->token = $token;
	}
	
	/**
	 * 
	 * @deprecated since version 20180704
	 * @return boolean
	 */
	public function getAuthenticated() {
		return true;
	}
	
	public function getRemote() {
		return $this->remote;
	}
	
	/**
	 * 
	 * @return Context
	 */
	public function getContext($name) {
		return $this->contexts[$name];
	}
	
	public function getRedirect($tgt, $contexts = null) {
		if ($contexts === null) {
			$contexts = [];
		}
		
		foreach ($contexts as $ctx) { 
			$signatures[] = (string)$this->sso->makeSignature($tgt, [$ctx instanceof Context? $ctx->getId() : $ctx]); 
		}
		
		return $this->sso->getEndpoint() . '/auth/connect?' . http_build_query(['signature' => $signatures]);
	}
	
	public function setRemote($remote) {
		$this->remote = $remote;
		return $this;
	}
	
	public function setContext($context) {
		$this->contexts = $context;
		return $this;
	}
	
	public function setToken($token) {
		$this->token = $token;
		return $this;
	}
	
	/**
	 * 
	 * @return App
	 */
	public function getSrc() {
		return $this->local;
	}
	
	public function getToken() {
		return $this->token;
	}

	public function setSrc($src) {
		$this->local = $src;
		return $this;
	}
}
