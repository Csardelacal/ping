<?php

use spitfire\exceptions\PublicException;

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

class FeedBackController extends AppController
{
	
	public function push(PingModel$ping) {
		
		if (!$this->user) {
			throw new PublicException('Not allowed', 403);
		}
		
		$author = AuthorModel::get(db()->table('user')->get('authId', $this->user->id)->first()? : UserModel::makeFromSSO($this->sso->getUser($this->user->id)));
		$exists = db()->table('feedback')->get('author', $author)->where('ping', $ping)->where('removed', null)->first();
		
		if ($exists) {
			$exists->removed = time();
			$exists->store();
		}
		
		$record = db()->table('feedback')->newRecord();
		$record->ping     = $ping;
		$record->author   = $author;
		$record->target   = $ping->src;
		$record->appId    = $this->authapp? ($this->authapp instanceof \auth\AppAuthentication? $this->authapp->getSrc()->getId() : $this->authapp) : null;
		
		switch($_GET['reaction']?? null) {
			case 'dislike': 
				$record->reaction = -1;
				break;
			
			case 'like': 
			default: 
				$record->reaction = 1;
				break;
		}
		
		$record->store();
		
		$this->view->set('feedback', $record);
	}
	
	public function revoke(PingModel$ping) {
		
		if (!$this->user) {
			throw new PublicException('Not allowed', 403);
		}
		
		$author = AuthorModel::get(db()->table('user')->get('authId', $this->user->id)->first()? : UserModel::makeFromSSO($this->sso->getUser($this->user->id)));
		
		if (!db()->table('feedback')->get('author', $author)->where('ping', $ping)->where('removed', null)->first()) {
			throw new PublicException('Not allowed', 403);
		}
		
		db()->table('feedback')->get('ping', $ping)->where('author', $author)->first()->delete();
	}
	
	public function retrieve(PingModel$ping) {
		
		$this->view->set('overall', [
			'like'    => db()->table('feedback')->get('ping', $ping)->where('reaction',  1)->where('removed', null)->where('biased', 0)->count(),
			'dislike' => db()->table('feedback')->get('ping', $ping)->where('reaction', -1)->where('removed', null)->where('biased', 0)->count()
		]);
		
		if ($this->user) {
			$author = AuthorModel::get(db()->table('user')->get('authId', $this->user->id)->first()? : UserModel::makeFromSSO($this->sso->getUser($this->user->id)));
			$this->view->set('mine', db()->table('feedback')->get('ping', $ping)->where('author', $author)->where('removed', null)->first()->reaction );
		}
	}
}
