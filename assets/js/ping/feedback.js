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


depend(['m3/core/delegate', 'm3/core/request', 'm3/core/collection', 'm3/core/parent', 'ping/ping'], function (delegate, request, collect, parent, Ping) {
	
	var ping = new Ping('baseurl', null);
	
	/*
	 * Delegation for the poll system. When the user clicks on a response to a poll,
	 * we transmit their selection to the server and update the UI.
	 */
	delegate('click', function (e) {
		/*
		 * Only register the click event when the user clicks on a poll response.
		 * As opposed to direct event listeners, the delegation will listen to all
		 * clicks and only perform an action when the element satisfies this condition.
		 */
		return e.classList.contains('poll-open-response');
	}, function (event, element) {
		/*
		 * Send the request to the server to update the selected option. If the call
		 * succeeds, we redraw the UI to reflect the change.
		 */
		ping.feedback().vote(element.getAttribute('data-option'), function () {
			var poll = parent(element, function (e) {
				return e.hasAttribute('data-poll');
			});

			collect(poll.querySelectorAll('*[data-option]')).each(
					  function (e) {
						  e.classList.remove('selected-response');
					  }
			);

			element.classList.add('selected-response');
		});
		
		event.preventDefault();
	});
	
	/*
	 * Reaction logic for the interface
	 */
	delegate('click', function (e) {
		return e.classList.contains('for-likes');
	}, function (event, element) {
		
		var active = element.classList.contains('liked');
		
		if (active) {
			ping.feedback().revoke(element.getAttribute('data-ping'), function () {
				element.classList.remove('liked');
				element.querySelector('span').innerHTML = (parseInt(element.querySelector('span').innerHTML) || 0) - 1;
			});
		}
		else {
			ping.feedback().push(element.getAttribute('data-ping'), 'like', function () {
				element.classList.add('liked');
				element.querySelector('span').innerHTML = (parseInt(element.querySelector('span').innerHTML) || 0) + 1;
			});
		}

		event.preventDefault();
	});
	
	return function (bu, t) { baseurl = bu; ping = new Ping(bu, t); };
});