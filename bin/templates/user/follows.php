
<?php try { $banner = $user->getAttribute('banner')->getPreviewURL(1280, 300); ?>
<div id="banner">
	<img src="<?= $banner ?>">
</div>
<?php } catch(Exception$e) {} ?>

<div class="spacer" style="height: 18px"></div>

<div class="row l4">
	
	<!--Sidebar (secondary navigation) -->
	<div class="span l1">
		<div class="profile-resume desktop-only">
			<a href="<?= url('user', $user->getUsername()) ?>"><img class="avatar" src="<?= $user->getAvatar(256) ?>"></a>
			<div class="spacer" style="height: 10px"></div>
			<a href="<?= url('user', $user->getUsername()) ?>"><span class="user-name"><?= $user->getUsername() ?></span></a>
			<div class="spacer" style="height: 10px"></div>
			<div class="bio"><?php try { $bio = $user->getAttribute('bio'); ?><?=  nl2br(__($bio)); ?><?php } catch(Exception$e) { ?><em>No bio provided</em><?php } ?></div>

			<div class="spacer" style="height: 50px"></div>

			<span class="follower-count"><a href="<?= url('user', 'following', $user->getUsername()) ?>"><strong><?= db()->table('follow')->get('prey__id', $user->getId())->count() ?></strong> followers</a></span>
			<span class="follow-count"><a href="<?= url('user', 'follows', $user->getUsername()) ?>"><strong><?= db()->table('follow')->get('follower__id', $user->getId())->count() ?></strong> follows</a></span>
			<span class="ping-count"><strong><?= db()->table('ping')->get('src__id', $user->getId())->addRestriction('target__id', null, 'IS')->count() ?></strong> posts</span>
		</div>

		<div class="material unpadded user-card mobile-only">
			<div class="banner" style="height: 47px">
				<?php try { $banner = $user->getAttribute('banner')->getPreviewURL(320, 75) ?>
				<?php if (!$banner) { throw new Exception(); } ?>
				<img src="<?= $banner ?>" width="275" height="64">
				<?php } catch (Exception$e) { } ?>
			</div>
			<div class="padded" style="margin-top: -35px;">
				<img class="avatar" src="<?= $user->getAvatar(128) ?>">
				<div class="user-info">
					<a href="<?= url('user', $user->getUsername()) ?>"><span class="user-name"><?= $user->getUsername() ?></span></a>
					<div class="user-bio">
						<a href="<?= url('user', 'following', $user->getUsername()) ?>"><?= db()->table('follow')->get('prey__id', $user->getId())->count() ?></a> followers
						<a href="<?= url('user', 'follows', $user->getUsername()) ?>"><?= db()->table('follow')->get('follower__id', $user->getId())->count() ?></a> follows
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<div class="span l3">
		<?php $every = new Every(3, '</div><div class="spacer" style="height:30px;"></div><div class="row3">'); ?>
		
		<div class="row l3">
			<?php foreach ($followers as $follower): ?>
			<?php $user = $sso->getUser($follower->authId); ?>
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

<script type="text/javascript" src="<?= \spitfire\core\http\URL::asset('js/banner.js') ?>"></script>