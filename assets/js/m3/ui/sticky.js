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
depend(['m3/core/debounce', 'm3/ui/rollingwindow'], function (debounce, RollingWindow) {
	
	"use strict";
	
	var offset     = {x : window.pageXOffset, y : window.pageYOffset, dir : undefined };
	var height     = window.innerheight;
	var registered = [];
	
	
	var Sticky = function (element, placeholder, context, direction) {
		
		/*
		 * Allows to configure the clearance that the element should maintain from
		 * the top of the screen at any given point in time.
		 */
		this.clear = 0;
		
		this.status = 'grounded';
		
		this.getElement   = function () { return element; };
		this.getContext   = function () { return context; };
		
		this.getDirection = function () { 
			return direction || 'top'; 
		};
		
		this.update = function (viewport, direction) {
			var contextB = this.getContext().getElement().getBoundaries();
			var elementB = this.getElement().getBoundaries();
			
			/*
			 * If the viewport and the context do not touch, then there's no way our
			 * item is going to be displayed.
			 */
			if (contextB.intersection(viewport) === undefined) { return this.reset(); }
			
			/*
			 * In the special case that our item has gigantic boundaries that fit the
			 * viewport in them, we don't move it so the user can scroll inside it.
			 */
			if (contextB.contains(viewport) && elementB.contains(viewport)) { 
				return this.ground(); 
			}
			
			/*
			 * If the context is large enough to contain the element, then we automatically
			 * pin the item to wherever it wants.
			 */
			if (contextB.contains(viewport)) { 
				return elementB.height() > viewport.height()? (this.pin(direction === 'up'? 'top' : 'bottom')) : this.pin(this.getDirection()); 
			}
			
			/*
			 * If the context is small enough to fit into the viewport, then our item 
			 * will be certianly not moving around.
			 */
			if (viewport.contains(contextB)) { return this.reset(); }
			
			/*
			 * At this point we know that the context and the viewport intersect,
			 * but that neither of them contain the other. This means that we need 
			 * to make a decision.
			 */
			if (contextB.above(viewport)) {
				if (this.getDirection() === 'top') { return elementB.height() > contextB.intersection(viewport).height()? this.latch('bottom') : this.pin('top'); }
				if (this.getDirection() === 'bottom') { return this.latch('bottom'); }
			}
			
			if (contextB.below(viewport)) {
				if (this.getDirection() === 'top') { return this.latch('top'); }
				if (this.getDirection() === 'bottom') { return elementB.height() > contextB.intersection(viewport).height()? this.latch('top') : this.pin('bottom'); }
			}
			
		};
		
		this.pin = function (position) {
			if (this.status === 'pinned.' + position) {
				return;
			}
			
			this.status = 'pinned.' + position;

			var wrapper = placeholder.getHTML();
			var detach  = this.getElement().getHTML();
			var c       = detach.getBoundingClientRect();
			var w       = wrapper.getBoundingClientRect();
			
			/*
			 * Create a placeholder so the layout doesn't shift when the element
			 * is being removed from the parent's static flow.
			 */
			wrapper.style.height  = c.height + 'px';
			
			/*
			 * Pin the element accordingly.
			 */
			detach.style = null;
			detach.style.position  = 'fixed';
			detach.style.width     = w.width + 'px';
					
				
			if (position === 'top') {
				detach.style.top = '0px';
			}

			if (position === 'bottom') {
				detach.style.bottom = '0px';
			}
		};
		
		
		this.latch = function (direction) {
			
			if (this.status === 'latch.' + direction) {
				return;
			}
			
			this.status =  'latch.' + direction;
			
			var wrapper = placeholder.getHTML();
			var detach  = this.getElement().getHTML();
			var c       = detach.getBoundingClientRect();
			
			/*
			 * Create a placeholder so the layout doesn't shift when the element
			 * is being removed from the parent's static flow.
			 */
			detach.style = null;
			wrapper.style.height  = c.height + 'px';
			
			detach.style.width     = c.width + 'px';
			detach.style.zIndex    = 5;
			
			if (direction === 'top') {
				detach.style.position  = 'absolute';
				detach.style.top       = context.getElement().getBoundaries().a + 'px';
			}
			
			if (direction === 'bottom') {
				detach.style.position  = 'absolute';
				detach.style.top       = (context.getElement().getBoundaries().b - c.height) + 'px';
			}

		};
		
		
		this.ground = function () {
			
			if (this.status === 'grounded') {
				return;
			}
			
			this.status =  'grounded';
			
			var wrapper = placeholder.getHTML();
			var detach  = this.getElement().getHTML();
			var c       = detach.getBoundingClientRect();
			
			detach.style = null;
			
			/*
			 * Create a placeholder so the layout doesn't shift when the element
			 * is being removed from the parent's static flow.
			 */
			wrapper.style.height  = c.height + 'px';
			
			
			detach.style.position  = 'absolute';
			detach.style.top       = (c.top + offset.y) + 'px';
			detach.style.width     = c.width + 'px';
			detach.style.zIndex    = 5;

		};
		
		
		this.reset = function () {
			
			if (this.status === 'grounded') {
				return;
			}
			
			this.status =  'grounded';
			
			var wrapper = placeholder.getHTML();
			var detach  = this.getElement().getHTML();
			
			/*
			 * Create a placeholder so the layout doesn't shift when the element
			 * is being removed from the parent's static flow.
			 */
			detach.style = null;
			wrapper.style = null;

		};
		
		registered.push(this);
	};
	
	var Context = function (element) {
		
		this.getElement = function () {
			return element;
		};
		
		/**
		 * 
		 * @type Array
		 */
		this.registered = [];
	};
	
	var Element = function (original) {
		
		this.getBoundaries = debounce(function () { 
			var box = original.getBoundingClientRect();
			return new RollingWindow(box.top + offset.y, offset.y + box.top + box.height);
		}, 50);
		
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
		
		/*
		 * Recalculate the offsets. Offsets do, for some reason, trigger reflows
		 * of the browser. So, we must read them before making any changes to the
		 * DOM
		 */
		offset = {x : window.pageXOffset, y : window.pageYOffset, dir : (window.pageYOffset - offset.y) > 0? 'down' : 'up' };
		height = window.innerHeight;

		/*
		 * Only elements with oncreen contexts are even remotely relevant to this 
		 * query, since offscreen contexts never allow their elements to escape.
		 */
		registered.forEach( function (e) { e.update(new RollingWindow(offset.y, offset.y + height), offset.dir); });
		

		
	}), false);
	
	
	document.addEventListener('load', function () {
		
		/*
		 * Recalculate the offsets. Offsets do, for some reason, trigger reflows
		 * of the browser. So, we must read them before making any changes to the
		 * DOM
		 */
		offset = {x : window.pageXOffset, y : window.pageYOffset, dir : (window.pageYOffset - offset.y) > 0? 'down' : 'up' };
		height = window.innerHeight;

		/*
		 * Only elements with oncreen contexts are even remotely relevant to this 
		 * query, since offscreen contexts never allow their elements to escape.
		 */
		registered.forEach( function (e) { e.update(new RollingWindow(offset.y, offset.y + height), offset.dir); });
		
	}, false);
	
	return {
		context : findContext,
		
		stick : function (element, context, direction) { 
			var ctx = new Context(new Element(context));
			/*
			 * Element gets wrapped, and the placeholder wraps the element again.
			 */
			var element = wrap(element);
			var stick = new Sticky(new Element(element), new Element(wrap(element)), ctx, direction);
			
			stick.update(new RollingWindow(offset.y, offset.y + height), offset.dir)
			return stick;
		}
	};
	
});