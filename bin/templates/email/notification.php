<body style="font-family: sans-serif">
	<div>
		<!--Header-->
		<div style="padding: 10px; font-weight: bold; color: #FFF; background: #5299cc">
			<div style="margin: 0 auto; max-width: 500px;">
				<img src="<?= spitfire\core\http\AbsoluteURL::asset('img/logo.png') ?>" height="24" style="vertical-align: middle">
				<?= spitfire\core\Environment::get('site.name')? : 'Ping' ?> - Notification
			</div>
		</div>
		
		<div style="height: 20px"></div>
		
		<div style="margin: 0 auto; max-width: 500px; background: #FFF;">
			
			<!--User badge-->
			<div style="padding: 10px 0px;">
				<img src="<?= $src? $src->getAvatar(128) : \spitfire\core\http\AbsoluteURL::asset('img/logo.png') ?>" style="border-radius: 50%; width: 24px; height: 24px; vertical-align: middle" width="24">
				<?= $src? $src->getDisplayName() : 'Someone' ?>
			</div>
			
			<!--Separator-->
			<div style="margin: 10px auto; border-top: solid 1px #CCC"></div>
			
			<!--Content-->
			<div style="padding: 10px 20px;">
				<p><?= $content ?></p>
				<p>&nbsp;</p>
				<p style="text-align: center">
					<?php if(!empty($url) && empty($media)): ?>
					<a href="<?= $url ?>" style="background: #3167f1; color: #FFF; border-radius: 5px; padding: 10px; text-decoration: none; font-weight: bold;">Go to the website</a>
					<?php elseif (!empty($media) && empty($url)): ?>
					<img src="<?= $media ?>" style="width: 100%">
					<?php elseif (!empty($media) && !empty($url)): ?>
					<a href="<?= $url ?>"><img src="<?= $media ?>" style="width: 100%"></a>
					<?php elseif(empty($url) && empty($media)): ?>
					<a href="<?= url('user', $src->user->displayName)->absolute() ?>" style="background: #3167f1; color: #FFF; border-radius: 5px; padding: 10px; text-decoration: none; font-weight: bold;">Reply to <?= $src->displayName ?></a>
					<?php endif; ?>
				</p>
			</div>
			
			<div style="height: 30px"></div>
			
			<p style="font-size: 12px; color: #555">
				Reply to this email to give us feedback or report issues.
			</p>
		</div>
	</div>
</body>