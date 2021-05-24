import SDK from "ping-sdk-js";
import Lysine from "lysine";
import delegate from "delegate";

/**
 * @todo Replace the height functions with a intersection observer
 * @todo Replace tokens and PHP prints
 */

var sdk = new SDK('<?= url() ?>', '<?= $token ?>');
var nextPage = undefined;

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

	if (nextPage && height() - scroll < html.clientHeight + 700) {
		nextPage();
		nextPage = null;
	}
};

sdk.ping().replies(<?= $notification->_id ?>, function (pingList) {
	
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

document.addEventListener('scroll', listener, false);


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