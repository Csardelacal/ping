<?php namespace spitfire\core\event;

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
 * A plugin target provides a namespace in which listeners and triggers can 
 * connect.
 * 
 * NOTE: Plugins are a costly component of every application and should 
 * therefore be provided carefully and deliberately to create very loose coupling
 * of the application and the plugin.
 * 
 * @author César de la Cal Bretschneider <cesar@magic3w.com>
 */
class Target extends Pluggable
{
	
	/**
	 * The nested targets within this instance.
	 *
	 * @var Target[]
	 */
	private $children = [];
	
	/**
	 * This is a convenience method, for providing the ability to users to quickly
	 * create complicated and nested listeners for events.
	 * 
	 * For example the following code:
	 * <code>
	 * $plugins = new Target();
	 * $plugins->comments->form->before()->do(function ($e) { return something($e); });
	 * </code>
	 * 
	 * Makes it easy to understand what the user is attempting to do. Events do 
	 * not bubble though, the feature just makes it easier to understand.
	 * 
	 * @param string $name
	 * @return Target
	 */
	public function __get($name) {
		
		if (isset($this->children[$name])) {
			return $this->children[$name];
		} 
		else {
			return $this->children[$name] = new Target();
		}
	}
	
	/**
	 * Triggers an event. The object is then passed along to every listener and 
	 * the result of the listener overrides the object it was provided (in case 
	 * the listener does return anything).
	 * 
	 * This code:
	 * <pre><code>
	 * $obj = new Object();
	 * $plugins->form->do(function ($o) { return something($o); }, $obj);
	 * </code></pre>
	 * 
	 * Is functionally identical to:
	 * <pre><code>
	 * $obj = new Object();
	 * $obj = $plugins->form->before()->run($obj);
	 * $obj = something($obj);
	 * $obj = $plugins->form->after()->run($obj);
	 * </code></pre>
	 * 
	 * While the second sample seems way more convoluted (and it is), it may be a
	 * good option for implementing plugins that should not end in a tree of nested
	 * functions calling the events of their components.
	 * 
	 * @param Closure|callable $operation
	 * @param mixed $object
	 * @return mixed
	 */
	public function do($operation, $object) {
		$before = $this->before()->run($object);
		return $this->after()->run($operation($before)?: $before);
	}

}
