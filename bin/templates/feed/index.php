
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
			
			<?= current_context()->view->element('ping/ping')->set('ping', $notification)->render() ?>
			<div class="spacer" style="height: 10px"></div>

		<?php endforeach; ?>

		<?php if (empty($notifications)): ?>
			<div style="padding: 50px; text-align: center; color: #777; font-size: .8em; font-style: italic; text-align: center">
				Nothing here yet. Follow or interact with users to build your feed!
			</div>
		<?php endif; ?>

		
		<?= current_context()->view->element('ping/ping.lysine.html')->set('ping', $notification)->render() ?>
	</div>

	<!-- Contextual menu-->
	<div class="span l1">
		<div class="material unpadded user-card">
			<?php $user = $sso->getUser($authUser->id); ?>
			<a href="<?= url('user', $user->getUsername()) ?>">
				<div class="banner">
					<?php try { ?>
						<?php $banner = $user->getAttribute('banner')->getPreviewURL(320, 120) ?>
						<?php if (!$banner) { throw new Exception();	} ?>
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

		p.ping().author('@patch', function (list) {
			console.log(list);
			list._next();
		});
	});
</script>


<script type="text/javascript">
depend(['ping/editor'], function (editor) {
	console.log('editor.loaded');
});
</script>