
<div class="row4">
	<div class="span1">
		<?= $secondary_navigation ?>
	</div>
	<div class="span3">
		<?php $every = new Every('4', '</div><div class="row4">'); ?>
		
		<div class="row4">
			<?php foreach ($followers as $follower): ?>
			<?php $user = $sso->getUser($follower->authId); ?>
			<div class="span1 material">
				<img src="<?= $user->getAvatar(64) ?>" style="width: 32px;">
				<?= $user->getUsername() ?>
			</div>
			<?php endforeach; ?>
		</div>
		
		<?= $pagination ?>
	</div>
</div>

