<?php namespace spitfire\core\collection;

use ArrayAccess;
use BadMethodCallException;
use spitfire\core\Collection;
use spitfire\exceptions\OutOfRangeException;
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

/**
 * The defined collection provides a known amount of results, effectively making
 * it an array or Hashmap like structure.
 * 
 * In Spitfire we just wrap a few array functions inside this class to provide the
 * consistent behavior and extendability that we need.
 * 
 * @author César de la Cal Bretschneider <cesar@magic3w.com>
 */
class DefinedCollection implements ArrayAccess, CollectionInterface
{
	private $items;
	
	/**
	 * The collection element allows to extend array functionality to provide
	 * programmers with simple methods to aggregate the data in the array.
	 * 
	 * @param Collection|mixed $e
	 */
	public function __construct($e = null) {
		if ($e === null)                  {	$this->items = []; }
		elseif ($e instanceof Collection) { $this->items = $e->toArray(); }
		elseif (is_array($e))             { $this->items = $e; }
		else                              { $this->items = [$e]; }
	}
	
	/**
	 * This method iterates over the elements of the array and applies a provided
	 * callback to each of them. The value your function returns if placed in the
	 * array.
	 * 
	 * @param callable|array $callable
	 * @return Collection
	 * @throws BadMethodCallException
	 */
	public function each($callable) {
		
		/*
		 * If the callback provided is not a valid callable then the function cannot
		 * properly continue.
		 */
		if (!is_callable($callable)) { 
			throw new BadMethodCallException('Invalid callable provided to collection::each()', 1703221329); 
		}
		
		return new Collection(array_map($callable, $this->items));
	}
	
	/**
	 * Reduces the array to a single value using a callback function.
	 * 
	 * @param callable $callback
	 * @param mixed    $initial
	 * @return mixed
	 */
	public function reduce($callback, $initial = null) {
		return array_reduce($this->items, $callback, $initial);
	}
	
	/**
	 * Reports whether the collection is empty.
	 * 
	 * @return boolean
	 */
	public function isEmpty() {
		return empty($this->items);
	}
	
	public function has($idx) {
		return isset($this->items[$idx]);
	}
	
	public function contains($e) {
		return array_search($e, $this->items, true) !== false;
	}
	
	/**
	 * Filters the collection using a callback. This allows a collection to shed
	 * values that are not useful to the programmer.
	 * 
	 * Please note that this will return a copy of the collection and the original
	 * collection will remain unmodified.
	 * 
	 * @param callable $callback
	 * @return Collection
	 */
	public function filter($callback = null) {
		#If there was no callback defined, then we filter the array without params
		if ($callback === null) { return new Collection(array_filter($this->items)); }
		
		#Otherwise we use the callback parameter to filter the array
		return new Collection(array_filter($this->items, $callback));
	}
	
	/**
	 * Counts the number of elements inside the collection.
	 * 
	 * @return int
	 */
	public function count() {
		return count($this->items);
	}
	
	public function push($element) {
		$this->items[] = $element;
		return $element;
	}
	
	public function add($elements) {
		if ($elements instanceof Collection) { $elements = $elements->toArray(); }
		
		$this->items = array_merge($this->items, $elements);
		return $this;
	}
	
	public function remove($element) {
		$i = array_search($element, $this->items, true);
		if ($i === false) { throw new OutOfRangeException('Not found', 1804292224); }
		
		unset($this->items[$i]);
		return $this;
	}
	
	public function reset() {
		$this->items = [];
		return $this;
	}
	
	public function current() {
		return current($this->items);
	}
	
	public function key() {
		return key($this->items);
	}
	
	public function next() {
		return next($this->items);
	}
	
	public function offsetExists($offset) {
		return array_key_exists($offset, $this->items);
	}
	
	public function offsetGet($offset) {
		if (!array_key_exists($offset, $this->items)) {
			throw new OutOfRangeException('Undefined index: ' . $offset, 1703221322);
		}
		
		return $this->items[$offset];
	}
	
	public function offsetSet($offset, $value) {
		$this->items[$offset] = $value;
	}
	
	public function offsetUnset($offset) {
		unset($this->items[$offset]);
	}
	
	public function rewind() {
		return reset($this->items);
	}
	
	public function last() {
		if (!isset($this->items)) { throw new PrivateException('Collection error', 1709042046); }
		return end($this->items);
	}

	public function shift() {
		return array_shift($this->items);
	}
	
	/**
	 * Indicates whether the current element in the Iterator is valid. To achieve
	 * this we use the key() function in PHP which will return the key the array
	 * is currently forwarded to or (which is interesting to us) NULL in the event
	 * that the array has been forwarded past it's end.
	 * 
	 * @see key
	 * @return boolean
	 */
	public function valid() {
		return null !== key($this->items);
	}
	
	/**
	 * Returns the items contained by this Collection. This method may only work
	 * if the data the collection is managing is actually a defined set and not a
	 * pointer or something similar.
	 * 
	 * @return mixed[]
	 */
	public function toArray() {
		return $this->items;
	}
	
	public function __isset($name) {
		return isset($this->items[$name]);
	}
}
