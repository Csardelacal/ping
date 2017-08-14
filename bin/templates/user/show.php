
<div class="profile <?= $authUser && $authUser->id === $user->getId()? 'mine' : '' ?>">

	<?php try { $banner = $user->getAttribute('banner')->getPreviewURL(1280, 300); ?>
	<div id="banner">
		<img src="<?= $banner ?>">
	</div>
	<?php } catch(Exception$e) {} ?>

	<div class="spacer" style="height: 18px"></div>

	<div class="row5">
		<!--Sidebar (secondary navigation) -->
		<div class="span1">
			<div class="profile-resume desktop-only">
				<img class="avatar" src="<?= $user->getAvatar(256) ?>">
				<div class="spacer" style="height: 10px"></div>
				<div class="bio"><?php try { $bio = $user->getAttribute('bio'); ?><?=  nl2br(__($bio)); ?><?php } catch(Exception$e) { ?><em>No bio provided</em><?php } ?></div>
			</div>
		</div>

		<!-- Main content-->
		<div class="span3">
			<div class="mobile-only" style="padding: 20px 0; text-align: right">
				<a class="button follow" href="<?= new URL('user', 'login') ?>" data-ping-follow="<?= $user->getId() ?>">Login to follow</a>
			</div>
			<div class="material unpadded">
				<?php if (!$authUser): ?>
				<p style="color: #777; font-size: .8em; text-align: center; padding: 15px 20px">
					Log in to send <?= $user->getUsername() ?> a ping...
				</p>
				<?php elseif ($user->getId() !== $authUser->id): ?>
				<form method="POST" action="<?= new URL('notification', 'push', Array('returnto' => (string)new URL('user', $user->getUsername()))) ?>" enctype="multipart/form-data">
					<input type="hidden" name="target" value="<?= $user->getId() ?>">
					<div class="padded add-ping">
						<div>
							<div class="row1">
								<div class="span1">
									<textarea name="content" id="new-ping-content" placeholder="Send ping to <?= $user->getUsername() ?>..."></textarea>
								</div>
							</div>
						</div>

						<div class="spacer" style="height: 10px"></div>

						<div>
							<div class="row2">
								<div class="span1">

								</div>
								<div class="span1" style="text-align: right">
									<span id="new-ping-character-count">250</span>
									<input type="file" name="media" id="ping_media" accept="image/*" style="display: none" onchange="document.getElementById('ping_media_selector').style.opacity = '1'">
									<img src="<?= spitfire\core\http\URL::asset('img/camera.png') ?>" id="ping_media_selector" onclick="document.getElementById('ping_media').click()" style="vertical-align: middle; height: 24px; opacity: .3; margin: 0 5px;">
									<input type="submit" value="Ping!">
								</div>
							</div>
						</div>
					</div>
				</form>

				<?php else: ?>

				<p style="color: #777; font-size: .8em; text-align: center; padding: 15px 20px">
					This is your own profile. You cannot send notifications to yourself.
				</p>

				<?php endif; ?>

				<div class="separator"></div>

				<?php foreach($notifications as $notification): ?>
				<?php $u = $sso->getUser($notification->src->authId); ?>
				<div class="padded" style="padding-top: 5px;">
					<div class="row10 fluid">
						<div class="span1 desktop-only" style="text-align: center">
							<img src="<?= $user->getAvatar(64) ?>" style="width: 100%; border: solid 1px #777; border-radius: 3px;">
						</div>
						<div class="span9">
							<div class="row4">
								<div class="span3">
									<a href="<?= new URL('user', $u->getUsername()) ?>" style="color: #000; font-weight: bold; font-size: .8em;"><?= $u->getUsername() ?></a>
								</div>
								<div class="span1 desktop-only" style="text-align: right; font-size: .8em; color: #777;">
									<?= Time::relative($notification->created) ?>
									<a class="delete-link" href="<?= new URL('notification', 'delete', $notification->_id) ?>" title="Delete this post">&times;</a>
								</div>
							</div>
							<div class="row1" style="margin-top: 5px">
								<div class="span1">
									<p style="margin: 0;">
										<?php if ($notification->url && !$notification->media): ?><a href="<?= $notification->url ?>"><?php endif; ?>
										<?= Mention::idToMentions(Strings::strToHTML($notification->content)) ?>
										<?php if ($notification->url && !$notification->media): ?></a><?php endif; ?>
									</p>

									<?php if ($notification->media): ?>
									<div class="spacer" style="height: 20px"></div>
										<?php if ($notification->url): ?><a href="<?= $notification->url ?>"><?php endif; ?>
										<img src="<?= $notification->getMediaURI() ?>" style="width: 100%">
										<?php if ($notification->url): ?></a><?php endif; ?>
									<?php endif; ?>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="separator"></div>
				<?php endforeach; ?>

				<div data-lysine-view="notification">
					<div class="padded" style="padding-top: 5px;">
						<div class="row10 fluid">
							<div class="span1 desktop-only" style="text-align: center">
								<img data-lysine-src="{{avatar}}" style="width: 100%; border: solid 1px #777; border-radius: 3px;">
							</div>
							<div class="span9">
								<div class="row4">
									<div class="span3">
										<img class="mobile-only" data-lysine-src="{{avatar}}" style="width: 16px; border: solid 1px #777; border-radius: 3px; vertical-align: middle">
										<a data-for="userName" data-lysine-href="{{userURL}}" style="color: #000; font-weight: bold; font-size: .8em;"></a>
									</div>
									<div class="span1 desktop-only" style="text-align: right; font-size: .8em; color: #777;">
										<span data-for="timeRelative"></span>
										<a class="delete-link" data-lysine-href="<?= new URL('notification', 'delete', '{{id}}') ?>" title="Delete this post">&times;</a>
									</div>
								</div>
								<div class="row1" style="margin-top: 5px">
									<div class="span1">
										<p style="margin: 0;">
											<a data-lysine-href="{{notificationURL}}" style="color: #000;" data-for="notificationContent">
											</a>
										</p>

										<div class="spacer" style="height: 20px"></div>

										<a class="media" data-lysine-href="{{notificationURL}}" >
											<img data-lysine-src="{{notificationMedia}}" style="width: 100%">
										</a>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="separator"></div>
				</div>
			</div>
		</div>

		<!-- Contextual menu-->
		<div class="span1 desktop-only">
			<a class="button follow" href="<?= new URL('user', 'login') ?>" data-ping-follow="<?= $user->getId() ?>">Login to follow</a>
		</div>
	</div>
</div>

<script type="text/javascript" src="<?= URL::asset('js/banner.js') ?>"></script>
<script type="text/javascript" src="<?= URL::asset('js/follow_button.js') ?>"></script>
<script type="text/javascript">
(function () {
	window.ping.setBaseURL('<?= url(); ?>');
	window.ping.init();
}());
</script>

<script type="text/javascript" src="<?= URL::asset('js/lysine.js') ?>"></script>

<script type="text/javascript">
(function() {
	var xhr = null;
	var current = <?= json_encode(isset($notification) && $notification? $notification->_id : null) ?>;
	var notifications = [];
	
	var request = function (callback) {
		if (xhr !== null)  { return; }
		if (current === 0) { return; }
		
		xhr = new XMLHttpRequest();
		xhr.open('GET', '<?= new URL('user', $user->getUsername() . '.json') ?>?until=' + current);
		
		xhr.onreadystatechange = function () {
			if (xhr.readyState === 4 && xhr.status === 200) {
				var data = JSON.parse(xhr.responseText);
				
				if (data.payload.length === 0 || data.until === null) {
					current = 0;
				} else {
					current = data.until;
				}
				
				for (var i= 0; i < data.payload.length; i++) { 
					var view =  new Lysine.view('notification');
					notifications.push(view);
					
					view.setData({
						id                 : data.payload[i].id,
						userName           : data.payload[i].user.username,
						avatar             : data.payload[i].user.avatar,
						userURL            : '<?= new URL('user') ?>/' + data.payload[i].user.username,
						notificationURL    : data.payload[i].url || '#',
						notificationContent: data.payload[i].content,
						notificationMedia  : data.payload[i].media? data.payload[i].media : 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
						timeRelative       : data.payload[i].timeRelative
					});
					
					if (!data.payload[i].media) {
						var child = view.getHTML().querySelector('.media');
						child.parentNode.removeChild(child);
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
}());

(function () {
	
	/**
	 * This little listener makes sure to display the amount of characters left for
	 * the user to type in
	 */
	var listener = function() {
		document.querySelector('#new-ping-character-count').innerHTML = 250 - this.value.length;
	};
	
	document.querySelector('#new-ping-content').addEventListener('keyup', listener, false);
	
}());
</script>