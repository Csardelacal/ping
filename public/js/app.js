/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./resources/assets/js/app.js":
/*!************************************!*\
  !*** ./resources/assets/js/app.js ***!
  \************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var delegate__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! delegate */ "./node_modules/delegate/src/delegate.js");
/* harmony import */ var delegate__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(delegate__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var m3w_dialog__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! m3w-dialog */ "./node_modules/m3w-dialog/dist/dialog.js");
/* harmony import */ var m3w_dialog__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(m3w_dialog__WEBPACK_IMPORTED_MODULE_1__);

 //import _SCSS  from 'm3w-_scss/dist/_scss';

/**
 * Share button functionality. Whenever a user clicks share, the application will attempt
 * to confirm their intent and check whether they actually meant to share it.
 * 
 * @todo This should be moved to the ping component as a dropdown, so it confirms the action
 * within it's context.
 */

try {
  var dialog = new (m3w_dialog__WEBPACK_IMPORTED_MODULE_1___default())(document.getElementById('share-dialog'));
  delegate__WEBPACK_IMPORTED_MODULE_0___default()(document.body, '.for-shares', 'click', function (e) {
    document.getElementById('share-confirm-link').href = this.href;
    dialog.show();
    e.preventDefault();
  });
  document.getElementById('share-confirm-link').addEventListener('click', function (e) {
    document.getElementById('share-processing').style.display = 'block';
    document.getElementById('share-confirm-link').style.display = 'none';
    fetch(this.href).then(function () {
      dialog.hide();
      document.getElementById('share-processing').style.display = 'none';
      document.getElementById('share-confirm-link').style.display = 'block';
    });
    e.preventDefault();
  });
} catch (e) {
  console.error(e);
}

/***/ }),

/***/ "./node_modules/delegate/src/closest.js":
/*!**********************************************!*\
  !*** ./node_modules/delegate/src/closest.js ***!
  \**********************************************/
/***/ ((module) => {

var DOCUMENT_NODE_TYPE = 9;

/**
 * A polyfill for Element.matches()
 */
if (typeof Element !== 'undefined' && !Element.prototype.matches) {
    var proto = Element.prototype;

    proto.matches = proto.matchesSelector ||
                    proto.mozMatchesSelector ||
                    proto.msMatchesSelector ||
                    proto.oMatchesSelector ||
                    proto.webkitMatchesSelector;
}

/**
 * Finds the closest parent that matches a selector.
 *
 * @param {Element} element
 * @param {String} selector
 * @return {Function}
 */
function closest (element, selector) {
    while (element && element.nodeType !== DOCUMENT_NODE_TYPE) {
        if (typeof element.matches === 'function' &&
            element.matches(selector)) {
          return element;
        }
        element = element.parentNode;
    }
}

module.exports = closest;


/***/ }),

/***/ "./node_modules/delegate/src/delegate.js":
/*!***********************************************!*\
  !*** ./node_modules/delegate/src/delegate.js ***!
  \***********************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var closest = __webpack_require__(/*! ./closest */ "./node_modules/delegate/src/closest.js");

/**
 * Delegates event to a selector.
 *
 * @param {Element} element
 * @param {String} selector
 * @param {String} type
 * @param {Function} callback
 * @param {Boolean} useCapture
 * @return {Object}
 */
function _delegate(element, selector, type, callback, useCapture) {
    var listenerFn = listener.apply(this, arguments);

    element.addEventListener(type, listenerFn, useCapture);

    return {
        destroy: function() {
            element.removeEventListener(type, listenerFn, useCapture);
        }
    }
}

/**
 * Delegates event to a selector.
 *
 * @param {Element|String|Array} [elements]
 * @param {String} selector
 * @param {String} type
 * @param {Function} callback
 * @param {Boolean} useCapture
 * @return {Object}
 */
function delegate(elements, selector, type, callback, useCapture) {
    // Handle the regular Element usage
    if (typeof elements.addEventListener === 'function') {
        return _delegate.apply(null, arguments);
    }

    // Handle Element-less usage, it defaults to global delegation
    if (typeof type === 'function') {
        // Use `document` as the first parameter, then apply arguments
        // This is a short way to .unshift `arguments` without running into deoptimizations
        return _delegate.bind(null, document).apply(null, arguments);
    }

    // Handle Selector-based usage
    if (typeof elements === 'string') {
        elements = document.querySelectorAll(elements);
    }

    // Handle Array-like based usage
    return Array.prototype.map.call(elements, function (element) {
        return _delegate(element, selector, type, callback, useCapture);
    });
}

/**
 * Finds closest match and invokes callback.
 *
 * @param {Element} element
 * @param {String} selector
 * @param {String} type
 * @param {Function} callback
 * @return {Function}
 */
function listener(element, selector, type, callback) {
    return function(e) {
        e.delegateTarget = closest(e.target, selector);

        if (e.delegateTarget) {
            callback.call(element, e);
        }
    }
}

module.exports = delegate;


/***/ }),

/***/ "./node_modules/m3w-dialog/dist/dialog.js":
/*!************************************************!*\
  !*** ./node_modules/m3w-dialog/dist/dialog.js ***!
  \************************************************/
/***/ ((module) => {

!function(e,t){ true?module.exports=t():0}(self,(function(){return(()=>{"use strict";var e={130:(e,t,n)=>{n.d(t,{Z:()=>r});var o=n(645),i=n.n(o)()((function(e){return e[1]}));i.push([e.id,"._4t7TiX0vQOD0jjQpQlsg5{position:fixed;top:0;left:0;height:100%;width:100%}\n",""]),i.locals={overlay:"_4t7TiX0vQOD0jjQpQlsg5"};const r=i},793:(e,t,n)=>{n.d(t,{Z:()=>r});var o=n(645),i=n.n(o)()((function(e){return e[1]}));i.push([e.id,"._1DOmVUfSkwJZ3s2MuilBz1{box-sizing:border-box;padding:2rem 2.5rem 2rem 2.5rem;text-align:left;background:#FFF;border-radius:.3rem;border:solid 1px #DDD;min-width:50%;box-shadow:0 0 10px rgba(0,0,0,0.2);max-height:calc(100% - 9rem);display:inline-block;position:relative}._1DOmVUfSkwJZ3s2MuilBz1 ._1yEofscHqzfBmClI1daIil{overflow:auto;position:relative;height:100%}._1DOmVUfSkwJZ3s2MuilBz1 ._3dV_Jb1sBEduzEhJd6L-Lk{color:#AAA;font-weight:bold;text-align:right;cursor:pointer;position:absolute;top:.5rem;right:.7rem;font-size:1.1rem;line-height:1.1rem}._1DOmVUfSkwJZ3s2MuilBz1 ._3dV_Jb1sBEduzEhJd6L-Lk:hover{color:#777}._1DOmVUfSkwJZ3s2MuilBz1._3iRqOp0bRmbpSioTmQBZVv{background:transparent;border:none;margin:.5rem auto;max-width:90%;box-shadow:none}._1DOmVUfSkwJZ3s2MuilBz1._3iRqOp0bRmbpSioTmQBZVv ._3dV_Jb1sBEduzEhJd6L-Lk{color:#FFF;text-shadow:0 0 3px #555}._1hgw1CbA67Rcyij6coocjU{position:absolute;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.2);z-index:9;display:flex;flex-direction:column;justify-content:center;align-items:center}body._3ee937Js5f3bkW24EKGGJD{height:100%;overflow:hidden}@media all and (max-width: 70rem){._1hgw1CbA67Rcyij6coocjU{justify-content:end}._1DOmVUfSkwJZ3s2MuilBz1{width:100%;padding:1rem 1.5rem 4rem 1.5rem;border-radius:1rem 1rem 0 0}._3dV_Jb1sBEduzEhJd6L-Lk{display:none}}@media all and (min-width: 70.01rem){._1DOmVUfSkwJZ3s2MuilBz1{max-width:90%}}\n",""]),i.locals={dialog:"_1DOmVUfSkwJZ3s2MuilBz1",inner:"_1yEofscHqzfBmClI1daIil",close:"_3dV_Jb1sBEduzEhJd6L-Lk",transparent:"_3iRqOp0bRmbpSioTmQBZVv","dialog-backdrop":"_1hgw1CbA67Rcyij6coocjU","has-dialog":"_3ee937Js5f3bkW24EKGGJD"};const r=i},645:e=>{e.exports=function(e){var t=[];return t.toString=function(){return this.map((function(t){var n=e(t);return t[2]?"@media ".concat(t[2]," {").concat(n,"}"):n})).join("")},t.i=function(e,n,o){"string"==typeof e&&(e=[[null,e,""]]);var i={};if(o)for(var r=0;r<this.length;r++){var a=this[r][0];null!=a&&(i[a]=!0)}for(var s=0;s<e.length;s++){var d=[].concat(e[s]);o&&i[d[0]]||(n&&(d[2]?d[2]="".concat(n," and ").concat(d[2]):d[2]=n),t.push(d))}},t}},379:(e,t,n)=>{var o,i=function(){var e={};return function(t){if(void 0===e[t]){var n=document.querySelector(t);if(window.HTMLIFrameElement&&n instanceof window.HTMLIFrameElement)try{n=n.contentDocument.head}catch(e){n=null}e[t]=n}return e[t]}}(),r=[];function a(e){for(var t=-1,n=0;n<r.length;n++)if(r[n].identifier===e){t=n;break}return t}function s(e,t){for(var n={},o=[],i=0;i<e.length;i++){var s=e[i],d=t.base?s[0]+t.base:s[0],c=n[d]||0,l="".concat(d," ").concat(c);n[d]=c+1;var u=a(l),f={css:s[1],media:s[2],sourceMap:s[3]};-1!==u?(r[u].references++,r[u].updater(f)):r.push({identifier:l,updater:p(f,t),references:1}),o.push(l)}return o}function d(e){var t=document.createElement("style"),o=e.attributes||{};if(void 0===o.nonce){var r=n.nc;r&&(o.nonce=r)}if(Object.keys(o).forEach((function(e){t.setAttribute(e,o[e])})),"function"==typeof e.insert)e.insert(t);else{var a=i(e.insert||"head");if(!a)throw new Error("Couldn't find a style target. This probably means that the value for the 'insert' parameter is invalid.");a.appendChild(t)}return t}var c,l=(c=[],function(e,t){return c[e]=t,c.filter(Boolean).join("\n")});function u(e,t,n,o){var i=n?"":o.media?"@media ".concat(o.media," {").concat(o.css,"}"):o.css;if(e.styleSheet)e.styleSheet.cssText=l(t,i);else{var r=document.createTextNode(i),a=e.childNodes;a[t]&&e.removeChild(a[t]),a.length?e.insertBefore(r,a[t]):e.appendChild(r)}}function f(e,t,n){var o=n.css,i=n.media,r=n.sourceMap;if(i?e.setAttribute("media",i):e.removeAttribute("media"),r&&"undefined"!=typeof btoa&&(o+="\n/*# sourceMappingURL=data:application/json;base64,".concat(btoa(unescape(encodeURIComponent(JSON.stringify(r))))," */")),e.styleSheet)e.styleSheet.cssText=o;else{for(;e.firstChild;)e.removeChild(e.firstChild);e.appendChild(document.createTextNode(o))}}var m=null,h=0;function p(e,t){var n,o,i;if(t.singleton){var r=h++;n=m||(m=d(t)),o=u.bind(null,n,r,!1),i=u.bind(null,n,r,!0)}else n=d(t),o=f.bind(null,n,t),i=function(){!function(e){if(null===e.parentNode)return!1;e.parentNode.removeChild(e)}(n)};return o(e),function(t){if(t){if(t.css===e.css&&t.media===e.media&&t.sourceMap===e.sourceMap)return;o(e=t)}else i()}}e.exports=function(e,t){(t=t||{}).singleton||"boolean"==typeof t.singleton||(t.singleton=(void 0===o&&(o=Boolean(window&&document&&document.all&&!window.atob)),o));var n=s(e=e||[],t);return function(e){if(e=e||[],"[object Array]"===Object.prototype.toString.call(e)){for(var o=0;o<n.length;o++){var i=a(n[o]);r[i].references--}for(var d=s(e,t),c=0;c<n.length;c++){var l=a(n[c]);0===r[l].references&&(r[l].updater(),r.splice(l,1))}n=d}}}}},t={};function n(o){var i=t[o];if(void 0!==i)return i.exports;var r=t[o]={id:o,exports:{}};return e[o](r,r.exports,n),r.exports}n.n=e=>{var t=e&&e.__esModule?()=>e.default:()=>e;return n.d(t,{a:t}),t},n.d=(e,t)=>{for(var o in t)n.o(t,o)&&!n.o(e,o)&&Object.defineProperty(e,o,{enumerable:!0,get:t[o]})},n.o=(e,t)=>Object.prototype.hasOwnProperty.call(e,t),n.r=e=>{"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})};var o={};return(()=>{n.r(o),n.d(o,{default:()=>f});var e=n(379),t=n.n(e),i=n(793);t()(i.Z,{insert:"head",singleton:!1});const r=i.Z.locals||{};function a(e){return e[e.length-1]}var s=n(130);t()(s.Z,{insert:"head",singleton:!1});const d=s.Z.locals||{};let c;if(window.m3w&&window.m3w.Overlay)c=window.m3w.Overlay,1!==c.version&&console.info("There are several different instances of overlay running. This may lead to versioning issues");else{let e=[],t=!1;const n=1e3;c=function(e){this.closed=!0,this._onclose=e,this.container=document.createElement("div"),this.container.classList.add(d.overlay)},c.prototype={isTop:function(){return a(e)===this},_notify:function(){this.closed||(this._onclose(this),t||this.close())},open:function(){if(this.closed)return this.closed=!1,e.push(this),document.body.appendChild(this.container),this.container.style.zIndex=this.zindex(),this},close:function(){this.closed||(this.closed=!0,e.splice(e.indexOf(this),1),document.body.removeChild(this.container))},html:function(){return this.container},zindex:function(){return n+e.indexOf(this)},block:function(){this.isTop()&&(t=!0)}},c.version=1,document.body.addEventListener("click",(function(){window.setTimeout((function(){window.setTimeout((function(){t=!1}),0),t||e[0]&&a(e)._notify()}),0)}))}window.m3w=window.m3w||{},window.m3w.Overlay=c;const l=c,u="function"==typeof document.createElement("div").animate;function f(e,t){var n=this,o=new l((function(){o.block(),n.hide()})),i=o.html().appendChild(document.createElement("div")),a=i.appendChild(document.createElement("div")),s=a.appendChild(document.createElement("div")),d=a.appendChild(document.createElement("div")),c=void 0,f=void 0;(t=t||{}).transparent?a.className=`${r.dialog} ${r.transparent}`:a.className=r.dialog,i.className=r["dialog-backdrop"],s.className=r.close,d.className=r.inner,f=e.parentNode.insertBefore(document.createElement("span"),e),this.show=function(){d.appendChild(e),t.width&&(a.style.width=t.width),s.innerHTML="&times;",c=window.pageYOffset,document.body.classList.add(r["has-dialog"]),document.body.scrollTo(0,c),s.addEventListener("click",(function(){n.hide()})),a.addEventListener("click",(function(){o.block()})),o.open(),o.block(),u?(i.animate({opacity:[0,1]},{duration:300}),a.animate({transform:["translate(0, 5rem)","translate(0, 0)"]},{duration:200})):i.style.opacity=1,d.style.maxHeight="calc("+window.innerHeight+"px - 14rem)"},this.hide=function(){const t=function(){f.parentNode.insertBefore(e,f),o.close(),document.body.classList.remove(r["has-dialog"]),window.scrollTo(0,c)};u?(a.animate({transform:["translate(0, 0)","translate(0, 5rem)"]},{duration:200}),i.animate({opacity:[1,0]},{duration:200}).onfinish=t):(i.style.opacity=0,t())}}})(),o})()}));

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
/******/ 	__webpack_require__("./resources/assets/js/app.js");
/******/ 	// This entry module used 'exports' so it can't be inlined
/******/ })()
;