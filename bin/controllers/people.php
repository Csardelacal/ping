<?php

class PeopleController extends AppController
{
	
	public function _onload() {
		parent::_onload();
		
		if (!$this->user && $this->context->request->getPath()->getAction() !== 'isFollowing') { 
			$this->response
				->setBody('Redirecting...')
				->getHeaders()->redirect(url('user', 'login'));
		}
	}
	
	public function followingMe() {
		
		
		$query     = db()->table('follow')->get('prey__id', AuthorModel::get(db()->table('user')->get('authId', $this->user->id)->fetch())->_id);
		$followers = db()->table('author')->get('following', $query);
		$followers->setOrder('_id', 'DESC');
		
		$paginator = new \spitfire\storage\database\pagination\Paginator($followers);
		
		$this->view->set('pagination', $paginator);
		$this->view->set('followers',  $paginator->records());
	}
	
	public function iFollow() {
		
		$me        = AuthorModel::get(db()->table('user')->get('authId', $this->user->id)->first());
		$query     = db()->table('follow')->get('follower__id', $me->_id);
		$followers = db()->table('author')->get('followers', $query);
		
		$followers->setOrder('_id', 'DESC');
		
		$paginator = new \spitfire\storage\database\pagination\Paginator($followers);
		
		$this->view->set('pagination', $paginator);
		$this->view->set('followers',  $paginator->records());
	}
	
	public function follow($user) {
		
		#Check if the user is already being followed
		$u = $this->sso->getUser($user);
		
		$q1 = AuthorModel::get(db()->table('user')->get('authId', $this->user->id)->fetch()? : UserModel::makeFromSSO($this->sso->getUser($this->user->id)));
		$q2 = AuthorModel::get(db()->table('user')->get('authId', $u->getId())->fetch()? : UserModel::makeFromSSO($u));
		
		$following = db()->table('follow')->get('follower', $q1)->addRestriction('prey', $q2)->fetch();
		if ($following) { throw new \spitfire\exceptions\PublicException('Already following', 400); }
		
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
		if (!$following) { throw new \spitfire\exceptions\PublicException('Not yet following', 400); }
		
		$following->delete();
	}
	
	public function whoToFollow() {
		
		$u = $this->user;
		$me = AuthorModel::get(db()->table('user')->get('_id', $u->id)->first())->_id;
		
		$following   = db()->table('follow')->get('follower__id', $me);
		$exclude     = db()->table('follow')->get('follower__id', $me);
		
		$suggestions = db()->table('follow')->getAll()->where('follower', db()->table('author')->get('followers', $following));
		$users       = db()->table('author')->get('followers', $suggestions)->where('followers', '!=', $exclude)->where('_id', '!=', $me);
		
		
		$this->view->set('authors', $users->range(0, 5));
		
	}
	
	public function isFollowing($uid) {
		if (!$this->user) { 
			$this->view->set('error', true);
			$this->view->set('errorMsg', 'Not authenticated');
			$this->view->set('following', false);
		} else {
			$q1 = AuthorModel::get(db()->table('user')->get('authId', $this->user->id)->first());
			$q2 = AuthorModel::get(db()->table('user')->get('authId', $uid)->first());

			$following = db()->table('follow')->get('follower', $q1)->addRestriction('prey', $q2)->fetch();

			$this->view->set('following', $following);
		}
	}
}
