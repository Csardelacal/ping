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
	
	/**
	 * The feed is in charge of delivering updates to the user from the authors
	 * they follow.
	 * 
	 * @returns {pingL#25.Feed}
	 */
	var Query = function (ctx) {
		this.ctx = ctx;
	};
	
	Query.prototype = {
		
		get : function (id, callback) { 
			var uri = this.ctx.endpoint().trim('/') + '/ping/detail/' + id +'.json?token=' + encodeURIComponent(this.ctx.token());
			var ctx = this.ctx;
			
			request(uri, null)
				.then(JSON.parse)
				.then(function (e) {
					if (!e.payload) { throw {'message' : 'Invalid response', 'response' : e}; }
					return new Ping(ctx, e.payload);
				})
				.then(callback)
				.catch(function (e) { console.error(e); });
			
		},
		
		author : function (author, callback, offset) { 
			var uri = this.ctx.endpoint().trim('/') + '/user/show/' + author + '.json' + (offset !== undefined? '?until=' + offset : '');
			var ctx = this.ctx;
			var slf = this;
			
			request(uri, null)
				.then(JSON.parse)
				.then(function (e) {
					if (!e.payload) { throw {'message' : 'Invalid response', 'response' : e}; }
					
					var pl = [];
					for (var i = 0; i < e.payload.length; i++) { pl.push(new Ping(ctx, e.payload[i])); }
					
					return new PingList(
						ctx, 
						pl, 
						function () { return e.until != 0? slf.author(author, callback, e.until) : null; }
					);
				})
				.then(callback)
				.catch(function (e) { console.error(e); });
		}
	};
	
	var Ping = function (ctx, payload) {
		this.payload = payload;
	};
	
	Ping.prototype = {
		
	};
	
	var PingList = function (ctx, pings, next) {
		this._pings = pings;
		this._ctx   = ctx;
		this._next  = next;
	};
	
	PingList.prototype = {
		
	};
	
	return Query;
});

