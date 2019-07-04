<?php $user = $ping->src->user ? $sso->getUser($ping->src->user->authId) : null; ?>
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
				<div class="row l4">
					<div class="span l3">
						<img src="<?= $user->getAvatar(64) ?>" class="not-desktop" style="width: 32px; border-radius: 50%; vertical-align: middle">
						<a href="<?= url('user', 'show', $user->getUsername()) ?>" style="color: #000; font-weight: bold; font-size: .8em;"><?= $user->getUsername() ?></a>
						<?php if ($ping->share): ?>
							<a href="<?= url('ping', 'detail', $ping->share->_id) ?>" style="font-size: .8em; color: #777;"> from <?= $sso->getUser($ping->share->src->_id)->getUsername() ?></a>
						<?php endif; ?>
					</div>
					<div class="span l1 desktop-only" style="text-align: right; font-size: .8em; color: #777;">
						<?= Time::relative($ping->created) ?>
					</div>
				</div>


				<div class="row l1 fluid" style="margin-top: 5px">
					<div class="span l1">
						<p style="margin: 0;">
							<?= Mention::idToMentions($ping->content) ?>
						</p>

						<?php $poll = db()->table('poll\option')->get('ping', $ping->original())->all() ?>
						<?php $resp = $authUser? db()->table('poll\reply')->get('ping', $ping->original())->where('author', AuthorModel::get(db()->table('user')->get('authId', $authUser->id)->first()))->first() : null ?>
						<?php if ($poll->count() > 0): ?>
							<div data-poll="<?= $ping->_id ?>">
								<div class="spacer" style="height: 10px"></div>
								<?php foreach ($poll as $option): ?>
									<a href="<?= url('poll', 'vote', $option->_id) ?>" 
										data-option="<?= $option->_id ?>" 
										class="poll-open-response <?= $resp && $resp->option->_id == $option->_id ? 'selected-response' : '' ?>"> 
											<?= __($option->text ?: "Untitled") ?>
									</a>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>

						<div class="spacer" style="height: 10px"></div>

						<?php $media = $ping->original()->attached; ?>
						<?= current_context()->view->element('media/preview')->set('media', collect($media->toArray()))->render() ?>

					</div>
				</div>

				<div class="spacer" style="height: 20px;"></div>

				<div class="row l3 fluid">
					<div class="span l1">
						<p style="margin: 0;">
							<?php if ($ping->url): ?>
								<a href="<?= $ping->url ?>" style="font-weight: bold;">Open</a>
							<?php endif; ?>
						</p>
					</div>
					<div class="span l2" style="text-align: right">
						<?php if (!$authUser): ?>
						<?php elseif (db()->table('feedback')->get('ping', $ping)->where('author', AuthorModel::get(db()->table('user')->get('authId', $authUser->id)->first()))->first()): ?>
							<a href="<?= url('feedback', 'revoke', $ping->_id) ?>" class="like-link like-active" data-ping="<?= $ping->_id ?>"><?= db()->table('feedback')->get('ping', $ping)->count() ?: 'Like' ?></a>
						<?php else: ?>
							<a href="<?= url('feedback', 'push', $ping->_id) ?>" class="like-link" data-ping="<?= $ping->_id ?>"><?= db()->table('feedback')->get('ping', $ping)->count() ?: 'Like' ?></a>
						<?php endif; ?>
						<a href="<?= url('ping', 'detail', $ping->_id) ?>#replies" class="reply-link"><?= $ping->replies->getQuery()->count() ?: 'Reply' ?></a>
						<a href="<?= url('ping', 'share', $ping->_id); ?>" class="share-link"><?= $ping->original()->shared->getQuery()->count() ?: 'Share' ?></a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
