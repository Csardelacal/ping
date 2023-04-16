<?php namespace spitfire\core\parser;

use spitfire\core\parser\lexemes\EndOfFile;
use spitfire\core\parser\lexemes\WhiteSpace;
use spitfire\core\parser\scanner\ScannerModuleInterface;
use spitfire\exceptions\PrivateException;
use function collect;

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

class Lexer
{
	
	private $modules;
	
	public function __construct() {
		$this->modules = collect(func_get_args());
		
		if (!$this->modules->containsOnly(ScannerModuleInterface::class)) {
			throw new PrivateException('Lexer error. Tried to provide invalid arguments', 1906100944);
		}
	}
	
	public function tokenize($str) {
		$buf = new StringBuffer($str);
		$ret = [];
		
		/*
		 * Main loop. If the lexer has not yet finished looping over the string, then
		 * it's not done yet.
		 */
		while ($buf->hasMore()) {
			
			/*
			 * Iterate over the modules. The way the lexer is constructed, it allows
			 * the programmer to define Reserved Words, Symbols and parsers for literals,
			 * allowing the programmer to fine tune the way the syntax should look.
			 */
			foreach ($this->modules as $module) {
				/*
				 * Check if the module can start tokenizing the string.
				 */
				$r = $module->in($buf);
				
				/*
				 * In case the module was unable to extract anything, we will skip 
				 * to the next module.
				 */
				if (!$r) { continue;  }
				
				$ret[] = $r;
				
				/*
				 * If the module worked, and extracted data out of the string, then
				 * we do not continue looping over the other modules, and instead
				 * jump back to the while loop.
				 */
				continue 2;
			}
			
			throw new PrivateException(sprintf('Unexpected character "%s" at offset %d', $buf->peek(), $buf->seek()), 190617);
		}
		
		$ret[] = new EndOfFile();
		
		return array_values(array_filter($ret, function ($e) { return !$e instanceof WhiteSpace && !empty($e); }));
	}
	
}
