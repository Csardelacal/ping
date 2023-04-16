<?php namespace tests\spitfire\core\parser;

use PHPUnit\Framework\TestCase;
use spitfire\core\parser\lexemes\Literal;
use spitfire\core\parser\lexemes\ReservedWord;
use spitfire\core\parser\lexemes\Symbol;
use spitfire\core\parser\Lexer;
use spitfire\core\parser\scanner\IdentifierScanner;
use spitfire\core\parser\scanner\IntegerLiteralScanner;
use spitfire\core\parser\scanner\LiteralScanner;
use spitfire\core\parser\scanner\WhiteSpaceScanner;

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

class LexerTest extends TestCase
{
	
	public function testBasicLexer() {
		$and = new ReservedWord('AND');
		$or  = new ReservedWord('OR');
		$lit = new LiteralScanner('"', '"');
		$pop = new Symbol('(');
		$pcl = new Symbol(')');
		$com = new Symbol(',');
		$whs = new WhiteSpaceScanner();
		$ids = new IdentifierScanner();
		$nms = new IntegerLiteralScanner();
		
		$lex = new Lexer($and, $or, $lit, $pop, $pcl, $com, $whs, $ids, $nms);
		$res = $lex->tokenize('hello("crazy world", 1)');
		
		$this->assertEquals(7, count($res));
		$this->assertInstanceOf(Literal::class, $res[2]);
		$this->assertEquals('crazy world', $res[2]->getBody());
		
	}
	
	public function testBasicLexer2() {
		$lit = new LiteralScanner('"', '"');
		$pls = new Symbol('+');
		$mns = new Symbol('-');
		$eqs = new Symbol('=');
		$mul = new Symbol('*');
		$div = new Symbol('/');
		$whs = new WhiteSpaceScanner();
		$nms = new IntegerLiteralScanner();
		
		$lex = new Lexer($lit, $pls, $mns, $eqs, $mul, $div, $whs, $nms);
		$res = $lex->tokenize('3+1*5/6');
		
		$this->assertEquals(8, count($res));
		$this->assertEquals('1', $res[2]->getBody());
		
	}
	
	public function testBasicLexer3() {
		$lit = new LiteralScanner('"', '"');
		$pls = new Symbol('+');
		$mns = new Symbol('-');
		$eqs = new Symbol('=');
		$mul = new Symbol('*');
		$div = new Symbol('/');
		$whs = new WhiteSpaceScanner();
		$ids = new IdentifierScanner();
		$nms = new IntegerLiteralScanner();
		
		$lex = new Lexer($lit, $pls, $mns, $eqs, $mul, $div, $whs, $ids, $nms);
		$res = $lex->tokenize('3 + myval * 5 / 6');
		
		$this->assertEquals(8, count($res));
		$this->assertEquals('myval', $res[2]->getBody());
		
	}
	
	public function testBasicLexer4() {
		$and = new ReservedWord('AND');
		$or  = new ReservedWord('OR');
		$lit = new LiteralScanner('"', '"');
		$pop = new Symbol('(');
		$pcl = new Symbol(')');
		$bop = new Symbol('[');
		$bcl = new Symbol(']');
		$com = new Symbol(',');
		$dot = new Symbol('.');
		$whs = new WhiteSpaceScanner();
		$ids = new IdentifierScanner();
		$nms = new IntegerLiteralScanner();
		
		$lex = new Lexer($and, $or, $lit, $pop, $pcl, $com, $dot, $whs, $bop, $bcl, $ids, $nms);
		
		$string = 'GET.input(string length[10,24] not["detail"]) OR POST.other(positive number)';
		$res = $lex->tokenize($string);
		
		$this->assertEquals(25, count($res));
		$this->assertEquals('input', $res[2]->getBody());
		
	}
	
}
