<?php namespace spitfire\core\parser\printer;

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

class Printer
{
	
	public function print(\spitfire\core\parser\parser\Block$block) {
		$context = new Context();
		$this->descend($block, $context);
	}
	
	private function descend($block, Context$context, $level = 0) {
		
		$context = clone $context;
		$context->add($block);
		
		if (is_string($block)) {
			echo sprintf('%s *string*%s%s', str_repeat('  ', $level), $block, PHP_EOL);
			return;
		}
		
		if ($block instanceof \spitfire\core\parser\lexemes\LexemeInterface) {
			echo sprintf('%s %s%s', str_repeat('  ', $level), $block, PHP_EOL);
			return;
		}
		
		echo str_repeat('  ', $level) . $block . PHP_EOL;
		
		foreach ($block->children() as $e) {
			if ($context->contains($e)) {
				echo sprintf('%s **recursion %s**%s', str_repeat('  ', $level + 1), $block, PHP_EOL);
			}
			else {
				$this->descend($e, $context, $level + 1);
			}
		}
	}
	
}