<?php namespace spitfire\core\parser\parser;

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

class ParseTree
{
	
	private $block;
	private $leafs = [];
	
	public function __construct($block, $nodes) {
		$this->block = $block;
		$this->leafs = $nodes;
	}
	
	public function getBlock() {
		return $this->block;
	}
	
	public function getLeafs() {
		return $this->leafs;
	}
	
	public function setBlock($block) {
		$this->block = $block;
		return $this;
	}
	
	public function setLeafs($nodes) {
		$this->leafs = $nodes;
		return $this;
	}
	
	public function stringify($offset = 0) {
		$leafs = collect($this->leafs);
		
		return str_repeat(' ', $offset) . sprintf('branch(%s - %s - %s)%s%s', count($this->leafs), $this->block->name, get_class($this->block), PHP_EOL, $leafs->each(function ($e) use ($offset) { 
			return $e instanceof ParseTree? $e->stringify($offset + 4) : str_repeat(' ', $offset + 4) . $e;
		})->join(PHP_EOL));
	}
	
	public function __toString() {
		return $this->stringify();
	}
	
}
