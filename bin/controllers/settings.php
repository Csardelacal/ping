<?php

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
		
		try {
			if (!$this->request->isPost()) { throw new HTTPMethodException('Not posted'); }
			
			#TODO: Store the data
		} 
		catch (HTTPMethodException $ex) { /*Do nothing, we just don't store */}
		
		#Get the settings made by the user
		$settings = db()->table('settings\notification')->get('user', $user)->fetchAll();
		
		$calculated = [];
		$settings->each(function ($e) use ($calculated) {
			$calculated[$e->type] = $e->setting;
		});
		
		$this->view->set('types', NotificationModel::getTypesAvailable());
		$this->view->set('calculated', $calculated);
	}
	
}