(()=>{var t={828:t=>{if("undefined"!=typeof Element&&!Element.prototype.matches){var e=Element.prototype;e.matches=e.matchesSelector||e.mozMatchesSelector||e.msMatchesSelector||e.oMatchesSelector||e.webkitMatchesSelector}t.exports=function(t,e){for(;t&&9!==t.nodeType;){if("function"==typeof t.matches&&t.matches(e))return t;t=t.parentNode}}},438:(t,e,n)=>{var i=n(828);function r(t,e,n,i,r){var s=o.apply(this,arguments);return t.addEventListener(n,s,r),{destroy:function(){t.removeEventListener(n,s,r)}}}function o(t,e,n,r){return function(n){n.delegateTarget=i(n.target,e),n.delegateTarget&&r.call(t,n)}}t.exports=function(t,e,n,i,o){return"function"==typeof t.addEventListener?r.apply(null,arguments):"function"==typeof n?r.bind(null,document).apply(null,arguments):("string"==typeof t&&(t=document.querySelectorAll(t)),Array.prototype.map.call(t,(function(t){return r(t,e,n,i,o)})))}},348:()=>{(()=>{var t={828:t=>{if("undefined"!=typeof Element&&!Element.prototype.matches){var e=Element.prototype;e.matches=e.matchesSelector||e.mozMatchesSelector||e.msMatchesSelector||e.oMatchesSelector||e.webkitMatchesSelector}t.exports=function(t,e){for(;t&&9!==t.nodeType;){if("function"==typeof t.matches&&t.matches(e))return t;t=t.parentNode}}},438:(t,e,n)=>{var i=n(828);function r(t,e,n,i,r){var s=o.apply(this,arguments);return t.addEventListener(n,s,r),{destroy:function(){t.removeEventListener(n,s,r)}}}function o(t,e,n,r){return function(n){n.delegateTarget=i(n.target,e),n.delegateTarget&&r.call(t,n)}}t.exports=function(t,e,n,i,o){return"function"==typeof t.addEventListener?r.apply(null,arguments):"function"==typeof n?r.bind(null,document).apply(null,arguments):("string"==typeof t&&(t=document.querySelectorAll(t)),Array.prototype.map.call(t,(function(t){return r(t,e,n,i,o)})))}},132:t=>{"use strict";var e={byMatcher:function(t,e,n){if(void 0===n&&(n={}),null===n||Array.isArray(n)||"object"!=typeof n)throw new Error("Expected opts to be an object.");if(t&&t!==document)return e(t)?t:this.byMatcher(t.parentNode,e,n);if(n.throwOnMiss)throw new Error("Expected to find parent node, but none was found.")},byClassName:function(t,e,n){return this.byMatcher(t,(function(t){return t.classList.contains(e)}),n)},withDataAttribute:function(t,e,n){return this.byMatcher(t,(function(t){return t.dataset.hasOwnProperty(e)}),n)}};t.exports=e},34:(t,e,n)=>{"use strict";n.r(e),n.d(e,{View:()=>m});var i=n(438),r=n.n(i),o=n(132),s=n.n(o),a=function(t){t instanceof NodeList&&(t=Array.prototype.slice.call(t,0)),this.elements=t};function u(t){return new a(t)}a.prototype={each:function(t){var e=this.elements instanceof Array?[]:{};if(!this.elements instanceof Array)for(var n in this.elements)this.elements.hasOwnProperty(n)&&(e[n]=t(this.elements[n],n));else for(n=0;n<this.elements.length;n++)e[n]=t(this.elements[n],n);return new a(e)},filter:function(t){var e=new a([]);return this.each((function(n){t(n)&&e.push(n)})),e},merge:function(t){var e=this;return t instanceof a||(t=new a(t)),t.each((function(t,n){e.elements instanceof Array?e.elements.push(t):e.elements[n]=t})),this},reduce:function(t){return this.elements.reduce(t)},push:function(t){this.elements.push(t)},pop:function(){return this.elements.pop()},set:function(t,e){this.elements[t]=e},get:function(t){return this.elements[t]},raw:function(){return this.elements},length:function(){if(this.elements instanceof Array)return this.elements.length;var t=0;return this.each((function(){t++})),t}};var h=function(t){this.element=t,this.view=void 0;var e=this;this.element.addEventListener("onkeyup",(function(){e.view.set(e.for()[0],this.value)})),this.element.addEventListener("change",(function(){"radio"===this.type||"checkbox"===this.type?e.view.set(e.for()[0],"on"===this.value||this.value||this.checked):e.view.set(e.for()[0],this.value)}))};function c(t){this.view=void 0,this.getValue=function(){return-1===t.selectedIndex?null:t.options[this.getElement().selectedIndex].value},this.setValue=function(e){var n=Array.prototype.slice.call(t.options,0);t.selectedIndex=n.indexOf(t.querySelector('[value="'+e+'"]'))},this.for=function(){return[t.getAttribute("data-for")]},this.parent=function(t){return this.view=t,this},this.refresh=function(){var e=Array.prototype.slice.call(t.options,0);t.selectedIndex=e.indexOf(t.querySelector('[value="'+this.view.get(this.for()[0])+'"]'))}}function l(t){this.view=void 0,this.getValue=function(){return t.innerHTML},this.setValue=function(e){return t.innerHTML=e,this},this.for=function(){return[t.getAttribute("data-for")]},this.parent=function(t){return this.view=t,this},this.refresh=function(){t.innerHTML=this.view.get(this.for()[0])}}function f(t,e,n){this.element=t,this.name=e,this.value=n,this.adapters=this.makeAdapters(),this.view=void 0,this.setData=function(t){for(var e=0;e<this.adapters.length;e++)this.adapters[e].setValue(t[this.adapters[e].getName()])},this.replace=function(){for(var t="",e=0;e<this.adapters.length;e++)t+=this.adapters[e].replace();return t}}function p(t,e){var n=null;this.setValue=function(t){n=t},this.getValue=function(){return n},this.getName=function(){return-1!==t.indexOf("?")?t.substr(0,t.indexOf("?")):t},this.isReadOnly=function(){return e},this.replace=function(){if(e)return t;if(-1!==t.indexOf("?")){var i=t.substr(t.indexOf("?")+1).split(":");return n?i[0]:i[1]}return n}}if(h.prototype={readOnly:function(){return!1},for:function(){return[this.element.getAttribute("data-for")]},parent:function(t){return this.view=t,this},refresh:function(){var t=this.view.get(this.for()[0]);if(console.log(this.element.type),"radio"===this.element.type||"checkbox"===this.element.type)return console.log(this.for()[0]),console.log(t),void(this.element.value&&this.element.value===t||!0===t?this.element.checked=!0:this.element.checked=!1);this.element.value=t}},f.prototype={hasLysine:function(){return-1!==this.name.search(/^data-lysine-/)},getAttributeName:function(){return this.name.replace(/^data-lysine-/,"").toLowerCase()},makeAdapters:function(){if(!this.hasLysine())return[];for(var t=/\{\{([A-Za-z0-9\.\s\?\-\:\_]+)\}\}/g,e=[],n=this.value.split(/\{\{[A-Za-z0-9\.\s\?\-\:\_]+\}\}/g),i=t.exec(this.value);i;)e.push(new p(n.shift(),!0)),e.push(new p(i[1],!1)),i=t.exec(this.value);return n.length>0&&e.push(new p(n.shift(),!0)),e},for:function(){var t=u([]);return u(this.adapters).each((function(e){e.isReadOnly()||t.push(e.getName())})),t.raw()},parent:function(t){return this.view=t,this},refresh:function(){var t=this;u(this.adapters).each((function(e){e.isReadOnly()||e.setValue(t.view.get(e.getName()))})),this.element.setAttribute(this.getAttributeName(),this.replace())}},void 0===HTMLElement)throw"Lysine requires a browser to work. HTMLElement class was not found";if(void 0===window)throw"Lysine requires a browser to work. Window variable was not found";function d(t){this.views=[],this.base=t,this.parentView=void 0,this.listeners=u([]),this.writeProtect=!1,this._setup=u([]),this._tearDown=u([]),this.getValue=function(){var t,e=[];for(this.views=this.views.filter((function(t){return!t.isDestroyed()})),t=0;t<this.views.length;t+=1)e.push(this.views[t].getValue());return e},this.setValue=function(t){var e,n;if(!this.writeProtect&&void 0!==t){for(t=t.filter((function(t){return!!t})),this.views=this.views.filter((function(t){return t.reset()&&!t.isDestroyed()}));t.length<this.views.length;)this.views[t.length].destroy();this.views=this.views.slice(0,t.length);var i=this._tearDown;for(u(this.views).each((function(t){i.each((function(e){e(t)}))})),e=this.views.length;e<t.length;e+=1)n=new m(this.base),this.views.push(n),n.setParent(this),this.listeners.each((function(t){n.on.apply(n,t)}));for(i=this._setup,u(this.views).each((function(t){i.each((function(e){e(t)}))})),e=0;e<t.length;e++)this.views[e].setValue(t[e])}},this.for=function(){return[this.base.getAttribute("data-for")]},this.on=function(t,e,n){this.listeners.push([t,e,n]),this.views.forEach((function(i){i.on(t,e,n)}))},this.setUp=function(t){this._setup.push(t)},this.tearDown=function(t){this._tearDown.push(t)},this.parent=function(t){return this.parentView=t,this},this.push=function(t){var e=new m(this.base);return this.views.push(e),e.setValue(t),e.setParent(this),this.listeners.each((function(t){e.on.apply(e,t)})),this.propagate(),e},this.refresh=function(){this.setValue(this.parentView.get(this.for()[0]))},this.propagate=function(){this.writeProtect=!0,this.parentView.set(this.for()[0],this.getValue()),this.writeProtect=!1}}function v(t,e,n){var i=/([a-zA-Z_0-9]+)\(([a-zA-Z_0-9\-]+)\)\s?(\=\=|\!\=)\s?(.+)/g.exec(t);if(null===i)throw"Malformed expression: "+t;var r=i[1],o=i[2],s=i[3],a=i[4],h=void 0,c=e.parentNode,l=e.nextSibling;this.isVisible=function(){var t=void 0;switch(r){case"null":t=null===h.get(o)?"true":"false";break;case"bool":t=!0===h.get(o)?"true":"false";break;case"count":t=h.get(o)?h.get(o).length:0;break;case"value":t=h.get(o)}return"=="===s?t==a:t!=a},this.test=function(){var t=this.isVisible();t!==(e.parentNode===c)&&(t?c.insertBefore(e,l):c.removeChild(e))},this.for=function(){var t=u([]);return n.each((function(e){t.merge(e.for())})),t.push(o),t.raw()},this.parent=function(t){return h=t,n.each((function(e){e.parent(t)})),this},this.refresh=function(){this.test(),this.isVisible()&&n.each((function(t){t.refresh()}))}}function m(t,e){var n,i,o=[],a={},p={modules:void 0};Object.assign(p,e),this.destroyed=!1,this.parent=void 0,this._module=p.modules,n=t instanceof HTMLElement?t:document.querySelector('*[data-lysine-view="'+t+'"]'),this.set=function(t,e){if("^"===t.substr(0,1))return this.parent.parentView.set(t.substr(1),e);for(var n=a,i=t.split("."),r=i.pop(),o=0;o<i.length;o++)n[i[o]]||(n[i[o]]={}),n=n[i[o]];n[r]=e,this.adapters.each((function(t){-1!==t.for().indexOf(i[0]||r)&&t.refresh()})),this.parent&&this.parent.propagate(this,a)},this.get=function(t){if("^"===t.substr(0,1))return this.parent.parentView.get(t.substr(1));for(var e=a,n=t.split("."),i=0;i<n.length;i++)e=e?e[n[i]]:void 0;return e},this.setData=function(t){a=t,this.adapters.each((function(t){t.refresh()}))},this.getData=function(){return a},this.reset=function(){for(var t=i.querySelectorAll("input"),e=0;e<t.length;e++)switch(t[e].type){case"checkbox":case"radio":t[e].checked=t[e].hasAttribute("checked");default:t[e].value=t[e].hasAttribute("value")?t[e].getAttribute("value"):""}return!0},this.getValue=this.getData,this.setValue=this.setData,this.fetchAdapters=function(t){t=void 0!==t?t:i;var e=u([]),n=this;return u(t.childNodes).each((function(t){var i,r=u([]);if(3!==t.nodeType){if(t.getAttribute&&t.getAttribute("data-for"))if(t.hasAttribute("data-lysine-view"))r.merge(u([new d(t).parent(n)]));else{var o=u([]).merge((i=t,!i.getAttribute("data-for")||"input"!==i.tagName.toLowerCase()&&"textarea"!==i.tagName.toLowerCase()?[]:[new h(i)])).merge(function(t){return"select"===t.tagName.toLowerCase()?[new c(t)]:[]}(t)).merge(function(t){return"input"!==t.tagName.toLowerCase()&&"textarea"!==t.tagName.toLowerCase()&&"select"!==t.tagName.toLowerCase()&&t.hasAttribute("data-for")?[new l(t)]:[]}(t));r.merge(o.each((function(t){return t.parent(n)})))}else r.merge(n.fetchAdapters(t));if(r.merge(function(t){var e=t.attributes,n=u([]);if(!e)return n;for(var i=0;i<e.length;i++)n.push(new f(t,e[i].name,e[i].value));return n.filter((function(t){return t.hasLysine()}))}(t).each((function(t){return t.parent(n)}))),t.getAttribute&&t.getAttribute("data-condition")){var s=new v(t.getAttribute("data-condition"),t,r);e.push(s.parent(n))}else e.merge(r)}})),e},this.getHTML=function(){return i},this.getElement=this.getHTML,this.destroy=function(){return this.destroyed=!0,this.parent&&this.parent.propagate(),i.parentNode.removeChild(i),u(o).each((function(t){document.removeEventListener(t[0],t[1])})),this},this.isDestroyed=function(){return this.destroyed},this.on=function(t,e,n){var i=this,a=r()(e,(function(e){return s()(e,(function(t){return t===i.getHTML()}))&&u(i.getHTML().querySelectorAll(t)).filter((function(t){return t===e})).raw()[0]}),(function(t,e){n.call(e,t,i)}));return o.push([e,a]),a},this.sub=function(t){return this.adapters.filter((function(e){return-1!==e.for().indexOf(t)})).get(0)},this.setParent=function(t){this.parent=t},this.find=function(t){return this.getHTML().querySelector(t)},this.findAll=function(t){return this.getHTML().querySelectorAll(t)},this.module=function(t){return this._module=t,this.refreshModules(),this},this.refreshModules=function(t){var e=this;if(void 0!==e._module){t=t||i;for(var n=[],r=0;r<t.classList.length;r++)n.push(e._module[t.classList[r]]||t.classList[r]);t.className="",t.classList.add(...n),t.id=e._module[t.id]||t.id,u(t.childNodes).each((function(t){3!==t.nodeType&&e.refreshModules(t)}))}};var m=void 0;if(n.content)m=n.content.firstElementChild;else for(m=n.firstChild;m&&1!=m.nodeType;)m=m.nextSibling;i=document.importNode(m,!0),this._module&&this.refreshModules(),this.adapters=this.fetchAdapters(),n.parentNode.insertBefore(i,n)}}},e={};function n(i){if(e[i])return e[i].exports;var r=e[i]={exports:{}};return t[i](r,r.exports,n),r.exports}n.n=t=>{var e=t&&t.__esModule?()=>t.default:()=>t;return n.d(e,{a:e}),e},n.d=(t,e)=>{for(var i in e)n.o(e,i)&&!n.o(t,i)&&Object.defineProperty(t,i,{enumerable:!0,get:e[i]})},n.o=(t,e)=>Object.prototype.hasOwnProperty.call(t,e),n.r=t=>{"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(t,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(t,"__esModule",{value:!0})},n(34)})()},340:(t,e)=>{(()=>{"use strict";var t={d:(e,n)=>{for(var i in n)t.o(n,i)&&!t.o(e,i)&&Object.defineProperty(e,i,{enumerable:!0,get:n[i]})},o:(t,e)=>Object.prototype.hasOwnProperty.call(t,e),r:t=>{"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(t,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(t,"__esModule",{value:!0})}},n={};t.r(n),t.d(n,{default:()=>w});var i=function(t){this.ctx=t};i.prototype={get:function(t,e){var n=this.ctx.endpoint().replace(/\/$/,"")+"/ping/detail/"+t+".json?token="+encodeURIComponent(this.ctx.token()),i=this.ctx;fetch(n).then((t=>t.json())).then((function(t){if(!t.payload)throw{message:"Invalid response",response:t};return new r(i,t.payload)})).then(e).catch((function(t){console.error(t)}))},author:function(t,e,n){var i=this.ctx.endpoint().replace(/\/$/,"")+"/user/show/"+t+".json"+(void 0!==n?"?until="+n:""),s=this.ctx,a=this;fetch(i).then((t=>t.json())).then((function(n){if(!n.payload)throw{message:"Invalid response",response:n};for(var i=[],u=0;u<n.payload.length;u++)i.push(new r(s,n.payload[u]));return new o(s,i,(function(){return 0!=n.until?a.author(t,e,n.until):null}))})).then(e).catch((function(t){console.error(t)}))},replies:function(t,e,n){var i=this.ctx.endpoint().replace(/\/$/,"")+"/ping/replies/"+t+".json"+(void 0!==n?"?until="+n:""),s=this.ctx,a=this;fetch(i).then((t=>t.json())).then((function(t){if(!t.payload)throw{message:"Invalid response",response:t};for(var n=[],i=0;i<t.payload.length;i++)n.push(new r(s,t.payload[i]));return new o(s,n,(function(){return 0!=t.until?a.replies(author,e,t.until):null}))})).then(e).catch((function(t){console.error(t)}))}};var r=function(t,e){this.payload=e};r.prototype={};var o=function(t,e,n){this._pings=e,this._ctx=t,this._next=n};o.prototype={};const s=i;var a=function(t){this._ctx=t};a.prototype={read:function(t,e){var n=this._ctx.endpoint().replace(/\/$/,"")+"/feed.json?token="+this._ctx.token()+(void 0!==e?"&until="+e:""),i=this._ctx,r=this;fetch(n).then((t=>t.json())).then((function(e){if(!e.payload)throw{message:"Invalid response",response:e};for(var n=[],o=0;o<e.payload.length;o++)n.push(new u(i,e.payload[o]));return new h(i,n,(function(){return 0!=e.until?r.read(t,e.until):null}))})).then(t).catch((function(t){console.error(t)}))}};var u=function(t,e){this.payload=e};u.prototype={};var h=function(t,e,n){this._pings=e,this._ctx=t,this._next=n};h.prototype={};const c=a;var l=function(t){this._ctx=t};l.prototype={retrieve:function(t,e){var n=this._ctx.endpoint().replace(/\/$/,"")+"/feedback/retrieve/"+t+".json?token="+encodeURIComponent(this._ctx.token());fetch(n).then((t=>t.json())).then((function(t){e(t)}))},push:function(t,e,n){var i=this._ctx.endpoint().replace(/\/$/,"")+"/feedback/push/"+t+".json?reaction="+e+"&token="+encodeURIComponent(this._ctx.token());fetch(i).then((t=>t.json())).then((function(t){n(t)}))},revoke:function(t,e){var n=this._ctx.endpoint().trim("/")+"/feedback/revoke/"+t+".json?token="+encodeURIComponent(this._ctx.token());fetch(n).then((t=>t.json())).then((function(t){e(t)}))},vote:function(t,e){var n=this._ctx.endpoint().trim("/")+"/poll/vote/"+t+".json?token="+encodeURIComponent(this._ctx.token());fetch(n).then((t=>t.json())).then((function(t){e(t)}))}};const f=l;var p=function(t){this._ctx=t};p.prototype={read:function(t,e){var n=this._ctx.endpoint().replace(/\/$/,"")+"/activity.json?token="+this._ctx.token()+(void 0!==e?"&until="+e:""),i=this._ctx,r=this;fetch(n).then((t=>t.json())).then((function(e){if(!e.payload)throw{message:"Invalid response",response:e};for(var n=[],o=0;o<e.payload.length;o++)n.push(e.payload[o]);return new d(i,n,(function(){return 0!=e.until?r.read(t,e.until):null}))})).then(t).catch((function(t){console.error(t)}))}};var d=function(t,e,n){this._pings=e,this._ctx=t,this._next=n};d.prototype={};const v=p;var m=function(t){this._ctx=t};m.prototype={push:function(t,e){var n=new FormData;n.append("file",t);var i=this._ctx.endpoint().replace(/\/$/,"")+"/media/upload.json?token="+encodeURIComponent(this._ctx.token());request(i,{method:"POST",body:n}).then((t=>t.json())).then((function(t){e(t)}))}};const g=m;var y=function(t,e){this._endpoint=t,this._token=e};y.prototype={feed:function(){return new c(this)},feedback:function(){return new f(this)},media:function(){return new g(this)},ping:function(){return new s(this)},activity:function(){return new v(this)},endpoint:function(){return this._endpoint},token:function(){return this._token}};const w=y;var b=e;for(var x in n)b[x]=n[x];n.__esModule&&Object.defineProperty(b,"__esModule",{value:!0})})()}},e={};function n(i){var r=e[i];if(void 0!==r)return r.exports;var o=e[i]={exports:{}};return t[i](o,o.exports,n),o.exports}n.n=t=>{var e=t&&t.__esModule?()=>t.default:()=>t;return n.d(e,{a:e}),e},n.d=(t,e)=>{for(var i in e)n.o(e,i)&&!n.o(t,i)&&Object.defineProperty(t,i,{enumerable:!0,get:e[i]})},n.o=(t,e)=>Object.prototype.hasOwnProperty.call(t,e),(()=>{"use strict";var t=n(340),e=n.n(t),i=n(348),r=n.n(i),o=n(438),s=n.n(o),a=document.querySelector('meta[name="ping.endpoint"]').content,u=document.querySelector('meta[name="ping.token"]').content,h=document.querySelector('meta[name="ping.id"]').content,c=new(e())(a,u),l=void 0;c.ping().replies(h,(function(t){for(var e=0;e<t._pings.length;e++){var n=new(r().view)("ping"),i=t._pings[e].payload;n.setData({id:i.id,userName:i.user.username,avatar:i.user.avatar,userURL:i.user.url,notificationURL:i.url||"#",notificationContent:i.content,media:i.media,poll:i.poll,timeRelative:i.timeRelative,feedback:i.feedback,replyCount:i.replies.count||"Reply",shareCount:i.shares||"Share",irt:i.irt?[i.irt]:[]})}})),new IntersectionObserver((function(t,e){t.forEach((function(t){t.isIntersecting&&l&&(l(),l=null)}))}),{root:null,rootMargin:"700px"}).observe(document.getElementById("loading-spinner"));var f=window.baseurl+"/xsrf/token.json";s()("click",".delegate-link",(function(t){var e=this;fetch(f).then((function(t){return t.json()})).then((function(t){var n=t.token;if(!confirm("Delete this ping?"))throw"User aborted the deletion";return fetch(e.href+n+".json")})).then((function(t){return t.json()})).then((function(t){"OK"===t.status&&(window.location="/feed")})).catch((function(t){console.error(t)})),t.stopPropagation(),t.preventDefault()}))})()})();