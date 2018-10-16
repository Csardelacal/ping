/* 
 * The MIT License
 *
 * Copyright 2018 César de la Cal Bretschneider <cesar@magic3w.com>.
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
 * 
 * @todo This code is a major mess, but it seems to work reliably enough for now.
 *       Needs refactoring.
 * 
 * @returns {undefined}
 */
depend(['core/debounce'], function (debounce) {
	
	"use strict";
	
	/**
	 * 
	 * @type Array
	 */
	var registered = [];
	var offset     = {x : window.pageXOffset, y : window.pageYOffset };
	
	/*
	 * Stuck elements are those two that are either pinned to the top or the bottom
	 * of the page.
	 * 
	 * @param {type} position
	 * @returns {stickyL#33.Stuck}
	 */
	var Pin = function (position) {
		var html    = undefined;
		var wrapper = undefined;
		var child   = undefined;
		
		this.setChild = function (c, ctx, next) {
			
			if (c) {
				
				if (child !== c) {
					if (html) {
						html.style    = null;
						wrapper.style = null;
						
						wrapper.style.display = 'inline-block';
						wrapper.style.width   = '100%';
						console.log(wrapper.style);
					}
					
					wrapper = c.getHTML();
					html = wrapper.firstChild;
					
					c.getHTML().style.display = 'inline-block';
					c.getHTML().style.height  = c.getBoundaries().getH() + 'px';
					c.getHTML().style.width   = c.getBoundaries().getW() + 'px';
					
					html.style.position  = 'fixed';
					html.style.display   = 'inline-block';
					html.style[position] = '0';
					html.style.height    = c.getBoundaries().getH() + 'px';
					html.style.width     = c.getBoundaries().getW() + 'px';
					html.style.zIndex    = 5;
					html.style.background = ctx.getBackground();
					
				}
				
				if (position === 'top') {
					html.style.top = Math.min(
						0, 
						next? (next.getBoundaries().getScreenOffsetTop() - c.getBoundaries().getH()) : 0, 
						ctx.getBoundaries().getY() + ctx.getBoundaries().getH() - c.getBoundaries().getH()
					) + 'px';
				}
				
				if (position === 'bottom') {
					html.style.bottom = Math.min(
						0, 
						next? next.getBoundaries().getScreenOffsetBottom() - c.getBoundaries().getH(): 0, 
						window.innerHeight - ctx.getBoundaries().getScreenOffsetTop() - c.getBoundaries().getH()
					) + 'px';
				}
			}
			else if (html){
				/*
				 * No new element is found, we can therefore replace the original styles
				 * to the wrappers and unset them.
				 */
				html.style    = null;
				wrapper.style = null;
				
				wrapper.style.display = 'inline-block';
				wrapper.style.width   = '100%';
				
				child = undefined;
				html  = undefined;
			}
			
			child = c;
		};
	};
	
	/**
	 * 
	 * @type Object
	 */
	var pinned = {
		top: new Pin('top'),
		bottom: new Pin('bottom')
	};
	
	var Sticky = function (element, context, direction) {
		
		this.getElement   = function () { return element; };
		this.getContext   = function () { return context; };
		this.getDirection = function () { return direction || 'top'; };
		
		registered.push(this);
	};
	
	var Context = function (element) {
		
		this.getElement = function () {
			return element;
		};
	};
	
	var Boundaries = function (x, y, h, w) {
		
		this.getX = function () { return x; };
		this.getY = function () { return y; };
		this.getH = function () { return h; };
		this.getW = function () { return w; };
		
		this.onscreen = function () {
			return (offset.x < x + w && offset.x + window.innerWidth  > x) &&
			       (offset.y < y + h && offset.y + window.innerHeight > y);
		};
		
		this.getScreenOffsetTop = function () {
			return y - offset.y;
		};
		
		this.getScreenOffsetBottom = function () {
			return offset.y + window.innerHeight - (y + h);
		};
		
		this.getScreenOffsetLeft = function () {
			return x - offset.x;
		};
	};
	
	var Element = function (original) {
		/*
		 * These lines prevent the system from collapsing the borders.
		 */
		original.style.display = 'inline-block';
		original.style.width   = '100%';
		
		this.getBoundaries = debounce(function () { 
			var box = original.getBoundingClientRect();
			
			return new Boundaries(
				box.left + window.pageXOffset, 
				box.top + window.pageYOffset,
				box.height,
				box.width
			);
		}, 2000);
		
		this.getBackground = function() {
			return '#fff'; //TODO: Implement
		};
		
		this.getHTML = function() {
			return original;
		};
	};
	
	
	var findContext = function (e) {
		if (e === document.body) { return e; }
		if (e.hasAttribute('data-sticky-context')) { return e; }
		
		return findContext(e.parentNode);
	};
	
	var wrap = function (element) {
		var wrapper = document.createElement('div');
		element.parentNode.insertBefore(wrapper, element);
		wrapper.appendChild(element);
		
		return wrapper;
	};
	
	/*
	 * Register a listener to defer all scroll listening. When the user scrolls, 
	 * the listener will check which elements it should pin to the top and which
	 * it should leave behind.
	 */
	window.addEventListener('scroll', debounce(function () {
		var stuck     = { top : undefined, bottom : undefined };
		var runnerups = { top : undefined, bottom : undefined };
		
		/*
		 * Recalculate the offsets. Offsets do, for some reason, trigger reflows
		 * of the browser. So, we must read them before making any changes to the
		 * DOM
		 */
		offset = {x : window.pageXOffset, y : window.pageYOffset };
		
		/*
		 * Only elements with oncreen contexts are even remotely relevant to this 
		 * query, since offscreen contexts never allow their elements to escape.
		 */
		var onscreen = registered.filter(function (e) { 
			return e.getContext().getElement().getBoundaries().onscreen(); 
		});
		
		/*
		 * Select only the elements to be bound to the top of the page to check 
		 * whether the element needs to be pinned
		 */
		var topbound = onscreen.filter(function(e) {
			return e.getDirection() === 'top';
		});
		
		topbound.sort(function (a, b) {
			var va = a.getElement().getBoundaries().getScreenOffsetTop();
			var vb = b.getElement().getBoundaries().getScreenOffsetTop();
			
			if (va < vb) { return -1; }
			if (vb < va) { return  1; }
			return 0;
		});
		
		stuck.top = topbound.filter(function(e) { return e.getElement().getBoundaries().getScreenOffsetTop() <= 0;}).pop();
		runnerups.top = topbound.filter(function(e) { return e.getElement().getBoundaries().getScreenOffsetTop() > 0;}).shift();
		
		/*
		 * Repeat the same, but do it only with the elements bound to the bottom of
		 * the page.
		 */
		var bottombound = onscreen.filter(function(e) {
			return e.getDirection() === 'bottom';
		});
		
		bottombound.sort(function (a, b) {
			var va = a.getElement().getBoundaries().getScreenOffsetBottom();
			var vb = b.getElement().getBoundaries().getScreenOffsetBottom();
			
			if (va < vb) { return -1; }
			if (vb < va) { return  1; }
			return 0;
		});
		
		stuck.bottom = bottombound.filter(function(e) { return e.getElement().getBoundaries().getScreenOffsetBottom() <= 0;}).pop();
		runnerups.bottom = bottombound.filter(function(e) { return e.getElement().getBoundaries().getScreenOffsetBottom() > 0;}).shift();
		
		/*
		 * Pin the found elements to the top and / or bottom respectively
		 */
		pinned.top.setChild(
			stuck.top && stuck.top.getElement(), 
			stuck.top && stuck.top.getContext().getElement(), 
			runnerups.top && runnerups.top.getElement()
		);
		
		pinned.bottom.setChild(
			stuck.bottom && stuck.bottom.getElement(), 
			stuck.bottom && stuck.bottom.getContext().getElement(), 
			runnerups.bottom && runnerups.bottom.getElement()
		);
		
	}), false);
	
	return {
		context : findContext,
		
		stick : function (element, context, direction) { 
			return new Sticky(new Element(wrap(wrap(element))), new Context(new Element(context)), direction);
		}
	};
	
});