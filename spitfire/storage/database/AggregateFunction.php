<?php namespace spitfire\storage\database;

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

class AggregateFunction
{
	
	
	/**
	 * Indicates that a query is accumulating the results and counting them
	 */
	const AGGREGATE_COUNT = 'count';
	
	/**
	 * 
	 * @var QueryField 
	 */
	private $field;
	
	/**
	 *
	 * @var string 
	 */
	private $operation;
	
	/**
	 * The alias allows the system to later recycle the 
	 *
	 * @var string
	 */
	private $alias;
	
	/**
	 * 
	 * @param QueryField $field
	 * @param string $operation
	 */
	public function __construct(QueryField $field, $operation) {
		$this->field = $field;
		$this->operation = $operation;
		$this->alias = '__META__' . $field->getField()->getName() . '_' . $operation . rand(0, 10);
	}
	
	public function getField(): QueryField {
		return $this->field;
	}
	
	public function getOperation() {
		return $this->operation;
	}
	
	public function getAlias() {
		return $this->alias;
	}
	
	public function setField(QueryField $field) {
		$this->field = $field;
		return $this;
	}
	
	public function setOperation($operation) {
		$this->operation = $operation;
		return $this;
	}
	
	public function setAlias($alias) {
		$this->alias = $alias;
		return $this;
	}

}
