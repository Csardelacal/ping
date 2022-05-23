
import Ping from "ping-sdk-js";


depend(['m3/core/lysine'], function (lysine) {
		
	var nextPage = null;
	var token = window.token;
	var ping = new Ping(window.baseurl, token);
	
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
			nextPage();
			nextPage = null;
		}
	};
	
	ping.feed().read(function(pingList) {
		
		for (var i = 0; i < pingList._pings.length; i++) {
			
			var view = new lysine.view('ping');
			var current = pingList._pings[i].payload;
			
			/*
			 * This block should be possible to have refactored out of the feed,
			 * making it less pointless code that adapts stuff around.
			 */
			view.setData({
				id: current.id,
				userName: current.user.username,
				avatar: current.user.avatar,
				userURL: current.user.url,
				notificationURL: current.url || '#',
				notificationContent: current.content,
				media: current.media,
				share: current.share,
				poll: current.poll,
				timeRelative: current.timeRelative,
				feedback : current.feedback,
				replyCount: current.replies || 'Reply',
				shareCount: current.shares || 'Share',
				irt: current.irt ? [current.irt] : []
			});
			
		}
		
		nextPage = pingList._next;
	}, window.oldestLoaded);

	//Attach the listener
	document.addEventListener('scroll', listener, false);
	
});


