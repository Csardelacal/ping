<?php current_context()->response->getHeaders()->set('Content-type', 'application/javascript;charset=utf8') ?>
<?php current_context()->response->getHeaders()->set('Expires', date("r", time() + 60)) ?>

//<script>
(function () {


	var init = function () {
		
		var all      = document.querySelectorAll('*[data-ping-counter]');
		var feed     = document.querySelectorAll('*[data-ping-feed]');
		var activity = document.querySelectorAll('*[data-ping-activity]');
		
		var count     = <?= json_encode($count + $activity) ?>;
		var feedC     = <?= json_encode($count) ?>;
		var activityC = <?= json_encode($activity) ?>;
		
		/*
		 * Attach the number to the elements that do display the counter. This 
		 * should make it easy to provide a notification counter on external sites.
		 */
		for (var j = 0; j < 3; j++) {
			var current = [all, feed, activity][j];
			
			for (var i = 0; i < current.length; i++) {
				current[i].innerHTML = [count, feedC, activityC][j];
				current[i].setAttribute('data-ping-amt', [count, feedC, activityC][j]);
			}
		}
	};
	
	if (window.addEventListener) {
		window.addEventListener('load', init, false);
	}

}());
//</script>