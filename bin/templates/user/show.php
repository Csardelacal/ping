

<?php try { $banner = $user->getAttribute('banner')->getPreviewURL(1280, 300); ?>
<div id="banner">
	<img src="<?= $banner ?>">
</div>
<?php } catch(Exception$e) {} ?>

<div class="spacer" style="height: 18px"></div>

<div class="row5">
	<!--Sidebar (secondary navigation) -->
	<div class="span1">
		<div class="profile-resume desktop-only">
			<img class="avatar" src="<?= $user->getAvatar(256) ?>">
			<div class="spacer" style="height: 10px"></div>
			<div class="bio"><?php try { $bio = $user->getAttribute('bio'); ?><?=  nl2br(__($bio)); ?><?php } catch(Exception$e) { ?><em>No bio provided</em><?php } ?></div>
		</div>
	</div>

	<!-- Main content-->
	<div class="span3">
		<div class="material unpadded">
			<?php if (!$authUser): ?>
			<p style="color: #777; font-size: .8em; text-align: center; padding: 15px 20px">
				Log in to send <?= $user->getUsername() ?> a ping...
			</p>
			<?php elseif ($user->getId() !== $authUser->id): ?>
			<form method="POST" action="<?= new URL('notification', 'push', Array('returnto' => (string)new URL('user', 'show', $user->getUsername()))) ?>">
				<input type="hidden" name="target" value="<?= $user->getId() ?>">
				<div class="padded add-ping">
					<div>
						<div class="row1">
							<div class="span1">
								<textarea name="content" placeholder="Send ping to <?= $user->getUsername() ?>..."></textarea>
							</div>
						</div>
					</div>
					
					<div class="spacer" style="height: 10px"></div>
					
					<div>
						<div class="row2">
							<div class="span1">
								
							</div>
							<div class="span1" style="text-align: right">
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
			<?php $user = $sso->getUser($notification->src->authId); ?>
			<div class="padded" style="padding-top: 5px;">
				<div class="row10 fluid">
					<div class="span1 desktop-only" style="text-align: center">
						<img src="<?= $user->getAvatar(64) ?>" style="width: 100%; border: solid 1px #777; border-radius: 3px;">
					</div>
					<div class="span9">
						<div class="row4">
							<div class="span3">
								<a href="<?= new URL('user', 'show', $user->getUsername()) ?>" style="color: #000; font-weight: bold; font-size: .8em;"><?= $user->getUsername() ?></a>
							</div>
							<div class="span1 desktop-only" style="text-align: right; font-size: .8em; color: #777;">
								<?= Time::relative($notification->created) ?>
							</div>
						</div>
						<div class="row1" style="margin-top: 5px">
							<div class="span1">
								<p style="margin: 0;">
									<?php if ($notification->url && !$notification->media): ?><a href="<?= $notification->url ?>"><?php endif; ?>
									<?= Mention::idToMentions(Strings::strToHTML($notification->content)) ?>
									<?php if ($notification->url && !$notification->media): ?></a><?php endif; ?>
								</p>
								
								<?php if ($notification->media): ?>
								<div class="spacer" style="height: 20px"></div>
									<?php if ($notification->url): ?><a href="<?= $notification->url ?>"><?php endif; ?>
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
	<div class="span1">
		<a class="button follow" href="<?= new URL('user', 'login') ?>" data-ping-follow="<?= $user->getId() ?>">Login to follow</a>
	</div>
</div>

<script type="text/javascript" src="<?= URL::asset('js/banner.js') ?>"></script>
<script type="text/javascript" src="<?= URL::asset('js/follow_button.js') ?>"></script>
<script type="text/javascript">
(function () {
	window.ping.setBaseURL('<?= new URL(); ?>');
	window.ping.init();
}());
</script>
