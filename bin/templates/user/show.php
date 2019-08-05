
<div class="profile <?= $authUser && $authUser->id === $user->user->_id? 'mine' : '' ?>">

	<?php if ($user->getBanner()): ?>
	<div id="page-banner">
		<img src="<?= $user->getBanner() ?>">
	</div>
	<?php endif; ?>

	<div class="spacer" style="height: 18px"></div>

	<div class="row l5" id="feed">
		<!--Sidebar (secondary navigation) -->
		<div class="span l1">
			<div class="profile-resume desktop-only">
				<div class="spacer" style="height: 10px"></div>
				<a href="<?= url('user', $user->getUsername()) ?>"><img class="avatar" src="<?= $user->getAvatar(256) ?>"></a>
				<div class="spacer" style="height: 10px"></div>
				<a href="<?= url('user', $user->getUsername()) ?>"><span class="user-name"><?= $user->getUsername() ?></span></a>
				<div class="spacer" style="height: 10px"></div>
				<div class="bio"><?=  __($user->getBio()?: 'No bio provided'); ?></div>
				
				<div class="spacer" style="height: 50px"></div>
				
				<span class="follower-count"><a href="<?= url('people', 'following', $user->getUsername()) ?>"><strong><?= db()->table('follow')->get('prey', $user)->count() ?></strong> followers</a></span>
				<span class="follow-count"><a href="<?= url('people', 'follows', $user->getUsername()) ?>"><strong><?= db()->table('follow')->get('follower', $user)->count() ?></strong> follows</a></span>
				<span class="ping-count"><strong><?= db()->table('ping')->get('src', $user)->addRestriction('target__id', null, 'IS')->count() ?></strong> posts</span>
			</div>
			
			<div class="material unpadded user-card mobile-only">
				<div class="banner" style="height: 47px">
					<?php if ($user->getBanner()): ?>
					<img src="<?= $user->getBanner() ?>" width="275" height="64">
					<?php endif; ?>
				</div>
				<div class="padded" style="margin-top: -35px;">
					<img class="avatar" src="<?= $user->getAvatar(256) ?>">
					<div class="user-info">
						<a href="<?= url('user', $user->getUsername()) ?>"><span class="user-name"><?= $user->getUsername() ?></span></a>
						<div class="user-bio">
							<a href="<?= url('people', 'following', $user->getUsername()) ?>"><?= db()->table('follow')->get('prey', $user)->count() ?></a> followers
							<a href="<?= url('people', 'follows', $user->getUsername()) ?>"><?= db()->table('follow')->get('follower', $user)->count() ?></a> follows
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
				<?php if (!$authUser): ?>
				<p style="color: #777; font-size: .8em; text-align: center; padding: 15px 20px">
					Log in to send <?= $user->getUsername() ?> a ping...
				</p>
				<?php elseif ($user->_id !== $authUser->id): ?>
					
				<?= current_context()->view->element('ping/editor.lysine.html')->set('target', $author->_id)->render() ?>
				<?php else: ?>

				<p style="color: #777; font-size: .8em; text-align: center; padding: 15px 20px">
					This is your own profile. You cannot send notifications to yourself.
				</p>

				<?php endif; ?>
			</div>
			
			<div class="spacer" style="height: 30px"></div>
			
			<?php foreach($notifications as $notification): ?>
			
			<?= current_context()->view->element('ping/ping')->set('ping', $notification)->render() ?>
			<div class="spacer" style="height: 10px"></div>

			<?php endforeach; ?>

			<div id="feed">
				<?= current_context()->view->element('ping/ping.lysine.html')->render() ?>
			</div>
			
			<div id="loading-spinner" style="text-align: center; padding: 1.5rem; color: #777; display: none;">
				<span class="spinner"></span> Loading more...
			</div>
			
			<div id="end-of-feed" style="text-align: center; padding: 1.5rem; color: #777; display: none;">
				<img src="<?= \spitfire\core\http\URL::asset('img/end-of-feed.png') ?>" style="height: .9rem"> That's it!
			</div>
		</div>

		<!-- Contextual menu-->
		<div class="span l1 desktop-only">
			<a class="button follow" href="<?= url('account', 'login') ?>" data-ping-follow="<?= $user->_id ?>">Login to follow</a>
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

<script type="text/javascript" src="<?= \spitfire\core\http\URL::asset('js/lysine.js') ?>"></script>

<script type="text/javascript">
depend(['m3/core/lysine'], function(Lysine) {
	var xhr = null;
	var current = <?= json_encode(isset($notification) && $notification? $notification->_id : null) ?>;
	var notifications = [];
	
	var request = function (callback) {
		if (xhr !== null)  { return; }
		if (current === 0) { 
			document.getElementById('end-of-feed').style.display = 'block';
			return; 
		}
		
		xhr = new XMLHttpRequest();
		xhr.open('GET', '<?= url('user', 'show', $user->getUsername())->setExtension('json') ?>?until=' + current);
		
		document.getElementById('loading-spinner').style.display = 'block';
		
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
						userURL            : data.payload[i].user.url,
						notificationURL    : data.payload[i].url || '#',
						notificationContent: data.payload[i].content,
						media              : data.payload[i].media,
						poll               : data.payload[i].poll,
						feedback           : data.payload[i].feedback,
						timeRelative       : data.payload[i].timeRelative,
						replyCount         : data.payload[i].replies || 'Reply',
						shareCount         : data.payload[i].shares  || 'Share',
						irt                : data.payload[i].irt? [data.payload[i].irt] : []
					});
					
					if (!data.payload[i].irt) {
						var child = view.getHTML().querySelector('.irt');
						child.parentNode.removeChild(child);
					}
					
					var media = view.getHTML().querySelector('.media-preview');
					
					if (data.payload[i].media && data.payload[i].explicit && media) {
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
				
				document.getElementById('loading-spinner').style.display = 'none';
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

<?php if ($authUser && $user->_id !== $authUser->id): ?>
<script type="text/javascript">
depend(['ping/editor'], function (editor) {
	console.log('Editor initialized');
	editor(<?= json_encode([
		'endpoint' => (string)url(), 
		'placeholder' => 'Message to broadcast...', 
		'user' => ['avatar' => $me->getAvatar() ],
		'target' => $author->_id
	]) ?>);
}); 
</script>


<script type="text/javascript">
depend(['ping/feedback'], function (baseurl) { baseurl('<?= spitfire()->baseUrl() ?>', '<?= (isset($_GET['token']) ? $this->sso->makeToken($_GET['token']) : \spitfire\io\session\Session::getInstance()->getUser())->getId() ?>'); });
</script>
<?php endif; ?>

<script type="text/javascript">
depend(['m3/ui/sticky'], function (sticky) {
	sticky.stick(document.querySelector('.profile-resume'), document.querySelector('#feed'));
}); 
</script>
