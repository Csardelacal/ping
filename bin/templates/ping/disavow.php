

<div class="spacer" style="height: 30px"></div>

<div class="row l5">
	<div class="span l1"></div>
	<div class="span l3">
		<div class="material">
			<h1>Confirm disavowal</h1>
			<p>
				You can disavow any response to a post you make. When you do so, the
				response ping will become unlinked from your ping and therefore no 
				longer show up as a response.
			</p>
			
			<p>
				Disavowing a ping does not delete it from the owner's feed. 
			</p>

			<p style="text-align: right">
			<a class="button" href="<?= url('ping', 'disavow', $id, $salt) ?>">Delete</a>
			</p>
		</div>
	</div>
</div>