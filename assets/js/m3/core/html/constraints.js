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
	
		
	/**
	 * This function returns the constraints that an element fits into. This allows
	 * an application to determine whether an item is onscreen, or whether two items
	 * intersect.
	 * 
	 * Note: this function provides only the vertical offset, which is most often
	 * needed since web pages tend to grow into the vertical space more than the 
	 * horizontal.
	 * 
	 * @param {type} el
	 * @returns {ui-layoutL#1.getConstraints.ui-layoutAnonym$0}
	 */
	return function (el) {
		var t = 0;
		var l = 0;
		var w = el.clientWidth;
		var h = el.clientHeight;
		
		do {
			t = t + el.offsetTop;
			l = l + el.offsetLeft;
		} while (null !== (el = el.offsetParent));
		
		return {top : t, bottom : document.body.clientHeight - t - h, left: l, width: w, height: h};
	};
});
