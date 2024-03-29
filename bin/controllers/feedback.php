<?php

use auth\AppAuthentication;
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
	
	public function push(PingModel$ping)
	{
		
		if (!$this->user) {
			throw new PublicException('Not allowed', 403);
		}
		
		$dbuser = db()->table('user')->get('authId', $this->user->id)->first();
		
		if (!$dbuser) {
			$dbuser = UserModel::makeFromSSO($this->sso->getUser($this->user->id));
		}
		
		$author = AuthorModel::get($dbuser);
		$exists = db()->table('feedback')
			->get('author', $author)
			->where('ping', $ping)
			->where('removed', null)
			->first();
		
		if ($exists) {
			$exists->removed = time();
			$exists->store();
		}
		
		$record = db()->table('feedback')->newRecord();
		$record->ping     = $ping;
		$record->author   = $author;
		$record->target   = $ping->src;
		$record->appId    = $this->authapp? strval($this->authapp) : null;
		
		switch ($_GET['reaction']?? null) {
			case 'dislike':
				$record->reaction = -1;
				break;
			
			case 'like':
			default:
				$record->reaction = 1;
				break;
		}
		
		$this->core->feedback->push->do(function ($feedback) {
			$feedback->store();
		}, $record);
		
		$this->view->set('feedback', $record);
	}
	
	public function revoke(PingModel$ping)
	{
		
		if (!$this->user) {
			throw new PublicException('Not allowed', 403);
		}
		
		$dbuser = db()->table('user')->get('authId', $this->user->id)->first();
		
		if (!$dbuser) {
			$dbuser = UserModel::makeFromSSO($this->sso->getUser($this->user->id));
		}
		
		$author = AuthorModel::get($dbuser);
		
		if (!db()->table('feedback')->get('author', $author)->where('ping', $ping)->where('removed', null)->first()) {
			throw new PublicException('Not allowed', 403);
		}
		
		db()->table('feedback')->get('ping', $ping)->where('author', $author)->first()->delete();
	}
	
	public function retrieve(PingModel$ping)
	{
		
		$mc = new \spitfire\cache\MemcachedAdapter;
		$mc->setTimeout(1800);
		
		$reactionCount = function (PingModel $ping, int $id) {
			return db()
				->table('feedback')
				->get('ping', $ping)
				->where('reaction', $id)
				->where('removed', null)
				->count();
		};
		
		$overall = $mc->get('ping_like_details_' . $ping->_id, function () use ($ping, $reactionCount) {
			return [
				'like'    => $reactionCount($ping, 1),
				'dislike' => $reactionCount($ping, -1),
				'sample'  => db()->table('feedback')->get('ping', $ping)
					->where('reaction', 1)
					->where('removed', null)
					->range(0, 10)
					->each(function ($e) {
						return [
							'author' => $e->author->_id,
							'user' => $e->author->user? $e->author->user->_id : null,
							'avatar' => $e->author->getAvatar(),
							'username' => $e->author->getUsername(),
						];
					})->toArray()
			];
		});
		
		$this->view->set('overall', $overall);
		
		if ($this->user) {
			$dbuser = db()->table('user')->get('authId', $this->user->id)->first();
			
			if (!$dbuser) {
				$dbuser = UserModel::makeFromSSO($this->sso->getUser($this->user->id));
			}
			
			$author = AuthorModel::get($dbuser);
			$this->view->set(
				'mine',
				db()->table('feedback')->get('ping', $ping)
					->where('author', $author)->where('removed', null)->first()->reaction
			);
		}
	}
	
	public function liked($username = null)
	{
		if (!$username) {
			$author = AuthorModel::get(db()->table('user')->get('_id', $this->user->id)->first(true));
		}
		else {
			$author = AuthorModel::find('@' . $username);
		}
		
		$query = db()
			->table('feedback')
			->get('author', $author)
			->where('reaction', 1)
			->where('removed', null)
			->setOrder('created', 'DESC');
		
		if (isset($_GET['until'])) {
			$query->where('_id', '<', $_GET['until']);
		}
		
		$feedback = $query->range(0, 20);
		
		$this->view->set('notifications', $feedback->extract('ping'));
		$this->view->set('until', $feedback->last()->_id);
		$this->view->set('author', $author);
		$this->view->set('user', $author);
		$this->view->set('me', AuthorModel::get(db()->table('user')->get('_id', $this->user->id)->first(true)));
	}
}
