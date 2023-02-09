<!doctype html>
<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title><?= isset(${'page.title'}) && ${'page.title'}? ${'page.title'} : 'Ping - Notifications' ?></title>
		<link href="https://fonts.googleapis.com/css?family=Nunito+Sans" rel="stylesheet"> 
		<link type="text/css" rel="stylesheet" href="<?= \spitfire\SpitFire::baseUrl() ?>/public/css/app.css">
		<meta name="_scss" content="<?= \spitfire\SpitFire::baseUrl() ?>/assets/scss/_/js/">
		<meta name="ping.endpoint" content="<?= rtrim(\spitfire\SpitFire::baseUrl(), '/') ?>/">
		<meta name="ping.token" content="none">
		<meta name="ping.id" content="none">
		
		<script src="<?= spitfire\core\http\URL::asset('js/m3/depend.js') ?>" type="text/javascript"></script>
		<script src="<?= spitfire\core\http\URL::asset('js/m3/depend/router.js') ?>" type="text/javascript"></script>
		
		<script type="text/javascript">
		(function () {
			depend(['m3/depend/router'], function(router) {
				var _SCSS = document.querySelector('meta[name="_scss"]').getAttribute('content') || '/assets/scss/_/js/';
				var ping  = document.querySelector('meta[name="ping.endpoint"]').getAttribute('content') || '/';
				
				router.all().to(function(e) { return ping + 'assets/js/' + e + '.js'; });
				router.equals('_scss').to( function() { return ping + 'assets/scss/_/js/_.scss.js'; });
				

				router.startsWith('_scss/').to(function(str) {
					return _SCSS + str.substring(6) + '.js';
				});
			});
		}());
		</script>
		
		<script type="application/json" id="config">
			{
				"user": {
					"id" : <?= $authUser? $authUser->id : null ?>,
					"name" : <?= json_encode($authUser? $authUser->username : null) ?>,
					"avatar": <?= json_encode($authUser->avatar) ?>
				}
			}
		</script>
		
		<?php if ($authUser) : ?>
		<style type="text/css">
			*[data-visibility] { display: none; }
			*[data-visibility="<?= $authUser->username ?>"] { display: inline-block; }
		</style>
		<?php endif; ?>
		
	</head>
	<body>
		
		<!--Top most navigation-->
		<nav class="h-16 z-10 fixed w-full py-2 px-2 md:px-8">
		<div 
			class="h-16 flex flex-nowrap justify-between items-center p-2 pr-0 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-900 shadow-md rounded-lg mx-auto transition-all"
			:class="{'-translate-y-16' : hidden}"
			@mouseover="hidden=false"
			>
			<span class="toggle-button dark"></span>
			<a href="<?= url() ?>" class="flex items-center md:justify-start justify-center ml-4">
				<img src="<?= spitfire\core\http\URL::asset('img/logo.png') ?>" class="h-8">
				<span class="p-2 hidden md:inline">Ping</span>
			</a>
			
			<div class="grow justify-end overflow-x-auto overflow-y-hidden whitespace-nowrap text-right">
				
				<?php if (isset($authUser) && $authUser) : ?>
					<a class="py-2 px-3 hidden md:inline-block" href="<?= url() ?>"><strong>Feed</strong></a>
					<a class="py-2 px-3 hidden md:inline-block" href="<?= url('activity') ?>">Activity <span class="notification-indicator" data-ping-activity data-ping-amt="0">?</span></a>
					<User-Menu></User-Menu>
				<?php else : ?>
					<a class="py-2 px-3 inline-block" href="<?= url('account', 'login') ?>">Login</a>
				<?php endif; ?>
				<div class="inline-block w-3 h-full"></div>
			</div>
		</div>
		</nav>
		
		<div class="h-20"></div>
		
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

				<?php if (isset($authUser) && $authUser) : ?>
				<div class="menu-title"> Account</div>
				<div class="menu-entry"><a href="<?= url() ?>"                  >Feed</a></div>
				<div class="menu-entry"><a href="<?= url('activity')         ?>">Activity <span class="notification-indicator" data-ping-activity data-ping-amt="0">?</span></a></div>
				<div class="menu-entry"><a href="<?= url('settings')         ?>">Settings</a></div>
				<?php else : ?>
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
			<img style="max-width: 100%; margin: 0 auto; display: block; box-shadow: 0 0 10px #444;" id="preview-img" src="about:blank">
			<video style="max-width: 100%; margin: 0 auto; display: block;" loop autoplay id="preview-vid" src="about:blank"></video>
		</div>
		<script type="text/javascript">
			depend(['_scss/gallery', 'm3/core/delegate'], function (Gallery, delegate) {
				
				console.info('Gallery loaded');
				var gallery = new Gallery();
				
				delegate('click', function (e) {
					console.log(e);
					return e.hasAttribute('data-large');
				}, function (e) {
					console.log('here');
					if (this.tagName === 'VIDEO') {
						gallery.show(this.getAttribute('data-large'), 'video');
					}
					else {
						gallery.show(this.getAttribute('data-large'), 'image');
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
				
				var dialog = new Dialog(document.getElementById('share-dialog'))
				
				delegate('click', function (e) {
					console.log(e);
					return e.classList.contains('for-shares');
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
		
		<script type="text/javascript" src="<?= spitfire()->baseUrl() ?>/public/js/app.js"></script>
		<script type="text/javascript" src="<?= url('feed', 'counter')->setExtension('js')->setParam('nonce', 60 * (int)(time() / 60)) ?>"></script>
	</body>
</html>
