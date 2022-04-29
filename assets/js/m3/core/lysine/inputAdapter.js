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

depend(function() {
	
	/**
	 * An input adapter defines data inside a &lt;input> tag. To do so, it changes
	 * or reads it's value when the user requests data.
	 * 
	 * @param {type} element
	 * @returns {lysine_L11.InputAdapter}
	 */
	var InputAdapter = function (element) {
		
		this.element = element;
		this.view = undefined;
		
		var self = this;
		
		/**
		 * If the user alters the data, we immediately inform the view.
		 */
		this.element.addEventListener('onkeyup', function() {
			self.view.set(self.for()[0], this.value);
		});
		
		this.element.addEventListener('change', function() {
			if (this.type === 'radio' || this.type === 'checkbox') {
				self.view.set(self.for()[0], this.value || this.checked);
			} 
			else {
				self.view.set(self.for()[0], this.value);
			}
		});
		
	};
	
	InputAdapter.prototype = {
		
		readOnly: function () {
			return false;
		},
		
		for: function () {
			return [this.element.getAttribute('data-for')];
		},
		
		parent : function (view) {
			this.view = view;
			return this;
		},
		
		refresh : function () {
			var val = this.view.get(this.for()[0]);
			
			console.log(this.element.type);
			if (this.element.type === 'radio' || this.element.type === 'checkbox') {
				console.log(val);
				if ((this.element.value && this.element.value === val) || val === true) { this.element.checked = true; }
				else { this.element.checked = false; }
				return;
			}
			
			this.element.value = val;
		}
	};
	
	var findAdapters = function (element) {
		
		if (!element.getAttribute('data-for')) { return []; }
		
		if (element.tagName.toLowerCase() === "input" || element.tagName.toLowerCase() === "textarea") {
			return [new InputAdapter(element)];
		}
		
		return [];
	};
	
	return {
		InputAdapter : InputAdapter,
		find : findAdapters
	};

});	
