
<div class="spacer" style="height: 18px"></div>
		
<div class="row l4">
	<!--Sidebar (secondary navigation) -->
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
	</div>

	<!-- Main content-->
	<div class="span l2">
		<div class="material unpadded">
			
			<div class="spacer" style="height: 10px"></div>
			
			<?php foreach($notifications as $notification): ?>
			<?php $user = $sso->getUser($notification->src->authId); ?>
			<div class="padded">
				<div class="row l10 fluid">
					<div class="span l8">
						<span class="notification-avatar">
							<img src="<?= $user->getAvatar(64) ?>" style="width: 24px; border: solid 1px #777; border-radius: 50%; vertical-align: middle">
							<span class="activity-type <?= array_search($notification->type, NotificationModel::getTypesAvailable()) ?>"></span>
						</span>
						<a href="<?= url('user', $user->getUsername()) ?>" style="color: #000; font-weight: bold;"><?= ucfirst($user->getUsername()) ?></a>
						<?php if ($notification->url): ?><a href="<?= $notification->url ?>" style="color: #000;"><?php endif; ?>
						<?= Mention::idToMentions($notification->content) ?>
						<?php if ($notification->url): ?></a><?php endif; ?>
					</div>
					<div class="span l2 desktop-only" style="color: #666; font-size: .8em; text-align: right">
						<?= Time::relative($notification->created) ?>
					</div>
				</div>
			</div>
			
			<div class="separator"></div>
			<?php endforeach; ?>
			<?php if ($notifications->isEmpty()): ?>
			<div style="padding: 50px; text-align: center; color: #777; font-size: .8em; font-style: italic; text-align: center">
				Nothing here yet. Follow or interact with users to build your feed!
			</div>
			<?php endif; ?>
			
			<div data-lysine-view="ping">
				<div class="padded">
					<div class="row10 fluid">
						<div class="span8">
							<span class="notification-avatar">
								<img data-lysine-src="{{avatar}}" style="width: 24px; border: solid 1px #777; border-radius: 50%; vertical-align: middle">
								<span class="activity-type other" data-lysine-class="activity-type {{type}}"></span>
							</span>
							<a data-for="userName" data-lysine-href="{{userURL}}" style="color: #000; font-weight: bold;"></a>
							<a data-lysine-href="{{notificationURL}}" style="color: #000;" data-for="notificationContent"></a>
						</div>
						<div class="span2 desktop-only" style="color: #666; font-size: .8em; text-align: right" data-for="timeRelative"></div>
					</div>
				</div>
				
				<div class="separator"></div>
			</div>
		</div>
		
		<div class="spacer" style="height: 50px;"></div>
	</div>
	
	<!-- Contextual menu-->
	<div class="span1"></div>
</div>

<script type="text/javascript" src="<?= spitfire\core\http\URL::asset('js/lysine.js') ?>"></script>

<script type="text/javascript">
(function() {
	var xhr = null;
	var current = <?= isset($notification) && $notification? $notification->_id : 0 ?>;
	var notifications = [];
	
	var request = function (callback) {
		if (xhr !== null)  { return; }
		if (current === 0) { return; }
		
		xhr = new XMLHttpRequest();
		xhr.open('GET', '<?= url('activity')->setExtension('json') ?>?until=' + current);
		
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
						userName           : data.payload[i].user.username,
						avatar             : data.payload[i].user.avatar,
						userURL            : '<?= url('user') ?>/' + data.payload[i].user.username,
						notificationURL    : data.payload[i].url || '#',
						notificationContent: data.payload[i].content,
						timeRelative       : data.payload[i].timeRelative
					});
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
</script>

