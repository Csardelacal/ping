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
depend([
	'ping/sdk/ping', 
	'ping/sdk/feed', 
	'ping/sdk/feedback', 
	'ping/sdk/activity', 
	'ping/sdk/media'], function (Ping, Feed, Feedback, Activity, Media) {
	
	/**
	 * 
	 * @param {string} endpoint
	 * @param {string} token optional Only needed for authenticated requests
	 * @returns {pingL#33.Ping}
	 */
	var SDK = function (endpoint, token) {
		this._endpoint = endpoint;
		this._token = token;
	};
	
	SDK.prototype = {
		feed     : function () { return new Feed(this); },
		feedback : function () { return new Feedback(this); },
		media    : function () { return new Media(this); },
		ping     : function () { return new Ping(this); },
		activity : function () { return new Activity(this); },
		
		//Getters and setters
		endpoint : function () { return this._endpoint; },
		token    : function () { return this._token; }
	};
	
	return SDK;
});