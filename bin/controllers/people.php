<?php

class PeopleController extends AppController
{
	
	public function _onload() {
		parent::_onload();
		
		if (!$this->user) { 
			$this->response
				->setBody('Redirecting...')
				->getHeaders()->redirect(new URL('user', 'login'));
		}
	}
	
	public function followingMe() {
		
		
		$this->secondaryNav->add(new URL('feed'), 'Feed');
		$this->secondaryNav->add(new URL('people', 'followingMe'), 'Followers')->setActive(true);
		$this->secondaryNav->add(new URL('people', 'iFollow'), 'Following');
		
		
		$query     = db()->table('follow')->get('prey__id', db()->table('user')->get('authId', $this->user->id)->fetch()->_id);
		$followers = db()->table('user')->get('following', $query);
		
		$paginator = new Pagination($followers);
		
		$this->view->set('pagination', $paginator);
		$this->view->set('followers',  $followers->fetchAll());
	}
	
	public function iFollow() {
		$this->secondaryNav->add(new URL('feed'), 'Feed');
		$this->secondaryNav->add(new URL('people', 'followingMe'), 'Followers');
		$this->secondaryNav->add(new URL('people', 'iFollow'), 'Following')->setActive(true);
		
		$query     = db()->table('follow')->get('follower__id', db()->table('user')->get('authId', $this->user->id)->fetch()->_id);
		$followers = db()->table('user')->get('followers', $query);
		
		$paginator = new Pagination($followers);
		
		$this->view->set('pagination', $paginator);
		$this->view->set('followers',  $followers->fetchAll());
	}
	
	public function follow($user) {
		
		#Check if the user is already being followed
		$u = $this->sso->getUser($user);
		
		$q1 = db()->table('user')->get('authId', $this->user->id)->fetch();
		$q2 = db()->table('user')->get('authId', $u->getId())->fetch();
		
		$following = db()->table('follow')->get('follower', $q1)->addRestriction('prey', $q2)->fetch();
		if ($following) { throw new \spitfire\exceptions\PublicException('Already following', 400); }
		
		$follow = db()->table('follow')->newRecord();
		$follow->follower = $q1;
		$follow->prey     = $q2;
		$follow->store();
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
}
