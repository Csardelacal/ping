import lysine from "lysine/dist/lysine.js";
import Ping from "ping-sdk-js";


var nextPage = null;
var baseurl = window.baseurl;
var ping = new Ping(baseurl, '');

var height = function () {
	var body = document.body,
			  html = document.documentElement;

	return Math.max(body.scrollHeight, body.offsetHeight,
			  html.clientHeight, html.scrollHeight, html.offsetHeight);
};

//This function listens to the scrolls
var listener = function () {
	var html = document.documentElement,
			  scroll = Math.max(html.scrollTop, window.scrollY);

	if (height() - scroll < html.clientHeight + 700) {
		nextPage && nextPage();
		nextPage = null;
	}
};

console.log(ping.activity());

ping.activity().read(function(pingList) {
	
	for (var i = 0; i < pingList._pings.length; i++) {

		var view = new lysine.view('ping');
		var data = pingList._pings[i];

		/*
		 * This block should be possible to have refactored out of the feed,
		 * making it less pointless code that adapts stuff around.
		 */
		view.setData({
			userName           : data.user.username,
			avatar             : data.user.avatar,
			userURL            : data.user.id? baseurl + '@' + data.user.username : '#',
			notificationURL    : data.url || '#',
			notificationContent: data.content,
			timeRelative       : data.timeRelative
		});

	}

	nextPage = pingList._next;
}, undefined);

//Attach the listener
document.addEventListener('scroll', listener, false);