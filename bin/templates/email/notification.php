<body style="font-family: sans-serif">
	<div style="padding: 30px">
		<div style="margin: 0 auto; max-width: 500px; background: #FFF;">
			<!--Header-->
			<div style="padding: 10px; font-weight: bold;">
				<img src="<?= absoluteURL::asset('img/logo.png') ?>">
				<?= spitfire\core\Environment::get('site.name')? : 'Ping' ?> - Notification
			</div>
			
			<div style="margin: 10px auto; border-top: solid 1px #CCC"></div>
			
			<!--User badge-->
			<div style="padding: 10px 20px;">
				<img src="<?= $src->getAvatar(64) ?>" style="border-radius: 50%; width: 16px; height: 16px; vertical-align: middle">
				<?= $src->getUsername() ?>
			</div>
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