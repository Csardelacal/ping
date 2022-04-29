
depend(['m3/animation/animation'], function (animation) {
	
	var Dialog = function (element, options) {
		
		var visible = false;
		var blocked  = false;
		var backdrop = undefined;
		var dialog = undefined;
		var close = undefined;
		var inner = undefined;
		var offset  = undefined;
		var sibling = undefined;
		var self = this;
		var options = options || {};
		
		this.show = function () {
			if (visible) { return; }
			
			backdrop = document.body.appendChild(document.createElement('div'));
			dialog = backdrop.appendChild(document.createElement('div'));
			close = dialog.appendChild(document.createElement('div'));
			inner = dialog.appendChild(document.createElement('div'));
			
			if (options.transparent) { dialog.className = 'dialog transparent'; }
			else { dialog.className = 'dialog'; }
			
			
			backdrop.className = 'dialog-backdrop';
			close.className = 'close';
			inner.className = 'inner';
			backdrop.style.opacity = 0;
			
			sibling = element.nextSibling;
			inner.appendChild(element);
			
			if (options.width) { dialog.style.width = options.width; }
			close.innerHTML = '&times;'
			
			offset = window.pageYOffset;
			
			document.body.classList.add('has-dialog');
			document.body.scrollTo(0, offset);
			
			backdrop.addEventListener('click', function () { !blocked && self.hide(); blocked = false; });
			close.addEventListener('click', function () { self.hide(); blocked = true; });
			dialog.addEventListener('click', function () { blocked = true; });
			
			visible = true;
			
			animation(function (progress) {
				backdrop.style.opacity = progress;
			}, 300, 'ease');
			
			inner.style.maxHeight = 'calc(' + (window.innerHeight) + 'px - 14rem)';
		};
		
		this.hide = function () {
			if (!visible) { return; }
			
			
			animation(function (progress) {
				backdrop.style.opacity = 1 - progress;
			}, 300, 'ease');
			
			setTimeout(function () {
				sibling.parentNode.insertBefore(element, sibling);
				backdrop.parentNode.removeChild(backdrop);

				document.body.classList.remove('has-dialog');
				window.scrollTo(0, offset);
				//Move element out of wrapper
				//Dismantle dialog
				visible = false;
		}, 300);
		};
		
		this.toggle = function () {
			visible? this.hide() : this.show();
		};
		
	};
	
	
	return Dialog;
	
});