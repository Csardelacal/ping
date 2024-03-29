<?php namespace tests\spitfire\core\router;

use PHPUnit\Framework\TestCase;
use spitfire\core\router\ParametrizedPath;
use spitfire\core\router\Pattern;

/* 
 * The MIT License
 *
 * Copyright 2017 César de la Cal Bretschneider <cesar@magic3w.com>.
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

class ParametrizedPathTest extends TestCase
{
	
	public function testReplacement() {
		$pp = new ParametrizedPath(new Pattern(':app'), new Pattern(':controller'), new Pattern(':action'), new Pattern(':object'));
		$path = $pp->replace(['app' => 'a', 'controller' => 'b', 'action' => 'c', 'object' => 'd']);
		
		$this->assertEquals('a', $path->getApp());
		$this->assertEquals('b', $path->getController()[0]);
		$this->assertEquals('c', $path->getAction());
	}
	
	public function testReplacementArrays() {
		$pp = new ParametrizedPath(new Pattern(':app'), [new Pattern(':c2'), new Pattern(':c1')], new Pattern(':action'), new Pattern(':object'));
		$path = $pp->replace(['app' => 'a', 'c1' => 'b1', 'c2' => 'b2', 'action' => 'c', 'object' => 'd']);
		
		$this->assertEquals('a',  $path->getApp());
		$this->assertEquals('b2', $path->getController()[0]);
		$this->assertEquals('c',  $path->getAction());
	}
	
	public function testExtract() {
		$pp = new ParametrizedPath(new Pattern(':app'), [new Pattern(':c2'), new Pattern(':c1')], new Pattern(':action'), new Pattern(':object'));
		$vars = $pp->extract(new \spitfire\core\Path('app', ['c1', 'c2'], 'action', ['o1']));
		
		$this->assertEquals('app', $vars->getParameter('app'));
	}
	
	public function testExtract2() {
		$pp = new ParametrizedPath(new Pattern(':app'), [new Pattern(':c2'), new Pattern(':c1')], new Pattern(':action'), new Pattern(':object'));
		$vars = $pp->extract(new \spitfire\core\Path('app', ['c1', 'c2'], 'action', ['o1', 'o2']));
		
		$this->assertEquals('app', $vars->getParameter('app'));
	}
	
}