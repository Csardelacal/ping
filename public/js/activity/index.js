/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./resources/assets/js/activity/index.js":
/*!***********************************************!*\
  !*** ./resources/assets/js/activity/index.js ***!
  \***********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var lysine_dist_lysine_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! lysine/dist/lysine.js */ "./node_modules/lysine/dist/lysine.js");
/* harmony import */ var lysine_dist_lysine_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(lysine_dist_lysine_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var ping_sdk_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ping-sdk-js */ "./node_modules/ping-sdk-js/dist/sdk.module.js");
/* harmony import */ var ping_sdk_js__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(ping_sdk_js__WEBPACK_IMPORTED_MODULE_1__);


var nextPage = null;
var baseurl = window.baseurl;
var ping = new (ping_sdk_js__WEBPACK_IMPORTED_MODULE_1___default())(baseurl, '');

var height = function height() {
  var body = document.body,
      html = document.documentElement;
  return Math.max(body.scrollHeight, body.offsetHeight, html.clientHeight, html.scrollHeight, html.offsetHeight);
}; //This function listens to the scrolls


var listener = function listener() {
  var html = document.documentElement,
      scroll = Math.max(html.scrollTop, window.scrollY);

  if (height() - scroll < html.clientHeight + 700) {
    nextPage && nextPage();
    nextPage = null;
  }
};

console.log(ping.activity());
ping.activity().read(function (pingList) {
  for (var i = 0; i < pingList._pings.length; i++) {
    var view = new (lysine_dist_lysine_js__WEBPACK_IMPORTED_MODULE_0___default().view)('ping');
    var data = pingList._pings[i];
    /*
     * This block should be possible to have refactored out of the feed,
     * making it less pointless code that adapts stuff around.
     */

    view.setData({
      userName: data.user.username,
      avatar: data.user.avatar,
      userURL: data.user.id ? baseurl + '@' + data.user.username : '#',
      notificationURL: data.url || '#',
      notificationContent: data.content,
      timeRelative: data.timeRelative
    });
  }

  nextPage = pingList._next;
}, undefined); //Attach the listener

document.addEventListener('scroll', listener, false);

/***/ }),

/***/ "./node_modules/lysine/dist/lysine.js":
/*!********************************************!*\
  !*** ./node_modules/lysine/dist/lysine.js ***!
  \********************************************/
/***/ (() => {

var Lysine;Lysine=(()=>{var e={828:e=>{if("undefined"!=typeof Element&&!Element.prototype.matches){var t=Element.prototype;t.matches=t.matchesSelector||t.mozMatchesSelector||t.msMatchesSelector||t.oMatchesSelector||t.webkitMatchesSelector}e.exports=function(e,t){for(;e&&9!==e.nodeType;){if("function"==typeof e.matches&&e.matches(t))return e;e=e.parentNode}}},438:(e,t,n)=>{var i=n(828);function r(e,t,n,i,r){var o=s.apply(this,arguments);return e.addEventListener(n,o,r),{destroy:function(){e.removeEventListener(n,o,r)}}}function s(e,t,n,r){return function(n){n.delegateTarget=i(n.target,t),n.delegateTarget&&r.call(e,n)}}e.exports=function(e,t,n,i,s){return"function"==typeof e.addEventListener?r.apply(null,arguments):"function"==typeof n?r.bind(null,document).apply(null,arguments):("string"==typeof e&&(e=document.querySelectorAll(e)),Array.prototype.map.call(e,(function(e){return r(e,t,n,i,s)})))}},132:e=>{"use strict";var t={byMatcher:function(e,t,n){if(void 0===n&&(n={}),null===n||Array.isArray(n)||"object"!=typeof n)throw new Error("Expected opts to be an object.");if(e&&e!==document)return t(e)?e:this.byMatcher(e.parentNode,t,n);if(n.throwOnMiss)throw new Error("Expected to find parent node, but none was found.")},byClassName:function(e,t,n){return this.byMatcher(e,(function(e){return e.classList.contains(t)}),n)},withDataAttribute:function(e,t,n){return this.byMatcher(e,(function(e){return e.dataset.hasOwnProperty(t)}),n)}};e.exports=t},34:(e,t,n)=>{"use strict";n.r(t),n.d(t,{View:()=>g});var i=n(438),r=n.n(i),s=n(132),o=n.n(s),a=function(e){e instanceof NodeList&&(e=Array.prototype.slice.call(e,0)),this.elements=e};function u(e){return new a(e)}a.prototype={each:function(e){var t=this.elements instanceof Array?[]:{};if(!this.elements instanceof Array)for(var n in this.elements)this.elements.hasOwnProperty(n)&&(t[n]=e(this.elements[n],n));else for(n=0;n<this.elements.length;n++)t[n]=e(this.elements[n],n);return new a(t)},filter:function(e){var t=new a([]);return this.each((function(n){e(n)&&t.push(n)})),t},merge:function(e){var t=this;return e instanceof a||(e=new a(e)),e.each((function(e,n){t.elements instanceof Array?t.elements.push(e):t.elements[n]=e})),this},reduce:function(e){return this.elements.reduce(e)},push:function(e){this.elements.push(e)},pop:function(){return this.elements.pop()},set:function(e,t){this.elements[e]=t},get:function(e){return this.elements[e]},raw:function(){return this.elements},length:function(){if(this.elements instanceof Array)return this.elements.length;var e=0;return this.each((function(){e++})),e}};var h=function(e){this.element=e,this.view=void 0;var t=this;this.element.addEventListener("onkeyup",(function(){t.view.set(t.for()[0],this.value)})),this.element.addEventListener("change",(function(){"radio"===this.type||"checkbox"===this.type?t.view.set(t.for()[0],"on"===this.value||this.value||this.checked):t.view.set(t.for()[0],this.value)}))};h.prototype={readOnly:function(){return!1},for:function(){return[this.element.getAttribute("data-for")]},parent:function(e){return this.view=e,this},refresh:function(){var e=this.view.get(this.for()[0]);if(console.log(this.element.type),"radio"===this.element.type||"checkbox"===this.element.type)return console.log(this.for()[0]),console.log(e),void(this.element.value&&this.element.value===e||!0===e?this.element.checked=!0:this.element.checked=!1);this.element.value=e}};function c(e){this.view=void 0,this.getValue=function(){return-1===e.selectedIndex?null:e.options[this.getElement().selectedIndex].value},this.setValue=function(t){var n=Array.prototype.slice.call(e.options,0);e.selectedIndex=n.indexOf(e.querySelector('[value="'+t+'"]'))},this.for=function(){return[e.getAttribute("data-for")]},this.parent=function(e){return this.view=e,this},this.refresh=function(){var t=Array.prototype.slice.call(e.options,0);e.selectedIndex=t.indexOf(e.querySelector('[value="'+this.view.get(this.for()[0])+'"]'))}}function f(e){this.view=void 0,this.getValue=function(){return e.innerHTML},this.setValue=function(t){return e.innerHTML=t,this},this.for=function(){return[e.getAttribute("data-for")]},this.parent=function(e){return this.view=e,this},this.refresh=function(){e.innerHTML=this.view.get(this.for()[0])}}function l(e,t,n){this.element=e,this.name=t,this.value=n,this.adapters=this.makeAdapters(),this.view=void 0,this.setData=function(e){for(var t=0;t<this.adapters.length;t++)this.adapters[t].setValue(e[this.adapters[t].getName()])},this.replace=function(){for(var e="",t=0;t<this.adapters.length;t++)e+=this.adapters[t].replace();return e}}function d(e,t){var n=null;this.setValue=function(e){n=e},this.getValue=function(){return n},this.getName=function(){return-1!==e.indexOf("?")?e.substr(0,e.indexOf("?")):e},this.isReadOnly=function(){return t},this.replace=function(){if(t)return e;if(-1!==e.indexOf("?")){var i=e.substr(e.indexOf("?")+1).split(":");return n?i[0]:i[1]}return n}}l.prototype={hasLysine:function(){return-1!==this.name.search(/^data-lysine-/)},getAttributeName:function(){return this.name.replace(/^data-lysine-/,"").toLowerCase()},makeAdapters:function(){if(!this.hasLysine())return[];for(var e=/\{\{([A-Za-z0-9\.\s\?\-\:\_]+)\}\}/g,t=[],n=this.value.split(/\{\{[A-Za-z0-9\.\s\?\-\:\_]+\}\}/g),i=e.exec(this.value);i;)t.push(new d(n.shift(),!0)),t.push(new d(i[1],!1)),i=e.exec(this.value);return n.length>0&&t.push(new d(n.shift(),!0)),t},for:function(){var e=u([]);return u(this.adapters).each((function(t){t.isReadOnly()||e.push(t.getName())})),e.raw()},parent:function(e){return this.view=e,this},refresh:function(){var e=this;u(this.adapters).each((function(t){t.isReadOnly()||t.setValue(e.view.get(t.getName()))})),this.element.setAttribute(this.getAttributeName(),this.replace())}};if(void 0===HTMLElement)throw"Lysine requires a browser to work. HTMLElement class was not found";if(void 0===window)throw"Lysine requires a browser to work. Window variable was not found";function p(e){this.views=[],this.base=e,this.parentView=void 0,this.listeners=u([]),this.writeProtect=!1,this._setup=u([]),this._tearDown=u([]),this.getValue=function(){var e,t=[];for(this.views=this.views.filter((function(e){return!e.isDestroyed()})),e=0;e<this.views.length;e+=1)t.push(this.views[e].getValue());return t},this.setValue=function(e){var t,n;if(!this.writeProtect&&void 0!==e){for(e=e.filter((function(e){return!!e})),this.views=this.views.filter((function(e){return e.reset()&&!e.isDestroyed()}));e.length<this.views.length;)this.views[e.length].destroy();this.views=this.views.slice(0,e.length);var i=this._tearDown;for(u(this.views).each((function(e){i.each((function(t){t(e)}))})),t=this.views.length;t<e.length;t+=1)n=new g(this.base),this.views.push(n),n.setParent(this),this.listeners.each((function(e){n.on.apply(n,e)}));for(i=this._setup,u(this.views).each((function(e){i.each((function(t){t(e)}))})),t=0;t<e.length;t++)this.views[t].setValue(e[t])}},this.for=function(){return[this.base.getAttribute("data-for")]},this.on=function(e,t,n){this.listeners.push([e,t,n]),this.views.forEach((function(i){i.on(e,t,n)}))},this.setUp=function(e){this._setup.push(e)},this.tearDown=function(e){this._tearDown.push(e)},this.parent=function(e){return this.parentView=e,this},this.push=function(e){var t=new g(this.base);return this.views.push(t),t.setValue(e),t.setParent(this),this.listeners.each((function(e){t.on.apply(t,e)})),this.propagate(),t},this.refresh=function(){this.setValue(this.parentView.get(this.for()[0]))},this.propagate=function(){this.writeProtect=!0,this.parentView.set(this.for()[0],this.getValue()),this.writeProtect=!1}}function v(e,t,n){var i=/([a-zA-Z_0-9]+)\(([a-zA-Z_0-9\-]+)\)\s?(\=\=|\!\=)\s?(.+)/g.exec(e);if(null===i)throw"Malformed expression: "+e;var r=i[1],s=i[2],o=i[3],a=i[4],h=void 0,c=t.parentNode,f=t.nextSibling;this.isVisible=function(){var e=void 0;switch(r){case"null":e=null===h.get(s)?"true":"false";break;case"bool":e=!0===h.get(s)?"true":"false";break;case"count":e=h.get(s)?h.get(s).length:0;break;case"value":e=h.get(s)}return"=="===o?e==a:e!=a},this.test=function(){var e=this.isVisible();e!==(t.parentNode===c)&&(e?c.insertBefore(t,f):c.removeChild(t))},this.for=function(){var e=u([]);return n.each((function(t){e.merge(t.for())})),e.push(s),e.raw()},this.parent=function(e){return h=e,n.each((function(t){t.parent(e)})),this},this.refresh=function(){this.test(),this.isVisible()&&n.each((function(e){e.refresh()}))}}function g(e,t){var n,i,s=[],a={},d={modules:void 0};Object.assign(d,t),this.destroyed=!1,this.parent=void 0,this._module=d.modules,n=e instanceof HTMLElement?e:document.querySelector('*[data-lysine-view="'+e+'"]'),this.set=function(e,t){if("^"===e.substr(0,1))return this.parent.parentView.set(e.substr(1),t);for(var n=a,i=e.split("."),r=i.pop(),s=0;s<i.length;s++)n[i[s]]||(n[i[s]]={}),n=n[i[s]];n[r]=t,this.adapters.each((function(e){-1!==e.for().indexOf(i[0]||r)&&e.refresh()})),this.parent&&this.parent.propagate(this,a)},this.get=function(e){if("^"===e.substr(0,1))return this.parent.parentView.get(e.substr(1));for(var t=a,n=e.split("."),i=0;i<n.length;i++)t=t?t[n[i]]:void 0;return t},this.setData=function(e){a=e,this.adapters.each((function(e){e.refresh()}))},this.getData=function(){return a},this.reset=function(){for(var e=i.querySelectorAll("input"),t=0;t<e.length;t++)switch(e[t].type){case"checkbox":case"radio":e[t].checked=e[t].hasAttribute("checked");default:e[t].value=e[t].hasAttribute("value")?e[t].getAttribute("value"):""}return!0},this.getValue=this.getData,this.setValue=this.setData,this.fetchAdapters=function(e){e=void 0!==e?e:i;var t=u([]),n=this;return u(e.childNodes).each((function(e){var i,r=u([]);if(3!==e.nodeType){if(e.getAttribute&&e.getAttribute("data-for"))if(e.hasAttribute("data-lysine-view"))r.merge(u([new p(e).parent(n)]));else{var s=u([]).merge((i=e,!i.getAttribute("data-for")||"input"!==i.tagName.toLowerCase()&&"textarea"!==i.tagName.toLowerCase()?[]:[new h(i)])).merge(function(e){return"select"===e.tagName.toLowerCase()?[new c(e)]:[]}(e)).merge(function(e){return"input"!==e.tagName.toLowerCase()&&"textarea"!==e.tagName.toLowerCase()&&"select"!==e.tagName.toLowerCase()&&e.hasAttribute("data-for")?[new f(e)]:[]}(e));r.merge(s.each((function(e){return e.parent(n)})))}else r.merge(n.fetchAdapters(e));if(r.merge(function(e){var t=e.attributes,n=u([]);if(!t)return n;for(var i=0;i<t.length;i++)n.push(new l(e,t[i].name,t[i].value));return n.filter((function(e){return e.hasLysine()}))}(e).each((function(e){return e.parent(n)}))),e.getAttribute&&e.getAttribute("data-condition")){var o=new v(e.getAttribute("data-condition"),e,r);t.push(o.parent(n))}else t.merge(r)}})),t},this.getHTML=function(){return i},this.getElement=this.getHTML,this.destroy=function(){return this.destroyed=!0,this.parent&&this.parent.propagate(),i.parentNode.removeChild(i),u(s).each((function(e){document.removeEventListener(e[0],e[1])})),this},this.isDestroyed=function(){return this.destroyed},this.on=function(e,t,n){var i=this,a=r()(t,(function(t){return o()(t,(function(e){return e===i.getHTML()}))&&u(i.getHTML().querySelectorAll(e)).filter((function(e){return e===t})).raw()[0]}),(function(e,t){n.call(t,e,i)}));return s.push([t,a]),a},this.sub=function(e){return this.adapters.filter((function(t){return-1!==t.for().indexOf(e)})).get(0)},this.setParent=function(e){this.parent=e},this.find=function(e){return this.getHTML().querySelector(e)},this.findAll=function(e){return this.getHTML().querySelectorAll(e)},this.module=function(e){return this._module=e,this.refreshModules(),this},this.refreshModules=function(e){var t=this;if(void 0!==t._module){e=e||i;for(var n=[],r=0;r<e.classList.length;r++)n.push(t._module[e.classList[r]]||e.classList[r]);e.className="",e.classList.add(...n),e.id=t._module[e.id]||e.id,u(e.childNodes).each((function(e){3!==e.nodeType&&t.refreshModules(e)}))}};var g=void 0;if(n.content)g=n.content.firstElementChild;else for(g=n.firstChild;g&&1!=g.nodeType;)g=g.nextSibling;i=document.importNode(g,!0),this._module&&this.refreshModules(),this.adapters=this.fetchAdapters(),n.parentNode.insertBefore(i,n)}}},t={};function n(i){if(t[i])return t[i].exports;var r=t[i]={exports:{}};return e[i](r,r.exports,n),r.exports}return n.n=e=>{var t=e&&e.__esModule?()=>e.default:()=>e;return n.d(t,{a:t}),t},n.d=(e,t)=>{for(var i in t)n.o(t,i)&&!n.o(e,i)&&Object.defineProperty(e,i,{enumerable:!0,get:t[i]})},n.o=(e,t)=>Object.prototype.hasOwnProperty.call(e,t),n.r=e=>{"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},n(34)})();

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
/******/ 		if(__webpack_module_cache__[moduleId]) {
/******/ 			return __webpack_module_cache__[moduleId].exports;
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
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => module['default'] :
/******/ 				() => module;
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
/******/ 		__webpack_require__.o = (obj, prop) => Object.prototype.hasOwnProperty.call(obj, prop)
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
/************************************************************************/
/******/ 	// startup
/******/ 	// Load entry module
/******/ 	__webpack_require__("./resources/assets/js/activity/index.js");
/******/ 	// This entry module used 'exports' so it can't be inlined
/******/ })()
;