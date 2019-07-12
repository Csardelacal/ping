<?php 

use spitfire\Model;
use spitfire\storage\database\Schema;

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

class FeedbackModel extends Model
{
	
	
	/**
	 * 
	 * @param Schema $schema
	 * @return Schema
	 */
	public function definitions(Schema $schema) {
		$schema->author   = new Reference(AuthorModel::class);
		$schema->target   = new Reference(AuthorModel::class);
		$schema->ping     = new Reference(PingModel::class);
		$schema->guid     = new StringField(250);
		$schema->appId    = new StringField(50);
		$schema->reaction = new IntegerField();
		$schema->biased   = new BooleanField();
		$schema->created  = new IntegerField(true);
		$schema->removed  = new IntegerField(true);
	}
	
	public function onbeforesave() {
		
		if (!$this->guid) {
			$this->guid = 'f' . strtolower(substr(str_replace(['+', '/', '='], '', base64_encode(random_bytes(200))), 0, 100));
		}
		
		if (!$this->created) {
			$this->created = time();
		}
	}

}