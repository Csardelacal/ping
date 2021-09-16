import SDK from "ping-sdk-js";
import Lysine from "lysine";
import delegate from "delegate";

/**
 * 
 * @todo Replace these declarations with proper settings, most sensibly we would be using vuex
 * to load the data.
 */
let url = document.querySelector('meta[name="ping.endpoint"]').content;
let token = document.querySelector('meta[name="ping.token"]').content;
let pingid = document.querySelector('meta[name="ping.id"]').content;

var sdk = new SDK(url, token);
var nextPage = undefined;

sdk.ping().replies(pingid, function (pingList) {
	
	for (var i = 0; i < pingList._pings.length; i++) {

		var view = new Lysine.view('ping');
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
			poll: current.poll,
			timeRelative: current.timeRelative,
			feedback : current.feedback,
			replyCount: current.replies.count || 'Reply',
			shareCount: current.shares || 'Share',
			irt: current.irt ? [current.irt] : []
		});

	}
});

let observer = new IntersectionObserver(function (entries, observer) {
	entries.forEach(entry => {
		
		/**
		 * If the loader is not yet close to the screen, we will obviously
		 * not do anyhting to fetch more content.
		 */
		if (!entry.isIntersecting) { return; }
		
		/**
		 * If more data is available, then load it.
		 */
		if (nextPage) {
			nextPage();
			nextPage = null;
		}
	});
}, {
	root: null, //Intersect with the viewport
	rootMargin: '700px',
});

/**
 * Listen whether the 'loading more pings' element is on screen. If this is the case, we should
 * jump to continue loading more pings (if they're available).
 */
observer.observe(document.getElementById('loading-spinner'));


/**
 * This section enables the deletion of pings.
 * @todo Move this to the ping component in vue when it's available.
 */
var tokenurl = window.baseurl + "/xsrf/token.json";
	
delegate(
	'click', 
	'.delegate-link',
	function (e) { 
		var target = this;
		
		fetch(tokenurl)
			.then(response => response.json())
			.then(function (payload) {
				var token = payload.token;
				
				if (!confirm('Delete this ping?')) { throw 'User aborted the deletion'; }
				return fetch(target.href + token + '.json');
			})
			.then(response => response.json())
			.then(function (e) { if (e.status === 'OK') { window.location = '/feed'; } })
			.catch (function (e) { console.error(e); });
	
		e.stopPropagation(); 
		e.preventDefault();
	}
);