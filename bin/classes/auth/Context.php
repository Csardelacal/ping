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

class Context
{
	
	private $sso;
	private $app;
	private $id;
	private $exists = null;
	private $granted = null;
	
	/**
	 * 
	 * @param SSO    $sso
	 * @param App    $app
	 * @param string $id
	 */
	public function __construct($sso, $app, $id) {
		$this->sso = $sso;
		$this->app = $app;
		$this->id = $id;
	}
	
	public function getApp() {
		return $this->app;
	}
	
	public function getId() {
		return $this->id;
	}
	
	public function exists() {
		return $this->exists;
	}
	
	public function setApp($app) {
		$this->app = $app;
		return $this;
	}
	
	public function setId($name) {
		$this->id = $name;
		return $this;
	}
	
	public function setExists($exists) {
		$this->exists = $exists;
		return $this;
	}
	
	public function setGranted($granted) {
		$this->granted = $granted;
	}
	
	public function isGranted() {
		return ((int)$this->granted) === 2;
	}
	
	public function isDenied() {
		return ((int)$this->granted) === 1;
	}

	public function create($name, $description) {
		$request = new Request(
			$this->sso->getEndpoint() . '/context/create.json', 
			['context' => $this->id, 'signature' => (string)$this->sso->makeSignature()]
		);
		
		$request->send(['name' => $name, 'description' => $description]);
		
		$this->exists = true;
		return true;
	}

}