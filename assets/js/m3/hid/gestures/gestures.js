/* 
 * The MIT License
 *
 * Copyright 2019 César de la Cal Bretschneider <cesar@magic3w.com>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

/*
 * Note that a gesture listener will listen for one type of gesture for one 
 * element, and will prevent appropriate events from bubbling. This means that
 * registering conflicting listeners, or registering them carelessly can cause 
 * your application to malfunctin.
 * 
 * @param {type} horizontalswipe
 * @returns {gesturesL#28.Gesture}
 */
depend(['m3/hid/gestures/swipe', 'm3/hid/gestures/pinch'], function (swipe, pinch) {
	
	var Gesture = function (element, manipulator) {
		this.element = element;
		this.blocked = false;
		
		this.started = undefined;
		this.meta    = undefined;
		
		/*
		 * These are the callbacks used to report back to the application on the 
		 * status of the 
		 */
		this._init   = undefined;
		this._follow = undefined;
		this._end    = undefined;
		
		var self = this;
		
		switch (manipulator) {
			case 'swipe':
				this.backend = swipe;
			break;
			case 'pinch':
				this.backend = pinch;
			break;
			default : 
				throw 'Unsupported manipulator';
		};
		
		var self = this;
		element.addEventListener('touchstart', function (e) { self.start(e); })
		element.addEventListener('touchmove', function (e) { self.move(e); });
		element.addEventListener('touchend', function (e) { self.terminate(e); });
		
		
	
		/*
		 * Check if the viewport is zoomed in. Gestures are not supposed to work 
		 * when the application is not in a consistent state.
		 */
		window.visualViewport && window.visualViewport.addEventListener('resize', function(e) {
			self.blocked = event.target.scale !== 1;
		});
	};
	
	Gesture.prototype = {
		start     : function (e) {
			if (this.blocked) {
				return;
			}
			
			/*
			 * Verify that the backend is receiving a gesture with the right amount
			 * of fingers.
			 */
			if (e.touches.length !== this.backend.fingers) {
				this.started = undefined;
				this.backend.end(this.meta, e.touches);
				return;
			}
			/*
			 * Otherwise we use the backend to intit the metadata for this gesture.
			 */
			else {
				this.meta = this.backend.start(e.touches);
				e.stopPropagation();
				e.preventDefault();
			}
			
			this.started = +new Date();
			this._init && this._init(this.meta);
			
		},
		move      : function (e) {
			if (this.blocked) {
				return;
			}
			
			var ts = +new Date();
			
			if (ts - this.started < this.backend.duration) {
				//The interaction just was not long enough.
				return;
			}
			
			this.meta = this.backend.update(this.meta, e.touches);
			this._follow && this._follow(this.meta, function () { e.stopPropagation(); e.preventDefault(); });
		},
		terminate : function (e) {
			if (this.blocked) {
				return;
			}
			
			var ts = +new Date();
			
			if (ts - this.started < this.backend.duration) {
				//The interaction just was not long enough.
				return;
			}
			
			this.meta = this.backend.end(this.meta, e.touches);
			this._end && this._end(this.meta, function () { e.stopPropagation(); e.preventDefault(); });
			
			this.meta = undefined;
			this.started = undefined;
		},
		
		init    : function (c) {
			this._init = c; 
		},
		follow    : function (c) {
			this._follow = c; 
		},
		end       : function (c) {
			this._end = c; 
		}
	};
	
	return Gesture;
});
