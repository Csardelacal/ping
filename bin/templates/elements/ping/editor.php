	
<!-- 
This editor should be wrapped in noscript tags, to provide a fallback for 
users who have Javascript disabled in their browser. Or for users where the Lysine
based editor has issues starting up.

It should, therefore, be stripped of any Javascript.
-->
<form method="POST" action="<?= url('ping', 'push') ?>" enctype="multipart/form-data" class="ping-editor">
	<?php if (isset($target) && $target) : ?>
		<input type="hidden" name="target" value="<?= $target ?>">
	<?php endif; ?>
	<?php if (isset($irt) && $irt) : ?>
		<input type="hidden" name="irt" value="<?= $irt ?>">
	<?php endif; ?>

	<div class="padded add-ping">
		<div class="row l10">
			<div class="span l1 desktop-only" style="text-align: center">
				<img src="<?= $sso->getUser($authUser->id)->getAvatar(64) ?>" style="width: 100%; border: solid 1px #777; border-radius: 3px;">
			</div>
			<div class="span l9">
				<textarea name="content" class="new-ping-content" placeholder="Message to broadcast..."></textarea>
			</div>
		</div>

		<div>
			<div class="row l10"><!--
				--><div class="span l1">
					<!--Just a spacer-->
				</div><!--
				--><div class="span l4">
					<input type="file" name="media" class="ping_media">
				</div><!--
				--><div class="span l5" style="text-align: right">
					<input type="checkbox" name="explicit" value="true" id="explicit">
					<label for="explicit">mark explicit</label>
					<input type="submit" value="Ping!" class="send-ping">
				</div><!--
				--></div>
		</div>
	</div>
</form>


