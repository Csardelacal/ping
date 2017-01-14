
		
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
					<div class="span1" style="text-align: center">
						<img src="<?= $user->getAvatar(64) ?>" style="width: 100%; border: solid 1px #777; border-radius: 3px;">
					</div>
					<div class="span9">
						<div class="row4">
							<div class="span3">
								<a href="<?= new URL('user', 'show', $user->getUsername()) ?>" style="color: #000; font-weight: bold; font-size: .8em;"><?= $user->getUsername() ?></a>
							</div>
							<div class="span1" style="text-align: right; font-size: .8em; color: #777;">
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
		</div>
	</div>
	
	<!-- Contextual menu-->
	<div class="span1"></div>
</div>

<script type="text/javascript">
(function() {
	
	var listener = function() {
		document.querySelector('#new-ping-character-count').innerHTML = 250 - this.value.length;
	};
	
	document.querySelector('#new-ping-content').addEventListener('keyup', listener, false);
	
}());
</script>

