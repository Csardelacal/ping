<!doctype html>
<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title><?= isset(${'page.title'}) && ${'page.title'}? ${'page.title'} : 'Ping - Notifications' ?></title>
		<link href="https://fonts.googleapis.com/css?family=Nunito+Sans" rel="stylesheet"> 
		<link type="text/css" rel="stylesheet" href="<?= URL::asset('css/app.css') ?>">
	</head>
	<body>
		<!--Top most navigation-->
		<div class="main navigation">
			<div class="row7">
				<div class="span1 logo">
					<a class="menu-item" href="<?= new URL() ?>">
						<img src="<?= URL::asset('img/logo.png') ?>" width="17" style="margin-right: 5px; vertical-align: -3px"> Ping
					</a>
				</div>
				<div class="span3 desktop-only"></div>
				<div class="span1">
					<?php if ($authUser): ?>
					<a class="menu-item" href="<?= new URL('feed') ?>">Feed <span class="badge" data-ping-counter></span></a>
					<?php endif; ?>
				</div>
				<div class="span1">
					<?php if ($authUser): ?>
					<!--<a href="<?= new URL('settings') ?>">Settings</a>-->
					<?php endif; ?>
				</div>
				<div class="span1">
					<?php if ($authUser): ?>
					<a class="menu-item" href="<?= new URL('user', 'logout') ?>">
						<img src="<?= $authUser->avatar ?>" width="17"  style="margin-right: 5px; vertical-align: -3px">
						Logout
					</a>
					<?php else : ?>
					<a class="menu-item" href="<?= new URL('user', 'login') ?>">Login</a>
					<?php endif; ?>
				</div>
			</div>
		</div>
		
		<?= $content_for_layout ?>
		
		<script type="text/javascript" src="<?= new URL('feed', 'counter.js') ?>"></script>
	</body>
</html>