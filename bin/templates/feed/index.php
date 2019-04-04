
	<div class="spacer" style="height: 10px"></div> 
	
	<div class="row l3">
		<!-- Main content-->
		<div class="span l2">
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

						<div class="spacer" style="height: 10px"></div>
						
						<div class="row l10" id="poll-dialog" style="display: none">
							<div class="span l1"></div>
							<div class="span l9">
								<div data-lysine-view="poll-create-option">
									<div class="row l5 m4 s4 fluid">
										<div class="span l4 m3 s3">
											<input type="text" name="poll[]" placeholder="Option..." style="width: 100%; border: none; border-bottom: solid 1px #ccc; padding: 3px;">
										</div>
										<div class="span l1 m1 s1">
											<a href="#remove-poll" class="poll-create-remove">Remove</a>
										</div>
									</div>
								</div>
								
								<div class="row l5 m4 s4 fluid">
									<div class="span l4 m3 s3">
										<a href="#add-poll" id="poll-create-add">Add option</a>
									</div>
								</div>
							</div>
							
							<div class="spacer" style="height: 10px"></div>
						</div>

						<div>
							<div class="row l10"><!--
								--><div class="span l1">
									<!--Just a spacer-->
								</div><!--
								--><div class="span l4">
									<input type="file" id="ping_media" style="display: none">
									<img src="<?= spitfire\core\http\URL::asset('img/camera.png') ?>" id="ping_media_selector" style="vertical-align: middle; height: 24px; opacity: .5; margin: 0 5px;">
									<img src="<?= spitfire\core\http\URL::asset('img/poll.png') ?>" id="ping_poll" style="vertical-align: middle; height: 24px; opacity: .3; margin: 0 5px;">
								</div><!--
								--><div class="span l5" style="text-align: right">
									<span id="new-ping-character-count">250</span>
									<input type="submit" value="Ping!" id="send-ping">
								</div><!--
							--></div>
						</div>
					</div>
				</form>

			</div>
				
			<?php if(db()->table('ping')->get('src', db()->table('author')->get('user', db()->table('user')->get('_id', $authUser->id)))->where('processed', 0)->first()): ?>
			<div class="spacer" style="height: 10px"></div>
			
			<div class="material" style="color: #0571B1">
				<div class="row l1 fluid">
					<div class="span l1">
						Your latest ping is being processed...
					</div>
				</div>
			</div>

			<?php endif; ?>
			
			<div class="spacer" style="height: 10px"></div>
			
			<?php foreach($notifications as $notification): ?>
			<?php $user = $notification->src->user? $sso->getUser($notification->src->user->authId) : null; ?>
			<div class="material unpadded">

				<?php if ($notification->irt): ?>
				<div class="source-ping">
					<div class="row l10 fluid">
						<div class="span l1 desktop-only" style="text-align: center;">
							<img src="<?= $sso->getUser($notification->irt->src->user->authId)->getAvatar(64) ?>" style="width: 32px; border: solid 1px #777; border-radius: 3px;">
						</div>
						<div class="span l9">
							<a href="<?= url('user', $sso->getUser($notification->irt->src->user->authId)->getUsername()) ?>"  style="color: #000; font-weight: bold; font-size: .8em;">
								<?= $sso->getUser($notification->irt->src->user->authId)->getUsername() ?>
							</a>

							<p style="margin: 0;">
								<?= Mention::idToMentions($notification->irt->content) ?>
							</p>
						</div>
					</div>
				</div>
				<?php endif; ?>
				
				
				<div class="padded">


					<div class="row l10 fluid">
						<div class="span l1 desktop-only" style="text-align: center">
							<img src="<?= $user->getAvatar(64) ?>" style="width: 100%; border: solid 1px #777; border-radius: 3px;">
						</div>
						<div class="span l9">
							<div class="row l4">
								<div class="span l3">
									<img src="<?= $user->getAvatar(64) ?>" class="not-desktop" style="width: 32px; border-radius: 50%; vertical-align: middle">
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

									<?php $poll = db()->table('poll\option')->get('ping', $notification)->all() ?>
									<?php $resp = db()->table('poll\reply')->get('ping', $notification)->where('author', AuthorModel::get(db()->table('user')->get('authId', $authUser->id)->first()))->first() ?>
									<?php if ($poll->count() > 0): ?>
									<div data-poll="<?= $notification->_id ?>">
										<div class="spacer" style="height: 10px"></div>
										<?php foreach($poll as $option): ?>
										<a href="<?= url('poll', 'vote', $option->_id) ?>" 
											data-option="<?= $option->_id ?>" 
											class="poll-open-response <?= $resp && $resp->option->_id == $option->_id? 'selected-response' : ''  ?>"> 
												  <?= __($option->text?: "Untitled") ?>
										</a>
										<?php endforeach; ?>
									</div>
									<?php endif; ?>
									
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
									<?php if (db()->table('feedback')->get('ping', $notification)->where('author', AuthorModel::get(db()->table('user')->get('authId', $authUser->id)->first()))->first()): ?>
									<a href="<?= url('feedback', 'revoke', $notification->_id) ?>" class="like-link like-active" data-ping="<?= $notification->_id ?>"><?= db()->table('feedback')->get('ping', $notification)->count()? : 'Like' ?></a>
									<?php else: ?>
									<a href="<?= url('feedback', 'push', $notification->_id) ?>" class="like-link" data-ping="<?= $notification->_id ?>"><?= db()->table('feedback')->get('ping', $notification)->count()? : 'Like' ?></a>
									<?php endif; ?>
									<a href="<?= url('ping', 'detail', $notification->_id) ?>#replies" class="reply-link"><?= $notification->replies->getQuery()->count()? : 'Reply' ?></a>
									<a href="<?= url('ping', 'share', $notification->_id); ?>" class="share-link"><?= $notification->original()->shared->getQuery()->count()? : 'Share' ?></a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<div class="spacer" style="height: 10px"></div>
			
			<?php endforeach; ?>
				<?php if (empty($notifications)): ?>
				<div style="padding: 50px; text-align: center; color: #777; font-size: .8em; font-style: italic; text-align: center">
					Nothing here yet. Follow or interact with users to build your feed!
				</div>
				<?php endif; ?>

				<div data-lysine-view="ping">
					<div class="material unpadded">
						<div class="irt" data-lysine-view data-for="irt">
							<div class="source-ping">
								<div class="row l10 fluid">
									<div class="span l1 desktop-only" style="text-align: center;">
										<img data-lysine-src="{{avatar}}" style="width: 32px; border: solid 1px #777; border-radius: 3px;">
									</div>
									<div class="span l9">
										<a  data-for="username" data-lysine-href="{{userURL}}"  style="color: #000; font-weight: bold; font-size: .8em;"></a>

										<p style="margin: 0;">
											<a  data-for="content" data-lysine-href="<?= url('ping', 'detail'); ?>{{id}}"></a>
											<a  data-condition="count(media) != 0" data-lysine-href="<?= url('ping', 'detail'); ?>{{id}}"><strong>[[Media]]</strong></a>
										</p>

									</div>
								</div>
							</div>
						</div>

						<div class="spacer" style="height: 10px"></div>

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
										<div class="span l1 desktop-only" style="text-align: right; font-size: .8em; color: #777;" data-for="timeRelative"></div>
									</div>



									<div class="row1" style="margin-top: 5px">
										<div class="span1">
											<p style="margin: 0;" style="color: #000;" data-for="notificationContent"></p>


											<div data-condition="count(poll) !== 0" data-poll="{{id}}">
												<div class="spacer" style="height: 10px"></div>
												<div data-for="poll" data-lysine-view>
													<a data-lysine-href="<?= url('poll', 'vote') ?>{{id}}" 
														data-lysine-data-option="{{id}}" 
														data-lysine-class="poll-open-response {{selected?selected-response:}}"
														data-for="body"> 

													</a>
												</div>
											</div>

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

					</div>
					<div class="spacer" style="height: 10px"></div>
				</div>
			</div>

		<!-- Contextual menu-->
		<div class="span l1">
			<div class="material unpadded user-card">
				<?php $user = $sso->getUser($authUser->id); ?>
				<a href="<?= url('user', $user->getUsername()) ?>">
					<div class="banner">
						<?php try { $banner = $user->getAttribute('banner')->getPreviewURL(320, 120) ?>
						<?php if (!$banner) { throw new Exception(); } ?>
						<img src="<?= $banner ?>" width="275" height="64">
						<?php } catch (Exception$e) { } ?>
					</div>
					<div class="padded" style="margin-top: -35px;">
						<img class="avatar" src="<?= $user->getAvatar(128) ?>">
						<div class="user-info">
							<span class="user-name">@<?= $user->getUsername() ?></span>
							<span class="user-bio"><?= db()->table('follow')->get('prey__id', $user->getId())->count() ?> followers</span>
						</div>
					</div>
				</a>
			</div>
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

<script type="text/javascript" src="<?= spitfire\core\http\URL::asset('js/queue.js') ?>"></script>

<script type="text/javascript">
depend(['m3/core/lysine'], function(lysine) {
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
					var view =  new lysine.view('ping');
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
});

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

depend(['m3/core/request', 'm3/core/array/iterate', 'm3/core/lysine'], function (request, iterate, lysine) {
	
	request('<?= url('people', 'whoToFollow')->setExtension('json') ?>')
	
	.then(function(response) {
		var json = JSON.parse(response).payload;
		
		iterate(json, function(e) {
			var view = new lysine.view('whotofollow');
			view.setData(e);
		});
	})
	.catch(function () {
		console.log('Error loading suggestions');
	});
});
</script>

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

<script type="text/javascript">
depend(['m3/core/lysine'], function (Lysine) {
	
	var addOption = function () {
		
		var v = new Lysine.view('poll-create-option');

		v.getHTML().addEventListener('click', function (e) { e.stopPropagation(); })
		v.getHTML().querySelector('.poll-create-remove').addEventListener('click', function (v) {return function (e) { v.destroy(); e.stopPropagation(); }}(v))
	};
	
	document.getElementById('ping_poll').addEventListener('click', function (e) {
		for (var i = 0; i < 3; i++) {
			addOption();
		}
		
		document.getElementById('poll-dialog').style.display = 'block';
		document.getElementById('ping_poll').style.display = 'none';
		e.preventDefault();
		e.stopPropagation();
	});
	
	document.getElementById('poll-create-add').addEventListener('click', function (e) {
		addOption();
	});
});
</script>

<script type="text/javascript">
depend(['m3/core/delegate', 'm3/core/request', 'm3/core/collection', 'm3/core/parent'], function (delegate, request, collect, parent) {
	
	/*
	 * Delegation for the poll system. When the user clicks on a response to a poll,
	 * we transmit their selection to the server and update the UI.
	 */
	delegate('click', function (e) {
		/*
		 * Only register the click event when the user clicks on a poll response.
		 * As opposed to direct event listeners, the delegation will listen to all
		 * clicks and only perform an action when the element satisfies this condition.
		 */
		return e.classList.contains('poll-open-response');
	}, function (event, element) {
		/*
		 * Send the request to the server to update the selected option. If the call
		 * succeeds, we redraw the UI to reflect the change.
		 */
		request('<?= url('poll', 'vote') ?>' + element.getAttribute('data-option') + '.json').then(function() {
			var poll = parent(element, function (e) { return e.hasAttribute('data-poll'); });
			
			collect(poll.querySelectorAll('*[data-option]')).each(
				function (e) {
					e.classList.remove('selected-response');
				}
			);
 
			element.classList.add('selected-response');
		}).catch(function (e) {
			console.error(e);
		});
		event.preventDefault();
	} );
	
	delegate('click', function (e) {
		return e.classList.contains('like-link');
	}, function (event, element) {
		var url = element.classList.contains('like-active')? 
			'<?= url('feedback', 'revoke') ?>' + element.getAttribute('data-ping') + '.json' : 
			'<?= url('feedback', 'push') ?>' + element.getAttribute('data-ping') + '.json';
		
		request(url).then(function() {
			if (element.classList.contains('like-active')) {
				element.classList.remove('like-active');
				element.innerHTML = (parseInt(element.innerHTML) || 0) - 1;
			}
			else {
				element.classList.add('like-active'); 
				element.innerHTML = (parseInt(element.innerHTML) || 0) + 1;
			}
		}).catch(function (e) {
			console.error(e);
		});
		
		event.preventDefault();
	});
});
</script>
