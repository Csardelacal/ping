<?php namespace ping\core;

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

/**
 * The pluggable class allows the application to define an object that will run
 * a piece of code, after it has run all the dependencies, and then runs all the
 * dependent code.
 */
abstract class Pluggable
{
	
	private $before;
	
	private $after;
	
	public function before() {
		if (!$this->before) {
			$this->before = new ClosurePluggable();
		}
		
		return $this->before;
	}
	
	public function after() {
		if (!$this->after) {
			$this->after = new ClosurePluggable();
		}
		
		return $this->after;
	}
	
	abstract protected function body($parameter);
	
	/**
	 * Run is the main function of the pluggable object, when invoked, it will first
	 * execute all the registered before objects, then the body, and finally, the
	 * after code.
	 * 
	 * @param mixed $parameter
	 */
	public function run($parameter) {
		$parameter = $this->before? $this->before->run($parameter) : $parameter;
		
		$parameter = $this->body($parameter)?: $parameter;
		
		return $this->after? $this->after->run($parameter) : $parameter;
	}
	
}
