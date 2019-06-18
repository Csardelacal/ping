
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
				
				<span class="follower-count"><a href="<?= url('user', 'following', $user->getUsername()) ?>"><strong><?= db()->table('follow')->get('prey__id', $user->_id)->count() ?></strong> followers</a></span>
				<span class="follow-count"><a href="<?= url('user', 'follows', $user->getUsername()) ?>"><strong><?= db()->table('follow')->get('follower__id', $user->_id)->count() ?></strong> follows</a></span>
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
							<a href="<?= url('user', 'following', $user->getUsername()) ?>"><?= db()->table('follow')->get('prey__id', $user->_id)->count() ?></a> followers
							<a href="<?= url('user', 'follows', $user->getUsername()) ?>"><?= db()->table('follow')->get('follower__id', $user->_id)->count() ?></a> follows
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Main content-->
		<div class="span l3">
			<div class="mobile-only" style="padding: 20px 0; text-align: right">
				<a class="button follow" href="<?= url('user', 'login') ?>" data-ping-follow="<?= $user->_id ?>">Login to follow</a>
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

									<?php $media = $notification->original()->attached; ?>
									<?= current_context()->view->element('media/preview')->set('media', collect($media->toArray()))->render() ?>

								</div>
							</div>

							<div class="spacer" style="height: 20px;"></div>

							<div class="row l2 fluid">
								<div class="span l1">
									<p style="margin: 0;">
										<?php if ($notification->url): ?>
										<a href="<?= $notification->url ?>" style="font-weight: bold;"><?=  __($notification->url, 50) ?></a>
										<?php endif; ?>
									</p>
								</div>
								<div class="span l1" style="text-align: right">
									<a href="<?= url('ping', 'detail', $notification->_id) ?>#replies" class="reply-link"><?= $notification->replies->getQuery()->count()? : 'Reply' ?></a>
									<a href="<?= url('ping', 'share', $notification->_id); ?>" class="share-link"><?= $notification->original()->shared->getQuery()->count()? : 'Share' ?></a>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="separator"></div>
				<?php endforeach; ?>
				
				<?php if (!$authUser): ?>
				<p style="color: #777; font-size: .8em; text-align: center; padding: 15px 20px">
					Log in to reply to <?= $user->getUsername() ?> a ping...
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
			<span style="border: solid 3px #FFF; display: inline-block; border-radius: 3px;"><a class="button follow" href="<?= url('user', 'login') ?>" data-ping-follow="<?= $user->_id ?>">Login to follow</a></span>
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


<script type="text/javascript">
depend(['ping/editor'], function(editor) {
	console.log('editor.loaded');
	editor(<?= json_encode(['endpoint' => (string)url(), 'irt' => $notification->_id]) ?>);
});
</script>

<script type="text/javascript">
depend(['m3/core/lysine'], function(Lysine) {
	var xhr     = null;
	var current = null;
	var notifications = [];
	
	var request = function (callback) {
		if (xhr !== null)  { return; }
		if (current === 0) { return; }
		
		xhr = new XMLHttpRequest();
		xhr.open('GET', '<?= url('ping', 'replies', $ping->_id)->setExtension('json') ?>' + (current? '?until=' + current : ''));
		
		xhr.onreadystatechange = function () {
			if (xhr.readyState === 4 && xhr.status === 200) {
				var data = JSON.parse(xhr.responseText);
				
				if (data.payload.length === 0 || data.until === null) {
					current = 0;
				} else {
					current = data.until;
				}
				
				for (var i= 0; i < data.payload.length; i++) { 
					var view =  new Lysine.view('ping');
					notifications.push(view);
					
					view.setData({
						id                 : data.payload[i].id,
						userName           : data.payload[i].user.username,
						avatar             : data.payload[i].user.avatar,
						userURL            : '<?= url('user') ?>/' + data.payload[i].user.username,
						notificationURL    : data.payload[i].url || '#',
						notificationContent: data.payload[i].content,
						notificationMedia  : data.payload[i].media? data.payload[i].media : 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
						timeRelative       : data.payload[i].timeRelative,
						replyCount         : data.payload[i].replies.count || 'Reply',
						shareCount         : data.payload[i].shares  || 'Share',
						irt                : data.payload[i].irt? [data.payload[i].irt] : []
					});
					
					if (!data.payload[i].media) {
						var child = view.getHTML().querySelector('.media');
						child.parentNode.removeChild(child);
					}
					
					if (!data.payload[i].irt) {
						var child = view.getHTML().querySelector('.irt');
						child && child.parentNode.removeChild(child);
					}
					
					if (data.payload[i].media && data.payload[i].explicit) {
						var media = view.getHTML().querySelector('.media');
						var cover = media.parentNode.insertBefore(document.createElement('div'), media);
						
						cover.className = 'media-cover';
						cover.appendChild(document.createElement('span')).appendChild(document.createTextNode('Ping may contain sensitive media'));
						cover.addEventListener('click', function (cover, media) { return function () {
							cover.style.display = 'none';
							media.style.display = null;
						}}(cover, media), false);
						
						media.style.display = 'none';
					}
				}
				
				
				xhr = null;
				callback();
			}
		};
		
		xhr.send();
	};
	
	var height = function () {
		var body = document.body,
			 html = document.documentElement;

		return Math.max( body.scrollHeight, body.offsetHeight, 
						html.clientHeight, html.scrollHeight, html.offsetHeight );
	};
	
	//This function listens to the scrolls
	var listener = function () {
		var html   = document.documentElement,
		    scroll = Math.max(html.scrollTop, window.scrollY);
		
		if (height() - scroll < html.clientHeight + 700) { request(listener); }
	};
	
	//Attach the listener
	window.addEventListener('load',   listener, false);
	document.addEventListener('scroll', listener, false);
});

</script>
