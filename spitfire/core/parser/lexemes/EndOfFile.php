<?php namespace spitfire\core\parser\lexemes;


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
 * The end of file is a really simple lexeme, it basically indicates that the 
 * scanner had no more content to read, appending this lexeme so the parser can
 * actually expect the end of file after it parsed the output from the lexer.
 * 
 * This follows a recommendation from the booking craftinginterpreters.com that
 * pointed out that it would then be far easier to build the parser tree.
 * 
 * @author César de la Cal Bretschneider <cesar@magic3w.com>
 */
class EndOfFile implements LexemeInterface
{
	
	/**
	 * Obviously, the end of a file is an empty string - it has no content, and it's
	 * presence indicates that the scanner is done.
	 * 
	 * @return string
	 */
	public function getBody(): string {
		return '';
	}
	
	/**
	 * Sometimes it's very interesting to print the output from the lexer (specially
	 * when debugging)
	 * 
	 * @return string
	 */
	public function __toString() {
		return '__EOF__';
	}

}
