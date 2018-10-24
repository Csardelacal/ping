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
			document.body.style.display = 'none';
			document.addEventListener('DOMContentLoaded', function () { document.body.style.display = null; }, false);
			setTimeout(function () { document.body.style.display = null; }, 500);
		}());
		</script>
		
		<!--Top most navigation-->
		<div class="navbar">
			<div class="row l1">
				<div class="span l1">
					<div class="left">
						<span class="toggle-button dark"></span>
						<a href="<?= url() ?>">
							<img src="<?= spitfire\core\http\URL::asset('img/logo.png') ?>" width="17" style="margin-right: 5px; vertical-align: -3px"> Ping
						</a>
					</div>
					<div class="right">
						<?php if(isset($authUser)): ?>
							<a class="menu-item" href="<?= url('settings') ?>">
								<img src="<?= $authUser->avatar ?>" width="17"  style="margin-right: 5px; vertical-align: -3px">
								Settings
							</a>
							<span class="h-spacer" style="display: inline-block; width: 10px;"></span>
							<a class="menu-item" href="<?= url('activity') ?>"><span class="badge" data-ping-activity data-ping-amt="0">?</span></a>
							<span class="h-spacer" style="display: inline-block; width: 10px;"></span>
							<div class="has-dropdown" style="display: inline-block">
								<span class="app-switcher toggle" data-toggle="app-drawer"></span>
								<div class="dropdown right-bound unpadded" data-dropdown="app-drawer">
									<div class="app-drawer" id="app-drawer"></div>
								</div>
							</div>
							<span class="h-spacer" style="display: inline-block; width: 20px;"></span>
						<?php else: ?>
							<a class="menu-item" href="<?= url('user', 'login') ?>">Login</a>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
		
		<div class="auto-extend" style="max-width: 71em; margin: 0 auto;">
			<?= $this->content() ?>
		</div>
		
		<script type="text/javascript">
		document.addEventListener('DOMContentLoaded', function () {
			var ae = document.querySelector('.auto-extend');
			var wh = window.innerheight || document.documentElement.clientHeight;
			var dh = document.body.clientHeight;
			
			ae.style.minHeight = Math.max(ae.clientHeight + (wh - dh), 0) + 'px';
		});
		</script>
		
		<script src="<?= spitfire\core\http\URL::asset('js/depend.js') ?>" type="text/javascript"></script>
		<script src="<?= spitfire\core\http\URL::asset('js/depend/router.js') ?>" type="text/javascript"></script>
		<script type="text/javascript" src="<?= spitfire\core\http\URL::asset('js/ui-layout.js') ?>"></script>
		
		<script type="text/javascript">
		(function () {
			depend(['depend/router'], function(router) {
				router.all().to(function(e) { return '<?= \spitfire\SpitFire::baseUrl() . '/assets/js/' ?>' + e + '.js'; });
				router.equals('phpas/app/drawer').to( function() { return '<?= $sso->getAppDrawerJS() ?>'; });
			});
			
			depend(['ui/dropdown'], function (dropdown) {
				dropdown('.app-switcher');
			});
			
			depend(['phpas/app/drawer'], function (drawer) {
				console.log(drawer);
			});
		}());
		</script>
		
		<script type="text/javascript">
			depend(['sticky'], function (sticky) {
				
				/*
				 * Create elements for all the elements defined via HTML
				 */
				var els = document.querySelectorAll('*[data-sticky]');

				for (var i = 0; i < els.length; i++) {
					sticky.stick(els[i], sticky.context(els[i]), els[i].getAttribute('data-sticky'));
				}
			});
		</script>
		
		<script type="text/javascript" src="<?= url('feed', 'counter')->setExtension('js')->setParam('nonce', 60 * (int)(time() / 60)) ?>" async="true"></script>
		<script type="text/javascript" src="<?= url('cron')->setExtension('js') ?>" async="true"></script>
	</body>
</html>
