
<div class="row5">
	<div class="span1">
		<?= $secondary_navigation ?>
	</div>
	<div class="span4">
		<?php $every = new Every(3, '</div><div class="row3">'); ?>
		
		<div class="row3">
			<?php foreach ($followers as $follower): ?>
			<?php $user = $sso->getUser($follower->authId); ?>
			<div class="span1 material unpadded user-card">
				<a href="<?= new URL('user', 'show', $user->getUsername()) ?>">
					<div class="banner">
						<?php try { $banner = $user->getAttribute('banner')->getPreviewURL(320, 75) ?>
						<img src="<?= $banner ?>">
						<?php } catch (Exception$e) { } ?>
					</div>
					<div class="padded" style="margin-top: -35px;">
						<img class="avatar" src="<?= $user->getAvatar(128) ?>">
						<div class="user-info">
							<span class="user-name"><?= $user->getUsername() ?></span>
							<span class="user-bio">This is a test bio</span>
						</div>
					</div>
				</a>
			</div>
			<?= $every->next() ?>
			<?php endforeach; ?>
		</div>
		
		<?= $pagination ?>
	</div>
</div>
