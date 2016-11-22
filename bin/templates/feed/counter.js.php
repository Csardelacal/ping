<?php current_context()->response->getHeaders()->set('Content-type', 'application/javascript;charset=utf8') ?>
<?php current_context()->response->getHeaders()->set('Expires', date("r", time() + 60)) ?>

//<script>
(function () {


	var init = function () {
		
		var elements = document.querySelectorAll('*[data-ping-counter]');
		var count    = <?= json_encode($count) ?>;
		var samples  = <?= json_encode($samples) ?>;
		
		/*
		 * Attach the number to the elements that do display the counter. This 
		 * should make it easy to provide a notification counter on external sites.
		 */
		for (var i = 0; i < elements.length; i++) {
			elements[i].innerHTML = count;
		}
	};
	
	if (window.addEventListener) {
		window.addEventListener('load', init, false);
	}

}());
//</script>