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
 * This Javascript file provides an entry point to the Ping SDK. This should make
 * it easy for your application to create and retrieve pings from the client 
 * itself, reducing load on the source application and making the application
 * more responsive to the end user.
 */
depend(['ping/sdk/ping', 'ping/sdk/feed'], function (Ping, Feed) {
	
	/**
	 * 
	 * @param {string} endpoint
	 * @param {string} token
	 * @param {string} signature optional signature to authenticate the source application
	 * @returns {pingL#33.Ping}
	 */
	var SDK = function (endpoint, token, signature) {
		this._endpoint = endpoint;
		this._token = token;
		this._signature = signature;
	};
	
	SDK.prototype = {
		feed     : function () { return new Feed(this); },
		media    : function () {  },
		ping     : function () { return new Ping(this); },
		activity : function () {},
		
		//Getters and setters
		endpoint : function () { return this._endpoint; },
		token    : function () { return this._token; },
		signature: function () { return this._signature; }
	};
	
	return SDK;
});