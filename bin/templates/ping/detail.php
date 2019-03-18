
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

				<div class="spacer" style="height: 10px"></div>
				
				<?php $parent = $ping->irt; ?>
				<?php $count  = 0; ?>
				<?php $notifications = []; ?>
				<?php while ($parent && $count < 10) { array_unshift($notifications, $parent); $parent = $parent->irt; $count++; } ?>
				<?php $notifications[] = $ping; ?>
				
				
				<?php foreach ($notifications as $notification): ?>
				<?php $u = $sso->getUser($notification->src->user->authId); ?>
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
					Log in to send <?= $user->getUsername() ?> a ping...
				</p>
				<?php else: ?>
				<form method="POST" action="<?= url('ping', 'push', Array('returnto' => (string)url('ping', 'detail', $notification->_id))) ?>" enctype="multipart/form-data">
					<input type="hidden" name="irt" value="<?= $ping->_id ?>">
					
					<?php if ($ping->target): ?>
					<input type="hidden" name="target" value="<?= $ping->src->_id === $authUser->id? $ping->target->_id : $ping->src->_id ?>">
					<?php endif; ?>
					
					<div class="padded add-ping">
						<div>
							<div class="row l10">
								<div class="span l1 desktop-only" style="text-align: center">
									<img src="<?= $sso->getUser($authUser->id)->getAvatar(64) ?>" style="width: 100%; border: solid 1px #777; border-radius: 3px;">
								</div>
								<div class="span l9">
									<textarea name="content" id="new-ping-content" placeholder="Message to broadcast..."></textarea>
								</div>
							</div>
						</div>

						<div class="spacer" style="height: 10px"></div>

						<div>
							<div class="row l10">
								<div class="span l1"></div>
								<div class="span l9">
									<div class="row l5 m4 s4 fluid">
										<div class="span l1 m1 s1" data-lysine-view="file-upload-preview" >
											<div>
												<img data-lysine-src="{{source}}" style="vertical-align: middle; " onload="if (this.width < this.height) {
														var mw = this.parentNode.clientWidth;
														this.style.width  = mw * (this.width / this.height) + 'px';
														this.style.height = mw + 'px';
													}
													else {
														var mw = this.parentNode.clientWidth;
														this.style.height = mw * (this.height / this.width) + 'px';
														this.style.width  = mw + 'px';
													}">
											</div>
											<input type="hidden" name="media[]" value="" data-for="id">
										</div>
									</div>
								</div>
							</div>
						</div>

						<div>
							<div class="row l10"><!--
								--><div class="span l1">
									<!--Just a spacer-->
								</div><!--
								--><div class="span l4">
									<input type="file" id="ping_media" style="display: none">
									<img src="<?= spitfire\core\http\URL::asset('img/camera.png') ?>" id="ping_media_selector" style="vertical-align: middle; height: 24px; opacity: .5; margin: 0 5px;">
								</div><!--
								--><div class="span l5" style="text-align: right">
									<span id="new-ping-character-count">250</span>
									<input type="submit" value="Ping!" id="send-ping">
								</div><!--
							--></div>
						</div>
					</div>
				</form>
				<?php endif; ?>
			</div>
			
			<div class="spacer" style="height: 30px"></div>
			
			<div class="material unpadded" id="replies">
				
				<div class="spacer" style="height: 10px"></div>
				
				<div data-lysine-view="ping">
					
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
									<div class="span1 desktop-only" style="text-align: right; font-size: .8em; color: #777;" data-for="timeRelative"></div>
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


								<div class="spacer" style="height: 20px;"></div>

								<div class="row1 fluid">
									<div class="span1" style="text-align: right">
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
						replyCount         : data.payload[i].replies || 'Reply',
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


<script type="text/javascript" src="<?= spitfire\core\http\URL::asset('js/queue.js') ?>"></script>
<script type="text/javascript">
	depend(['m3/core/request', 'm3/core/array/iterate', 'm3/core/lysine'], function(request, iterate, lysine) {
		
		var mediaLimit = 4;
		
		/*
		 * The forms used for media input
		 */
		var form = {
			input : document.getElementById('ping_media'),
			ui: document.getElementById('ping_media_selector')
		};
		
		var queue = new Queue();
		var uploads = [];
		
		queue.onProgress = function() {
			//Disable the post ping button
			document.getElementById('send-ping').setAttribute('disabled', 'disabled');
		};
		
		queue.onComplete = function() {
			//Enable the post ping button
			document.getElementById('send-ping').removeAttribute('disabled');
		};
		
		form.ui.addEventListener('click', function () {
			form.input.click();
		});

		form.input.addEventListener('change', function (e) {
			var files = e.target.nodeName.toLowerCase() === 'input'? e.target.files : null;

			iterate(files, function (e) {
				var job = queue.job();

				if (e.size > 25 * 1024 * 1024) {
					//Needs a better error
					alert('Files must be smaller than 25MB');
					job.complete();
					return;
				}
				
				var v = new lysine.view('file-upload-preview');
				
				if (e.type.substring(0, 5) === 'image') {

					var reader = new FileReader();

					reader.onload = function (e) {
						v.setData({
							source: e.target.result,
							id: null
						});

						uploads.push({
							view: v
						});

					};

					reader.readAsDataURL(e);
				}
				else {
					v.setData({
						source: '<?= \spitfire\core\http\URL::asset('img/video.png') ?>',
						id: null
					});
				}

				if (uploads.length >= mediaLimit) {
					document.getElementById('ping_media_selector').style.display = 'none';
				}

				var fd = new FormData();
				fd.append('file', e);

				request('<?= url('media', 'upload')->setExtension('json') ?>', fd)
				.then(function(response) {
					var json  = JSON.parse(response);
					v.set('id', json.id + ':' + json.secret);

					job.complete();
				})
				.catch(function(error) {
					alert('Error uploading file. Please retry');
					v.destroy();
				});
			});
			
		});
	});
</script>
