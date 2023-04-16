<?php namespace spitfire\core\parser\scanner;

use spitfire\core\parser\lexemes\Identifier;
use spitfire\core\parser\lexemes\LexemeInterface;
use spitfire\core\parser\lexemes\Literal;
use spitfire\core\parser\scanner\ScannerModuleInterface;
use spitfire\core\parser\StringBuffer;

/* 
 * The MIT License
 *
 * Copyright 2018 César de la Cal Bretschneider <cesar@magic3w.com>.
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


class IntegerLiteralScanner implements ScannerModuleInterface
{
	
	
	private $body = null;
	

	public function setBody($body): Literal {
		$this->body = $body;
		return $this;
	}

	public function getBody(): string {
		return $this->body;
	}

	public function in(StringBuffer $buffer): ?LexemeInterface {
		
		$next = $buffer->peek();
		$matched = '';
		
		/*
		 * If the first character is indeed a character, we will continue
		 */
		if (!preg_match('/[0-9\.]/i', $next)) { return null; }
		
		while (preg_match('/[0-9\.]/', $buffer->peek())) {
			$matched.= $buffer->read();
		}
		
		return new Literal($matched);
		
	}

}
