
<div class="profile <?= $authUser && $authUser->id === $user->user->_id? 'mine' : '' ?>">

	<?php if ($user->getBanner()): ?>
	<div id="page-banner">
		<img src="<?= $user->getBanner() ?>">
	</div>
	<?php endif; ?>

	<div class="spacer" style="height: 18px"></div>

	<div class="row l5" id="feed">
		<!--Sidebar (secondary navigation) -->
		<div class="span l1" style="position: sticky; top: 0">
			<div class="profile-resume desktop-only">
				<div class="spacer" style="height: 10px"></div>
				<a href="<?= url('user', $user->getUsername()) ?>"><img class="avatar" src="<?= $user->getAvatar(256) ?>"></a>
				<div class="spacer" style="height: 10px"></div>
				<a href="<?= url('user', $user->getUsername()) ?>"><span class="user-name"><?= $user->getUsername() ?></span></a>
				<div class="spacer" style="height: 10px"></div>
				<div class="bio"><?=  __($user->getBio()?: 'No bio provided'); ?></div>
				
				<div class="spacer" style="height: 50px"></div>
				
				<span class="follower-count"><a href="<?= url('people', 'following', $user->getUsername()) ?>"><strong><?= db()->table('follow')->get('prey', $user)->count() ?></strong> followers</a></span>
				<span class="follow-count"><a href="<?= url('people', 'follows', $user->getUsername()) ?>"><strong><?= db()->table('follow')->get('follower', $user)->count() ?></strong> follows</a></span>
				<span class="ping-count"><strong><?= db()->table('ping')->get('src', $user)->addRestriction('target__id', null, 'IS')->count() ?></strong> posts</span>
				
				<!-- Add link to the different feedback the user gave on the platform -->
			</div>
			
			<div class="material unpadded user-card mobile-only">
				<div class="banner" style="height: 47px">
					<?php if ($user->getBanner()): ?>
					<img src="<?= $user->getBanner() ?>" width="275" height="64">
					<?php endif; ?>
				</div>
				<div class="padded" style="margin-top: -35px;">
					<img class="avatar" src="<?= $user->getAvatar(256) ?>">
					<div class="user-info">
						<a href="<?= url('user', $user->getUsername()) ?>"><span class="user-name"><?= $user->getUsername() ?></span></a>
						<div class="user-bio">
							<a href="<?= url('people', 'following', $user->getUsername()) ?>"><?= db()->table('follow')->get('prey', $user)->count() ?></a> followers
							<a href="<?= url('people', 'follows', $user->getUsername()) ?>"><?= db()->table('follow')->get('follower', $user)->count() ?></a> follows
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Main content-->
		<div class="span l3">
			<div class="mobile-only" style="padding: 20px 0; text-align: right">
				<a class="button follow" href="<?= url('account', 'login') ?>" data-ping-follow="<?= $user->_id ?>">Login to follow</a>
			</div>
			<div class="material unpadded">
				<?php if (!$authUser): ?>
				<p style="color: #777; font-size: .8em; text-align: center; padding: 15px 20px">
					Log in to send <?= $user->getUsername() ?> a ping...
				</p>
				<?php elseif ($user->_id !== $authUser->id): ?>
					
				<?= current_context()->view->element('ping/editor.lysine.html')->set('target', ':' . $author->guid)->render() ?>
				<?php else: ?>

				<p style="color: #777; font-size: .8em; text-align: center; padding: 15px 20px">
					This is your own profile. You cannot send notifications to yourself.
				</p>

				<?php endif; ?>
			</div>
			
			<div class="spacer" style="height: 30px"></div>
			
			<?php foreach($notifications as $notification): ?>
			
			<?= current_context()->view->element('ping/ping')->set('ping', $notification)->render() ?>
			<div class="spacer" style="height: 10px"></div>

			<?php endforeach; ?>

			<div id="feed">
				<?= current_context()->view->element('ping/ping.lysine.html')->render() ?>
			</div>
			
			<div id="loading-spinner" style="text-align: center; padding: 1.5rem; color: #777; display: none;">
				<span class="spinner"></span> Loading more...
			</div>
			
			<div id="end-of-feed" style="text-align: center; padding: 1.5rem; color: #777; display: none;">
				<img src="<?= \spitfire\core\http\URL::asset('img/end-of-feed.png') ?>" style="height: .9rem"> That's it!
			</div>
		</div>

		<!-- Contextual menu-->
		<div class="span l1 desktop-only">
			<a class="button follow" href="<?= url('account', 'login') ?>" data-ping-follow="<?= $user->_id ?>">Login to follow</a>
		</div>
	</div>
</div>

<script type="text/javascript" src="<?= \spitfire\core\http\URL::asset('js/follow_button.js') ?>"></script>
<script type="text/javascript">
(function () {
	window.ping.setBaseURL('<?= url(); ?>');
	window.ping.init();
}());
</script>

<script type="text/javascript" src="<?= \spitfire\core\http\URL::asset('js/lysine.js') ?>"></script>

<script type="text/javascript" src="<?= asset('js/user/show.js') ?>"></script>
