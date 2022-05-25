
depend(['m3/animation/animation'], function (animation) {
	
	var Gallery = function (options) {
		
		var visible = false;
		var blocked  = false;
		var backdrop = undefined;
		var gallery = undefined;
		var close = undefined;
		var inner = undefined;
		var offset  = undefined;
		var image = undefined;
		var self = this;
		var options = options || {};
		
		this.show = function (url, contentType) {
			if (visible) { return; }
			
			backdrop = document.body.appendChild(document.createElement('div'));
			gallery = backdrop.appendChild(document.createElement('div'));
			close = gallery.appendChild(document.createElement('div'));
			
			if (contentType === 'video') {
				inner = gallery.appendChild(document.createElement('video'));
				inner.loop = true;
				inner.autoplay = true;
			}
			else {
				inner = gallery.appendChild(document.createElement('img'));
			}
			
			
			backdrop.className = 'gallery-backdrop';
			gallery.className = 'gallery'; 
			close.className = 'close';
			inner.className = 'inner';
			backdrop.style.opacity = 0;
			
			inner.src = url;
			
			if (options.width) { gallery.style.width = options.width; }
			close.innerHTML = '&times;'
			
			offset = window.pageYOffset;
			
			document.body.classList.add('has-dialog');
			document.body.scrollTo(0, offset);
			
			backdrop.addEventListener('click', function () { !blocked && self.hide(); blocked = false; });
			close.addEventListener('click', function () { self.hide(); blocked = true; });
			inner.addEventListener('click', function () { blocked = true; });
			
			visible = true;
			
			animation(function (progress) {
				backdrop.style.opacity = progress;
			}, 300, 'ease');
			
		};
		
		this.hide = function () {
			if (!visible) { return; }
			
			
			animation(function (progress) {
				backdrop.style.opacity = 1 - progress;
			}, 300, 'ease');
			
			setTimeout(function () {
				backdrop.parentNode.removeChild(backdrop);

				document.body.classList.remove('has-dialog');
				window.scrollTo(0, offset);
				//Move element out of wrapper
				//Dismantle gallery
				visible = false;
		}, 300);
		};
		
		this.toggle = function () {
			visible? this.hide() : this.show();
		};
		
	};
	
	
	return Gallery;
	
});