<?php $share = $ping->share? $sso->getUser($ping->src->user->authId) : false; ?>
<?php $ping  = $ping->original(); ?>
<?php $user  = $ping->src->user ? $sso->getUser($ping->src->user->authId) : null; ?>
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
									<a href="<?= url('poll', 'vote', $option->_id) ?>" 
										data-option="<?= $option->_id ?>" 
										class="poll-open-response <?= $resp && $resp->option->_id == $option->_id ? 'selected-response' : '' ?>"> 
											<?= __($option->text ?: "Untitled") ?>
									</a>
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
						<a href="<?= url('ping', 'share', $ping->_id); ?>" class="ping-contextual-link for-shares">
							<i class="im im-sync"></i>
							<span><?= $ping->shared->getQuery()->count() ?: 'Share' ?></span>
						</a>
						<?php $reactions = \ping\Reaction::all() ?>
						<?php foreach ($reactions as $reaction): ?>
						
							<?php if (!$authUser): ?>
								<?= $reaction->getEmoji() ?>
								<span><?= strval(db()->table('feedback')->get('ping', $ping)->where('reaction', $reaction->getIdentifier())->where('removed', null)->count()) ?></span>
							<?php elseif (db()->table('feedback')->get('ping', $ping)->where('reaction', $reaction->getIdentifier())->where('author', AuthorModel::get(db()->table('user')->get('authId', $authUser->id)->first()))->where('removed', null)->first()): ?>
								<a href="<?= url('feedback', 'revoke', $ping->_id) ?>" class="ping-contextual-link for-likes liked" data-ping="<?= $ping->_id ?>">
									<?= $reaction->getEmoji() ?>
									<span><?= strval(db()->table('feedback')->get('ping', $ping)->where('reaction', $reaction->getIdentifier())->where('removed', null)->count()) ?></span>
								</a>
							<?php elseif (db()->table('feedback')->get('ping', $ping)->where('reaction', $reaction->getIdentifier())->where('removed', null)->first()): ?>
								<a href="<?= url('feedback', 'push', $ping->_id) ?>" class="ping-contextual-link for-likes" data-ping="<?= $ping->_id ?>">
									<?= $reaction->getEmoji() ?>
									<span><?= strval(db()->table('feedback')->get('ping', $ping)->where('reaction', $reaction->getIdentifier())->where('removed', null)->count())?></span>
								</a>
								<?php else: ?>
								<!-- The reaction was omitted due to not having any reactions of this type -->
								<noscript>
									<a href="<?= url('feedback', 'push', $ping->_id, ['reaction' => $reaction->getIdentifier(), 'returnto' => strval(spitfire\core\http\URL::current())]) ?>" class="ping-contextual-link for-likes" data-ping="<?= $ping->_id ?>">
										<?= $reaction->getEmoji() ?>
										<span><?= strval(db()->table('feedback')->get('ping', $ping)->where('reaction', $reaction->getIdentifier())->where('removed', null)->count())?></span>
									</a>
								</noscript>
							<?php endif; ?>
						<?php endforeach; ?>
						<a id="reactions-toggle-<?= $ping->_id ?>" href="#add-reaction" data-role="add-reaction" data-ping="<?= $ping->_id ?>">
							Add reaction
						</a>
						<div id="reactions-for-<?= $ping->_id ?>" style="display: none">
							<div class="reactions-container">
								<?php foreach ($reactions as $reaction): ?>
								<a href="<?= url('feedback', 'push', $ping->_id, ['reaction' => $reaction->getIdentifier(), 'returnto' => strval(spitfire\core\http\URL::current())]) ?>" class="ping-contextual-link for-likes" data-ping="<?= $ping->_id ?>" data-reaction="<?= $reaction->getIdentifier() ?>">
									<?= $reaction->getEmoji() ?>
								</a>
								<?php endforeach; ?>
							</div>
						</div>
						<script>
						(function () {
							window.addEventListener('load', function () {
								depend(['m3/core/delegate'], function (delegate) {
									delegate(
										'click', 
										function (e) { return e.dataset.role === 'add-reaction' && e.dataset.ping === '<?= $ping->_id ?>'; }, 
										function () {
											var clone = document.querySelector('#reactions-for-<?= $ping->_id ?> .reactions-container').cloneNode(true);
											console.log(clone);
											var drop = new Dropdown.Dropdown(clone, document.querySelector('#reactions-toggle-<?= $ping->_id ?>'));
											setTimeout(function () { drop.show(); }, 150);
										}
									);
								});
							});
						}())
						</script>
						<a href="<?= url('ping', 'delete', $ping->_id); ?>" data-visibility="<?= $share? $share->getUsername() : $user->getUsername() ?>" class="ping-contextual-link delete-link">
							<i class="im im-x-mark-circle"></i>
							<span>Delete</span>
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
