<?php

use cron\FlipFlop;
use spitfire\exceptions\PublicException;

/**
 * Pings are the base data type of Ping, they allow a user to broadcast a message
 * (status update, ping) to a following. Allowing them to collect feedback and 
 * comments on it.
 * 
 * @author César de la Cal Bretschneider <cesar@magic3w.com>
 */
class PingController extends AppController
{
	
	/**
	 * There's not really much value in the /ping method. This could potentially
	 * be used to redirect the user or provide information about the server.
	 */
	public function index() {
		
	}
	
	/**
	 * Pushes a ping to the server's public feed. This allows the ping to be displayed,
	 * shared and replied to.
	 * 
	 * @validate >> POST#src (positive number)
	 * @validate >> POST#target (positive number)
	 * @validate >> POST#msg (required string length[0, 250])
	 * @validate >> POST#url (string url)
	 * @validate >> POST#irt (positive number)
	 * 
	 * @request-method POST
	 */
	public function push() {
		
		/*
		 * This happens when the user has an active session with the application,
		 * and not represented by an application. We assume that there's no intermediary.
		 * 
		 * Applications should log into the application using their signature, and
		 * while they will receive very open privileges, Ping likes to be 
		 * transparent about where pings originated from.
		 */
		if ($this->user) {
			$srcid = $this->user->id;
			$authapp = $this->authapp instanceof auth\App? $this->authapp->getSrc()->getId() : null;
		}
		else {
			throw new PublicException('Authentication required', 403);
		}
		
		
		#Read POST data
		$tgtid    = $_POST['target']?? null;
		$content  = str_replace("\r", '', $_POST['content']);
		$url      = $_POST['url']?? null;
		$media    = $_POST['media']?? null;
		$poll     = $_POST['poll']?? null;
		$irt      = $_POST['irt']?? null;
		$explicit = !!($_POST['explicit']?? false);
		
		#There needs to be a src user. That means that somebody is originating the
		#notification. There has to be one, and no more than one.
		$src = AuthorModel::get(db()->table('user')->get('authId', $srcid)->first()? : UserModel::makeFromSSO($this->sso->getUser($srcid)));
		
		/*
		 * Check whether the source defined a target for this ping. If the target
		 * can be traced to an author, we will record that.
		 */
		$target = $tgtid === null? null : AuthorModel::find($tgtid);
		
		/*
		 * If there is a target, and it's not an author known to the server, then
		 * we must stop the application from creating the ping.
		 */
		if (!($target instanceof AuthorModel || $target === null)) {
			throw new PublicException('Invalid target', 400);
		}

		#Make it a record
		$notification = db()->table('ping')->newRecord();
		$notification->src = $src;
		$notification->authapp = $authapp;
		$notification->target = $target;
		$notification->content = Mention::mentionsToId($content);
		$notification->url     = $url;
		$notification->explicit= $explicit;
		$notification->irt     = $irt? db()->table('ping')->get('_id', $irt)->first(true) : null;
		$notification->processed = false;
		$notification->locked = false;
		
		
		/**
		 * @todo This method should be deprecated in favor of batch processing the
		 * files
		 */
		if (is_string($media)) {
			$notification->media = $media;
			
			$media = [];
		}
		
		$this->core->feed->push->do(function ($notification) use ($poll, $media) {
			$notification->store();

			#Attach the media
			foreach (array_filter($media) as $file) {
				list($id, $secret) = explode(':', $file);
				$record = db()->table('media\media')->get('_id', $id)->where('secret', $secret)->first(true);
				$record->ping = $notification;
				$record->store();
			}

			#Create poll options
			foreach (array_filter($poll) as $option) {
				$record = db()->table('poll\option')->newRecord();
				$record->ping = $notification;
				$record->text = $option;
				$record->store();
			}
		}, $notification);
		
		try {
			$sem = new FlipFlop(spitfire()->getCWD() . '/bin/usr/.media.cron.lock');
			$sem->notify();
		}
		catch (Exception $ex) {
			#Notifying the cron-job failed. Gracefully recover.
		}
		
		$this->view->set('ping', $notification);
		
	}
	
	public function delete($id, $confirm = null) {
		$notification = db()->table('ping')->get('_id', $id)->fetch();
		$salt = sha1('somethingrandom' . $id . (int)(time() / 86400));
		
		if (!$notification) { throw new PublicException('No notification found', 404); }
		
		if (!$this->user) {
			throw new PublicException('Login required', 403);
		}
		
		if ($notification->target === null && $notification->src->_id !== $this->user->id)  
			{ throw new PublicException('No notification found', 404); }
		
		if ($notification->target !== null && $notification->target->_id !== $this->user->id)  
			{ throw new PublicException('No notification found', 404); }
		
		if ($confirm === $salt) {
			$notification->deleted = time();
			
			$this->core->feed->delete->do(function ($notification) {
				$notification->store();
			}, $notification);
			
			return $this->response->setBody('OK')->getHeaders()->redirect(url('feed'));
		}
		
		$this->view->set('id', $id);
		$this->view->set('salt', $salt);
	}
	
	public function detail($pingid) {
		$ping = db()->table('ping')->get('_id', $pingid)->fetch();
		
		if (!$ping || $ping->deleted) { throw new PublicException('Ping does not exist', 404);}
		
		$this->view->set('user', $this->sso->getUser($ping->src->_id));
		$this->view->set('ping', $ping);
	}
	
	public function replies($pingid) {
		$ping = db()->table('ping')->get('_id', $pingid)->fetch();
		
		if (!$ping || $ping->deleted) { throw new PublicException('Ping does not exist', 404); }
		if ($ping->target && $ping->src->authId !== $this->user->id && $ping->target->authId !== $this->user->id) { throw new PublicException('Ping does not exist', 404); }
		
		$query = $ping->replies->getQuery();
		$g = $query->group();
		$g->addRestriction('target', null, 'IS');
		$g->addRestriction('target', AuthorModel::get(db()->table('user')->get('authId', $this->user? $this->user->id : null)->first()));
		$g->addRestriction('src', AuthorModel::get(db()->table('user')->get('authId', $this->user->id)->first()));
		
		$query->setOrder('_id', 'desc');
		
		if (isset($_GET['until'])) { $query->addRestriction('_id', $_GET['until'], '<'); }
		
		$replies = $query->range(0, 20);
		
		$this->view->set('notifications', $replies);
	}
	
	public function share($pingid) {
		$original = db()->table('ping')->get('_id', $pingid)->where('deleted', null)->first(true);
		
		if (!$this->user)      { throw new PublicException('Log in required', 403); }
		if ($original->target) { throw new PublicException('Ping cannot be shared', 403); }
		
		$src = AuthorModel::get(db()->table('user')->get('_id', $this->user->id)->fetch());
		
		$shared = db()->table('ping')->newRecord();
		$shared->_id     = null;
		$shared->src     = $src;
		$shared->target  = null;
		$shared->content = $original->content;
		$shared->url     = $original->url;
		$shared->media   = $original->media;
		$shared->explicit= $original->explicit;
		$shared->deleted = $original->deleted;
		$shared->created = time();
		$shared->irt     = $original->irt;
		$shared->share   = $original;
		
		$this->core->feed->push->do(function ($notification) {
			$notification->store();
		}, $shared);
		
		$this->view->set('shared', $shared);
	}
}