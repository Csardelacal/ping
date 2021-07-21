
<div class="spacer" style="height: 10px"></div> 

<div class="row l3">
	<!-- Main content-->
	<div class="span l2">

		<div class="material unpadded">
			<div class="editor"></div>
			<noscript>(Javascript is required to add a ping)</noscript>
		</div>

		<?php if (db()->table('ping')->get('src__id', AuthorModel::find($authUser->id)->_id)->where('processed', 0)->first()): ?>
			<div class="spacer" style="height: 10px"></div>

			<div class="material" style="color: #0571B1">
				<div class="row l1 fluid">
					<div class="span l1">
						Your latest ping is being processed...
					</div>
				</div>
			</div>

		<?php endif; ?>

		<div class="spacer" style="height: 10px"></div>

		<?php foreach ($notifications as $notification): ?>
			
			<?= current_context()->view->element('ping/ping')->set('ping', $notification)->render() ?>
			<div class="spacer" style="height: 10px"></div>

		<?php endforeach; ?>

		<?php if (empty($notifications)): ?>
			<div style="padding: 50px; text-align: center; color: #777; font-size: .8em; font-style: italic; text-align: center">
				Nothing here yet. Follow or interact with users to build your feed!
			</div>
		<?php endif; ?>
			
		<div class="spacer" style="height: 20px;"></div>
		<div class="row l3">
			<div class="span l1">
				<div style="color: #888; font-size: .8em">Users you may like to follow:</div>
			</div>
		</div>
		<div class="spacer" style="height: 10px;"></div>
		
		<div class="row l2 ng-lr">
			<template data-lysine-view="whotofollow">
				<div class="span l1">
					<div class="material unpadded user-card">
						<a data-lysine-href="<?= url('user', 'show', '{{username}}') ?>?ref=whotofollow">
							<div class="banner" style="height: 127px">
								<img src="about:blank" data-lysine-src="{{banner}}" width="275" height="64">
							</div>
							<div class="padded" style="margin-top: -35px;">
								<img class="avatar" data-lysine-src="{{avatar}}">
								<div class="user-info">
									<span class="user-name" data-for="username"></span>
									<span class="user-bio"><span data-for="followers"></span> followers</span>
								</div>
							</div>
						</a>
					</div>
				</div>
			</template>

		</div>
		<div class="spacer" style="height: 30px;"></div>

		
	</div>

	<!-- Contextual menu-->
	<div class="span l1">
		<div class="material unpadded user-card">
			<?php $user = $sso->getUser($authUser->id); ?>
			<a href="<?= url('user', 'show', $user->getUsername()) ?>">
				<div class="banner">
					<?php try { ?>
						<?php $banner = $user->getAttribute('banner')->getPreviewURL(320, 120) ?>
						<?php if (!$banner) { throw new Exception();	} ?>
						<img src="<?= $banner ?>" width="275" height="64">
					<?php } catch (Exception$e) {

					} ?>
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
		
		<div class="spacer" style="height: 500px"></div>
		<p class="very secondary small">
			Ping is developed by Magic3W and licensed under MIT License
			&centerdot;
			<a href="https://github.com/Csardelacal/Ping">Help and source code</a>
		</p>
	</div>
</div>

<script>
window.token = '<?= (isset($_GET['token']) ? $this->sso->makeToken($_GET['token']) : \spitfire\io\session\Session::getInstance()->getUser())->getId() ?>';
window.baseurl = '<?= spitfire()->baseUrl() ?>';
</script>

<script type="text/javascript" src="/ping/public/js/feed/index.js"></script>