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

/**
 * For pinches to work properly, we need to make sure the viewport is set to 
 * ignore zooming. This is due to the way we handle touch events.
 * 
 * Touch events can get awfully messy when the user has control over the viewport
 * scaling, and therefore we block them from being used when the device is at any
 * zoom level but the default.
 * 
 * Therefore, pinch events are mostly not gonna work unless the viewport is set to:
 * width=device-width, initial-scale=1.0, maximum-scale=1.0, minimim-scale=1.0
 * 
 * @returns {pinchL#28.pinchAnonym$1}
 */
depend([], function () {
	
	return {
		duration : 150, //If the user is not holding his fingers on the screen for
		                //at least 150 miliseconds we consider it a tap
		
		fingers  : 2,   //A pinch uses two fingers.
		
		movementRerquired : 100,
		
		start : function (touches) {
			var f1 = touches[0];
			var f2 = touches[1];
			
			return {
				start: [{x: f1.screenX, y: f1.screenY}, {x: f2.screenX, y: f2.screenY}],
				end: [],
				last: undefined,
				started: undefined,
				delta : undefined
			};
		},
		
		update : function (meta, touches) {
			var f1 = touches[0];
			var f2 = touches[1];

			meta.end = [{x: f1.screenX, y: f1.screenY}, {x: f2.screenX, y: f2.screenY}];
			meta.delta = {x : Math.abs(meta.end[0].x - meta.end[1].x) - Math.abs(meta.start[0].x - meta.start[1].x) };

			return meta;
		},
		
		end : function (meta, touches) {
			return meta;
		}
	};
});
