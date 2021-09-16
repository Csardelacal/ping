/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

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

/***/ "./node_modules/m3w-_scss/dist/_scss.js":
/*!**********************************************!*\
  !*** ./node_modules/m3w-_scss/dist/_scss.js ***!
  \**********************************************/
/***/ (() => {

var _SCSS;_SCSS=(()=>{var t={491:(t,e,n)=>{"use strict";n.r(e),n.d(e,{sidebar:()=>o.a});var r=n(171),o=n.n(r);o()(document.querySelector(".sidebar"));var a=document.body;a&&a.classList.add("_scss-js-loaded")},171:()=>{depend(["m3/ui/sticky","m3/animation/animation","m3/hid/gestures/gestures"],(function(t,e,n){var r=function(t,e){for(var n in e)e.hasOwnProperty(n)&&t.addEventListener(n,e[n],!1)};return function(t){var o=t.parentNode,a=0,i=300,s=!1,u=!1,c=function(n){var r=a;e((function(e){var s=(a=r+(n-r)*e)/i*.3;t.style.transform="translate("+(a-i)+"px, 0px)",o.style.background="rgba(0, 0, 0, "+s+")",o.style.display=a/i==0?"none":"block"}),300,"easeInEaseOut")},d=function(){u=!1,c(0)},l=function(){u=!0,c(i)},f=new n(document,"swipe"),p=void 0;return f.init((function(t){if(s)return!1;p=a})),f.follow((function(e,n){"h"===e.direction&&e.startX>100&&(window.requestAnimationFrame((function(){var n=1+Math.max(0,Math.min(p+e.endX-e.startX,i)),r=n/i*.3;t.style.transform="translate("+(n-i)+"px, 0px)",o.style.background="rgba(0, 0, 0, "+r+")",o.style.display="block"})),n())})),f.end((function(t,e){"h"!==t.direction||t.startX<100||(a=1+Math.max(0,Math.min(p+t.endX-t.startX,i)),t.endX-t.startX>0?l():d(),e())})),r(document,{click:function(t){t.target.classList.contains("toggle-button")&&(u?d():l(),t.preventDefault())}}),r(o,{click:d}),r(t,{click:function(t){t.stopPropagation()}}),{disabled:function(){d(),s=!0},enable:function(){s=!1}}}}))}},e={};function n(r){if(e[r])return e[r].exports;var o=e[r]={exports:{}};return t[r](o,o.exports,n),o.exports}return n.n=t=>{var e=t&&t.__esModule?()=>t.default:()=>t;return n.d(e,{a:e}),e},n.d=(t,e)=>{for(var r in e)n.o(e,r)&&!n.o(t,r)&&Object.defineProperty(t,r,{enumerable:!0,get:e[r]})},n.o=(t,e)=>Object.prototype.hasOwnProperty.call(t,e),n.r=t=>{"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(t,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(t,"__esModule",{value:!0})},n(491)})();

/***/ }),

/***/ "./node_modules/m3w-dialog/dist/dialog.js":
/*!************************************************!*\
  !*** ./node_modules/m3w-dialog/dist/dialog.js ***!
  \************************************************/
/***/ ((module) => {

!function(e,t){ true?module.exports=t():0}(self,(function(){return(()=>{"use strict";var e={130:(e,t,n)=>{n.d(t,{Z:()=>r});var i=n(645),o=n.n(i)()((function(e){return e[1]}));o.push([e.id,"._4t7TiX0vQOD0jjQpQlsg5{position:absolute;top:0;left:0}._4t7TiX0vQOD0jjQpQlsg5.nzZBSmVLQ0SqrhPuSCGgZ{position:fixed;height:100%;width:100%}\n",""]),o.locals={overlay:"_4t7TiX0vQOD0jjQpQlsg5",fixed:"nzZBSmVLQ0SqrhPuSCGgZ"};const r=o},793:(e,t,n)=>{n.d(t,{Z:()=>r});var i=n(645),o=n.n(i)()((function(e){return e[1]}));o.push([e.id,"._1DOmVUfSkwJZ3s2MuilBz1{box-sizing:border-box;padding:2rem 2.5rem 2rem 2.5rem;text-align:left;background:#FFF;border-radius:.3rem;border:solid 1px #DDD;min-width:50%;box-shadow:0 0 10px rgba(0,0,0,0.2);max-height:calc(100% - 9rem);display:inline-block;position:relative}._1DOmVUfSkwJZ3s2MuilBz1 ._1yEofscHqzfBmClI1daIil{overflow:auto;position:relative;height:100%}._1DOmVUfSkwJZ3s2MuilBz1 ._3dV_Jb1sBEduzEhJd6L-Lk{color:#AAA;font-weight:bold;text-align:right;cursor:pointer;position:absolute;top:.5rem;right:.7rem;font-size:1.1rem;line-height:1.1rem}._1DOmVUfSkwJZ3s2MuilBz1 ._3dV_Jb1sBEduzEhJd6L-Lk:hover{color:#777}._1DOmVUfSkwJZ3s2MuilBz1._3iRqOp0bRmbpSioTmQBZVv{background:transparent;border:none;margin:.5rem auto;max-width:90%;box-shadow:none}._1DOmVUfSkwJZ3s2MuilBz1._3iRqOp0bRmbpSioTmQBZVv ._3dV_Jb1sBEduzEhJd6L-Lk{color:#FFF;text-shadow:0 0 3px #555}._1hgw1CbA67Rcyij6coocjU{position:absolute;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.2);z-index:9;display:flex;flex-direction:column;justify-content:center;align-items:center}body._3ee937Js5f3bkW24EKGGJD{height:100%;overflow:hidden}@media all and (max-width: 70rem){._1hgw1CbA67Rcyij6coocjU{justify-content:end}._1DOmVUfSkwJZ3s2MuilBz1{width:100%;padding:1rem 1.5rem 4rem 1.5rem;border-radius:1rem 1rem 0 0}._3dV_Jb1sBEduzEhJd6L-Lk{display:none}}@media all and (min-width: 70.01rem){._1DOmVUfSkwJZ3s2MuilBz1{max-width:90%}}\n",""]),o.locals={dialog:"_1DOmVUfSkwJZ3s2MuilBz1",inner:"_1yEofscHqzfBmClI1daIil",close:"_3dV_Jb1sBEduzEhJd6L-Lk",transparent:"_3iRqOp0bRmbpSioTmQBZVv","dialog-backdrop":"_1hgw1CbA67Rcyij6coocjU","has-dialog":"_3ee937Js5f3bkW24EKGGJD"};const r=o},645:e=>{e.exports=function(e){var t=[];return t.toString=function(){return this.map((function(t){var n=e(t);return t[2]?"@media ".concat(t[2]," {").concat(n,"}"):n})).join("")},t.i=function(e,n,i){"string"==typeof e&&(e=[[null,e,""]]);var o={};if(i)for(var r=0;r<this.length;r++){var a=this[r][0];null!=a&&(o[a]=!0)}for(var s=0;s<e.length;s++){var d=[].concat(e[s]);i&&o[d[0]]||(n&&(d[2]?d[2]="".concat(n," and ").concat(d[2]):d[2]=n),t.push(d))}},t}},695:e=>{var t={};e.exports=function(e){if(void 0===t[e]){var n=document.querySelector(e);if(window.HTMLIFrameElement&&n instanceof window.HTMLIFrameElement)try{n=n.contentDocument.head}catch(e){n=null}t[e]=n}return t[e]}},379:e=>{var t=[];function n(e){for(var n=-1,i=0;i<t.length;i++)if(t[i].identifier===e){n=i;break}return n}function i(e,i){for(var r={},a=[],s=0;s<e.length;s++){var d=e[s],c=i.base?d[0]+i.base:d[0],l=r[c]||0,u="".concat(c," ").concat(l);r[c]=l+1;var f=n(u),m={css:d[1],media:d[2],sourceMap:d[3]};-1!==f?(t[f].references++,t[f].updater(m)):t.push({identifier:u,updater:o(m,i),references:1}),a.push(u)}return a}function o(e,t){var n=t.domAPI(t);return n.update(e),function(t){if(t){if(t.css===e.css&&t.media===e.media&&t.sourceMap===e.sourceMap)return;n.update(e=t)}else n.remove()}}e.exports=function(e,o){var r=i(e=e||[],o=o||{});return function(e){e=e||[];for(var a=0;a<r.length;a++){var s=n(r[a]);t[s].references--}for(var d=i(e,o),c=0;c<r.length;c++){var l=n(r[c]);0===t[l].references&&(t[l].updater(),t.splice(l,1))}r=d}}},216:e=>{e.exports=function(e){var t=document.createElement("style");return e.setAttributes(t,e.attributes),e.insert(t),t}},795:e=>{e.exports=function(e){var t=e.insertStyleElement(e);return{update:function(n){!function(e,t,n){var i=n.css,o=n.media,r=n.sourceMap;o?e.setAttribute("media",o):e.removeAttribute("media"),r&&"undefined"!=typeof btoa&&(i+="\n/*# sourceMappingURL=data:application/json;base64,".concat(btoa(unescape(encodeURIComponent(JSON.stringify(r))))," */")),t.styleTagTransform(i,e)}(t,e,n)},remove:function(){!function(e){if(null===e.parentNode)return!1;e.parentNode.removeChild(e)}(t)}}}}},t={};function n(i){var o=t[i];if(void 0!==o)return o.exports;var r=t[i]={id:i,exports:{}};return e[i](r,r.exports,n),r.exports}n.n=e=>{var t=e&&e.__esModule?()=>e.default:()=>e;return n.d(t,{a:t}),t},n.d=(e,t)=>{for(var i in t)n.o(t,i)&&!n.o(e,i)&&Object.defineProperty(e,i,{enumerable:!0,get:t[i]})},n.o=(e,t)=>Object.prototype.hasOwnProperty.call(e,t),n.r=e=>{"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})};var i={};return(()=>{n.r(i),n.d(i,{default:()=>g});var e=n(379),t=n.n(e),o=n(795),r=n.n(o),a=n(695),s=n.n(a),d=n(216),c=n.n(d),l=n(793),u={styleTagTransform:function(e,t){if(t.styleSheet)t.styleSheet.cssText=e;else{for(;t.firstChild;)t.removeChild(t.firstChild);t.appendChild(document.createTextNode(e))}},setAttributes:function(e){var t=n.nc;t&&e.setAttribute("nonce",t)},insert:function(e){var t=s()("head");if(!t)throw new Error("Couldn't find a style target. This probably means that the value for the 'insert' parameter is invalid.");t.appendChild(e)}};u.domAPI=r(),u.insertStyleElement=c(),t()(l.Z,u);const f=l.Z&&l.Z.locals?l.Z.locals:void 0;function m(e){return e[e.length-1]}var h=n(130),p={styleTagTransform:function(e,t){if(t.styleSheet)t.styleSheet.cssText=e;else{for(;t.firstChild;)t.removeChild(t.firstChild);t.appendChild(document.createTextNode(e))}},setAttributes:function(e){var t=n.nc;t&&e.setAttribute("nonce",t)},insert:function(e){var t=s()("head");if(!t)throw new Error("Couldn't find a style target. This probably means that the value for the 'insert' parameter is invalid.");t.appendChild(e)}};p.domAPI=r(),p.insertStyleElement=c(),t()(h.Z,p);const v=h.Z&&h.Z.locals?h.Z.locals:void 0;let b;if(window.m3w&&window.m3w.Overlay)b=window.m3w.Overlay,1!==b.version&&console.info("There are several different instances of overlay running. This may lead to versioning issues");else{let e=[],t=!1;const n=1e3;b=function(e){this.closed=!0,this._onclose=e,this.container=document.createElement("div"),this.container.classList.add(v.overlay)},b.prototype={isTop:function(){return m(e)===this},_notify:function(){this.closed||(this._onclose(this),this.close())},open:function(){if(this.closed)return this.closed=!1,e.push(this),document.body.appendChild(this.container),this.container.style.zIndex=this.zindex(),this},close:function(){this.closed||(this.closed=!0,e.splice(e.indexOf(this),1),document.body.removeChild(this.container))},html:function(){return this.container},zindex:function(){return n+e.indexOf(this)},block:function(){this.isTop()&&(t=!0)},setFixed:function(e){e?this.container.classList.add(v.fixed):this.container.classList.remove("fixed")}},b.version=1,document.body.addEventListener("click",(function(){window.setTimeout((function(){window.setTimeout((function(){t=!1}),0),t||(console.log("Document click"),e[0]&&m(e)._notify())}),0)}))}window.m3w=window.m3w||{},window.m3w.Overlay=b;const y=b,w="function"==typeof document.createElement("div").animate;function g(e,t){var n=this,i=new y((function(){i.block(),n.hide()}));i.setFixed(!0);var o=i.html().appendChild(document.createElement("div")),r=o.appendChild(document.createElement("div")),a=r.appendChild(document.createElement("div")),s=r.appendChild(document.createElement("div")),d=void 0,c=void 0;(t=t||{}).transparent?r.className=`${f.dialog} ${f.transparent}`:r.className=f.dialog,o.className=f["dialog-backdrop"],a.className=f.close,s.className=f.inner,c=e.parentNode.insertBefore(document.createElement("span"),e),this.show=function(){s.appendChild(e),t.width&&(r.style.width=t.width),a.innerHTML="&times;",d=window.pageYOffset,document.body.classList.add(f["has-dialog"]),document.body.scrollTo(0,d),a.addEventListener("click",(function(){n.hide()})),r.addEventListener("click",(function(){i.block()})),i.open(),i.block(),w?(o.animate({opacity:[0,1]},{duration:300}),r.animate({transform:["translate(0, 5rem)","translate(0, 0)"]},{duration:200})):o.style.opacity=1,s.style.maxHeight="calc("+window.innerHeight+"px - 14rem)"},this.hide=function(){const t=function(){c.parentNode.insertBefore(e,c),i.close(),document.body.classList.remove(f["has-dialog"]),window.scrollTo(0,d)};w?(r.animate({transform:["translate(0, 0)","translate(0, 5rem)"]},{duration:200}),o.animate({opacity:[1,0]},{duration:200}).onfinish=t):(o.style.opacity=0,t())}}})(),i})()}));

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
/************************************************************************/
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
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be in strict mode.
(() => {
"use strict";
/*!************************************!*\
  !*** ./resources/assets/js/app.js ***!
  \************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var delegate__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! delegate */ "./node_modules/delegate/src/delegate.js");
/* harmony import */ var delegate__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(delegate__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var m3w_dialog__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! m3w-dialog */ "./node_modules/m3w-dialog/dist/dialog.js");
/* harmony import */ var m3w_dialog__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(m3w_dialog__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var m3w_scss_dist_scss__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! m3w-_scss/dist/_scss */ "./node_modules/m3w-_scss/dist/_scss.js");
/* harmony import */ var m3w_scss_dist_scss__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(m3w_scss_dist_scss__WEBPACK_IMPORTED_MODULE_2__);



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
})();

/******/ })()
;