<?php namespace spitfire\core\parser;

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

class StringBuffer
{
	
	private $pointer = 0;
	private $string;
	
	
	public function __construct($string) {
		$this->string = $string;
	}
	
	public function seek($pos = null) {
		$old = $this->pointer;
		if ($pos !== null) { $this->pointer = $pos; }
		return $old;
	}
	
	public function fastforward($amt = 1) {
		$this->pointer+= $amt;
	}
	
	public function peek($amt = 1, $offset = 0) {
		return substr($this->string, $this->pointer + $offset, $amt);
	}
	
	public function read($amt = 1) {
		$ret = substr($this->string, $this->pointer, $amt);
		$this->pointer += $amt;
		return $ret;
	}
	
	public function readUntil($char) {
		$old = $this->pointer;
		$cur = $this->pointer;
		
		while(isset($this->string[$this->pointer]) && $this->string[$this->pointer] != $char) {
			$this->pointer++;
		}
		
		
		$ret = substr($this->string, $old, $cur - $old);
		$this->pointer = $cur;
		return $ret;
	}
	
	public function slack() {
		return new StringBuffer(substr($this->string, $this->pointer));
	}
	
	public function hasMore() {
		return isset($this->string[$this->pointer ]);
	}
	
}
