	
<form method="POST" action="<?= url('ping', 'push') ?>" enctype="multipart/form-data" id="ping-editor">
	<?php if (isset($target) && $target): ?>
		<input type="hidden" name="target" value="<?= $target ?>">
	<?php endif; ?>
	<?php if (isset($irt) && $irt): ?>
		<input type="hidden" name="irt" value="<?= $irt ?>">
	<?php endif; ?>

	<div class="padded add-ping">
		<div class="row l10">
			<div class="span l1 desktop-only" style="text-align: center">
				<img src="<?= $sso->getUser($authUser->id)->getAvatar(64) ?>" style="width: 100%; border: solid 1px #777; border-radius: 3px;">
			</div>
			<div class="span l9">
				<textarea name="content" id="new-ping-content" placeholder="Message to broadcast..."></textarea>

				<div class="spacer" style="height: 10px"></div>

				<div class="row l5 m4 s4 fluid">

					<div class="span l1 m1 s1" data-lysine-view="file-upload-preview" >
						<div style="text-align: center; height: 100%; border: solid 1px #DDD; border-radius: 3px; overflow: hidden; position: relative">
							<img style="vertical-align: middle" data-lysine-src="{{source}}" class="upload-preview">
							<input type="hidden" name="media[]" value="" data-for="id">
							<a class="remove-media" href="#" style="color: #FFF; font-weight: bold; font-size: 1.8em; position: absolute; top: -5px; right: 5px; text-shadow: 0 0 8px rgba(0, 0, 0, .7); line-height: 1em; ">&times;</a>
						</div>
					</div>
				</div>
			</div>
		</div>


		<div class="spacer" style="height: 10px"></div>

		<div class="row l10" id="poll-dialog" style="display: none">
			<div class="span l1"></div>
			<div class="span l9">
				<div data-lysine-view="poll-create-option">
					<div class="row l5 m4 s4 fluid">
						<div class="span l4 m3 s3">
							<input type="text" name="poll[]" placeholder="Option..." style="width: 100%; border: none; border-bottom: solid 1px #ccc; padding: 3px;">
						</div>
						<div class="span l1 m1 s1">
							<a href="#remove-poll" class="poll-create-remove">Remove</a>
						</div>
					</div>
				</div>

				<div class="row l5 m4 s4 fluid">
					<div class="span l4 m3 s3">
						<a href="#add-poll" id="poll-create-add">Add option</a>
					</div>
				</div>
			</div>

			<div class="spacer" style="height: 10px"></div>
		</div>

		<div>
			<div class="row l10"><!--
				--><div class="span l1">
					<!--Just a spacer-->
				</div><!--
				--><div class="span l4">
					<input type="file" id="ping_media" style="display: none">
					<img src="<?= spitfire\core\http\URL::asset('img/camera.png') ?>" id="ping_media_selector" style="vertical-align: middle; height: 24px; opacity: .5; margin: 0 5px;">
					<img src="<?= spitfire\core\http\URL::asset('img/poll.png') ?>" id="ping_poll" style="vertical-align: middle; height: 24px; opacity: .3; margin: 0 5px;">
				</div><!--
				--><div class="span l5" style="text-align: right">
					<span id="new-ping-character-count">250</span>
					<input type="submit" value="Ping!" id="send-ping">
				</div><!--
				--></div>
		</div>
	</div>
</form>


