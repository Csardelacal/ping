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
					<a href="<?= new URL('feed') ?>">Feed <span data-ping-counter></span></a>
				</div>
				<div class="span1">
					<a href="<?= new URL('feed') ?>">Settings</a>
				</div>
				<div class="span1">
					<a href="<?= new URL('feed') ?>">
						<img src="<?= $authUser->avatar ?>" width="17"  style="margin-right: 5px; vertical-align: -3px">
						Logout
					</a>
				</div>
			</div>
		</div>
		
		<div class="spacer" style="height: 18px"></div>
		
		<?= $content_for_layout ?>
		
		<script type="text/javascript" src="<?= new URL('feed', 'counter.js') ?>"></script>
	</body>
</html>