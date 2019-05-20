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

/**
 * The poll controller allows users to interact with polls. Polls are used to 
 * retrieve feedback from users in a way that allows gauging the interest of 
 * a following.
 * 
 * Due to the nature of polls, the options available are immutable after the
 * poll was created. Otherwise they could be used to fake the behavior of users.
 * 
 * Polls can be closed at any given point in time. Votes can be cast if the poll
 * is open and the ping has not yet been deleted.
 */
class PollController extends AppController
{
	
	/**
	 * Allows a user to cast a vote on an open poll. The user can modify their
	 * vote at any given time, unless the voting has been closed.
	 * 
	 * @param \poll\OptionModel $option
	 * @throws PublicException
	 */
	public function vote(\poll\OptionModel$option) {
		
		if (!$this->user) {
			throw new PublicException('Not allowed', 403);
		}
		
		if ($option->ping->pollClosed || $option->ping->deleted) {
			throw new PublicException('Poll ended', 401);
		}
		
		$user = db()->table('user')->get('authId', $this->user->id)->first()? : UserModel::makeFromSSO($this->sso->getUser($this->user->id));
		$author = AuthorModel::get($user);
		
		$response = db()->table('poll\reply')->get('ping', $option->ping)->where('author', $author)->first()?: db()->table('poll\reply')->newRecord();
		
		$response->ping   = $option->ping;
		$response->author = $author;
		$response->option = $option;
		
		$this->core->poll->response->do(function ($response) {
			$response->store();
		}, $response);
		
		$this->view->set('response', $response);
	}
	
	/**
	 * In the event that the user does no longer wish to receive answers to the 
	 * poll they can submit a request to close it.
	 * 
	 * @param PingModel $ping
	 * @throws PublicException
	 */
	public function close(PingModel$ping) {
		if (!$this->user) {
			throw new PublicException('Not allowed', 403);
		}
		
		$user = db()->table('user')->get('authId', $this->user->id)->first()? : UserModel::makeFromSSO($this->sso->getUser($this->user->id));
		$author = AuthorModel::get($user);
		
		if ($ping->src->_id != $author->_id) {
			throw new PublicException('Not allowed', 403);
		}
		
		$ping->pollEnd = time();
		
		$this->core->feed->update->do(function ($ping) {
			$ping->store();
		}, $ping);
		
		$this->view->set('ping', $ping);
	}
	
}