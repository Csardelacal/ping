<!doctype html>
<html>
	<head>
		<link type="text/css" rel="stylesheet" href="<?= URL::asset('css/app.css') ?>">
	</head>
	<body>
		<!--Top most navigation-->
		<div class="navbar">
			<div class="row7">
				<div class="span1">
					<a href="<?= new URL() ?>">
						<img src="<?= URL::asset('img/logo.png') ?>" width="17" style="margin-right: 5px; vertical-align: -3px"> Ping
					</a>
				</div>
				<div class="span3"></div>
				<div class="span1">
					<?php if ($authUser): ?>
					<a href="<?= new URL('feed') ?>">Feed <span class="badge" data-ping-counter></span></a>
					<?php endif; ?>
				</div>
				<div class="span1">
					<?php if ($authUser): ?>
					<!--<a href="<?= new URL('settings') ?>">Settings</a>-->
					<?php endif; ?>
				</div>
				<div class="span1">
					<?php if ($authUser): ?>
					<a href="<?= new URL('user', 'logout') ?>">
						<img src="<?= $authUser->avatar ?>" width="17"  style="margin-right: 5px; vertical-align: -3px">
						Logout
					</a>
					<?php else : ?>
					<a href="<?= new URL('user', 'login') ?>">Login</a>
					<?php endif; ?>
				</div>
			</div>
		</div>
		
		<div class="spacer" style="height: 18px"></div>
		
		<?= $content_for_layout ?>
		
		<script type="text/javascript" src="<?= new URL('feed', 'counter.js') ?>"></script>
	</body>
</html>