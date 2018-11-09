<div class="media-preview">
	<?php $count = $media->count(); ?>
	<?php if ($count == 0): ?>
	<?php elseif ($count == 1): ?>
	<div class="row l1">
		<div class="span l1 ng">
			<?= $media[0]->preview('m')->getEmbed() ?>
		</div>
	</div>
	<?php elseif ($count == 2): ?>
	<?php elseif ($count == 3): ?>
	<div class="row l3 m3 s3">
		<div class="span l2 m2 s2 ng">
			<?= $media[0]->preview('t')->getEmbed() ?>
		</div>
		<div class="span l1 m1 s1 ng">
			<?= $media[1]->preview('t')->getEmbed() ?>
			<?= $media[2]->preview('t')->getEmbed() ?>
		</div>
	</div>
	<?php elseif ($count == 4): ?>
	<div class="row l2 m2 s2">
		<div class="span l1 m1 s1 ng">
			<?= $media[0]->preview('t')->getEmbed() ?>
			<?= $media[2]->preview('t')->getEmbed() ?>
		</div>
		<div class="span l1 m1 s1 ng">
			<?= $media[1]->preview('t')->getEmbed() ?>
			<?= $media[3]->preview('t')->getEmbed() ?>
		</div>
	</div>
	<?php endif; ?>
</div>