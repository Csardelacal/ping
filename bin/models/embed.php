<?php

use spitfire\Model;
use spitfire\storage\database\Schema;

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

class EmbedModel extends Model
{
	
	/**
	 * 
	 * @param Schema $schema
	 * @return Schema
	 */
	public function definitions(Schema $schema) {
		$schema->ping = new Reference('ping');
		
		$schema->url = new StringField(256);
		
		/*
		 * Allows ping to store a shortened version of a URL. To do so, it will require
		 * a URL shortener that supports feeding it with metadata on the URL shortened.
		 * 
		 * Ping will then store the shortened URL and use the shortener to proxy it
		 * the data it needs.
		 */
		$schema->short = new StringField(64); 
		
		/*
		 * Cache the title, description, etc for the record. This will allow Ping 
		 * to provide previews for the content. Ideally, the link shortener will 
		 * provide a version of the image that is chached / proxied so the origin 
		 * can't track / exploit the previews.
		 */
		$schema->title = new StringField(64);
		$schema->description = new StringField(255);
		$schema->image = new StringField(256);
		
		$schema->created = new IntegerField(true);
		
		$schema->index($schema->title);
		$schema->index($schema->url);
		$schema->index($schema->short);
	}

}
