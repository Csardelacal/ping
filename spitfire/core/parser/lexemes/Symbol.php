<?php namespace spitfire\core\parser\lexemes;

use spitfire\core\parser\scanner\ScannerModuleInterface;
use spitfire\core\parser\StringBuffer;

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
 * The main difference between symbols and reserved words, is that the tokenizer
 * will search for symbols as breaking characters, while reserved words will be
 * only matched if they are properly delimited.
 */
class Symbol implements LexemeInterface, ScannerModuleInterface
{
	
	private $literal;
	
	
	public function __construct($literal) {
		$this->literal = $literal;
	}
	
	public function getBody() : string {
		return $this->literal;
	}

	public function in(StringBuffer $buffer): ?LexemeInterface {
		
		if ($buffer->peek(1) === $this->literal) {
			$buffer->read();
			return $this;
		}
		else {
			return null;
		}
	}
	
	public function __toString() {
		return sprintf('sym(%s)', strval($this->literal));
	}

}
