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

depend([], function () {
	
	return {
		duration : 150, //If an horizontal swipe happens for less than 150 miliseconds,
		                //it just didn't happen.
		
		fingers  : 1,   //An horizontal swipe happens with just one finger.
		
		movementRerquired : 100,
		
		start : function (touches) {
			var finger = touches[0];
			
			return {
				startX: finger.screenX,
				startY: finger.screenY,
				endX: undefined,
				endY: undefined,
				last: undefined,
				started: undefined,

				/*
				 * These are needed for dragging the sidebar open and closed on mobile devices.
				 */
				direction: undefined,
			};
		},
		
		update : function (meta, touches) {
			var finger = touches[0];

			meta.endX = finger.screenX;
			meta.endY = finger.screenY;
			
			meta.direction = meta.direction || (Math.abs(meta.endX - meta.startX) > Math.abs(meta.endY - meta.startY)? 'h' : 'v');

			return meta;
		},
		
		end : function (meta, touches) {
			return meta;
		}
	};
});
