<!doctype html>
<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title><?= isset(${'page.title'}) && ${'page.title'}? ${'page.title'} : 'Ping - Notifications' ?></title>
		<link href="https://fonts.googleapis.com/css?family=Nunito+Sans" rel="stylesheet"> 
		<link type="text/css" rel="stylesheet" href="<?= \spitfire\core\http\URL::asset('css/app.css') ?>">
		
		<?php if (\spitfire\core\Environment::get('analytics.id')): ?>
		<script>
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

			ga('create', '<?= \spitfire\core\Environment::get('analytics.id') ?>', 'auto');
			ga('send', 'pageview');

		 </script>
		 <?php endif; ?>
		
	</head>
	<body>
		<!--Top most navigation-->
		<div class="main navigation">
			<div class="row7">
				<div class="span1 logo">
					<a class="menu-item" href="<?= url() ?>">
						<img src="<?= spitfire\core\http\URL::asset('img/logo.png') ?>" width="17" style="margin-right: 5px; vertical-align: -3px"> Ping
					</a>
				</div>
				<div class="span3 desktop-only"></div>
				<div class="span1">
					<?php if ($authUser): ?>
					<a class="menu-item" href="<?= url('feed') ?>">Feed <span class="badge" data-ping-counter data-ping-amt="0">?</span></a>
					<?php endif; ?>
				</div>
				<div class="span1">
					<?php if ($authUser): ?>
					<!--<a href="<?= url('settings') ?>">Settings</a>-->
					<?php endif; ?>
				</div>
				<div class="span1">
					<?php if ($authUser): ?>
					<a class="menu-item" href="<?= url('user', 'logout') ?>">
						<img src="<?= $authUser->avatar ?>" width="17"  style="margin-right: 5px; vertical-align: -3px">
						Logout
					</a>
					<?php else : ?>
					<a class="menu-item" href="<?= url('user', 'login') ?>">Login</a>
					<?php endif; ?>
				</div>
			</div>
		</div>
		
		<?= $content_for_layout ?>
		
		<script type="text/javascript" src="<?= url('feed', 'counter')->setExtension('js') ?>"></script>
	</body>
</html>