

<div class="spacer" style="height: 30px"></div>

<div class="row l5">
	<div class="span l1"></div>
	<div class="span l3">
		<div class="material">
			<form action="<?= url('ping', 'remove', $id, $salt) ?>" method="post">
			<h1>Confirm removal</h1>
			<p>
				Please click the button below to confirm removing this ping. Otherwise,
				feel free to navigate away from this page.
			</p>
			<p>
				After removal the content will only be visible to staff. Embedded media
				may still be accessible through direct links.
			</p>

			<p style="text-align: right">
				<button class="button" type="submit">Remove</button>
			</p>
			</form>
		</div>
	</div>
</div>
