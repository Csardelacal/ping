<?php namespace spitfire\core\parser\scanner;

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


class LiteralScanner implements ScannerModuleInterface
{
	
	private $open;
	
	private $close;
	
	
	public function __construct($open, $close) {
		$this->open = $open;
		$this->close = $close;
	}

	public function in(StringBuffer $buffer): ?LexemeInterface {
		/**
		 * Check if the opening is present
		 */
		if ($buffer->peek(strlen($this->open)) !== $this->open) {
			return null;
		}
		
		$pos  = $buffer->seek();
		$_ret = '';
		$esc  = false;
		
		/*
		 * Skip the first character. We know it's the opening character
		 */
		$buffer->read();
		
		while(false !== $next = $buffer->read()) {
			
			/*
			 * Obviously, if the next character is a closing character, and the character
			 * has not been escaped, we return the result.
			 */
			if ($next == $this->close && !$esc) {
				return new Literal($_ret);
			}
			
			/*
			 * If the next character is an escape character, we remember it for the 
			 * next run - obviously, if it's an escaped escape character, we don't
			 */
			if ($next == '\\' && !$esc) {
				$esc = true;
			}
			elseif ($next == '\\') {
				$esc = false;
				$_ret.= $next;
			}
			else {
				$_ret.= ($esc? '\\' : '') . $next;
				$esc = false;
			}
		}
		
		/*
		 * Place the buffer back to it's original position
		 */
		$buffer->seek($pos);
		
		/*
		 * If the buffer is empty and we did not receive a return, then the string
		 * is invalid.
		 */
		return null;
		
	}

}
