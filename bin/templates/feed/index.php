
<?php var_dump($authUser); ?>

		
<div class="row5">
	<!--Sidebar (secondary navigation) -->
	<div class="span1">
		<?= $secondary_navigation ?>
	</div>

	<!-- Main content-->
	<div class="span3">
		<div class="material unpadded">
			<div class="padded">
				<div class="row10 fluid">
					<div class="span1" style="text-align: center">
						<img src="<?= $authUser->avatar ?>/48" style="border: solid 1px #777; border-radius: 3px;">
					</div>
					<div class="span9">
						<div>
							<a href="<?= new URL('user', 'show', $authUser->username) ?>" style="color: #000; font-weight: bold; font-size: .8em;"><?= $authUser->username ?></a>
						</div>
						<div style="padding-top: 10px;">
							<p style="margin: 0;">Test notification</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<!-- Contextual menu-->
	<div class="span1"></div>
</div>