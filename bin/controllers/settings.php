<?php

use settings\NotificationModel as EmailNotification;
use spitfire\exceptions\HTTPMethodException;
use spitfire\exceptions\PublicException;


class SettingsController extends AppController
{
	
	public function index() {
		$this->response->setBody('redirection')->getHeaders()->redirect(url('settings', 'notification'));
	}
	
	public function notification() {
		if (!$this->user) {
			throw new PublicException('Login required', 401);
		}
		
		#Get the user
		$user     = db()->table('user')->get('authId', $this->user->id)->fetch()? : UserModel::makeFromSSO($this->sso->getUser($this->user->id));
		$types    = NotificationModel::getTypesAvailable();
		
		try {
			if (!$this->request->isPost()) { throw new HTTPMethodException('Not posted'); }
			
			foreach ($types as $type) {
				$r = db()->table('settings\notification')->get('user', $user)->addRestriction('type', $type)->fetch();
				
				if (!$r) {
					$r = db()->table('settings\notification')->newRecord();
					$r->type = $type;
					$r->user = $user;
				}
				
				$r->setting = in_array($_POST['notification'][$type], [0, 1, 2])? $_POST['notification'][$type] : EmailNotification::NOTIFY_EMAIL;
				$r->store();
			}
		} 
		catch (HTTPMethodException $ex) { /*Do nothing, we just don't store */}
		
		#Get the settings made by the user
		$settings = db()->table('settings\notification')->get('user', $user)->fetchAll();
		
		$calculated = [];
		$settings->each(function ($e) use (&$calculated) {
			$calculated[$e->type] = $e->setting;
		});
		
		$this->view->set('types', $types);
		$this->view->set('calculated', $calculated);
	}
	
}