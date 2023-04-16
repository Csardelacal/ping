<?php namespace spitfire\core\app;

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

class NamespaceMapping
{
	
	/**
	 * The basedir is the root directory of an application. For spitfire this is 
	 * usually the /bin directory. This directory contains all the app specific
	 * data. Including controllers, views and models.
	 * 
	 * In the specific case of Spitfire this folder also contains the 'child apps'
	 * that can be added to it.
	 *
	 * @var string
	 */
	private $basedir;
	private $URISpace;
	private $namespace;
	
	/**
	 * Creates a new App. Receives the directory where this app resides in
	 * and the URI namespace it uses.
	 * 
	 * @param string $basedir The root directory of this app
	 * @param string $URISpace The URI name-space it 'owns'
	 * @param string $namespace The class name-space it 'owns'
	 */
	public function __construct($basedir, $URISpace, $namespace) {
		$this->basedir  = $basedir;
		$this->URISpace = $URISpace;
		$this->namespace = $namespace;
	}
	
	/**
	 * 
	 * @return string
	 */
	public function getBaseDir() : string {
		return $this->basedir;
	}
	
	/**
	 * 
	 * @return string|null
	 */
	public function getURISpace() {
		return $this->URISpace;
	}
	
	/**
	 * 
	 * @return string|null
	 */
	public function getNameSpace() {
		return $this->namespace;
	}
}
