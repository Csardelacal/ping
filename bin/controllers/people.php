<?php

use spitfire\exceptions\PublicException;
use spitfire\storage\database\AggregateFunction;
use spitfire\storage\database\pagination\Paginator;

class PeopleController extends AppController
{
	
	public function _onload() {
		parent::_onload();
		
		if (!$this->user && $this->context->request->getPath()->getAction() !== 'isFollowing') { 
			$this->response
				->setBody('Redirecting...')
				->getHeaders()->redirect(url('account', 'login'));
		}
	}
	
	public function following($username = null) {
		
		$author    = AuthorModel::find($username?: $this->user->id);
		$query     = db()->table('follow')->get('prey__id', $author->_id);
		$followers = db()->table('author')->get('following', $query);
		$followers->setOrder('_id', 'DESC');
		
		$paginator = new Paginator($followers);
		
		$this->view->set('author', $author);
		$this->view->set('user', $this->sso->getUser($author->user->_id));
		$this->view->set('pagination', $paginator);
		$this->view->set('followers',  $paginator->records());
	}
	
	public function follows($username = null) {
		
		$author        = AuthorModel::find($username?: $this->user->id);
		$query     = db()->table('follow')->get('follower__id', $author->_id);
		$followers = db()->table('author')->get('followers', $query);
		
		$followers->setOrder('_id', 'DESC');
		
		$paginator = new Paginator($followers);
		
		$this->view->set('author', $author);
		$this->view->set('user', $this->sso->getUser($author->user->_id));
		$this->view->set('pagination', $paginator);
		$this->view->set('followers',  $paginator->records());
	}
	
	public function follow($user) {
		
		#Check if the user is already being followed
		$u = $this->sso->getUser($user);
		
		$q1 = AuthorModel::get(db()->table('user')->get('authId', $this->user->id)->fetch()? : UserModel::makeFromSSO($this->sso->getUser($this->user->id)));
		$q2 = AuthorModel::get(db()->table('user')->get('authId', $u->getId())->fetch()? : UserModel::makeFromSSO($u));
		
		$following = db()->table('follow')->get('follower', $q1)->addRestriction('prey', $q2)->fetch();
		if ($following) { throw new PublicException('Already following', 400); }
		
		$follow = db()->table('follow')->newRecord();
		$follow->follower = $q1;
		$follow->prey     = $q2;
		
		$this->core->people->follow->do(function ($follow) {
			$follow->store();
		}, $follow);
		
	}
	
	public function unfollow($user) {
		
		#Check if the user is already being followed
		$u = $this->sso->getUser($user);
		
		$q1 = AuthorModel::get(db()->table('user')->get('authId', $this->user->id)->first());
		$q2 = AuthorModel::get(db()->table('user')->get('authId', $u->getId())->first());
		
		$following = db()->table('follow')->get('follower', $q1)->addRestriction('prey', $q2)->fetch();
		if (!$following) { throw new PublicException('Not yet following', 400); }
		
		$following->delete();
	}
	
	public function whoToFollow() {
		
		$u = $this->user;
		$me = AuthorModel::get(db()->table('user')->get('_id', $u->id)->first())->_id;
		
		$following   = db()->table('follow')->get('follower__id', $me);
		$exclude     = db()->table('follow')->get('follower__id', $me);
		
		$suggestions = db()->table('follow')->getAll()->where('follower', db()->table('author')->get('followers', $following));
		$users       = db()->table('author')->get('followers', $suggestions)->where('followers', '!=', $exclude)->where('_id', '!=', $me);
		
		$count = new AggregateFunction($users->getQueryTable()->getField('_id'), AggregateFunction::AGGREGATE_COUNT);
		$users->aggregateBy($users->getQueryTable()->getField('_id'));
		$users->setOrder($count, 'DESC');
		
		$rss = $users->execute([$users->getQueryTable()->getField('_id'), $count], 0, 2);
		$_ret = collect();
		
		while($row = $rss->fetchArray()) {
			$_ret->push(db()->table('author')->get('_id', $row['_id'])->first());
		}
		
		$this->view->set('authors', $_ret);
		
	}
	
	public function isFollowing($uid) {
		if (!$this->user) { 
			$this->view->set('error', true);
			$this->view->set('errorMsg', 'Not authenticated');
			$this->view->set('following', false);
		} else {
			$q1 = AuthorModel::get(db()->table('user')->get('authId', $this->user->id)->first());
			$q2 = AuthorModel::get(db()->table('user')->get('authId', $uid)->first());

			$following = db()->table('follow')->get('follower__id', $q1->_id)->addRestriction('prey__id', $q2->_id)->fetch();

			$this->view->set('following', $following);
		}
	}
}
