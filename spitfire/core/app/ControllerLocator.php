<?php namespace spitfire\core\app;

use spitfire\core\Context;
use ReflectionClass;
use spitfire\exceptions\PublicException;
use spitfire\exceptions\PrivateException;

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

class ControllerLocator
{
	
	/**
	 *
	 * @var NamespaceMapping
	 */
	private $mapping;
	
	public function __construct($mapping) {
		$this->mapping = $mapping;
	}

	/**
	 * Checks if the current application has a controller with the name specified
	 * by the single argument this receives. In case a controller is found and
	 * it is not abstract the app will return the fully qualified class name of 
	 * the Controller.
	 *
	 * It should not be necessary to check the return value with the === operator
	 * as the return value on success should never be matched otherwise.
	 *
	 * @param  string[] $name The name of the controller being searched
	 * @return string|boolean The name of the class that has the controller
	 */
	public function hasController($name) {
		$c    = $this->mapping->getNameSpace() . implode('\\', (array)$name) . 'Controller';
		if (!class_exists($c)) { return false; }

		$reflection = new ReflectionClass($c);
		if ($reflection->isAbstract()) { return false; }
			
		return $c;
	}
	
	/**
	 * Creates a new Controller inside the context of the request. Please note 
	 * that this may throw an Exception due to the controller not being found.
	 * 
	 * @param string[] $controller
	 * @param Context $intent
	 * @return Controller
	 * @throws PublicException
	 */
	public function getController($controller, Context$intent) {
		#Get the controllers class name. If it doesn't exist it'll be false
		$c = $this->hasController($controller);
		
		#If no controller was found, we can throw an exception letting the user know
		if ($c === false) { throw new PublicException("Page not found", 404, new PrivateException("Controller {$controller[0]} not found", 0) ); }
		
		#Otherwise we will instantiate the class and return it
		return new $c($intent);
	}
	
	public function getControllerURI($controller) {
		return explode('\\', substr(get_class($controller), strlen($this->mapping->getNameSpace()), 0-strlen('Controller')));
	}
}
