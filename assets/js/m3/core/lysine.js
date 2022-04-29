/*jslint browser:true */
/*global HTMLElement*/

/*
 * First thing first. If we do not have access to any HTMLElement class it implies
 * that lysine can't work properly since it manipulates these elements.
 */
if (HTMLElement === undefined) { throw 'Lysine requires a browser to work. HTMLElement class was not found'; }
if (window      === undefined) { throw 'Lysine requires a browser to work. Window variable was not found'; }

depend([
	'm3/core/collection',
	'm3/core/delegate',
	'm3/core/parent',
	'm3/core/lysine/inputAdapter',
	'm3/core/lysine/selectAdapter',
	'm3/core/lysine/htmlAdapter',
	'm3/core/lysine/attributeAdapter'
],
function (collection, delegate, parent, input, select, htmlAdapter, attributeAdapter) {
	"use strict";

	function ArrayAdapter(view) {
		this.views = [];
		this.base  = view;
		this.parentView = undefined;
		this.listeners = collection([]);
		this.writeProtect = false;

		this.getValue = function () {
			var ret = [],
				 i;

			/*
			 * Ensure there's no destroyed views being used to read the data
			 */
			this.views = this.views.filter(function (e) { return !e.isDestroyed();});

			for (i = 0; i < this.views.length; i+=1) {
				ret.push(this.views[i].getValue());
			}
			return ret;
		};

		this.setValue = function (val) {
			/**
			 * While the code is propagating, as in: writing to the parent, it should
			 * expect the parent to try an return the data back to the element.
			 *
			 * Since we're currently writing data, we can safely reject it, since it
			 * will provide the same data we are sending.
			 */
			if (this.writeProtect) {
				return;
			}

			var i, v;

			if (val === undefined) {
				return;
			}

			val = val.filter(function (e) { return !!e;});
			this.views = this.views.filter(function (e) { return e.reset() && !e.isDestroyed();});

			/*
			 * In this scenario, we have more views than necessary and need to get
			 * rid of some. We first loop over the array to remove them from the
			 * HTML (destroy them). Then we slice the array with them in it.
			 */
			while (val.length < this.views.length) {
				this.views[val.length].destroy();
			}

			this.views = this.views.slice(0, val.length);

			/*
			 * In the event of the views not being enough to hold the data, we will
			 * add new views.
			 */
			for (i = this.views.length; i < val.length; i+=1) {
				v = new lysine(this.base);
				this.views.push(v);

				v.setParent(this);
				this.listeners.each(function (e) { v.on.apply(v, e); })
			}

			for (i = 0; i < val.length; i++) {
				this.views[i].setValue(val[i]);
			}

		};

		this.for = function() {
			return [this.base.getAttribute('data-for')];
		};

		this.on = function (selector, event, callback) {
			this.listeners.push([selector, event, callback]);

			this.views.forEach(function (e) { e.on(selector, event, callback); })
		};

		this.parent = function(v) {
			this.parentView = v;
			return this;
		};

		this.push = function(d) {

			var v = new lysine(this.base);
			this.views.push(v);

			v.setValue(d);
			v.setParent(this);

			this.listeners.each(function (e) { v.on.apply(v, e); })
			this.propagate();

			return v;
		};

		this.refresh = function () {
			this.setValue(this.parentView.get(this.for()[0]));
		};

		this.propagate = function () {
			this.writeProtect = true;
			this.parentView.set(this.for()[0], this.getValue());
			this.writeProtect = false;
		};
	}

	function Condition(expression, element, adapters) {
		var exp = /([a-zA-Z_0-9]+)\(([a-zA-Z_0-9\-]+)\)\s?(\=\=|\!\=)\s?(.+)/g;
		var res = exp.exec(expression);

		if (res === null) {
			throw 'Malformed expression: ' + expression;
		}

		var fn = res[1];
		var id = res[2];
		var comp = res[3];
		var tgt = res[4];

		var view = undefined;

		var parent = element.parentNode;
		var nextSib = element.nextSibling;

		this.isVisible = function () {
			var val = undefined;

			switch(fn) {
				case 'null':
					val = view.get(id) === null? 'true' : 'false';
					break;
				case 'bool':
					val = view.get(id) === true? 'true' : 'false';
					break;
				case 'count':
					val = !view.get(id)? 0 : view.get(id).length;
					break;
				case 'value':
					val = view.get(id);
					break;
			}

			return comp === '=='? val == tgt : val != tgt;
		};

		this.test = function () {
			var visible = this.isVisible();

			if (visible === (element.parentNode === parent)) {
				return;
			}

			if (visible) {
				parent.insertBefore(element, nextSib);
			}
			else {
				parent.removeChild(element);
			}
		};

		this.for = function() {
			var c = collection([]);
			adapters.each(function (e) { c.merge(e.for()); });
			c.push(id);

			return c.raw();
		};

		this.parent = function(v) {
			view = v;
			adapters.each(function(e) { e.parent(v); });
			return this;
		};

		this.refresh = function () {
			this.test();

			if (this.isVisible()) {
				adapters.each(function(e) { e.refresh(); });
			}
		};
	}

	/**
	 * Creates a new Lysine view that handles the user's HTML and accepts objects as
	 * data to fill in said HTML.
	 *
	 * Beware of the following: IDs will potentially not properly work inside Lysine.
	 * Lysine maintains several copies of the original node and will potentially
	 * create issues. You should dinamically generate ID to use with your objects.
	 *
	 * @param {HTMLElement|String} id
	 * @returns {lysine_L11.lysine}
	 */
	function lysine(id) {

		var view,
			 html,
			 listeners = [],
			 data = {};

		this.destroyed = false;
		this.parent = undefined;

		/*
		 * First we receive the id and check whether it is a string or a HTMLElement
		 * this way we can handle several types of arguments received there.
		 */
		if (id instanceof HTMLElement) { view = id; }
		else { view = document.querySelector('*[data-lysine-view="'+ id +'"]'); }


		this.set = function (k, v) {

			if (k.substr(0, 1) === '^') {
				return this.parent.parentView.set(k.substr(1), v);
			}

			var ret = data;
			var pieces = k.split('.');

			for (var i = 0; i < pieces.length - 1; i++) {
				if (!ret[pieces[i]]) { ret[pieces[i]] = {}; }
				ret = ret[pieces[i]];
			}

			ret[k] = v;

			this.adapters.each(function(e) {
				if (e.for().indexOf(pieces[0]) === -1) { return; }
				e.refresh();
			});

			this.parent && this.parent.propagate(this, data);
		};

		this.get = function (k) {
			if (k.substr(0, 1) === '^') {
				return this.parent.parentView.get(k.substr(1));
			}

			var ret = data;
			var pieces = k.split('.');

			for (var i = 0; i < pieces.length; i++) { ret = ret? ret[pieces[i]] : undefined; }
			return ret;
		};

		/**
		 * Defines the data that we're gonna be using for the view. This way the
		 * application can quickly pass a big amount of data to the view.
		 *
		 * @todo Remove the data variable that is not currently needed.
		 * @param {Object} newData
		 * @returns {undefined}
		 */
		this.setData = function (newData) {
			data = newData;

			this.adapters.each(function(e) {
				e.refresh();
			});
		};

		this.getData = function () {
			return data;
		};

		/**
		 * Resets all the components in the view to their original state. This allows
		 * lysine based applications to have inputs that are reset whenever the data
		 * changes.
		 *
		 * If the data should not be reset, please make sure to store it before
		 * overwriting it.
		 *
		 * @returns {Boolean}
		 */
		this.reset = function () {
			var inputs = html.querySelectorAll('input');

			for (var i = 0; i < inputs.length; i++) {
				switch(inputs[i].type) {
					case 'checkbox':
					case 'radio':
						inputs[i].checked = inputs[i].hasAttribute('checked');
					default:
						inputs[i].value = inputs[i].hasAttribute('value')? inputs[i].getAttribute('value') : '';
				}
			}

			return true;
		};

		this.getValue = this.getData;
		this.setValue = this.setData;

		this.fetchAdapters = function (parent) {
			//Argument validation
			parent = (parent !== undefined)? parent : html;

			var adapters = collection([]), self = this;

			collection(parent.childNodes).each(function (e) {
				var extracted = collection([]);

				if (e.nodeType === 3) {
					return;
				}

				if (e.getAttribute && e.getAttribute('data-for')) {

					/*
					 * Array adapters may not be overridden in multiple places, it just
					 * makes little to no sense to have that feature.
					 */
					if (e.hasAttribute('data-lysine-view')) {
						extracted.merge(collection([(new ArrayAdapter(e)).parent(self)]));
					}
					else {
						/*
						 * This needs some fixing. The issue is that the system returns
						 * an array of adapters for a given value, which is okay, but
						 * the system cannot handle having multiple adapters for one
						 * property.
						 */
						var adapter = collection([]).merge(input.find(e)).merge(select.find(e)).merge(htmlAdapter.find(e));
						extracted.merge(adapter.each(function (e) { return e.parent(self); }));
					}
				}
				else {
					extracted.merge(self.fetchAdapters(e));
				}

				/*
				 * Get the adapters for the attributes, then informt them that the parent
				 * for them is this view and attach them to the attributes.
				 */
				extracted.merge(attributeAdapter.find(e).each(function (e) { return e.parent(self); }));

				if (e.getAttribute && e.getAttribute('data-condition')) {
					var c = new Condition(e.getAttribute('data-condition'), e, extracted);
					adapters.push(c.parent(self));
				}
				else {
					adapters.merge(extracted);
				}
			});

			return adapters;
		};

		this.getHTML = function () {
			return html;
		};

		this.getElement = this.getHTML;

		this.destroy = function () {
			this.destroyed = true;
			this.parent && this.parent.propagate();
			html.parentNode.removeChild(html);
			collection(listeners).each(function (e) { document.removeEventListener(e[0], e[1]); })
			return this;
		};

		this.isDestroyed = function () {
			return this.destroyed;
		};

		this.on = function (selector, event, callback) {
			var slf = this;

			/*
			 * Assemble the listener. Please note, that, for Lysine to work, it needs
			 * to delegate listeners (otherwise it would just spam the system with
			 * unwanted listeners all over the place)
			 *
			 * The condition for Lysine to capture an event is that the element matches
			 * the queryselector AND is a child of the current view.
			 *
			 * Listeners should only be used with the root view, and may not work as
			 * expected on nested ones.
			 */
			var t = delegate(event,
				function (e) {
					var p = parent(e, function (f) { return f === slf.getHTML(); });
					return p && collection(slf.getHTML().querySelectorAll(selector)).filter(function (f) { return f === e; }).raw()[0];
				},
				function (e, f) { callback.call(f, e, slf); }
			);

			listeners.push([event, t]);
			return t;
		};

		this.sub = function (f) {
			return this.adapters.filter(function(e) {
				if (e.for().indexOf(f) === -1) { return false; }
				return true;
			}).get(0);
		};

		this.setParent = function (p) {
			this.parent = p;
		};

		this.find = function (selector) {
			return this.getHTML().querySelector(selector);
		};

		this.findAll = function (selector) {
			return this.getHTML().querySelectorAll(selector);
		};

		//Constructor tasks

		/*
		 * Make a deep copy of the node. This allows Lysine to create as many copies
		 * of the original without causing trouble among the copies.
		 */
		html = document.importNode(view.content.firstElementChild, true);
		this.adapters = this.fetchAdapters();
		view.parentNode.insertBefore(html, view);
	}

	/*
	 * Return the entry point so other pieces of the application may be able to
	 * use Lysine.
	 */
	return {
		view : lysine
	};
});
