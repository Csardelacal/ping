<?php

class PeopleController extends AppController
{
	
	public function _onload() {
		parent::_onload();
		
		if (!$this->user && $this->context->request->getPath()->getAction() !== 'isFollowing') { 
			$this->response
				->setBody('Redirecting...')
				->getHeaders()->redirect(new URL('user', 'login'));
		}
	}
	
	public function followingMe() {
		
		
		$this->secondaryNav->add(url('feed'), 'Feed');
		$this->secondaryNav->add(url('people', 'followingMe'), 'Followers')->setActive(true);
		$this->secondaryNav->add(url('people', 'iFollow'), 'Following');
		
		
		$query     = db()->table('follow')->get('prey__id', db()->table('user')->get('authId', $this->user->id)->fetch()->_id);
		$followers = db()->table('user')->get('following', $query)->setResultsPerPage(21);
		
		$paginator = new Pagination($followers);
		
		$this->view->set('pagination', $paginator);
		$this->view->set('followers',  $followers->fetchAll());
	}
	
	public function iFollow() {
		$this->secondaryNav->add(url('feed'), 'Feed');
		$this->secondaryNav->add(url('people', 'followingMe'), 'Followers');
		$this->secondaryNav->add(url('people', 'iFollow'), 'Following')->setActive(true);
		
		$query     = db()->table('follow')->get('follower__id', db()->table('user')->get('authId', $this->user->id)->fetch()->_id);
		$followers = db()->table('user')->get('followers', $query)->setResultsPerPage(21);
		
		$paginator = new Pagination($followers);
		
		$this->view->set('pagination', $paginator);
		$this->view->set('followers',  $followers->fetchAll());
	}
	
	public function follow($user) {
		
		#Check if the user is already being followed
		$u = $this->sso->getUser($user);
		
		$q1 = db()->table('user')->get('authId', $this->user->id)->fetch()? : UserModel::makeFromSSO($this->sso->getUser($this->user->id));
		$q2 = db()->table('user')->get('authId', $u->getId())->fetch()? : UserModel::makeFromSSO($u);
		
		$following = db()->table('follow')->get('follower', $q1)->addRestriction('prey', $q2)->fetch();
		if ($following) { throw new \spitfire\exceptions\PublicException('Already following', 400); }
		
		$follow = db()->table('follow')->newRecord();
		$follow->follower = $q1;
		$follow->prey     = $q2;
		$follow->store();
		
		$notification = db()->table('notification')->newRecord();
		$notification->src     = $q1;
		$notification->target  = $q2;
		$notification->content = "Started following you";
		$notification->type    = NotificationModel::TYPE_FOLLOW;
		$notification->store();
	}
	
	public function unfollow($user) {
		
		#Check if the user is already being followed
		$u = $this->sso->getUser($user);
		
		$q1 = db()->table('user')->get('authId', $this->user->id);
		$q2 = db()->table('user')->get('authId', $u->getId());
		
		$following = db()->table('follow')->get('follower', $q1)->addRestriction('prey', $q2)->fetch();
		if (!$following) { throw new \spitfire\exceptions\PublicException('Not yet following', 400); }
		
		$following->delete();
	}
	
	public function whoToFollow() {
		
		try {
		$u = $this->user;
		$following   = db()->table('follow')->get('follower__id', $u->id);
		$exclude     = db()->table('follow')->get('follower__id', $u->id);
		
		$suggestions = db()->table('follow')->get('follower', db()->table('user')->get('followers', $following));
		$users       = db()->table('user')->get('followers', $suggestions)->addRestriction('followers', $exclude, '!=');
		
		$users->setResultsPerPage(100);
		$users->fetchAll()->each(function ($e) { echo $this->sso->getUser($e->_id)->getUsername(), ', ';});
		
		} catch (\Exception$e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
		
		var_dump(spitfire()->getMessages());
		die();
	}
	
	public function isFollowing($uid) {
		if (!$this->user) { 
			$this->view->set('error', true);
			$this->view->set('errorMsg', 'Not authenticated');
			$this->view->set('following', false);
		} else {
			$q1 = db()->table('user')->get('authId', $this->user->id);
			$q2 = db()->table('user')->get('authId', $uid);

			$following = db()->table('follow')->get('follower', $q1)->addRestriction('prey', $q2)->fetch();

			$this->view->set('following', $following);
		}
	}
}
