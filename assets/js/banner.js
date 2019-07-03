(function () {
	var banner = document.getElementById('page-banner');
	
	if (!banner) { return; } //There's no banner.
	
	var img    = banner.querySelector('img');
	
	var imgResize = function () {
		var sh     = Math.max(banner.clientHeight, 200);
		var ih     = img.clientHeight;
		
		img.style.marginTop = ((sh - ih) / 2) + 'px'; //This is always negative
	};
	
	var imgLoad = function () {
		imgResize();
	};
	
	
	img.addEventListener('load', imgLoad);
	window.addEventListener('resize', imgResize);
	
	if (img.complete) { imgLoad(); }
	
	//On scroll we do follow with the image
	document.addEventListener('scroll', function (e) {
		var scroll    = e.pageY? e.pageY : window.pageYOffset;
		var offsetTop = 50;
		var top       = scroll - offsetTop;
		
		if (top < 0) { top = 0; }
		img.style.top = ( top / 2) + 'px';
	});
	
	/*
	 * By default we use the standard size to approximate the banner while loading.
	 * Since these dimensions are better than anything we might get from the
	 * user agent while loading.
	 */
	imgResize();
}());