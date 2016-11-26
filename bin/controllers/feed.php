<?php

class FeedController extends AppController
{
	
	public function index() {
		
		/*
		 * The feed only works with a logged in user. This is due to the fact that
		 * there is no point in having a public feed that displays contents 
		 */
		if (!$this->user) { 
			return $this->response->getHeaders()->redirect(new URL('user', 'login')); 
		}
		
		$this->secondaryNav->add(new URL('feed'), 'Feed')->setActive(true);
		$this->secondaryNav->add(new URL('people', 'followingMe'), 'Followers');
		$this->secondaryNav->add(new URL('people', 'iFollow'), 'Following');
		
		/*
		 * Read the notifications for the user and send them to the view
		 */
		
		$dbuser  = db()->table('user')->get('authId', $this->user->id)->fetch()? : UserModel::makeFromSSO($this->sso->getUser($this->user->id));
		$follows = db()->table('follow')->get('follower__id', $dbuser->_id);
		$users   = db()->table('user')->get('followers', $follows);
		
		$query = db()->table('notification')->getAll()
				->group()
				  ->addRestriction('target__id', $dbuser->_id)
				  ->group(spitfire\storage\database\RestrictionGroup::TYPE_AND)
					->addRestriction('src', $users)
				   ->addRestriction('target', null)
				  ->endGroup()
				->endGroup()
				->addRestriction('created', time() - 720 * 3600, '>')
				->setResultsPerPage(50)
				->setOrder('created', 'DESC');
		
		$notifications = $query->fetchAll();
		
		#Set the notifications that were unseen as seen
		$dbuser->lastSeen = time();
		$dbuser->store();
		
		$this->view->set('notifications', $notifications);
	}
	
	public function counter() {
		
		$dbuser = db()->table('user')->get('authId', $this->user->id)->fetch();
		$query  = db()->table('notification')->get('target__id', $dbuser->_id)->addRestriction('created', $dbuser->lastSeen, '>');
		$query->setResultsPerPage(10); #For the sample loading
		
		$samples = array_map(function ($e) {
			return Array(
				'msg' => $e->content
			);
		}, $query->fetchAll());
		
		$this->view->set('count', $query->count());
		$this->view->set('samples', $samples);
	}
	
}
