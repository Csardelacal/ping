
<!--Sidebar (secondary navigation) -->
<div class="contains-sidebar">
	<div class="sidebar">
		<div class="spacer" style="height: 10px"></div> 
		<div class="material unpadded user-card">
			<?php $user = $sso->getUser($authUser->id); ?>
			<a href="<?= url('user', $user->getUsername()) ?>">
				<div class="banner" style="height: 47px">
					<?php try { $banner = $user->getAttribute('banner')->getPreviewURL(320, 75) ?>
					<?php if (!$banner) { throw new Exception(); } ?>
					<img src="<?= $banner ?>" width="275" height="64">
					<?php } catch (Exception$e) { } ?>
				</div>
				<div class="padded" style="margin-top: -35px;">
					<img class="avatar" src="<?= $user->getAvatar(128) ?>">
					<div class="user-info">
						<span class="user-name"><?= $user->getUsername() ?></span>
						<span class="user-bio"><?= db()->table('follow')->get('prey__id', $user->getId())->count() ?> followers</span>
					</div>
				</div>
			</a>
		</div>

		<?= $secondary_navigation ?>
	</div>
</div>
	
<div class="content">
	<div class="spacer" style="height: 10px"></div> 
	
	<div class="row l4 fluid">
		<!-- Main content-->
		<div class="span l3">
			<div class="material unpadded">
				<form method="POST" action="<?= url('ping', 'push') ?>" enctype="multipart/form-data">
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
											<div style="line-height: 100px; text-align: center; overflow: hidden; border-radius: 8px; height: 100px">
												<img data-lysine-src="{{source}}" style="vertical-align: middle; " onload="if (this.width > this.height) {
														this.style.width  = 100 * (this.width / this.height) + 'px';
														this.style.height = 100 + 'px';
													}
													else {
														this.style.height = 100 * (this.height / this.width) + 'px';
														this.style.width  = 100 + 'px';
													}">
											</div>
											<input type="hidden" name="media[]" value="" data-for="id">
										</div>
									</div>
									<div class="row l1 m1 s1 fluid">
										<div class="span l1 m1 s1" data-lysine-view="video-upload-preview" >
											<video data-lysine-src="{{source}}" style="vertical-align: middle; width: 100px; ">
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
									<input type="file" id="ping_media" accept="image/*" style="display: none">
									<img src="<?= spitfire\core\http\URL::asset('img/camera.png') ?>" id="ping_media_selector" style="vertical-align: middle; height: 24px; opacity: .5; margin: 0 5px;">

									<input type="file" id="ping_video" accept="video/*" style="display: none">
									<img src="<?= spitfire\core\http\URL::asset('img/video.png') ?>" id="ping_video_selector" style="vertical-align: middle; height: 24px; opacity: .5; margin: 0 5px;">
								</div><!--
								--><div class="span l5" style="text-align: right">
									<span id="new-ping-character-count">250</span>
									<input type="submit" value="Ping!">
								</div><!--
							--></div>
						</div>
					</div>
				</form>

				<div class="separator"></div>

				<?php foreach($notifications as $notification): ?>
				<?php $user = $sso->getUser($notification->src->authId); ?>
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
									<a href="<?= url('ping', 'detail', $notification->_id) ?>#replies" class="reply-link"><?= $notification->replies->getQuery()->count()? : 'Reply' ?></a>
									<a href="<?= url('ping', 'share', $notification->_id); ?>" class="share-link"><?= $notification->original()->shared->getQuery()->count()? : 'Share' ?></a>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="separator"></div>
				<?php endforeach; ?>
				<?php if (empty($notifications)): ?>
				<div style="padding: 50px; text-align: center; color: #777; font-size: .8em; font-style: italic; text-align: center">
					Nothing here yet. Follow or interact with users to build your feed!
				</div>
				<?php endif; ?>

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


								<div class="irt" data-lysine-view data-for="irt">
									<div class="spacer" style="height: 10px"></div>

									<div class="source-ping">
										<div class="row10 fluid">
											<div class="span1 desktop-only" style="text-align: center;">
												<img data-lysine-src="{{avatar}}" style="width: 32px; border: solid 1px #777; border-radius: 3px;">
											</div>
											<div class="span9">
												<a  data-for="username" data-lysine-href="{{userURL}}"  style="color: #000; font-weight: bold; font-size: .8em;"></a>

												<p style="margin: 0;" data-for="content"></p>
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

										<a class="media" data-lysine-href="{{notificationURL}}" data-for="notificationMediaEmbed">
											<!--<img data-lysine-src="{{notificationMedia}}" style="width: 100%">-->
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
		<div class="span l1">
			<div class="spacer" style="height: 70px;"></div>
			<div style="color: #888; font-size: .8em">Users you may like to follow:</div>
			<div class="spacer" style="height: 10px;"></div>

			<div data-lysine-view="whotofollow">
				<div class="material unpadded user-card">
					<a data-lysine-href="<?= url('user', '{{username}}') ?>?ref=whotofollow">
						<div class="banner" style="height: 47px">
							<img src="about:blank" data-lysine-src="{{banner}}" width="275" height="64">
						</div>
						<div class="padded" style="margin-top: -35px;">
							<img class="avatar" data-lysine-src="{{avatar}}">
							<div class="user-info">
								<span class="user-name" data-for="username"></span>
								<span class="user-bio"><span data-for="followers"></span> followers</span>
							</div>
						</div>
					</a>
				</div>

				<div class="spacer" style="height: 10px;"></div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript" src="<?= spitfire\core\http\URL::asset('js/lysine.js') ?>"></script>
<script type="text/javascript" src="<?= spitfire\core\http\URL::asset('js/queue.js') ?>"></script>

<script type="text/javascript">
(function() {
	var xhr = null;
	var current = <?= isset($notification) && $notification? $notification->_id : 0 ?>;
	var notifications = [];
	
	var request = function (callback) {
		if (xhr !== null)  { return; }
		if (current === 0) { return; }
		
		xhr = new XMLHttpRequest();
		xhr.open('GET', '<?= url('feed')->setExtension('json') ?>?until=' + current);
		
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
						notificationMedia  : data.payload[i].media? data.payload[i].media : 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
						notificationMediaEmbed  : data.payload[i].media? data.payload[i].mediaEmbed : '<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==">',
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
						child.parentNode.removeChild(child);
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
		
		var self = this;
		
		setTimeout(function() { 
			var height = self.scrollHeight;
			var length = self.value.length;
			
		  self.style.height = height + 'px';
			document.querySelector('#new-ping-character-count').innerHTML = 250 - length;
		}, 1);
		
	};
	
	document.querySelector('#new-ping-content').addEventListener('keypress', listener, false);
	document.querySelector('.add-ping').addEventListener('click', function() { document.querySelector('#new-ping-content').focus(); }, false);
	
}());

(function () {
	
	var xhr = new XMLHttpRequest();
	xhr.open('GET', '<?= url('people', 'whoToFollow')->setExtension('json') ?>');
	
	xhr.onreadystatechange = function () {
		if (xhr.readyState === 4 && xhr.status === 200) {
			var json = JSON.parse(xhr.responseText).payload;
			
			for (var i in json) {
				if (!json.hasOwnProperty(i)) { continue; }
				
				var view = new Lysine.view('whotofollow');
				view.setData(json[i]);
			}
		}
	};
	
	xhr.send();
}());
</script>

<script type="text/javascript">
	(function() {
		var input = document.getElementById('ping_media');
		var ui    = document.getElementById('ping_media_selector');
		var queue = new Queue();
		
		ui.addEventListener('click', function () {
			input.click();
		});
		
		input.addEventListener('change', function (e) {
			var files = e.target.nodeName.toLowerCase() === 'input'? e.target.files : null;

			for (var i = 0; i < files.length; i++) {
				var job = queue.job();

				if (files[i].size > 25 * 1024 * 1024) {
					//Needs a better error
					alert('Files must be smaller than 25MB');
					job.complete();
					continue;
				}

				var reader = new FileReader();
				var v = new Lysine.view('file-upload-preview');

				reader.onload = function (e) {
					
					v.setData({
						source: e.target.result,
						id: null
					});
					
				};

				reader.readAsDataURL(files[i]);

				var upload = new XMLHttpRequest();
				upload.onreadystatechange = function(job) { return function () {
					if (this.readyState === 4){
						const status = this.status;
						if (status === 200) {
							var json  = JSON.parse(this.responseText);
							v.id = json.id + ':' + json.secret;

							job.complete();
						}
						else {
							img.style.borderColor = 'red';
							img.style.backgroundColor = 'rgba(255,0,0,.1)';
							img.title = (function(html){
								try {
									let div = document.createElement('div');
									div.innerHTML = html;
									return div.querySelector('.errormsg .wrapper p').innerHTML
								}
								catch(e){ return `Image upload failed (HTTP ${status})` }
							})(this.responseText);
						}
					}
				}; }(job);

				var fd = new FormData();
				fd.append('file', files[i]);
				fd.append('type', 'image');

				upload.open('POST', '<?= url('media', 'upload')->setExtension('json') ?>');
				upload.send(fd);

			}
		});
	}());
</script>


<script type="text/javascript">
	//VIDEO UPLOAD
	(function() {
		var input = document.getElementById('ping_video');
		var ui    = document.getElementById('ping_video_selector');
		var queue = new Queue();
		
		ui.addEventListener('click', function () {
			input.click();
		});
		
		input.addEventListener('change', function (e) {
			var files = e.target.nodeName.toLowerCase() === 'input'? e.target.files : null;

			for (var i = 0; i < files.length; i++) {
				var job = queue.job();

				if (files[i].size > 25 * 1024 * 1024) {
					//Needs a better error
					alert('Files must be smaller than 25MB');
					job.complete();
					continue;
				}

				var reader = new FileReader();
				var v = new Lysine.view('video-upload-preview');

				reader.onload = function (e) {
					
					v.setData({
						source: e.target.result,
						id: null
					});
					
				};

				reader.readAsDataURL(files[i]);

				var upload = new XMLHttpRequest();
				upload.onreadystatechange = function(job) { return function () {
					if (this.readyState === 4){
						const status = this.status;
						if (status === 200) {
							var json  = JSON.parse(this.responseText);
							v.id = json.id + ':' + json.secret;

							job.complete();
						}
					}
				}; }(job);

				var fd = new FormData();
				fd.append('file', files[i]);
				fd.append('type', 'video');

				upload.open('POST', '<?= url('media', 'upload')->setExtension('json') ?>');
				upload.send(fd);

			}
		});
	}());
</script>

