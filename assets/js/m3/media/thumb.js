
depend([], function () {
	/*
	 * This function receives a image (as data URI), creates a thumbnail and invokes
	 * the provided callback with the datauri of the image.
	 * 
	 * The reason behind this is mostly image upload tools. When you upload an 
	 * image and wish to present a preview, the browser can generally manipulate
	 * the original file, but will have a harder time refolwing the page, when using 
	 * a image that has been properly scaled down most devices (specially mobile)
	 * should have a way easier time.
	 * 
	 * Note: The original intention for this thumbnail tool was to find a way to 
	 * make thumbs of videos, but it seems that browser support is not there yet.
	 * 
	 * @param {type} dataURI
	 * @param {type} width
	 * @param {type} height
	 * @param {type} cb
	 * @returns {undefined}
	 */
	return function (dataURI, width, height, cb) {
		var img = new Image();
		img.onload = function () {

			var canvas = document.createElement('canvas');
			canvas.height = width;
			canvas.width  = height;

			var ctx = canvas.getContext('2d');

			if (this.naturalHeight / canvas.height > this.naturalWidth / canvas.width) {
				var w = canvas.width;
				var h = this.naturalHeight / this.naturalWidth * canvas.width;
			}
			else {
				var w = this.naturalWidth / this.naturalHeight * canvas.height;
				var h = canvas.height;
			}

			ctx.imageSmoothingEnabled = true;
			ctx.drawImage(this, (canvas.width - w) / 2, (canvas.height - h) / 2, w, h);
			cb(canvas.toDataURL());
			
			/**
			 * Just to potentially help the garbage collector, this will remove the
			 * reference and free the memory used by this.
			 */
			img = undefined;
		};

		img.src = dataURI;

	};
});