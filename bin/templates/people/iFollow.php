
<div class="spacer" style="height: 18px"></div>

<div class="row l4">
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
	<div class="span l3">
		<?php $every = new Every(3, '</div><div class="spacer" style="height:30px;"></div><div class="row l3">'); ?>
		
		<div class="row l3">
			<?php foreach ($followers as $follower): ?>
			<?php $user = $sso->getUser($follower->user->authId); ?>
			<div class="span l1 material unpadded user-card">
				<a href="<?= url('user', $user->getUsername()) ?>">
					<div class="banner">
						<?php try { $banner = $user->getAttribute('banner')->getPreviewURL(320, 75) ?>
						<?php if (!$banner) { throw new Exception(); } ?>
						<img src="<?= $banner ?>" width="275" height="64">
						<?php } catch (Exception$e) { } ?>
					</div>
					<div class="padded" style="margin-top: -35px;">
						<img class="avatar" src="<?= $user->getAvatar(128) ?>">
						<div class="user-info">
							<span class="user-name"><?= $user->getUsername() ?></span>
							<span class="user-bio"><?php try { $bio = $user->getAttribute('bio'); ?><?=  __($bio, 30); ?><?php } catch(Exception$e) { ?><em>No bio provided</em><?php } ?></span>
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
