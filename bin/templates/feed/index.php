
<div class="spacer" style="height: 18px"></div>
		
<div class="row5">
	<!--Sidebar (secondary navigation) -->
	<div class="span1">
		<?= $secondary_navigation ?>
	</div>

	<!-- Main content-->
	<div class="span3">
		<div class="material unpadded">
			<form method="POST" action="<?= new URL('notification', 'push') ?>">
				<div class="padded add-ping">
					<div>
						<div class="row1">
							<div class="span1">
								<textarea name="content" id="new-ping-content" placeholder="Message to broadcast..."></textarea>
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
								<input type="submit" value="Ping!">
							</div>
						</div>
					</div>
				</div>
			</form>
			
			<div class="separator"></div>
			
			<?php foreach($notifications as $notification): ?>
			<?php $user = $sso->getUser($notification->src->authId); ?>
			<div class="padded" style="padding-top: 5px;">
				<div class="row10 fluid">
					<div class="span1 desktop-only" style="text-align: center">
						<img src="<?= $user->getAvatar(64) ?>" style="width: 100%; border: solid 1px #777; border-radius: 3px;">
					</div>
					<div class="span9">
						<div class="row4">
							<div class="span3">
								<img class="mobile-only" src="<?= $user->getAvatar(64) ?>" style="width: 16px; border: solid 1px #777; border-radius: 3px; vertical-align: middle">
								<a href="<?= new URL('user', $user->getUsername()) ?>" style="color: #000; font-weight: bold; font-size: .8em;"><?= $user->getUsername() ?></a>
							</div>
							<div class="span1 desktop-only" style="text-align: right; font-size: .8em; color: #777;">
								<?= Time::relative($notification->created) ?>
							</div>
						</div>
						<div class="row1" style="margin-top: 5px">
							<div class="span1">
								<p style="margin: 0;">
									<?php if ($notification->url && !$notification->media): ?><a href="<?= $notification->url ?>" style="color: #000;"><?php endif; ?>
									<?= Mention::idToMentions($notification->content) ?>
									<?php if ($notification->url && !$notification->media): ?></a><?php endif; ?>
								</p>
								
								<?php if ($notification->media): ?>
								<div class="spacer" style="height: 20px"></div>
									<?php if ($notification->url): ?><a href="<?= $notification->url ?>" ><?php endif; ?>
									<img src="<?= $notification->media ?>" style="width: 100%">
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
								<div class="span1 desktop-only" style="text-align: right; font-size: .8em; color: #777;" data-for="timeRelative">
									
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
	<div class="span1"></div>
</div>

<script type="text/javascript" src="<?= URL::asset('js/lysine.js') ?>"></script>

<script type="text/javascript">
(function() {
	var xhr = null;
	var current = <?= $notification->_id ?>;
	var notifications = [];
	
	var request = function (callback) {
		if (xhr !== null)  { return; }
		if (current === 0) { return; }
		
		xhr = new XMLHttpRequest();
		xhr.open('GET', '<?= new URL('feed.json') ?>?until=' + current);
		
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

