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

depend(['m3/core/request'], function (request) {
	
	var Feedback = function (ctx) {
		this._ctx = ctx;
	};
	
	Feedback.prototype = {
			
		push : function (pingId, reaction, cb) {

			var uri = this._ctx.endpoint().trim('/') + '/feedback/push/' + pingId +'.json?reaction=' + reaction + 'token=' + encodeURIComponent(this._ctx.token());

			request(uri, null)
				.then(JSON.parse)
				.then(function (resp) {
					cb(resp);
				});
		},

		revoke : function (pingId, cb) {

			var uri = this._ctx.endpoint().trim('/') + '/feedback/revoke/' + pingId +'.json?token=' + encodeURIComponent(this._ctx.token());

			request(uri, null)
				.then(JSON.parse)
				.then(function (resp) {
					cb(resp);
				});
		},

		vote : function (id, cb) {

			var uri = this._ctx.endpoint().trim('/') + '/poll/vote/' + id +'.json?token=' + encodeURIComponent(this._ctx.token());

			request(uri, null)
				.then(JSON.parse)
				.then(function (resp) {
					cb(resp);
				});
		}
	};
	
	return Feedback;
});
