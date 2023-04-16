<?php namespace tests\spitfire\core\parser;

use PHPUnit\Framework\TestCase;
use spitfire\core\parser\Identifier;
use spitfire\core\parser\lexemes\ReservedWord;
use spitfire\core\parser\lexemes\Symbol;
use spitfire\core\parser\Lexer;
use spitfire\core\parser\Literal;
use spitfire\core\parser\parser\Block;
use spitfire\core\parser\parser\IdentifierTerminal;
use spitfire\core\parser\parser\LiteralTerminal;
use spitfire\core\parser\parser\Parser;
use spitfire\core\parser\parser\SymbolTerminal;
use spitfire\core\parser\scanner\IdentifierScanner;
use spitfire\core\parser\scanner\IntegerLiteralScanner;
use spitfire\core\parser\scanner\LiteralScanner;
use spitfire\core\parser\scanner\WhiteSpaceScanner;
use spitfire\core\parser\StringLiteral;

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

class ParserTest extends TestCase
{
	
	
	
	public function testBasicParser() {
		$lit = new LiteralScanner('"', '"');
		$pls = new Symbol('+');
		$mns = new Symbol('-');
		$mul = new Symbol('*');
		$div = new Symbol('/');
		$not = new Symbol('!');
		$pop = new Symbol('(');
		$pcl = new Symbol(')');
		$whs = new WhiteSpaceScanner();
		$ids = new IdentifierScanner();
		$int = new IntegerLiteralScanner();
		
		$expression     = new Block();
		$addition       = new Block();
		$multiplication = new Block();
		$unary          = new Block();
		$primary        = new Block();
		
		$expression->matches($addition);
		$addition->matches($multiplication, Block::optional(Block::multiple(Block::either([$pls], [$mns]), $multiplication)));
		
		$multiplication->matches(
			$unary, 
			Block::optional(Block::multiple(Block::either([new SymbolTerminal($mul)], [new SymbolTerminal($div)]), $unary))
		);
		
		$unary->matches(
			Block::either([new SymbolTerminal($not), $primary], [$primary])->name('unary.either')
		);
		
		$primary->matches(Block::either(
			[new IdentifierTerminal()], 
			[new LiteralTerminal()], 
			[new SymbolTerminal($pop), $expression, new SymbolTerminal($pcl)]
			)->name('primary.either')
		);
		
		$expression->name = 'expression';
		$addition->name = 'addition';
		$multiplication->name = 'multiplication';
		$unary->name = 'unary';
		$primary->name = 'primary';
		
		$lex = new Lexer($lit, $pls, $mns, $mul, $div, $whs, $ids, $int, $not, $pop, $pcl);
		$parser = new Parser($expression);
		
		$res = $parser->parse($lex->tokenize('3 * myval + 5 / (6 - 2)'));
		
		$this->assertEquals(true, is_array($res));
		$this->assertArrayHasKey(1, $res);
		$this->assertArrayNotHasKey(2, $res);
	}
	
	public function testBasicParser2() {
		$and = new ReservedWord('AND');
		$or  = new ReservedWord('OR');
		$lit = new LiteralScanner('"', '"');
		$pop = new Symbol('(');
		$pcl = new Symbol(')');
		$bop = new Symbol('[');
		$bcl = new Symbol(']');
		$com = new Symbol(',');
		$ran = new Symbol('>');
		$dot = new Symbol('.');
		$whs = new WhiteSpaceScanner();
		$ids = new IdentifierScanner();
		$int = new IntegerLiteralScanner();
		
		$statement = new Block();
		$expression = new Block();
		$binaryand = new Block();
		$binaryor  = new Block();
		$rule      = new Block();
		$fnname    = new Block();
		$arguments = new Block();
		$options   = new Block();
		$additional= new Block();
		
		
		
		$binaryand->name = 'AND';
		$binaryor->name = 'OR';
		$statement->name = 'statement';
		$expression->name = 'expression';
		$rule->name = 'rule';
		$fnname->name = 'function identifier';
		$arguments->name = 'arguments';
		$options->name = 'options';
		
		
		
		$statement->matches($binaryor);
		$binaryor->matches($binaryand, Block::optional(Block::multiple($or, $binaryand)));
		$binaryand->matches($expression, Block::optional(Block::multiple($and, $expression)));
		
		$expression->matches(Block::either([$rule], [$pop, $statement, $pcl]));
		$rule->matches($fnname, $pop, $arguments, $pcl);
		$fnname->matches(new IdentifierTerminal(), $dot, new IdentifierTerminal());
		$arguments->matches(new IdentifierTerminal(), Block::optional($options), Block::optional($arguments));
		
		$options->matches($bop, new LiteralTerminal(), Block::optional($additional), $bcl);
		$additional->matches($com, new LiteralTerminal(), Block::optional($additional));
		
		
		
		
		$lex = new Lexer($and, $or, $lit, $pop, $pcl, $com, $dot, $whs, $bop, $bcl, $ids, $int, $ran);
		
		
		$string = '(GET.input(string length[10, 24] not["detail"]) OR POST.other(positive number)) AND POST.test(file) OR t.s(something) AND t.i(s else["else"])';
		$parser = new Parser($statement);
		
		$printer = new \spitfire\core\parser\printer\Printer();
		$printer->print($statement);
		
		$res = $parser->parse($lex->tokenize($string));
		
		$this->assertEquals(true, is_array($res));
		$this->assertArrayHasKey(1, $res);
		$this->assertArrayNotHasKey(2, $res);
		
	}/**/
	
}
