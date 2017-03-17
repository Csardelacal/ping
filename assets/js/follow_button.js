
(function () {
	
	"use strict";
	
	/**
	 * This will prefix all the requests that ping makes. This is specially 
	 * important when the 
	 * 
	 * @type String
	 */
	var baseURL = '/ping/';
	
	var token   = undefined;
	var actions = { follow: 'Follow', unfollow: 'Unfollow', pending: 'Loading...' };
	
	var JSRequest = function (url, callback) {
		//Export the callback to a global scope for the other elements to read
		var name = 'pingcb' + parseInt(Math.random() * 1000000);
		var s = document.createElement('script');
		
		//Unregister the callback and pass the data we received
		window[name]  = function (e) {
			callback(e);
			
			window[name] = undefined;
			document.head.removeChild(s);
		};
		
		s.src = baseURL + url + '?' + 'p=' + name + (token? '&token=' + token : '');
		s.async = true;
		document.head.appendChild(s);
	};
	
	var Button = function (html, userid) {
		var ctx = this;
		
		this.following = false;
		
		this.toggle = function () {
			this.following = !this.following;
			html.innerHTML = actions.pending;
			
			JSRequest( (this.following? 'people/follow' : 'people/unfollow') + '/' + userid + '.json', function () {
				html.innerHTML = ctx.following? actions.unfollow : actions.follow;
			} );
		};
		
		JSRequest('people/isFollowing/' + userid + '.json', function (e) {
			if (e.error) { return; }
			
			html.innerHTML = e.following? actions.unfollow : actions.follow;
			ctx.following  = e.following;
			
			html.addEventListener('click', function (e) { 
				e.preventDefault();
				ctx.toggle();
			}, false);
		});
	};
	
	var init = function () {
		var buttons = document.querySelectorAll('*[data-ping-follow]');
		
		for (var i = 0; i < buttons.length; i++) {
			new Button(buttons[i], buttons[i].getAttribute('data-ping-follow'));
		}
	};
	
	window.ping = window.ping? window.ping : {};
	window.ping.setBaseURL = function (baseurl)  { baseURL = baseurl; };
	window.ping.setToken   = function (newToken) { token   = newToken; };
	window.ping.Button     = Button;
	window.ping.init       = init;
	
}());