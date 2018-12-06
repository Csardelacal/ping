/*jslint browser:true */
/*global HTMLElement*/

/*
 * First thing first. If we do not have access to any HTMLElement class it implies
 * that lysine can't work properly since it manipulates these elements.
 */
if (HTMLElement === undefined) { throw 'Lysine requires a browser to work. HTMLElement class was not found'; }
if (window      === undefined) { throw 'Lysine requires a browser to work. Window variable was not found'; }

(function () {
	"use strict";
	
	/**
	 * An adapter is any element that allows lysine to manipulate data. It's an 
	 * interface for the reading and writing of data.
	 * 
	 * This class also allows or testing the different Adapters as compatible since
	 * they all inherit from the Adapter class
	 * 
	 * Since JS is duck typed we can't define the abstract getValue and setValue
	 * functions that we normally would. Instead we will have to make sure that
	 * the methods are present in the implementing classes.
	 * 
	 * @returns {lysine_L11.Adapter}
	 */
	function Adapter() {
		/*
		 * The element the adapter wraps around. This will always be an HTML node.
		 */
		this.element = null;
		
		/**
		 * This method defines the element this adapter is managing. This way Lysine
		 * can leave a HTML node that the implementing adapter can retrieve.
		 * 
		 * @param {Element} e
		 * @returns {undefined}
		 */
		this.setElement = function (e) {
			this.element = e;
		};

		/**
		 * The implementing
		 * classes can use this to provide the data they're supposed to handle
		 * 
		 * @returns {Element}
		 */
		this.getElement = function () {
			return this.element;
		};
	}

	/**
	 * An input adapter defines data inside a &lt;input> tag. To do so, it changes
	 * or reads it's value when the user requests data.
	 * 
	 * @param {type} element
	 * @returns {lysine_L11.InputAdapter}
	 */
	function InputAdapter(element) {
		this.setElement(element);
		
		/**
		 * Gets the value of the input being managed. It will therefore just read 
		 * the object's value property.
		 * 
		 * @returns {String}
		 */
		this.getValue = function () {
			return this.getElement().value;
		};
		
		/**
		 * Defines the value for the element. This way we can change it on the 
		 * browser to 'output' it to the user
		 * 
		 * @param {String} val
		 * @returns {undefined}
		 */
		this.setValue = function (val) {
			if (val === undefined) { val = ''; }
			this.getElement().value = val;
		};
	}
	
	/*
	 * Define the prototype of the InputAdapter so it can inherit from the Adapter
	 * properly.
	 */
	InputAdapter.prototype = new Adapter();
	InputAdapter.prototype.constructor = InputAdapter;

	/**
	 * An input adapter defines data inside a &lt;input> tag. To do so, it changes
	 * or reads it's value when the user requests data.
	 * 
	 * @param {type} element
	 * @returns {lysine_L11.InputAdapter}
	 */
	function SelectAdapter(element) {
		this.setElement(element);
		
		/**
		 * Gets the value of the input being managed. It will therefore just read 
		 * the object's value property.
		 * 
		 * @todo Add a special case for the event of a textarea
		 * @returns {String}
		 */
		this.getValue = function () {
			if (this.getElement().selectedIndex === -1) { return null; }
			return this.getElement().options[this.getElement().selectedIndex].value;
		};
		
		/**
		 * Defines the value for the element. This way we can change it on the 
		 * browser to 'output' it to the user
		 * 
		 * @param {String} val
		 * @returns {undefined}
		 */
		this.setValue = function (val) {
			var options = Array.prototype.slice.call(this.getElement().options, 0)
			this.getElement().selectedIndex = options.indexOf(this.getElement().querySelector('[value="' + val + '"]'));
		};
	}
	
	/*
	 * Define the prototype of the InputAdapter so it can inherit from the Adapter
	 * properly.
	 */
	SelectAdapter.prototype = new Adapter();
	SelectAdapter.prototype.constructor = SelectAdapter;

	function ArrayAdapter(view) {
		this.views = [];
		this.base  = view;

		this.getValue = function () {
			var ret = [],
				 i;

			for (i = 0; i < this.views.length; i+=1) {
				ret.push(this.views[i].getValue());
			}
			return ret;
		};

		this.setValue = function (val) {

			var i, v;

			if (val === undefined) {
				return;
			}
			
			/*
			 * In this scenario, we have more views than necessary and need to get 
			 * rid of some. We first loop over the array to remove them from the 
			 * HTML (destroy them). Then we slice the array with them in it.
			 */
			for (i = val.length; i < this.views.length; i+=1) {
				this.views[i].destroy();
			}
			
			this.views = this.views.slice(0, val.length);
			
			/*
			 * In the event of the views not being enough to hold the data, we will
			 * add new views.
			 */
			for (i = this.views.length; i < val.length; i+=1) {
				v = new lysine(this.base);
				this.views.push(v);
				
				//Create a gettter so we can read the data
				this.makeGetter(i);
			}
			
			for (i = 0; i < val.length; i++) {
				this.views[i].setValue(val[i]);
			}
			
		};
		
		this.makeGetter = function (idx) {
			var ctx = this;
			
			Object.defineProperty(this, idx, {
				get: function () { return ctx.views[idx]; },
				configurable: true
			});
			
		};
	}

	ArrayAdapter.prototype = new Adapter();
	ArrayAdapter.prototype.constructor = ArrayAdapter;

	function HTMLNodeAdapter(element) {

		this.setElement(element);

		this.getValue = function () {
			return this.getElement().innerHTML;
		};

		this.setValue = function (val) {
			this.getElement().innerHTML = val;
		};
	}

	HTMLNodeAdapter.prototype = new Adapter();
	HTMLNodeAdapter.prototype.constructor = HTMLNodeAdapter;
	
	/**
	 * The Attribute Array Adapter provides a easy way to accessing the attributes
	 * an element has that contain Lysine functionality.
	 * 
	 * An attribute called data-lysine-src for example will be used to set the value
	 * of the src attribute, allowing you to use the src as a fallback in case the
	 * attribute has no value in Lysine.
	 * 
	 * @param {HTMLElement} element
	 * @returns {lysine_L11.AttributeArrayAdapter}
	 */
	function AttributeArrayAdapter(element) {

		this.setElement(element);
		this.adapters = [];

		this.fetchData = function (view) {
			var data = view.getData();

			for (var i = 0; i < this.adapters.length; i++) {
				var a = this.adapters[i];
				a.setData(data);
				this.getElement().setAttribute(
					a.getAttributeName(), 
					a.replace()
				);
			}
		};

		this.hasLysine = function() {
			
			for (var i = 0; i < this.adapters.length; i++) {
				if (this.adapters[i].hasLysine()) { return true; }
			}
			return false;
		};
		
		var dataset = this.getElement().dataset,
		    i;
		
		for (var i in dataset) {
			if (element.dataset.hasOwnProperty(i)) {
				this.adapters.push(new AttributeAdapter(i, element.dataset[i]));
			}
		}
	}

	AttributeArrayAdapter.prototype = new Adapter();
	AttributeArrayAdapter.prototype.constructor = AttributeArrayAdapter;
	
	/**
	 * 
	 * @param {string} name
	 * @param {string} value
	 * @returns {lysine_L11.AttributeAdapter}
	 */
	function AttributeAdapter(name, value) {
		
		this.name     = name;
		this.value    = value;
		this.adapters = this.makeAdapters();
		
		this.setData  = function (data) {
			for (var i = 0; i < this.adapters.length; i++) {
				this.adapters[i].setValue(data[this.adapters[i].getName()]);
			}
		};
		
		this.replace  = function () {
			var str = '';
			
			for (var i = 0; i < this.adapters.length; i++) {
				str+= this.adapters[i].replace();
			}
			
			return str;
		};
	}
	
	AttributeAdapter.prototype = {
		hasLysine: function () { 
			return this.name.search(/^lysine/) !== -1; 
		},
		
		getAttributeName: function() {
			return this.name.replace(/^lysine/, '').toLowerCase();
		},
		
		makeAdapters: function () {
			if (!this.hasLysine()) { return []; }
			
			var exp1 = /\{\{([A-Za-z0-9]+)\}\}/g;
			var exp2 = /\{\{[A-Za-z0-9]+\}\}/g;
			
			var adapters = [];
			
			var pieces = this.value.split(exp2);
			var m      = exp1.exec(this.value);
			while (m) {
				adapters.push(new AttributeVariableAdapter(pieces.shift(), true));
				adapters.push(new AttributeVariableAdapter(m[1], false));
				//Continue the loop
				m = exp1.exec(this.value);
			}
			
			if (pieces.length > 0) { adapters.push(new AttributeVariableAdapter(pieces.shift(), true)); }
			
			return adapters;
		}
	};
	
	function AttributeVariableAdapter(name, readonly) {
		var value = null;
		
		this.setValue = function (v) {
			value = v;
		};
		
		this.getValue = function () {
			return value;
		};
		
		this.getName  = function () {
			return name;
		};
		
		this.replace  = function () {
			if (readonly) { return name; }
			else          { return value; }
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
			 data = {},
			 adapters = {},
			 attributeAdapters = [];
		
		/*
		 * First we receive the id and check whether it is a string or a HTMLElement
		 * this way we can handle several types of arguments received there.
		 */
		if (id instanceof HTMLElement) { view = id; } 
		else { view = document.querySelector('*[data-lysine-view="'+ id +'"]'); }
		
		/*
		 * Make a deep copy of the node. This allows Lysine to create as many copies
		 * of the original without causing trouble among the copies.
		 */
		html = view.cloneNode(true);

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
			this.exportData();
		};
		
		this.getData = function () {
			this.importData();
			return data;
		};

		this.getValue = this.getData;
		this.setValue = this.setData;

		this.importData = function () {
			var i;

			for (i in adapters) {
				if (adapters.hasOwnProperty(i)) {
					data[i] = adapters[i].getValue();
				}
			}

		};

		this.exportData = function () {
			var i;

			for (i in adapters) {
				if (adapters.hasOwnProperty(i)) {
					adapters[i].setValue(data[i]);
				}
			}

			for (i = 0; i < attributeAdapters.length; i+=1) {
				attributeAdapters[i].fetchData(this);
			}
		};

		this.fetchAdapters = function fetchAdapters(parent) {
			//Argument validation
			parent = (parent !== undefined)? parent : html;

			var elements = Array.prototype.slice.call(parent.childNodes, 0),
				 i, v, attrAdapter;


			for (i = 0; i < elements.length; i+=1) {
				if (elements[i].getAttribute && elements[i].getAttribute('data-for')) {
					if (elements[i].hasAttribute('data-lysine-view')) {
						v = new ArrayAdapter(elements[i]);
						adapters.push(v);
					}
					else {
						adapters.push(this.getAdapter(elements[i], null));
					}
				}
				else if (elements[i].nodeType !== 3) {
					this.fetchAdapters(elements[i]);
				}

				if (elements[i].nodeType !== 3) {
					attrAdapter = new AttributeArrayAdapter(elements[i]);
					if (attrAdapter.hasLysine()) {
						attributeAdapters.push(attrAdapter);
					}
				}
			}
			
			for (i in adapters) {
				if (adapters.hasOwnProperty(i)) { this.registerGetter(i, adapters[i]); }
			}
		};

		this.getHTML = function getHTML() {
			return html;
		};

		this.getElement = this.getHTML;

		this.destroy = function () {
			html.parentNode.removeChild(html);
			return this;
		};

		this.getAdapter = function getAdapter(element, value) {
			var adapter;

			if (element.tagName.toLowerCase() === "input" || element.tagName.toLowerCase() === "textarea") {
				adapter = new InputAdapter(element);
			}
			else if (element.tagName.toLowerCase() === "select") {
				adapter = new SelectAdapter(element);
			}
			else {
				adapter = new HTMLNodeAdapter(element);
			}
			adapter.setValue(value);
			return adapter;
		};
		
		this.registerGetter = function (key, adapter) {
			
			Object.defineProperty(this, key, {
				get: function()  { return adapter.getValue(); },
				set: function(v) { return adapter.setValue(v); },
				configurable: true,
				enumerable  : true
			});
			
		};

		//Constructor tasks
		html.removeAttribute('data-lysine-view');
		this.fetchAdapters();
		view.parentNode.insertBefore(html, view);
	}
	
	//Hide the unneeded view prototypes
	var style = document.createElement('style');
	style.type = "text/css";
	style.innerHTML = "*[data-lysine-view] { display: none !important;}";
	document.head.appendChild(style);

	window.Lysine = {};
	window.Lysine.view = lysine;
}());