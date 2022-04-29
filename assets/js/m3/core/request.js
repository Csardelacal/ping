/* 
 * The MIT License
 *
 * Copyright 2018 César de la Cal Bretschneider <cesar@magic3w.com>.
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


depend(['m3/promises/promise'], function(Promise) {
	
	return function (url, pd, blob) {
		return new Promise(function(success, error) {
			
			var xhr = new XMLHttpRequest();
			xhr.open(!pd? 'GET' : 'POST', url);

			xhr.onreadystatechange = function () {
				if (xhr.readyState !== 4) { return; }
				
				if (xhr.status !== 200) {
					error(xhr.response);
				}
				else {
					success(xhr.response);
				}
			};
			
			if (typeof(pd) === 'object' && !(pd instanceof FormData)) {
				xhr.setRequestHeader('Content-type', 'application/json');
				pd = JSON.stringify(pd);
				console.log(pd);
			} 
			
			if (blob) {
				xhr.responseType = 'blob';
			}

			xhr.send(pd);
		});
	};
});