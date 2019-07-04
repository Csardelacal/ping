<!doctype html>
<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title><?= isset(${'page.title'}) && ${'page.title'}? ${'page.title'} : 'Ping - Notifications' ?></title>
		<link href="https://fonts.googleapis.com/css?family=Nunito+Sans" rel="stylesheet"> 
		<link type="text/css" rel="stylesheet" href="<?= \spitfire\core\http\URL::asset('css/app.css') ?>">
		<meta name="_scss" content="<?= \spitfire\SpitFire::baseUrl() ?>/assets/scss/_/js/">
		<meta name="ping.endpoint" content="<?= \spitfire\SpitFire::baseUrl() ?>">
		
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
		
		<script src="<?= spitfire\core\http\URL::asset('js/m3/depend.js') ?>" type="text/javascript"></script>
		<script src="<?= spitfire\core\http\URL::asset('js/m3/depend/router.js') ?>" type="text/javascript"></script>
		
		<script type="text/javascript">
		(function () {
			depend(['m3/depend/router'], function(router) {
				var _SCSS = document.querySelector('meta[name="_scss"]').getAttribute('content') || '/assets/scss/_/js/';
				var ping  = document.querySelector('meta[name="ping.endpoint"]').getAttribute('content') || '/';
				
				router.all().to(function(e) { return ping + '/assets/js/' + e + '.js'; });
				router.equals('_scss').to( function() { return ping + '/assets/scss/_/js/_.scss.js'; });
				

				router.startsWith('_scss/').to(function(str) {
					return _SCSS + str.substring(6) + '.js';
				});
			});
		}());
		</script>
		
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
				<a href="<?= url() ?>">
					<img src="<?= spitfire\core\http\URL::asset('img/logo.png') ?>" width="17" style="margin-right: 5px; vertical-align: -3px">
				</a>
			</div>
			<div class="right">
				<?php if(isset($authUser) && $authUser): ?>
					<span class="h-spacer" style="display: inline-block; width: 10px;"></span>
					<div class="has-dropdown" style="display: inline-block">
						<a href="<?= url('user', $authUser->username) ?>" class="app-switcher" data-toggle="app-drawer">
							<img src="<?= $authUser->avatar ?>" width="24" height="24" style="border-radius: 50%; vertical-align: middle" >
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
					<span class="h-spacer" style="display: inline-block; width: 20px;"></span>
				<?php else: ?>
					<a class="menu-item" href="<?= url('user', 'login') ?>">Login</a>
				<?php endif; ?>
			</div>
		</div>
		
		<div class="auto-extend">
			
			<div class="content" data-sticky-context>
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
				<div class="menu-entry"><a href="<?= url('user', 'login') ?>"   >Login</a></div>
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
		<script type="text/javascript">
		(function () {
			depend(['ui/dropdown'], function (dropdown) {
				dropdown('.app-switcher');
			});
			
			depend(['_scss'], function() {
				console.log('Loaded _scss');
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
		
		<div style="display: none">
			<img style="max-width: 100%; vertical-align: top; margin: 0 auto; display: block; box-shadow: 0 0 10px #444;" id="preview-img" src="about:blank">
			<video style="max-width: 100%; vertical-align: top; margin: 0 auto; display: block;" loop autoplay id="preview-vid" src="about:blank"></video>
		</div>
		<script type="text/javascript">
			depend(['_scss/dialog', 'm3/core/delegate'], function (Dialog, delegate) {
				
				console.info('Gallery loaded');
				var dialogImg = new Dialog(document.getElementById('preview-img'), { transparent : true })
				var dialogVid = new Dialog(document.getElementById('preview-vid'), { transparent : true })
				
				delegate('click', function (e) {
					console.log(e);
					return e.hasAttribute('data-large');
				}, function (e) {
					if (this.tagName === 'VIDEO') {
						document.getElementById('preview-vid').src = this.getAttribute('data-large');
						dialogVid.show();
					}
					else {
						document.getElementById('preview-img').src = this.getAttribute('data-large');
						dialogImg.show();
					}
				});
			});
		</script>
		
		
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
		<script type="text/javascript">
			depend(['_scss/dialog', 'm3/core/delegate', 'm3/core/request'], function (Dialog, delegate, request) {
				
				console.info('Gallery loaded');
				var dialog = new Dialog(document.getElementById('share-dialog'))
				
				delegate('click', function (e) {
					console.log(e);
					return e.classList.contains('share-link');
				}, function (e) {
					document.getElementById('share-confirm-link').href = this.href;
					dialog.show();
					e.preventDefault();
				});
				
				document.getElementById('share-confirm-link').addEventListener('click', function (e) {
					document.getElementById('share-processing').style.display = 'block';
					document.getElementById('share-confirm-link').style.display = 'none';
					
					
					request(this.href).then(function () { 
						dialog.hide(); 
						document.getElementById('share-processing').style.display = 'none';
						document.getElementById('share-confirm-link').style.display = 'block';
					})
					e.preventDefault();
				});
			});
			
			/*
			 * Load the applications into the sidebar
			 */
			depend(['m3/core/request'], function (Request) {
				var request = new Request('<?= $sso->getEndpoint() ?>/appdrawer.json');
				request
					.then(JSON.parse)
					.then(function (e) {
						e.forEach(function (i) {
							console.log(i)
							var entry = document.createElement('div');
							var link  = entry.appendChild(document.createElement('a'));
							var icon  = link.appendChild(document.createElement('img'));
							entry.className = 'menu-entry';
							
							link.href = i.url;
							link.appendChild(document.createTextNode(i.name));
							
							icon.src = i.icon.m;
							document.getElementById('appdrawer').appendChild(entry);
						});
					})
					.catch(console.log);
			});
		</script>
		
		<script type="text/javascript" src="<?= url('feed', 'counter')->setExtension('js')->setParam('nonce', 60 * (int)(time() / 60)) ?>"></script>
	</body>
</html>
