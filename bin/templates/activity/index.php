
<div class="spacer" style="height: 18px"></div>
		
<div class="row l3">

	<!-- Main content-->
	<div class="span l2">
			
		
		<?php if ($notifications->isEmpty()): ?>
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
</div>

<script type="text/javascript">window.baseurl = '<?= url() ?>';</script>
<script type="text/javascript" src="<?= url() ?>/public/js/activity/index.js"></script>
