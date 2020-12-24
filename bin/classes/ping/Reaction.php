<?php namespace ping;

use spitfire\core\collection\DefinedCollection;
use spitfire\core\Environment;
use function collect;
use function spitfire;

/* 
 * The MIT License
 *
 * Copyright 2020 César de la Cal Bretschneider <cesar@magic3w.com>.
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

class Reaction
{
	
	private static $cache;
	
	private $emoji;
	private $identifier;
	private $caption;
	private $sentiment;
	
	public function __construct($emoji, $identifier, $caption, $sentiment) {
		$this->emoji = $emoji;
		$this->identifier = $identifier;
		$this->caption = $caption;
		$this->sentiment = $sentiment;
	}
	
	public static function getCache() {
		return self::$cache;
	}

	public function getEmoji() {
		return $this->emoji;
	}

	public function getIdentifier() {
		return $this->identifier;
	}

	public function getCaption() {
		return $this->caption;
	}

	public function getSentiment() {
		return $this->sentiment;
	}
		
	/**
	 * 
	 * @return DefinedCollection <Reaction>
	 */
	public static function all() {
		
		if (!self::$cache) { 
			self::$cache = collect(explode(';', Environment::get('reactions')?: file_get_contents(spitfire()->getCWD() . '/bin/settings/reactions.dat')))
				->each(function ($e) {
					$data = explode(':', trim($e));
					return new Reaction($data[0], $data[1], $data[2], $data[3]);
				});
		}
		
		return self::$cache;
	}
}
