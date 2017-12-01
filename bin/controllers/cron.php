<?php

class CronController extends AppController
{
	
	/**
	 * 
	 * @template none
	 */
	public function index() {
		
		$lock = 'bin/usr/.cron.lock';
		$fh = fopen($lock, file_exists($lock)? 'r' : 'w+');
		
		if (flock($fh, LOCK_EX|LOCK_NB)) {
			$old  = db()->table('email\digestqueue')->get('created', time() - 86400, '<')->fetch();
			
			if ($old) {
				$user = $old->notification->target;

				$emailsender = new EmailSender($this->sso);
				$emailsender->sendDigest($this->sso->getUser($user->authId));

				#Delete the digest queue
				$q  = db()->table('email\digestqueue')->get('user', $user);

				$res = $q->fetchAll();
				foreach ($res as $record) { $record->delete(); }
			}
			
			flock($fh, LOCK_UN);
		}
	}
	
}
