<!doctype html>
<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title><?= isset(${'page.title'}) && ${'page.title'}? ${'page.title'} : 'Ping - Notifications' ?></title>
		<link href="https://fonts.googleapis.com/css?family=Nunito+Sans" rel="stylesheet"> 
		<link href="https://cdn.iconmonstr.com/1.3.0/css/iconmonstr-iconic-font.min.css" rel="stylesheet"> 
		<link type="text/css" rel="stylesheet" href="<?= \spitfire\core\http\URL::asset('css/app.css') ?>">
		<meta name="_scss" content="<?= \spitfire\SpitFire::baseUrl() ?>/assets/scss/_/js/">
		<meta name="ping.endpoint" content="<?= rtrim(\spitfire\SpitFire::baseUrl(), '/') ?>/">
		<meta name="ping.token" content="none">
		<meta name="ping.id" content="none">
		
		
		<?php if ($authUser) : ?>
		<style type="text/css">
			*[data-visibility] { display: none; }
			*[data-visibility="<?= $authUser->username ?>"] { display: inline-block; }
		</style>
		<?php endif; ?>
		
	</head>
	<body>
		<script>
		/*
		 * This little script prevents an annoying flickering effect when the layout
		 * is being composited. Basically, since we layout part of the page with JS,
		 * when the browser gets to the JS part it will discard everything it rendered
		 * to this point and reflow.
		 * 
		 * Since the reflow MUST happen in order to render the layout, we can tell 
		 * the browser to not render the layout at all. This will prevent the layout
		 * from shift around before the user had the opportunity to click on it.
		 * 
		 * If, for some reason the layout was unable to start up within 500ms, we 
		 * let the browser render the page. Risking that the browser may need to 
		 * reflow once the layout is ready
		 */
		(function() {
			return;
			document.body.style.display = 'none';
			document.addEventListener('DOMContentLoaded', function () { document.body.style.display = null; }, false);
			setTimeout(function () { document.body.style.display = null; }, 500);
		}());
		</script>
		
		<!--Top most navigation-->
		<div class="navbar">
			<div class="left">
				<span class="toggle-button dark"></span>
			</div>
			<div class="right">
				<?php if(isset($authUser) && $authUser): ?>
					<div class="has-dropdown" style="display: inline-block">
						<a href="<?= url('user', $authUser->username) ?>" class="app-switcher" data-toggle="app-drawer">
							<img src="<?= $authUser->avatar ?>" width="32" height="32" style="border-radius: 50%; vertical-align: middle" >
						</a>
						<div class="dropdown right-bound unpadded" data-dropdown="app-drawer">
							<div class="app-drawer" id="app-drawer">
								<div class="navigation vertical">
									<a class="navigation-item" href="<?= url('settings')         ?>">Settings</a>
									<a class="navigation-item" href="<?= url('user', 'show', $authUser->username) ?>">My profile</a>
									<a class="navigation-item" href="<?= url('account', 'logout') ?>">Logout</a>
								</div>
							</div>
						</div>
					</div>
				<?php else: ?>
					<a class="menu-item" href="<?= url('account', 'login') ?>">Login</a>
				<?php endif; ?>
			</div>
			<div class="center">
				<a href="<?= url() ?>">
					<img src="<?= spitfire\core\http\URL::asset('img/logo.png') ?>" height="32px">
					<span class="desktop-only" style="vertical-align: .4rem">Ping</span>
				</a>
			</div>
		</div>
		
		<div class="auto-extend">
			
			<div class="content">
				<?= $this->content() ?>
			</div>
		</div>
		
		<!--Sidebar -->
		<div class="contains-sidebar">
			<div class="sidebar">
				<div class="navbar">
					<div class="left">
						<a href="<?= url() ?>">
							<img src="<?= spitfire\core\http\URL::asset('img/logo.png') ?>" width="17" style="margin-right: 5px; vertical-align: -3px"> Ping
						</a>
					</div>
				</div>

				<?php if(isset($authUser) && $authUser): ?>
				<div class="menu-title"> Account</div>
				<div class="menu-entry"><a href="<?= url() ?>"                  >Feed</a></div>
				<div class="menu-entry"><a href="<?= url('activity')         ?>">Activity <span class="notification-indicator" data-ping-activity data-ping-amt="0">?</span></a></div>
				<div class="menu-entry"><a href="<?= url('settings')         ?>">Settings</a></div>
				<?php else: ?>
				<div class="menu-title"> Account</div>
				<div class="menu-entry"><a href="<?= url('account', 'login') ?>"   >Login</a></div>
				<?php endif; ?>

				<div class="spacer" style="height: 10px"></div>

				<div class="menu-title">Our network</div>
				<div id="appdrawer"></div>
			</div>
		</div>
		
		<script type="text/javascript">
		document.addEventListener('DOMContentLoaded', function () {
			var ae = document.querySelector('.auto-extend');
			var wh = window.innerheight || document.documentElement.clientHeight;
			var dh = document.body.clientHeight;
			
			ae.style.minHeight = Math.max(ae.clientHeight + (wh - dh), 0) + 'px';
		});
		</script>
		
		<div style="display: none">
			<img style="max-width: 100%; margin: 0 auto; display: block; box-shadow: 0 0 10px #444;" id="preview-img" src="about:blank">
			<video style="max-width: 100%; margin: 0 auto; display: block;" loop autoplay id="preview-vid" src="about:blank"></video>
		</div>
		
		
		<div style="display: none">
			<div id="share-dialog" class="confirm">
				<a id="share-confirm-link" href="#">Share</a>
				<div id="share-processing" style="display: none;">
					<div style="text-align: center; padding: 1.5rem; color: #777;">
						<span class="spinner"></span> Sharing...
					</div>
				</div>
			</div>
		</div>
		
		<script type="text/javascript" src="<?= url() ?>/public/js/app.js"></script>
		<script type="text/javascript" src="<?= url('feed', 'counter')->setExtension('js')->setParam('nonce', 60 * (int)(time() / 60)) ?>"></script>
		
		<script src="https://cdn.jsdelivr.net/npm/m3w-dropdown@latest" type="text/javascript"></script>
	</body>
</html>
