<?php namespace spitfire\core\event;

use Closure;

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
 * The listener represents the leaf of a target tree, but generates a binary Listener
 * tree below it.
 * 
 * @author César de la Cal Bretschneider <cesar@magic3w.com>
 */
class Listener extends Pluggable
{
	
	/**
	 * 
	 *
	 * @var Closure[] 
	 */
	private $body = [];
	
	/**
	 * 
	 * @param Closure $callable
	 * @return $this
	 */
	public function do(Closure$callable) {
		$this->body[] = $callable;
		return $this;
	}
	
	public function remove(Closure$callable) {
		unset($this->body[array_search($callable, $this->body, true)]);
		return true;
	}
	
	public function _body($parameter) {
		$arg = $parameter;
		
		foreach ($this->body as $callable) {
			$arg = $callable($arg)?: $arg;
		}
		
		return $arg;
	}
	
	/**
	 * Run is the main function of the pluggable object, when invoked, it will first
	 * execute all the registered before objects, then the body, and finally, the
	 * after code.
	 * 
	 * @param mixed $parameter
	 */
	public function run($parameter) {
		$parameter = $this->before? $this->before->run($parameter) : $parameter;
		
		$parameter = $this->_body($parameter)?: $parameter;
		
		return $this->after? $this->after->run($parameter) : $parameter;
	}
}
