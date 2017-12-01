<body style="font-family: sans-serif">
	<div>
		<!--Header-->
		<div style="padding: 10px; font-weight: bold; color: #FFF; background: #5299cc">
			<div style="margin: 0 auto; max-width: 500px;">
				<img src="<?= spitfire\core\http\AbsoluteURL::asset('img/logo.png') ?>" height="24" style="vertical-align: middle">
				<?= spitfire\core\Environment::get('site.name')? : 'Ping' ?> - Notification digest
			</div>
		</div>
		
		<div style="height: 20px"></div>
		
		<div style="margin: 0 auto; max-width: 500px; background: #FFF;">
			
			<!--User badge-->
			<div style="padding: 10px 0px;">
				<img src="<?= $tgt->getAvatar(64) ?>" style="border-radius: 50%; width: 24px; height: 24px; vertical-align: middle" width="24">
				<?= $tgt->getUsername() ?>
			</div>
			
			<!--Separator-->
			<div style="margin: 10px auto; border-top: solid 1px #CCC"></div>
			
			<!--Content-->
			<div style="padding: 10px 20px;">
				<p>This is your daily digest with the updates from the last 24 hours.</p>
				<p>&nbsp;</p>
				
				<?php foreach (NotificationModel::getTypesAvailable() as $name => $type): ?>
				<?php $u = db()->table('user')->get('_id', $tgt->getId()); ?>
				<?php $q = db()->table('email\digestqueue')->get('user', $u)->addRestriction('type', $type)->setResultsPerPage('6') ?>
				<?php if ($q->count() === 0) { continue; } ?>
				<div>
					<p><?= $q->count() ?> <?= $name ?>s</p>
					<div>
						<?php foreach($q->fetchAll() as $f): ?>
						<a href="<?= $f->notification->url? : '#' ?>"><img src="<?= $sso->getUser($f->notification->src->_id)->getAvatar(64) ?>" width="32"></a>
						<?php endforeach; ?>
					</div>
				</div>
				
				<div style="border-top: solid 1px #CCC; margin: 30px 0;"></div>
				<?php endforeach; ?>
			</div>
			
			<div style="height: 30px"></div>
			
			<p style="font-size: 12px; color: #555">
				Reply to this email to give us feedback or report issues.
			</p>
		</div>
	</div>
</body>