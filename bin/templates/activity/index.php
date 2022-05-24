
<div class="spacer" style="height: 18px"></div>
		
<div class="row l3">

	<!-- Main content-->
	<div class="span l2">
			
		
		<?php foreach ($notifications as $notification) : ?>
			<?php try {
				$user = $notification->src? $sso->getUser($notification->src->user->_id) : null;
			} catch (\Exception$e) {
				$user = null;
			} ?>
		<div class="material">
			<div class="row l10 fluid">
				<div class="span l1" style="text-align: center">
					<div class="spacer" style="height: 5px"></div>
					<a href="<?= $user? url('user', $user->getUsername()) : '#' ?>" class="notification-avatar">
						<img src="<?= $user? $user->getAvatar(64) : \spitfire\core\http\URL::asset('img/logo.png') ?>" style="width: 32px; border: solid 1px #777; border-radius: 50%; vertical-align: middle">
						<span class="activity-type <?= array_search($notification->type, NotificationModel::getTypesAvailable()) ?>"></span>
					</a>
				</div>
				<div class="span l7">

					<div>
						<span style="color: #555; font-size: .8rem;"><?= $user? ucfirst($user->getUsername()) : 'Someone' ?></span>
					</div>

					<div>
			<?php if ($notification->url) :
				?><a href="<?= $notification->url ?>" style="color: #000;  padding: .2rem 0"><?php
			endif; ?>
			<?= Mention::idToMentions($notification->content) ?>
			<?php if ($notification->url) :
				?></a><?php
			endif; ?>
					</div>
				</div>
				<div class="span l2 desktop-only" style="color: #666; font-size: .8rem; text-align: right">
			<?= Time::relative($notification->created) ?>
				</div>
			</div>
		</div>

		<div class="spacer" style="height: 10px"></div>
		<?php endforeach; ?>
		<?php if ($notifications->isEmpty()) : ?>
		<div style="padding: 50px; text-align: center; color: #777; font-size: .8rem; font-style: italic; text-align: center">
			Nothing here yet. Follow or interact with users to build your feed!
		</div>
		<?php endif; ?>
		
		<template data-lysine-view="ping">
			<div>
				<div class="material">
					<div class="row l10 fluid">
						<div class="span l1" style="text-align: center">
							<div class="spacer" style="height: 5px"></div>
							<a data-lysine-href="{{userURL}}" class="notification-avatar">
								<img data-lysine-src="{{avatar}}" style="width: 32px; border: solid 1px #777; border-radius: 50%; vertical-align: middle">
								<span class="activity-type other" data-lysine-class="activity-type {{type}}"></span>
							</a>
						</div>
						<div class="span l7">
							<div>
								<a data-for="userName" data-lysine-href="{{userURL}}" style="color: #555; font-size: .8rem;"></a>
								<span data-for="userName" style="color: #555; font-size: .8rem;"></span>
							</div>
							<div>
								<a data-lysine-href="{{notificationURL}}" style="color: #000; padding: .2rem 0" data-for="notificationContent"></a>
							</div>
						</div>
						<div class="span l2 desktop-only" style="color: #666; font-size: .8rem; text-align: right" data-for="timeRelative"></div>
					</div>
				</div>

				<div class="spacer" style="height: 10px;"></div>
			</div>
		</template>
		
		<div class="spacer" style="height: 50px;"></div>
	</div>
	
	<!--Sidebar (secondary navigation) -->
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
			nextPage && nextPage();
			nextPage = null;
		}
	};
	
	console.log(ping.activity());

	ping.activity().read(function(pingList) {
		
		console.log(pingList);
		for (var i = 0; i < pingList._pings.length; i++) {

			var view = new lysine.view('ping');
			var data = pingList._pings[i];

			/*
			 * This block should be possible to have refactored out of the feed,
			 * making it less pointless code that adapts stuff around.
			 */
			view.setData({
				userName           : data.user.username,
				avatar             : data.user.avatar,
				userURL            : data.user.id? '<?= url('user') ?>/' + data.user.username : '#',
				notificationURL    : data.url || '#',
				notificationContent: data.content,
				timeRelative       : data.timeRelative
			});

		}

		nextPage = pingList._next;
	}, <?= isset($notification) && $notification? $notification->_id : 0 ?>);

	//Attach the listener
	document.addEventListener('scroll', listener, false);
});

</script>
