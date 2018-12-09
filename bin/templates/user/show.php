
<div class="profile <?= $authUser && $authUser->id === $user->getId()? 'mine' : '' ?>">

	<?php try { $banner = $user->getAttribute('banner')->getPreviewURL(1280, 300); ?>
	<div id="banner">
		<img src="<?= $banner ?>">
	</div>
	<?php } catch(Exception$e) {} ?>

	<div class="spacer" style="height: 18px"></div>

	<div class="row l5">
		<!--Sidebar (secondary navigation) -->
		<div class="span l1">
			<div class="profile-resume desktop-only">
				<a href="<?= url('user', $user->getUsername()) ?>"><img class="avatar" src="<?= $user->getAvatar(256) ?>"></a>
				<div class="spacer" style="height: 10px"></div>
				<a href="<?= url('user', $user->getUsername()) ?>"><span class="user-name"><?= $user->getUsername() ?></span></a>
				<div class="spacer" style="height: 10px"></div>
				<div class="bio"><?php try { $bio = $user->getAttribute('bio'); ?><?=  nl2br(__($bio)); ?><?php } catch(Exception$e) { ?><em>No bio provided</em><?php } ?></div>
				
				<div class="spacer" style="height: 50px"></div>
				
				<span class="follower-count"><a href="<?= url('user', 'following', $user->getUsername()) ?>"><strong><?= db()->table('follow')->get('prey__id', $user->getId())->count() ?></strong> followers</a></span>
				<span class="follow-count"><a href="<?= url('user', 'follows', $user->getUsername()) ?>"><strong><?= db()->table('follow')->get('follower__id', $user->getId())->count() ?></strong> follows</a></span>
				<span class="ping-count"><strong><?= db()->table('ping')->get('src__id', $user->getId())->addRestriction('target__id', null, 'IS')->count() ?></strong> posts</span>
			</div>
			
			<div class="material unpadded user-card mobile-only">
				<div class="banner" style="height: 47px">
					<?php try { $banner = $user->getAttribute('banner')->getPreviewURL(320, 75) ?>
					<?php if (!$banner) { throw new Exception(); } ?>
					<img src="<?= $banner ?>" width="275" height="64">
					<?php } catch (Exception$e) { } ?>
				</div>
				<div class="padded" style="margin-top: -35px;">
					<img class="avatar" src="<?= $user->getAvatar(128) ?>">
					<div class="user-info">
						<a href="<?= url('user', $user->getUsername()) ?>"><span class="user-name"><?= $user->getUsername() ?></span></a>
						<div class="user-bio">
							<a href="<?= url('user', 'following', $user->getUsername()) ?>"><?= db()->table('follow')->get('prey__id', $user->getId())->count() ?></a> followers
							<a href="<?= url('user', 'follows', $user->getUsername()) ?>"><?= db()->table('follow')->get('follower__id', $user->getId())->count() ?></a> follows
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Main content-->
		<div class="span l3">
			<div class="mobile-only" style="padding: 20px 0; text-align: right">
				<a class="button follow" href="<?= url('user', 'login') ?>" data-ping-follow="<?= $user->getId() ?>">Login to follow</a>
			</div>
			<div class="material unpadded">
				<?php if (!$authUser): ?>
				<p style="color: #777; font-size: .8em; text-align: center; padding: 15px 20px">
					Log in to send <?= $user->getUsername() ?> a ping...
				</p>
				<?php elseif ($user->getId() !== $authUser->id): ?>
				<form method="POST" action="<?= url('ping', 'push', Array('returnto' => (string)url('user', $user->getUsername()))) ?>" enctype="multipart/form-data">
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
							<div class="row l2">
								<div class="span l1">

								</div>
								<div class="span l1" style="text-align: right">
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
					<div class="row l10 fluid">
						<div class="span l1 desktop-only" style="text-align: center">
							<img src="<?= $user->getAvatar(64) ?>" style="width: 100%; border: solid 1px #777; border-radius: 3px;">
						</div>
						<div class="span l9">
							<div class="row l4">
								<div class="span l3">
									<img class="mobile-only" src="<?= $user->getAvatar(64) ?>" style="width: 16px; border: solid 1px #777; border-radius: 3px; vertical-align: middle">
									<a href="<?= url('user', $user->getUsername()) ?>" style="color: #000; font-weight: bold; font-size: .8em;"><?= $user->getUsername() ?></a>
									<?php if ($notification->share): ?>
									<a href="<?= url('ping', 'detail', $notification->share->_id) ?>" style="font-size: .8em; color: #777;"> from <?= $sso->getUser($notification->share->src->_id)->getUsername() ?></a>
									<?php endif; ?>
								</div>
								<div class="span l1 desktop-only" style="text-align: right; font-size: .8em; color: #777;">
									<?= Time::relative($notification->created) ?>
								</div>
							</div>

							<?php if ($notification->irt): ?>
							<div class="spacer" style="height: 10px"></div>

							<div class="source-ping">
								<div class="row10 fluid">
									<div class="span1 desktop-only" style="text-align: center;">
										<img src="<?= $sso->getUser($notification->irt->src->authId)->getAvatar(64) ?>" style="width: 32px; border: solid 1px #777; border-radius: 3px;">
									</div>
									<div class="span9">
										<a href="<?= url('user', $sso->getUser($notification->irt->src->authId)->getUsername()) ?>"  style="color: #000; font-weight: bold; font-size: .8em;">
											<?= $sso->getUser($notification->irt->src->authId)->getUsername() ?>
										</a>

										<p style="margin: 0;">
											<?= Mention::idToMentions($notification->irt->content) ?>
										</p>
									</div>
								</div>
							</div>

							<div class="spacer" style="height: 10px"></div>
							<?php endif; ?>

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
									<a class="delete-link" href="<?= url('ping', 'delete', $notification->_id) ?>" title="Delete this post">&times; Delete</a>
									<a href="<?= url('ping', 'detail', $notification->_id) ?>#replies" class="reply-link"><?= $notification->replies->getQuery()->count()? : 'Reply' ?></a>
									<a href="<?= url('ping', 'share', $notification->_id); ?>" class="share-link"><?= $notification->original()->shared->getQuery()->count()? : 'Share' ?></a>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="separator"></div>
				<?php endforeach; ?>

				<div data-lysine-view="ping">
					
					<div class="padded" style="padding-top: 5px;">
						<div class="row l10 fluid">
							<div class="span l1 desktop-only" style="text-align: center">
								<img data-lysine-src="{{avatar}}" style="width: 100%; border: solid 1px #777; border-radius: 3px;">
							</div>
							<div class="span l9">
								<div class="row l4">
									<div class="span l3">
										<img class="mobile-only" data-lysine-src="{{avatar}}" style="width: 16px; border: solid 1px #777; border-radius: 3px; vertical-align: middle">
										<a data-for="userName" data-lysine-href="{{userURL}}" style="color: #000; font-weight: bold; font-size: .8em;"></a>
									</div>
									<div class="span1 desktop-only" style="text-align: right; font-size: .8em; color: #777;" data-for="timeRelative"></div>
								</div>


								<div class="irt" data-lysine-view data-for="irt">
									<div class="spacer" style="height: 10px"></div>

									<div class="source-ping">
										<div class="row l10 fluid">
											<div class="span1 desktop-only" style="text-align: center;">
												<img data-lysine-src="{{avatar}}" style="width: 32px; border: solid 1px #777; border-radius: 3px;">
											</div>
											<div class="span l9">
												<a  data-for="username" data-lysine-href="{{userURL}}"  style="color: #000; font-weight: bold; font-size: .8em;"></a>

												<p style="margin: 0;"><a  data-for="content" data-lysine-href="<?= url('ping', 'detail'); ?>{{id}}"></a></p>
												
											</div>
										</div>
									</div>

									<div class="spacer" style="height: 10px"></div>
								</div>

								<div class="row1" style="margin-top: 5px">
									<div class="span1">
										<p style="margin: 0;">
											<a data-lysine-href="{{notificationURL}}" style="color: #000;" data-for="notificationContent">
											</a>
										</p>
										
										<div class="spacer" style="height: 20px"></div>
										
										<div class="media-preview" data-condition="count(media) != 0">
											<!--Single images-->
											<div class="row l1" data-condition="count(media) == 1">
												<div class="span l1 ng" data-for="media.0.embed"></div>
											</div>
											
											<!-- Two images -->
											<div class="row l2 m2 s2" data-condition="count(media) == 2">
												<div class="span l1 ng" data-for="media.0.embed"></div>
												<div class="span l1 ng" data-for="media.1.embed"></div>
											</div>
											
											<!--Three images-->
											<div class="row l3 m3 s3" data-condition="count(media) == 3">
												<div class="span l2 ng" data-for="media.0.embed"></div>
												<div class="span l1 ng" >
													<div data-for="media.1.embed"></div>
													<div data-for="media.2.embed"></div>
												</div>
											</div>
											
											<!--Four images-->
											<div class="row l2 m2 s2" data-condition="count(media) == 4">
												<div class="span l1 ng">
													<div data-for="media.0.embed"></div>
													<div data-for="media.1.embed"></div>
												</div>
												<div class="span l1 ng">
													<div data-for="media.2.embed"></div>
													<div data-for="media.3.embed"></div>
												</div>
											</div>
										</div>

									</div>
								</div>
								
								
								<div class="spacer" style="height: 20px;"></div>

								<div class="row1 fluid">
									<div class="span1" style="text-align: right">
										<a data-lysine-href="{{notificationURL}}" data-condition="value(notificationURL) != #" >Open</a>
										<a data-lysine-href="<?= url('ping', 'detail') ?>{{id}}#replies" class="reply-link" data-for="replyCount"></a>
										<a data-lysine-href="<?= url('ping', 'share'); ?>{{id}}" class="share-link" data-for="shareCount"></a>
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
		<div class="span l1 desktop-only">
			<a class="button follow" href="<?= url('user', 'login') ?>" data-ping-follow="<?= $user->getId() ?>">Login to follow</a>
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
		if (current === 0) { return; }
		
		xhr = new XMLHttpRequest();
		xhr.open('GET', '<?= url('user', $user->getUsername())->setExtension('json') ?>?until=' + current);
		
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
						mediaCount         : data.payload[i].mediaCount,
						media              : data.payload[i].media,
						notificationMedia  : data.payload[i].media? data.payload[i].media : 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
						timeRelative       : data.payload[i].timeRelative,
						replyCount         : data.payload[i].replies || 'Reply',
						shareCount         : data.payload[i].shares  || 'Share',
						irt                : data.payload[i].irt? [data.payload[i].irt] : []
					});
					
					if (!data.payload[i].irt) {
						var child = view.getHTML().querySelector('.irt');
						child.parentNode.removeChild(child);
					}
					
					if (data.payload[i].media && data.payload[i].explicit) {
						var media = view.getHTML().querySelector('.media-preview');
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
