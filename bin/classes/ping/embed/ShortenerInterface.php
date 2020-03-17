<?php namespace ping\embed;

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

interface ShortenerInterface
{
	
	/**
	 * Shortens a URL with this shortener and returns the shortened URL.
	 * 
	 * @param string $url A valid URL
	 * @return string
	 */
	public function shorten($url) : string;
	
	/**
	 * Reads the metadata for the URL that has been shortened earlier. Ping will
	 * cache this information to assemble the UI for the user.
	 * 
	 * @throws UnavailableException If the URL is not available to be fetched
	 * @throws NotYetAvailableException If the URL has not been retrieved yet
	 * 
	 * @param string $shortcode
	 * @return \ping\embed\URLMetadata
	 */
	public function read($shortcode) : URLMetadata;
	
}
