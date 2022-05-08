/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./resources/assets/js/feed/index.js":
/*!*******************************************!*\
  !*** ./resources/assets/js/feed/index.js ***!
  \*******************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var ping_sdk_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ping-sdk-js */ "./node_modules/ping-sdk-js/dist/sdk.module.js");
/* harmony import */ var ping_sdk_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(ping_sdk_js__WEBPACK_IMPORTED_MODULE_0__);

depend(['m3/core/lysine'], function (lysine) {
  var nextPage = null;
  var token = window.token;
  var ping = new (ping_sdk_js__WEBPACK_IMPORTED_MODULE_0___default())(window.baseurl, token);

  var height = function height() {
    var body = document.body,
        html = document.documentElement;
    return Math.max(body.scrollHeight, body.offsetHeight, html.clientHeight, html.scrollHeight, html.offsetHeight);
  }; //This function listens to the scrolls


  var listener = function listener() {
    var html = document.documentElement,
        scroll = Math.max(html.scrollTop, window.scrollY);

    if (height() - scroll < html.clientHeight + 700) {
      nextPage();
      nextPage = null;
    }
  };

  ping.feed().read(function (pingList) {
    for (var i = 0; i < pingList._pings.length; i++) {
      var view = new lysine.view('ping');
      var current = pingList._pings[i].payload;
      /*
       * This block should be possible to have refactored out of the feed,
       * making it less pointless code that adapts stuff around.
       */

      view.setData({
        id: current.id,
        userName: current.user.username,
        avatar: current.user.avatar,
        userURL: current.user.url,
        notificationURL: current.url || '#',
        notificationContent: current.content,
        media: current.media,
        share: current.share,
        poll: current.poll,
        timeRelative: current.timeRelative,
        feedback: current.feedback,
        replyCount: current.replies || 'Reply',
        shareCount: current.shares || 'Share',
        irt: current.irt ? [current.irt] : []
      });
    }

    nextPage = pingList._next;
  }); //Attach the listener

  document.addEventListener('scroll', listener, false);
});

/***/ }),

/***/ "./resources/assets/css/app.scss":
/*!***************************************!*\
  !*** ./resources/assets/css/app.scss ***!
  \***************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./node_modules/ping-sdk-js/dist/sdk.module.js":
/*!*****************************************************!*\
  !*** ./node_modules/ping-sdk-js/dist/sdk.module.js ***!
  \*****************************************************/
/***/ ((__unused_webpack_module, exports) => {

/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	// The require scope
/******/ 	var __nested_webpack_require_107__ = {};
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__nested_webpack_require_107__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__nested_webpack_require_107__.o(definition, key) && !__nested_webpack_require_107__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__nested_webpack_require_107__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__nested_webpack_require_107__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// ESM COMPAT FLAG
__nested_webpack_require_107__.r(__webpack_exports__);

// EXPORTS
__nested_webpack_require_107__.d(__webpack_exports__, {
  "default": () => (/* binding */ sdk)
});

;// CONCATENATED MODULE: ./src/sdk/ping.js
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
		var uri = this.ctx.endpoint().replace(/\/$/, '') + '/ping/detail/' + id +'.json?token=' + encodeURIComponent(this.ctx.token());
		var ctx = this.ctx;
		
		fetch(uri)
			.then(response => response.json())
			.then(function (e) {
				if (!e.payload) { throw {'message' : 'Invalid response', 'response' : e}; }
				return new Ping(ctx, e.payload);
			})
			.then(callback)
			.catch(function (e) { console.error(e); });
		
	},
	
	author : function (author, callback, offset) { 
		var uri = this.ctx.endpoint().replace(/\/$/, '') + '/user/show/' + author + '.json' + (offset !== undefined? '?until=' + offset : '');
		var ctx = this.ctx;
		var slf = this;
		
		fetch(uri)
			.then(response => response.json())
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
	},
	
	replies : function (parent, callback, offset) { 
		var uri = this.ctx.endpoint().replace(/\/$/, '') + '/ping/replies/' + parent + '.json' + (offset !== undefined? '?until=' + offset : '');
		var ctx = this.ctx;
		var slf = this;
		
		fetch(uri)
			.then(response => response.json())
			.then(function (e) {
				if (!e.payload) { throw {'message' : 'Invalid response', 'response' : e}; }
				
				var pl = [];
				for (var i = 0; i < e.payload.length; i++) { pl.push(new Ping(ctx, e.payload[i])); }
				
				return new PingList(
					ctx, 
					pl, 
					function () { return e.until != 0? slf.replies(author, callback, e.until) : null; }
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

/* harmony default export */ const ping = (Query);


;// CONCATENATED MODULE: ./src/sdk/feed.js
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
 * The feed is in charge of delivering updates to the user from the authors
 * they follow.
 * 
 * @returns {pingL#25.Feed}
 */
var Feed = function (ctx) {
	this._ctx = ctx;
};

Feed.prototype = {
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
		var uri = this._ctx.endpoint().replace(/\/$/, '') + '/feed.json?token=' + this._ctx.token() + (offset !== undefined? '&until=' + offset : '');
		var ctx = this._ctx;
		var slf = this;
		
		/*
			* Send the request to ping.
			*/
		fetch(uri)
			.then(response => response.json())
			.then(function (e) {
				if (!e.payload) { throw {message : 'Invalid response', response : e}; }
				
				var pl = [];
				for (var i = 0; i < e.payload.length; i++) { pl.push(new feed_Ping(ctx, e.payload[i])); }
				
				return new feed_PingList(
					ctx, 
					pl, 
					function () { return e.until != 0? slf.read(callback, e.until) : null; }
				);
			})
			.then(callback)
			.catch(function (e) { console.error(e); });
	}
};


var feed_Ping = function (ctx, payload) {
	this.payload = payload;
};

feed_Ping.prototype = {
	
};

var feed_PingList = function (ctx, pings, next) {
	this._pings = pings;
	this._ctx   = ctx;
	this._next  = next;
};

feed_PingList.prototype = {
	
};

/* harmony default export */ const feed = (Feed);

;// CONCATENATED MODULE: ./src/sdk/feedback.js
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


var Feedback = function (ctx) {
	this._ctx = ctx;
};

Feedback.prototype = {
		
	retrieve : function (pingId, cb) {

		var uri = this._ctx.endpoint().replace(/\/$/, '') + '/feedback/retrieve/' + pingId +'.json?token=' + encodeURIComponent(this._ctx.token());

		fetch(uri)
			.then(response => response.json())
			.then(function (resp) {
				cb(resp);
			});
	},
	
	push : function (pingId, reaction, cb) {

		var uri = this._ctx.endpoint().replace(/\/$/, '') + '/feedback/push/' + pingId +'.json?reaction=' + reaction + '&token=' + encodeURIComponent(this._ctx.token());

		fetch(uri)
			.then(response => response.json())
			.then(function (resp) {
				cb(resp);
			});
	},

	revoke : function (pingId, cb) {

		var uri = this._ctx.endpoint().trim('/') + '/feedback/revoke/' + pingId +'.json?token=' + encodeURIComponent(this._ctx.token());

		fetch(uri)
			.then(response => response.json())
			.then(function (resp) {
				cb(resp);
			});
	},

	vote : function (id, cb) {

		var uri = this._ctx.endpoint().trim('/') + '/poll/vote/' + id +'.json?token=' + encodeURIComponent(this._ctx.token());

		fetch(uri)
			.then(response => response.json())
			.then(function (resp) {
				cb(resp);
			});
	}
};

/* harmony default export */ const feedback = (Feedback);

;// CONCATENATED MODULE: ./src/sdk/activity.js
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
		fetch(uri)
			.then(response => response.json())
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

/* harmony default export */ const activity = (Activity);


;// CONCATENATED MODULE: ./src/sdk/media.js
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


var Media = function (ctx) {
	this._ctx = ctx;
};

Media.prototype = {
		
	push : function (payload, cb) {
		
		var fd = new FormData();
		fd.append('file', payload);
		
		var uri = this._ctx.endpoint().replace(/\/$/, '') + '/media/upload.json?token=' + encodeURIComponent(this._ctx.token());

		request(uri, { method: "POST", body: fd})
			.then(response => response.json())
			.then(function (resp) {
				cb(resp);
			});
	}
};

/* harmony default export */ const media = (Media);

;// CONCATENATED MODULE: ./src/sdk.js
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
	feed     : function () { return new feed(this); },
	feedback : function () { return new feedback(this); },
	media    : function () { return new media(this); },
	ping     : function () { return new ping(this); },
	activity : function () { return new activity(this); },
	
	//Getters and setters
	endpoint : function () { return this._endpoint; },
	token    : function () { return this._token; }
};

/* harmony default export */ const sdk = (SDK);
var __webpack_export_target__ = exports;
for(var i in __webpack_exports__) __webpack_export_target__[i] = __webpack_exports__[i];
if(__webpack_exports__.__esModule) Object.defineProperty(__webpack_export_target__, "__esModule", { value: true });
/******/ })()
;

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = __webpack_modules__;
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/chunk loaded */
/******/ 	(() => {
/******/ 		var deferred = [];
/******/ 		__webpack_require__.O = (result, chunkIds, fn, priority) => {
/******/ 			if(chunkIds) {
/******/ 				priority = priority || 0;
/******/ 				for(var i = deferred.length; i > 0 && deferred[i - 1][2] > priority; i--) deferred[i] = deferred[i - 1];
/******/ 				deferred[i] = [chunkIds, fn, priority];
/******/ 				return;
/******/ 			}
/******/ 			var notFulfilled = Infinity;
/******/ 			for (var i = 0; i < deferred.length; i++) {
/******/ 				var [chunkIds, fn, priority] = deferred[i];
/******/ 				var fulfilled = true;
/******/ 				for (var j = 0; j < chunkIds.length; j++) {
/******/ 					if ((priority & 1 === 0 || notFulfilled >= priority) && Object.keys(__webpack_require__.O).every((key) => (__webpack_require__.O[key](chunkIds[j])))) {
/******/ 						chunkIds.splice(j--, 1);
/******/ 					} else {
/******/ 						fulfilled = false;
/******/ 						if(priority < notFulfilled) notFulfilled = priority;
/******/ 					}
/******/ 				}
/******/ 				if(fulfilled) {
/******/ 					deferred.splice(i--, 1)
/******/ 					var r = fn();
/******/ 					if (r !== undefined) result = r;
/******/ 				}
/******/ 			}
/******/ 			return result;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/jsonp chunk loading */
/******/ 	(() => {
/******/ 		// no baseURI
/******/ 		
/******/ 		// object to store loaded and loading chunks
/******/ 		// undefined = chunk not loaded, null = chunk preloaded/prefetched
/******/ 		// [resolve, reject, Promise] = chunk loading, 0 = chunk loaded
/******/ 		var installedChunks = {
/******/ 			"/js/feed/index": 0,
/******/ 			"css/app": 0
/******/ 		};
/******/ 		
/******/ 		// no chunk on demand loading
/******/ 		
/******/ 		// no prefetching
/******/ 		
/******/ 		// no preloaded
/******/ 		
/******/ 		// no HMR
/******/ 		
/******/ 		// no HMR manifest
/******/ 		
/******/ 		__webpack_require__.O.j = (chunkId) => (installedChunks[chunkId] === 0);
/******/ 		
/******/ 		// install a JSONP callback for chunk loading
/******/ 		var webpackJsonpCallback = (parentChunkLoadingFunction, data) => {
/******/ 			var [chunkIds, moreModules, runtime] = data;
/******/ 			// add "moreModules" to the modules object,
/******/ 			// then flag all "chunkIds" as loaded and fire callback
/******/ 			var moduleId, chunkId, i = 0;
/******/ 			if(chunkIds.some((id) => (installedChunks[id] !== 0))) {
/******/ 				for(moduleId in moreModules) {
/******/ 					if(__webpack_require__.o(moreModules, moduleId)) {
/******/ 						__webpack_require__.m[moduleId] = moreModules[moduleId];
/******/ 					}
/******/ 				}
/******/ 				if(runtime) var result = runtime(__webpack_require__);
/******/ 			}
/******/ 			if(parentChunkLoadingFunction) parentChunkLoadingFunction(data);
/******/ 			for(;i < chunkIds.length; i++) {
/******/ 				chunkId = chunkIds[i];
/******/ 				if(__webpack_require__.o(installedChunks, chunkId) && installedChunks[chunkId]) {
/******/ 					installedChunks[chunkId][0]();
/******/ 				}
/******/ 				installedChunks[chunkId] = 0;
/******/ 			}
/******/ 			return __webpack_require__.O(result);
/******/ 		}
/******/ 		
/******/ 		var chunkLoadingGlobal = self["webpackChunkping"] = self["webpackChunkping"] || [];
/******/ 		chunkLoadingGlobal.forEach(webpackJsonpCallback.bind(null, 0));
/******/ 		chunkLoadingGlobal.push = webpackJsonpCallback.bind(null, chunkLoadingGlobal.push.bind(chunkLoadingGlobal));
/******/ 	})();
/******/ 	
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module depends on other loaded chunks and execution need to be delayed
/******/ 	__webpack_require__.O(undefined, ["css/app"], () => (__webpack_require__("./resources/assets/js/feed/index.js")))
/******/ 	var __webpack_exports__ = __webpack_require__.O(undefined, ["css/app"], () => (__webpack_require__("./resources/assets/css/app.scss")))
/******/ 	__webpack_exports__ = __webpack_require__.O(__webpack_exports__);
/******/ 	
/******/ })()
;