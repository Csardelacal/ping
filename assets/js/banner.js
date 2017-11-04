(function () {
	var banner = document.getElementById('banner');
	
	if (!banner) { return; } //There's no banner.
	
	var img    = banner.querySelector('img');
	var w      = 1280;
	var h      =  300;
	
	var imgResize = function () {
		var sw     = banner.clientWidth;
		var ratio  = sw / w;

		//Set the banner height. This will prevent the content from flopping around
		banner.style.height   = (h*ratio) + 'px';
		banner.style.overflow = 'hidden';
		banner.style.position = 'relative';
		
		//Stretch the banner to the screen width
		img.style.width  = sw + 'px';
		img.style.height = (h * ratio) + 'px';

		//The img is absolute so we can move it in the parent
		img.style.position = 'absolute';
		img.style.top      = 0;
		img.style.left     = 0;
	};
	
	var imgLoad = function () {
		/*
		 * Get the actual sizes for the image, this prevents the image from popping.
		 */
		w = img.naturalWidth || w;
		h = img.naturalHeight || h;
		
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