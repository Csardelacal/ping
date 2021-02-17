<?php $share = $ping->share? $sso->getUser($ping->src->user->authId) : false; ?>
<?php $ping  = $ping->original(); ?>
<?php $user  = $ping->src->user ? $sso->getUser($ping->src->user->authId) : null; ?>
<noscript>
	<div class="material unpadded">

		<?php if ($ping->irt): ?>
			<div class="source-ping" onclick="window.location = '<?= url('ping', 'detail', $ping->irt->_id) ?>'">
				<div class="row l10 fluid">
					<div class="span l1 desktop-only" style="text-align: center;">
						<img src="<?= $sso->getUser($ping->irt->src->user->authId)->getAvatar(64) ?>" style="width: 32px; border: solid 1px #777; border-radius: 3px;">
					</div>
					<div class="span l9">
						<a href="<?= url('user', 'show', $sso->getUser($ping->irt->src->user->authId)->getUsername()) ?>"  style="color: #000; font-weight: bold; font-size: .8em;">
							<?= $sso->getUser($ping->irt->src->user->authId)->getUsername() ?>
						</a>

						<p style="margin: 0;">
							<?= Mention::idToMentions($ping->irt->content) ?>
						</p>
					</div>
				</div>
			</div>
		<?php endif; ?>


		<div class="padded">


			<div class="row l10 fluid">
				<div class="span l1 desktop-only" style="text-align: center">
					<img src="<?= $user->getAvatar(64) ?>" style="width: 100%; border: solid 1px #777; border-radius: 3px;">
				</div>
				<div class="span l9">
					<div class="row l4 ng-lr">
						<div class="span l3">

							<img src="<?= $user->getAvatar(64) ?>" class="not-desktop" style="width: 32px; border-radius: 50%; vertical-align: middle">
							<a href="<?= url('user', 'show', $user->getUsername()) ?>" style="color: #000; font-weight: bold; font-size: .8em;"><?= $user->getUsername() ?></a>

							<?php if ($share): ?>
							<a href="<?= url('user', 'show', $share->getUsername()) ?>" style="color: #555; font-size: .8em;">
								shared by <?= $share->getUsername() ?> 
							</a>
							<?php endif; ?>
						</div>
						<div class="span l1 desktop-only" style="text-align: right; font-size: .8rem; color: #777;">
							<?= Time::relative($ping->created) ?>
						</div>
					</div>


					<div class="row l1 ng-lr fluid" style="margin-top: 5px">
						<div class="span l1">
							<p style="margin: 0;">
								<?= Mention::idToMentions($ping->content) ?>
							</p>

							<?php $poll = db()->table('poll\option')->get('ping__id', $ping->_id)->all() ?>
							<?php $resp = $authUser? db()->table('poll\reply')->get('ping__id', $ping->_id)->where('author__id', AuthorModel::find($authUser->id)->_id)->first() : null ?>
							<?php if ($poll->count() > 0): ?>
								<div data-poll="<?= $ping->_id ?>">
									<div class="spacer" style="height: 10px"></div>
									<?php foreach ($poll as $option): ?>
									<span ><?= __($option->text ?: "Untitled") ?></span>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>

							<div class="spacer" style="height: 10px"></div>

							<?php $media = $ping->attached; ?>
							<?= current_context()->view->element('media/preview')->set('media', collect($media->toArray()))->render() ?>

						</div>
					</div>

					<div class="spacer" style="height: 20px;"></div>

					<div class="row l3 fluid">
						<div class="span l2">
							<a href="<?= url('ping', 'detail', $ping->_id) ?>#replies" class="ping-contextual-link for-replies">
								<i class="im im-speech-bubble"></i>
								<span><?= strval(db()->table('ping')->get('irt__id', $ping->_id)->count()) ?></span>
							</a>
						</div>
						<div class="span l1" style="text-align: right">
							<p style="margin: 0;">
								<?php if ($ping->url): ?>
								<a href="<?= $ping->url ?>" class="ping-contextual-link">
									<span>Open</span>
									<i class="im im-external-link"></i>
								</a>
								<?php endif; ?>
							</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</noscript>
