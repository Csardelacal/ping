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
	private $authenticated;
	private $grantStatus;
	private $src;
	private $remote;
	
	/**
	 *
	 * @var Context[]
	 */
	private $contexts;
	
	/**
	 *
	 * @deprecated since version 20180326
	 * @var type 
	 */
	private $redirect;
	
	public function __construct($sso, $authenticated, $grantStatus, $src, $remote, $context) {
		$this->sso = $sso;
		$this->authenticated = $authenticated;
		$this->grantStatus = $grantStatus;
		$this->src = $src;
		$this->remote = $remote;
		$this->contexts = $context;
	}
	
	public function isAuthenticated() {
		return !!$this->authenticated;
	}
	
	/**
	 * 
	 * @deprecated since version 20180326
	 * @return type
	 */
	public function getAuthenticated() {
		return $this->authenticated;
	}
	
	public function getGrantStatus() {
		return $this->grantStatus;
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
	
	public function getRedirect($contexts = null, $returnurl = null) {
		if ($contexts === null) {
			$contexts = [];
			foreach ($this->contexts as $ctx) { $contexts[] = $ctx->getId(); }
		}
		
		return $this->sso->getEndpoint() . '/auth/connect?' . http_build_query([
			'signature' => $this->sso->makeSignature($this->getSrc()->getId(), $this->sso->getAppId(), $contexts),
			'returnto'  => $returnurl
		]);
	}
	
	public function setAuthenticated($authenticated) {
		$this->authenticated = $authenticated;
		return $this;
	}
	
	public function setGrantStatus($grantStatus) {
		$this->grantStatus = $grantStatus;
		return $this;
	}
	
	public function setRemote($remote) {
		$this->remote = $remote;
		return $this;
	}
	
	public function setContext($context) {
		$this->contexts = $context;
		return $this;
	}
	
	/**
	 * 
	 * @deprecated since version 20180326
	 * @param type $redirect
	 * @return $this
	 */
	public function setRedirect($redirect) {
		$this->redirect = $redirect;
		return $this;
	}
	
	/**
	 * 
	 * @return App
	 */
	public function getSrc() {
		return $this->src;
	}

	public function setSrc($src) {
		$this->src = $src;
		return $this;
	}
}
