
<div class="profile <?= $me && $me->_id === $user->_id? 'mine' : '' ?>">

	<?php if ($user->getBanner()): ?>
	<div id="page-banner">
		<img src="<?= $user->getBanner() ?>">
	</div>
	<?php endif; ?>

	<div class="spacer" style="height: 18px"></div>

	<div class="row l5">
		<!--Sidebar (secondary navigation) -->
		<div class="span l1">
			<div class="profile-resume desktop-only">
				<a href="<?= url('user', $user->getUsername()) ?>"><img class="avatar" src="<?= $user->getAvatar(256) ?>"></a>
				<div class="spacer" style="height: 10px"></div>
				<a href="<?= url('user', $user->getUsername()) ?>"><span class="user-name"><?= $user->getUsername() ?></span></a>
				<div class="spacer" style="height: 10px"></div>
				<div class="bio"><?=  __($user->getBio()?: 'No bio provided'); ?></div>
				
				<div class="spacer" style="height: 50px"></div>
				
				<span class="follower-count"><a href="<?= url('people', 'following', $user->getUsername()) ?>"><strong><?= db()->table('follow')->get('prey__id', $user->_id)->count() ?></strong> followers</a></span>
				<span class="follow-count"><a href="<?= url('people', 'follows', $user->getUsername()) ?>"><strong><?= db()->table('follow')->get('follower__id', $user->_id)->count() ?></strong> follows</a></span>
				<span class="ping-count"><strong><?= db()->table('ping')->get('src__id', $user->_id)->addRestriction('target__id', null, 'IS')->count() ?></strong> posts</span>
			</div>
			
			<div class="material unpadded user-card mobile-only">
				<div class="banner" style="height: 47px">
					<?php if ($user->getBanner()): ?>
					<img src="<?= $user->getBanner() ?>" width="275" height="64">
					<?php endif; ?>
				</div>
				<div class="padded" style="margin-top: -35px;">
					<img class="avatar" src="<?= $user->getAvatar(128) ?>">
					<div class="user-info">
						<a href="<?= url('user', $user->getUsername()) ?>"><span class="user-name"><?= $user->getUsername() ?></span></a>
						<div class="user-bio">
							<a href="<?= url('people', 'following', $user->getUsername()) ?>"><?= db()->table('follow')->get('prey__id', $user->_id)->count() ?></a> followers
							<a href="<?= url('people', 'follows', $user->getUsername()) ?>"><?= db()->table('follow')->get('follower__id', $user->_id)->count() ?></a> follows
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Main content-->
		<div class="span l3">
			<div class="mobile-only" style="padding: 20px 0; text-align: right">
				<a class="button follow" href="<?= url('account', 'login') ?>" data-ping-follow="<?= $user->_id ?>">Login to follow</a>
			</div>
			<div class="material unpadded">

				<div class="spacer" style="height: 10px"></div>
				
				<?php $parent = $ping->irt; ?>
				<?php $count  = 0; ?>
				<?php $notifications = []; ?>
				<?php while ($parent && $count < 10) { array_unshift($notifications, $parent); $parent = $parent->irt; $count++; } ?>
				<?php $notifications[] = $ping; ?>
				
				
				<?php foreach ($notifications as $notification): ?>
				<?php $u = $notification->src; ?>
				<div class="padded">
					<div class="row l10 fluid">
						<div class="span l1 desktop-only" style="text-align: center">
							<img src="<?= $u->getAvatar() ?>" style="width: 100%; border: solid 1px #777; border-radius: 3px;">
						</div>
						<div class="span l9">
							<div class="row l4">
								<div class="span l3">
									<img class="mobile-only" src="<?= $u->getAvatar() ?>" style="width: 16px; border: solid 1px #777; border-radius: 3px; vertical-align: middle">
									<a href="<?= url('user', $u->getUsername()) ?>" style="color: #000; font-weight: bold; font-size: .8em;"><?= $u->getUsername() ?></a>
									<?php if ($notification->share): ?>
									<a href="<?= url('ping', 'detail', $notification->share->_id) ?>" style="font-size: .8em; color: #777;"> from <?= $sso->getUser($notification->share->src->_id)->getUsername() ?></a>
									<?php endif; ?>
								</div>
								<div class="span l1 desktop-only" style="text-align: right; font-size: .8em; color: #777;">
									<?= Time::relative($notification->created) ?>
								</div>
							</div>

							<div class="row l1 fluid" style="margin-top: 5px">
								<div class="span l1">
									<p style="margin: 0;">
										<?= Mention::idToMentions($notification->content) ?>
									</p>

									<div class="spacer" style="height: 10px"></div>
									
									<?php $poll = db()->table('poll\option')->get('ping__id', $notification->original()->_id)->all() ?>
									<?php $resp = $authUser? db()->table('poll\reply')->get('ping__id', $notification->original()->_id)->where('author__id', AuthorModel::find($authUser->id)->_id)->first() : null ?>
									<?php if ($poll->count() > 0): ?>
										<div data-poll="<?= $notification->_id ?>">
											<div class="spacer" style="height: 10px"></div>
											<?php foreach ($poll as $option): ?>
												<a href="<?= url('poll', 'vote', $option->_id) ?>" 
													data-option="<?= $option->_id ?>" 
													class="poll-open-response <?= $resp && $resp->option->_id == $option->_id ? 'selected-response' : '' ?>"> 
														<?= __($option->text ?: "Untitled") ?>
												</a>
											<?php endforeach; ?>
										</div>
									<?php endif; ?>

									<?php $media = $notification->original()->attached; ?>
									<?= current_context()->view->element('media/preview')->set('media', collect($media->toArray()))->render() ?>

								</div>
							</div>

							<div class="spacer" style="height: 20px;"></div>

							<div class="row l3 fluid">
								<div class="span l2">
									<?php if (!$authUser): ?>
									<?php elseif (db()->table('feedback')->get('ping', $notification)->where('author', AuthorModel::get(db()->table('user')->get('authId', $authUser->id)->first()))->first()): ?>
										<a href="<?= url('feedback', 'revoke', $notification->_id) ?>" class="ping-contextual-link for-likes liked" data-ping="<?= $notification->_id ?>">
											<i class="im im-heart"></i>
											<span><?= strval(db()->table('feedback')->get('ping', $notification)->where('reaction', 1)->where('removed', null)->count()) ?></span>
										</a>
									<?php else: ?>
										<a href="<?= url('feedback', 'push', $notification->_id) ?>" class="ping-contextual-link for-likes" data-ping="<?= $notification->_id ?>">
											<i class="im im-heart"></i>
											<span><?= strval(db()->table('feedback')->get('ping', $notification)->where('reaction', 1)->where('removed', null)->count())?></span>
										</a>
									<?php endif; ?>
									<a href="<?= url('ping', 'detail', $notification->_id) ?>#replies" class="ping-contextual-link for-replies">
										<i class="im im-speech-bubble"></i>
										<span><?= strval(db()->table('ping')->get('irt__id', $notification->_id)->count()) ?></span>
									</a>
									<a href="<?= url('ping', 'share', $notification->_id); ?>" class="ping-contextual-link for-shares">
										<i class="im im-sync"></i>
										<span><?= $notification->original()->shared->getQuery()->count() ?: 'Share' ?></span>
									</a>
								</div>
								<div class="span l1" style="text-align: right">
									<p style="margin: 0;">
										<?php if ($notification->url): ?>
										<a href="<?= $notification->url ?>" class="ping-contextual-link">
											<span>Open</span>
											<i class="im im-external-link"></i>
										</a>
										<?php endif; ?>
									</p>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="separator"></div>
				<?php endforeach; ?>
				
				<?php if (!$authUser): ?>
				<p style="color: #777; font-size: .8em; text-align: center; padding: 15px 20px">
					<a href="<?= url('account', 'login', ['returnto' => (string)spitfire\core\http\URL::current()]) ?>">Log in</a> to reply to <?= $user->getUsername() ?>...
				</p>
				<?php else: ?>
				<?= current_context()->view->element('ping/editor.lysine.html')->render() ?>
				<?php endif; ?>
			</div>
			
			<div class="spacer" style="height: 30px"></div>
			
			<div id="replies">
				<?= current_context()->view->element('ping/ping.lysine.html')->render() ?>
			</div>
		</div>

		<!-- Contextual menu-->
		<div class="span l1 desktop-only" style="text-align: center;">
			<span style="border: solid 3px #FFF; display: inline-block; border-radius: 3px;"><a class="button follow" href="<?= url('account', 'login') ?>" data-ping-follow="<?= $user->_id ?>">Login to follow</a></span>
		</div>
	</div>
</div>

<script type="text/javascript" src="<?= \spitfire\core\http\URL::asset('js/banner.js') ?>"></script>
<script type="text/javascript" src="<?= \spitfire\core\http\URL::asset('js/follow_button.js') ?>"></script>
<script type="text/javascript">
(function () {
	window.ping.setBaseURL('<?= url(); ?>');
	window.ping.init();
}());
</script>

<?php if (isset($me)): ?>
<script type="text/javascript">
depend(['ping/editor'], function(editor) {
	console.log('editor.loaded');
	editor(<?= json_encode([
		'endpoint' => (string)url(), 
		'placeholder' => 'Your reply...', 
		'irt' => $notification->_id,
		'user' => [ 'avatar' => $me->getAvatar() ]
	]) ?>);
});
</script>
<?php endif; ?>

<?php $token = null; ?>
<?php if(isset($_GET['token'])) { $token = $this->sso->makeToken($_GET['token'])->getId(); } ?>
<?php if(\spitfire\io\session\Session::getInstance()->getUser()) { $token = \spitfire\io\session\Session::getInstance()->getUser()->getId(); } ?>

<script type="text/javascript">
depend(['ping/ping', 'm3/core/lysine'], function(SDK, Lysine) {
	var sdk = new SDK('<?= url() ?>', '<?= $token ?>');
	var nextPage = undefined;
	
	var height = function () {
		var body = document.body,
				  html = document.documentElement;

		return Math.max(body.scrollHeight, body.offsetHeight,
				  html.clientHeight, html.scrollHeight, html.offsetHeight);
	};

	//This function listens to the scrolls
	var listener = function () {
		var html = document.documentElement,
				  scroll = Math.max(html.scrollTop, window.scrollY);

		if (nextPage && height() - scroll < html.clientHeight + 700) {
			nextPage();
			nextPage = null;
		}
	};
	
	sdk.ping().replies(<?= $notification->_id ?>, function (pingList) {
		
		for (var i = 0; i < pingList._pings.length; i++) {

			var view = new Lysine.view('ping');
			var current = pingList._pings[i].payload;

			/*
			 * This block should be possible to have refactored out of the feed,
			 * making it less pointless code that adapts stuff around.
			 */
			view.setData({
				id: current.id,
				userName: current.user.username,
				avatar: current.user.avatar,
				userURL: current.user.url,
				notificationURL: current.url || '#',
				notificationContent: current.content,
				media: current.media,
				poll: current.poll,
				timeRelative: current.timeRelative,
				feedback : current.feedback,
				replyCount: current.replies.count || 'Reply',
				shareCount: current.shares || 'Share',
				irt: current.irt ? [current.irt] : []
			});

		}
	});
	
	document.addEventListener('scroll', listener, false);
});
</script>

<script type="text/javascript">
depend(['ping/feedback'], function (baseurl) { baseurl('<?= spitfire()->baseUrl() ?>', '<?= (isset($_GET['token']) ? $this->sso->makeToken($_GET['token']) : \spitfire\io\session\Session::getInstance()->getUser())->getId() ?>'); });
</script>