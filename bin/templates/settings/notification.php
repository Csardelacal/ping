
<div class="spacer" style="height: 80px"></div>

<div class="row5">
	<div class="span1">
		
	</div>
	
	<div class="span3">
		<div class="material">
			<p style="color: #555; font-size: .8em;">
				Use this page to define how you wish to receive email notifications 
				from the system. If you select digest, you will receive all your 
				notifications combined in a daily digest.
			</p>
			
			<div class="spacer" style="height:50px"></div>
			
			<form method="POST" action="">
				<?php foreach (array_reverse($types) as $label => $id): ?>
				<div class="row3 fluid">
					<div class="span1">
						<label style="color: #555; font-size: .8em;"><?= ucfirst($label) ?></label>
					</div>
					<div class="span2">
						<span class="styled-select">
							<select name="notification[<?= $id ?>]">
								<option value="<?= settings\NotificationModel::NOTIFY_EMAIL ?>" <?= isset($calculated[$id]) && $calculated[$id] == 0? 'selected' : '' ?>>Instant email notifications</option>
								<option value="<?= settings\NotificationModel::NOTIFY_DIGEST ?>" <?= isset($calculated[$id]) && $calculated[$id] == 1? 'selected' : '' ?>>Add to daily digest</option>
								<option value="<?= settings\NotificationModel::NOTIFY_NONE ?>" <?= isset($calculated[$id]) && $calculated[$id] == 2? 'selected' : '' ?>>Do not send emails</option>
							</select>
						</span>
					</div>
				</div>
				<?php endforeach; ?>
				
				<div class="spacer" style="height: 30px"></div>
				
				<div class="row1">
					<div class="span1" style="text-align: right">
						<input type="submit" value="Store" class="button">
					</div>
				</div>
			</form>
		</div>
	</div>
	
	<div class="span1">
		
	</div>
</div>