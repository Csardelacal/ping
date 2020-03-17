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

class PssmsShortener implements ShortenerInterface
{
	
	private $endpoint;
	
	public function __construct($endpoint) {
		$this->endpoint = $endpoint;
	}
	
	public function read($shortcode): URLMetadata {
		console()->info('Reading meta for shortened URI '. $shortcode)->ln();
		
		if (\Strings::startsWith($shortcode, $this->endpoint)) {
			$shortcode = substr($shortcode, strlen($this->endpoint));
			console()->info('Extracted short code from URL '. $shortcode)->ln();
		}
		
		$request = request($this->endpoint . 'url/meta/' . $shortcode . '.json');
		$response = $request->send();
		
		$payload = $response->expect(200)->json();
		
		if (!$payload->fetched) {
			throw new NotYetAvailableException('Content was not yet fetched by PSSMS');
		}
		
		return new URLMetadata($payload->url, $payload->title, $payload->description, $payload->image);
	}

	public function shorten($url): string {
		$request = request($this->endpoint . 'url/shorten.json');
		$request->get('url', $url);
		$json = $request->send()->expect(200)->json();
		
		return $this->endpoint . $json->payload->short;
	}

}
