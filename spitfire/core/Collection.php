<?php namespace spitfire\core;

use BadMethodCallException;
use spitfire\core\collection\DefinedCollection;
use spitfire\exceptions\OutOfBoundsException;

/**
 * The collection class is intended to supercede the array and provide additional
 * functionality and ease of use to the programmer.
 */
class Collection extends DefinedCollection
{
	
	public function flatten() {
		$_ret  = new self();
		
		foreach ($this->toArray() as $item) {
			if ($item instanceof Collection) { $_ret->add($item->flatten()); }
			elseif (is_array($item))         { $c = new self($item); $_ret->add($c->flatten()); }
			else { $_ret->push($item); }
		}
		
		return $_ret;
	}
	
	/**
	 * This function checks whether a collection contains only elements with a 
	 * given type. This function also accepts base types.
	 * 
	 * Following base types are accepted:
	 * 
	 * <ul>
	 * <li>int</li><li>float</li>
	 * <li>number</li><li>string</li>
	 * <li>array</li>
	 * <ul>
	 * 
	 * @param string $type Base type or class name to check.
	 * @return boolean
	 */
	public function containsOnly($type) {
		switch($type) {
			case 'int'   : return $this->reduce(function ($p, $c) { return $p && is_int($c); }, true);
			case 'number': return $this->reduce(function ($p, $c) { return $p && is_numeric($c); }, true);
			case 'string': return $this->reduce(function ($p, $c) { return $p && is_string($c); }, true);
			case 'array' : return $this->reduce(function ($p, $c) { return $p && is_array($c); }, true);
			default      : return $this->reduce(function ($p, $c) use ($type) { return $p && is_a($c, $type); }, true);
		}
	}
	
	/**
	 * Removes all duplicates from the collection.
	 * 
	 * @return Collection
	 */
	public function unique() {
		return new Collection(array_unique($this->toArray()));
	}
	
	/**
	 * Adds up the elements in the collection. Please note that this method will
	 * double check to see if all the provided elements are actually numeric and
	 * can be added together.
	 * 
	 * @return int|float
	 * @throws BadMethodCallException
	 */
	public function sum() {
		if ($this->isEmpty())               { throw new BadMethodCallException('Collection is empty'); }
		if (!$this->containsOnly('number')) { throw new BadMethodCallException('Collection does contain non-numeric types'); }
		
		return array_sum($this->toArray());
	}
	
	public function sort($callback = null) {
		$copy = $this->toArray();
		
		if (!$callback) { sort($copy); }
		else            { usort($copy, $callback); }
		
		return new Collection($copy);
	}
	
	/**
	 * Returns the average value of the elements inside the collection.
	 * 
	 * @throws BadMethodCallException If the collection contains non-numeric values
	 * @return int|float
	 */
	public function avg() {
		return $this->sum() / $this->count();
	}
	
	public function join($glue) {
		return implode($glue, $this->toArray());
	}
	
	/**
	 * Extracts a certain key from every element in the collection. This requires
	 * every element in the collection to be either an object or an array.
	 * 
	 * The method does not accept values that are neither array nor object, but 
	 * will return null if the key is undefined in the array or object being used.
	 * 
	 * @param mixed $key
	 */
	public function extract($key) {
		return new Collection(array_map(function ($e) use ($key) {
			if (is_array($e))  { return isset($e[$key])? $e[$key] : null; }
			if (is_object($e)) { return isset($e->$key)? $e->$key : null; }
			
			throw new OutOfBoundsException('Collection::extract requires array to contain only arrays and objects');
		}, $this->toArray()));
	}
	
	public function groupBy($callable) {
		$groups = new self();
		
		$this->each(function ($e) use ($groups, $callable) {
			$key = $callable($e);
			
			if (!isset($groups[$key])) {
				$groups[$key] = new self();
			}
			
			$groups[$key]->push($e);
		});
		
		return $groups;
	}
	
	public function reverse() {
		return new Collection(array_reverse($this->toArray()));
	}

}
