<?php

use spitfire\validation\ValidationRule;

/**
 * 
 * @todo This should be moved into SF at some point.
 */
class ClosureValidationRule implements ValidationRule
{
	
	private $c;
	
	public function __construct(Closure$c) {
		$this->c = $c;
	}
	
	public function test($value) {
		$c = $this->c;
		return $c($value);
	}

}

