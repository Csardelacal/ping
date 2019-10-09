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
	var Activity = function (ctx) {
		this._ctx = ctx;
	};
	
	Activity.prototype = {
		/**
		 * Reads the feed. If you pass the offset (the ID of the last ping you have
		 * available) it will return the feed starting at that point.
		 * 
		 * The callback will receive a PingList object containing an array of pings
		 * as it's first and only parameter. This pinglist object gives you access
		 * to:
		 * 
		 * * The array of pings the server returned
		 * * The context. Which allows you to make further calls to the SDK (for responses, for example)
		 * * A function that calls your function again with the next set of pings.
		 * 
		 * This allows your application to simply let itself be called over and over
		 * to generate a timeline.
		 * 
		 * @param {function} callback
		 * @param {int} offset
		 * @returns {undefined}
		 */
		read : function (callback, offset) {
			
			/*
			 * Construct an URI that idenitfies the user against Ping and send a 
			 * request to Ping.
			 * 
			 * The token will authenticate both the user and the application embedding
			 * the SDK since Ping will be able to retrieve metadata from PHPAS, allowing
			 * it to determine whether it should share the feed for this user with 
			 * the application.
			 * 
			 * Additionally, (although at the time of writing this is not the case)
			 * Ping should send a Access-Control header limited to the application's 
			 * domain to ensure the user is not being attacked with XSRF to leak data.
			 * 
			 * For this, Ping would also need to be able to understand the options 
			 * header. Which (again, at this point) is not being supported, but as 
			 * soon as it is, this should transparently become operational - only
			 * breaking non compliant applications.
			 */
			var uri = this._ctx.endpoint().replace(/\/$/, '') + '/activity.json?token=' + this._ctx.token() + (offset !== undefined? '&until=' + offset : '');
			var ctx = this._ctx;
			var slf = this;
			
			/*
			 * Send the request to ping.
			 */
			request(uri, null)
				.then(JSON.parse)
				.then(function (e) {
					if (!e.payload) { throw {message : 'Invalid response', response : e}; }
					
					var pl = [];
					for (var i = 0; i < e.payload.length; i++) { pl.push(e.payload[i]); }
					
					return new ActivityList(
						ctx, 
						pl, 
						function () { return e.until != 0? slf.read(callback, e.until) : null; }
					);
				})
				.then(callback)
				.catch(function (e) { console.error(e); });
		}
	};
	
	
	var ActivityItem = function (ctx, payload) {
		this.payload = payload;
		this.user = payload.user;
		this.url = payload.url;
		this.timeRelative = payload.timeRelative;
	};
	
	ActivityItem.prototype = {
		
	};
	
	var ActivityList = function (ctx, pings, next) {
		this._pings = pings;
		this._ctx   = ctx;
		this._next  = next;
	};
	
	ActivityList.prototype = {
		
	};
	
	return Activity;
});
