
<div class="spacer" style="height: 10px"></div> 

<div class="row l3">
	<!-- Main content-->
	<div class="span l2">

		<div class="material unpadded">
			<?= current_context()->view->element('ping/editor')->render() ?>
		</div>

		<?php if (db()->table('ping')->get('src', db()->table('author')->get('user', db()->table('user')->get('_id', $authUser->id)))->where('processed', 0)->first()): ?>
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

		<?php foreach ($notifications as $notification): ?>
			<?php $user = $notification->src->user ? $sso->getUser($notification->src->user->authId) : null; ?>
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

									<?php $poll = db()->table('poll\option')->get('ping', $notification->original())->all() ?>
									<?php $resp = db()->table('poll\reply')->get('ping', $notification->original())->where('author', AuthorModel::get(db()->table('user')->get('authId', $authUser->id)->first()))->first() ?>
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
											<a href="<?= $notification->url ?>" style="font-weight: bold;"><?= __($notification->url, 50) ?></a>
										<?php endif; ?>
									</p>
								</div>
								<div class="span l1" style="text-align: right">
									<?php if (db()->table('feedback')->get('ping', $notification)->where('author', AuthorModel::get(db()->table('user')->get('authId', $authUser->id)->first()))->first()): ?>
										<a href="<?= url('feedback', 'revoke', $notification->_id) ?>" class="like-link like-active" data-ping="<?= $notification->_id ?>"><?= db()->table('feedback')->get('ping', $notification)->count() ?: 'Like' ?></a>
									<?php else: ?>
										<a href="<?= url('feedback', 'push', $notification->_id) ?>" class="like-link" data-ping="<?= $notification->_id ?>"><?= db()->table('feedback')->get('ping', $notification)->count() ?: 'Like' ?></a>
									<?php endif; ?>
									<a href="<?= url('ping', 'detail', $notification->_id) ?>#replies" class="reply-link"><?= $notification->replies->getQuery()->count() ?: 'Reply' ?></a>
									<a href="<?= url('ping', 'share', $notification->_id); ?>" class="share-link"><?= $notification->original()->shared->getQuery()->count() ?: 'Share' ?></a>
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
					<?php try {
						$banner = $user->getAttribute('banner')->getPreviewURL(320, 120) ?>
						<?php if (!$banner) {
							throw new Exception();
						} ?>
						<img src="<?= $banner ?>" width="275" height="64">
<?php } catch (Exception$e) {
	
} ?>
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

<script type="text/javascript">
	depend(['m3/core/lysine', 'ping/ping'], function (lysine, Ping) {
		
		var nextPage = null;
		var token = '<?= (isset($_GET['token']) ? $this->sso->makeToken($_GET['token']) : \spitfire\io\session\Session::getInstance()->getUser())->getId() ?>';
		var ping = new Ping('<?= spitfire()->baseUrl() ?>', token);
		
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

			if (height() - scroll < html.clientHeight + 700) {
				nextPage();
				nextPage = null;
			}
		};
		
		ping.feed().read(function(pingList) {
			
			for (var i = 0; i < pingList._pings.length; i++) {
				
				var view = new lysine.view('ping');
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
					replyCount: current.replies || 'Reply',
					shareCount: current.shares || 'Share',
					irt: current.irt ? [current.irt] : []
				});

				if (!current.media) {
					var child = view.getHTML().querySelector('.media');
					child.parentNode.removeChild(child);
				}

				if (!current.irt) {
					var child = view.getHTML().querySelector('.irt');
					child.parentNode.removeChild(child);
				}
				
			}
			
			nextPage = pingList._next;
		}, <?= isset($notification) && $notification? $notification->_id : 0 ?>);

		//Attach the listener
		document.addEventListener('scroll', listener, false);
	});


	depend(['m3/core/request', 'm3/core/array/iterate', 'm3/core/lysine'], function (request, iterate, lysine) {

		request('<?= url('people', 'whoToFollow')->setExtension('json') ?>')

				  .then(function (response) {
					  var json = JSON.parse(response).payload;

					  iterate(json, function (e) {
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
			request('<?= url('poll', 'vote') ?>' + element.getAttribute('data-option') + '.json').then(function () {
				var poll = parent(element, function (e) {
					return e.hasAttribute('data-poll');
				});

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
		});

		delegate('click', function (e) {
			return e.classList.contains('like-link');
		}, function (event, element) {
			var url = element.classList.contains('like-active') ?
					  '<?= url('feedback', 'revoke') ?>' + element.getAttribute('data-ping') + '.json' :
					  '<?= url('feedback', 'push') ?>' + element.getAttribute('data-ping') + '.json';

			request(url).then(function () {
				if (element.classList.contains('like-active')) {
					element.classList.remove('like-active');
					element.innerHTML = (parseInt(element.innerHTML) || 0) - 1;
				} else {
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


<script type="text/javascript">
	depend(['ping/ping'], function (ping) {
		var p = new ping('<?= spitfire()->baseUrl() ?>', '<?= (isset($_GET['token']) ? $this->sso->makeToken($_GET['token']) : \spitfire\io\session\Session::getInstance()->getUser())->getId() ?>');
		p.ping().get(265, function (e) {
			console.log(e);
			return e;
		});

		p.ping().author('patch', function (list) {
			console.log(list);
			list._next();
		});
	});
</script>