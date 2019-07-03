<?php

use auth\AppAuthentication;
use cron\FlipFlop;
use spitfire\exceptions\PublicException;
use spitfire\io\XSSToken;

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
			$authapp = $this->authapp instanceof AppAuthentication? $this->authapp->getSrc()->getId() : null;
		}
		else {
			throw new PublicException('Authentication required', 403);
		}
		
		/*
		 * Default optional input to null. Spitfire already handles validation via
		 * the @validate annotation, so we just have to assume that we need to set
		 * fields that were empty to null.
		 */
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
		
		/**
		 * If the user uploaded a file instead, we process it. This should be DRYed
		 * so it doesn't repeat the code from the media::upload action.
		 */
		if ($media instanceof \spitfire\io\Upload) {
			$local = $media->store();
			$media = media()->load($local);

			$record = db()->table('media\media')->newRecord();
			$record->file = $local->uri();
			$record->source = null;
			$record->type   = $media instanceof \spitfire\io\media\FFMPEGManipulator && $media->hasAudio()? 'video' : 'image';
			$record->secret = base64_encode(random_bytes(50));
			$record->store();
			
			$media = [sprintf('%s:%s', $record->_id, $record->secret)];
		}
		
		/*
		 * Once all the validation has been performed and we are sure that data can
		 * be handled properly by the database we send it to the event handler that 
		 * will execute the code appropriately.
		 */
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
		
		/*
		 * This will notify the cron job, allowing the system to process the ping 
		 * immediately as it is received.
		 */
		try {
			$sem = new FlipFlop(spitfire()->getCWD() . '/bin/usr/.media.cron.lock');
			$sem->notify();
		}
		catch (Exception $ex) {
			#Notifying the cron-job failed. Gracefully recover.
		}
		
		$this->view->set('ping', $notification);
		
	}
	
	/**
	 * Delete a ping. This method will not actually remove it  from the database,
	 * instead, it will flag it for deletion at a later point.
	 * 
	 * @param int $id
	 * @param string $confirm
	 * @return type
	 * @throws PublicException
	 */
	public function delete($id, $confirm = null) {
		/**
		 * Find the ping in question and generate a random hash that the user will
		 * have to return to confirm they know what they're doing.
		 * 
		 * @todo Replace with Spitfire's XSRF token method.
		 */
		$notification = db()->table('ping')->get('_id', $id)->fetch();
		$salt = new XSSToken();
		
		if (!$notification) { throw new PublicException('No notification found', 404); }
		
		/*
		 * If the user is not logged in, there is no point to even continue. A guest 
		 * must never be allowed to delete any ping.
		 */
		if (!$this->user) {
			throw new PublicException('Login required', 403);
		}
		
		/*
		 * Check if the user deleting the ping is actually the person who generated
		 * the ping. Users must only be able to delete a ping they actually posted
		 * themselves.
		 */
		if ($notification->src->_id !== AuthorModel::find($this->user->id)->_id) { 
			throw new PublicException('No notification found', 404); 
		}
		
		/*
		 * Confirm the user actually wishes to delete the ping in question. To do
		 * so, the application will generate a random hash that the user will have
		 * to send back properly.
		 * 
		 * This way we ensure that the user is not deleting a ping via XSRF.
		 */
		if ($salt->verify($confirm)) {
			$notification->deleted = time();
			
			$this->core->feed->delete->do(function ($notification) {
				$notification->store();
			}, $notification);
			
			return $this->response->setBody('OK')->getHeaders()->redirect(url('feed'));
		}
		
		$this->view->set('id', $id);
		$this->view->set('salt', $salt);
	}
	
	/**
	 * Permalink to a ping. The user may provide either a numeric ID or a string based
	 * GUID to retrieve the content and details of the ping.
	 * 
	 * @param int|string $pingid
	 * @throws PublicException
	 */
	public function detail($pingid) {
		/*
		 * Retrieve the ping from the database.
		 */
		$ping = db()->table('ping')->get(is_numeric($pingid)? '_id' : 'guid', $pingid)->fetch();
		$me = AuthorModel::find($this->user->id);
		
		/*
		 * If the ping was deleted, then the user is obviously not allowed to see 
		 * the contents any more. The ping will be deleted sooner or later by the 
		 * garbage collector.
		 */
		if (!$ping || $ping->deleted) { throw new PublicException('Ping does not exist', 404);}
		if ($ping->target && $ping->src->_id !== $me->_id && $ping->target->_id !== $me->_id) { throw new PublicException('Ping does not exist', 404); }
		
		/*
		 * Pass the data onto the view.
		 */
		$this->view->set('user', $ping->src);
		$this->view->set('ping', $ping);
		$this->view->set('me', $me);
	}
	
	/**
	 * Returns a list of pings related to a certain URL. This will only query public
	 * Pings.
	 * 
	 * @param int|string $pingid
	 * @throws PublicException
	 */
	public function url() {
		/*
		 * Retrieve the ping from the database.
		 */
		$ping = db()->table('ping')->get('url', $_GET['url'])->where('deleted', null)->where('target', null)->all();
		
		/*
		 * Pass the data onto the view.
		 */
		$this->view->set('notifications', $ping);
	}
	
	/**
	 * Retrieves a list of replies to the ping.
	 * 
	 * @param int|string $pingid
	 * @throws PublicException
	 */
	public function replies($pingid) {
		/*
		 * Find the ping by either ID or GUID. Please note that there's a very slight
		 * chance that this code will missunderstand a GUID (specifically when a 
		 * numeric GUID is generated - which should be extremely rare)
		 */
		$ping = db()->table('ping')->get(is_numeric($pingid)? '_id' : 'guid', $pingid)->fetch();
		$me = AuthorModel::find($this->user->id);
		
		/*
		 * If the ping has been deleted, or is private, we do not show it ot the 
		 * public.
		 */
		if (!$ping || $ping->deleted) { throw new PublicException('Ping does not exist', 404); }
		if ($ping->target && $ping->src->_id !== $me->_id && $ping->target->_id !== $me->_id) { throw new PublicException('Ping does not exist', 404); }
		
		/*
		 * Fetch the query that provides all the replies to this ping.
		 */
		$query = $ping->replies->getQuery();
		$g = $query->group();
		
		/*
		 * If the ping is part of a private conversation, we include the pings from
		 * me and the ones directed at me.
		 */
		if ($ping->target) {
			$g->addRestriction('target', AuthorModel::get(db()->table('user')->get('authId', $this->user? $this->user->id : null)->first()));
			$g->addRestriction('src', AuthorModel::get(db()->table('user')->get('authId', $this->user->id)->first()));
		}
		/*
		 * Otherwise we include the ones that were not sent to a specifid user.
		 * I need to revisit this behavior, since it appears that the user should 
		 * not be able to send private messages as a reply to a public thread.
		 */
		else {
			$g->addRestriction('target', null, 'IS');
		}
		
		$query->where('share', null);
		$query->setOrder('_id', 'desc');
		
		if (isset($_GET['until'])) { $query->addRestriction('_id', $_GET['until'], '<'); }
		
		$replies = $query->range(0, 20);
		
		$this->view->set('notifications', $replies);
		$this->view->set('me', $me);
	}
	
	/**
	 * Amplifies the reach of a ping by sharing it to the followers of the current
	 * user. Shared pings cannot receive interactions of their own, replies, shares
	 * and other should be normalized back to the source.
	 * 
	 * @param int|string $pingid
	 * @throws PublicException
	 */
	public function share($pingid) {
		$original = db()->table('ping')->get(is_numeric($pingid)? '_id' : 'guid', $pingid)->where('deleted', null)->first(true);
		
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