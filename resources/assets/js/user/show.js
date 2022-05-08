import Lysine from "lysine";

var xhr = null;
var current = document.querySelector('meta[name="ping.id"]').content;
var notifications = [];

var request = function (callback) {
	if (xhr !== null)  { return; }
	if (current === 0) { 
		document.getElementById('end-of-feed').style.display = 'block';
		return; 
	}
	
	xhr = new XMLHttpRequest();
	xhr.open('GET', document.location + '.json?until=' + current);
	
	document.getElementById('loading-spinner').style.display = 'block';
	
	xhr.onreadystatechange = function () {
		if (xhr.readyState === 4 && xhr.status === 200) {
			var data = JSON.parse(xhr.responseText);
			
			if (data.payload.length === 0 || data.until === null) {
				current = 0;
			} else {
				current = data.until;
			}
			
			for (var i= 0; i < data.payload.length; i++) { 
				var view =  new Lysine.view('ping');
				notifications.push(view);
				
				view.setData({
					id                 : data.payload[i].id,
					userName           : data.payload[i].user.username,
					avatar             : data.payload[i].user.avatar,
					userURL            : data.payload[i].user.url,
					embed              : data.payload[i].embed,
					notificationURL    : data.payload[i].url || '#',
					notificationContent: data.payload[i].content,
					media              : data.payload[i].media,
					share              : data.payload[i].share,
					poll               : data.payload[i].poll,
					feedback           : data.payload[i].feedback,
					timeRelative       : data.payload[i].timeRelative,
					replyCount         : data.payload[i].replies || 'Reply',
					shareCount         : data.payload[i].shares  || 'Share',
					irt                : data.payload[i].irt? [data.payload[i].irt] : []
				});
				
				if (!data.payload[i].irt && view.getHTML().querySelector('.irt')) {
					var child = view.getHTML().querySelector('.irt');
					child.parentNode.removeChild(child);
				}
				
				var media = view.getHTML().querySelector('.media-preview');
				
				if (data.payload[i].media && data.payload[i].explicit && media) {
					var cover = media.parentNode.insertBefore(document.createElement('div'), media);
					
					cover.className = 'media-cover';
					cover.appendChild(document.createElement('span')).appendChild(document.createTextNode('Ping may contain sensitive media'));
					cover.addEventListener('click', function (cover, media) { return function () {
						cover.style.display = 'none';
						media.style.display = null;
					}}(cover, media), false);
					
					media.style.display = 'none';
				}
			}
			
			
			xhr = null;
			callback();
			
			document.getElementById('loading-spinner').style.display = 'none';
		}
	};
	
	xhr.send();
};

var height = function () {
	var body = document.body,
		 html = document.documentElement;

	return Math.max( body.scrollHeight, body.offsetHeight, 
					html.clientHeight, html.scrollHeight, html.offsetHeight );
};

//This function listens to the scrolls
var listener = function () {
	var html   = document.documentElement,
		scroll = Math.max(html.scrollTop, window.scrollY);
	
	if (height() - scroll < html.clientHeight + 700) { request(listener); }
};

//Attach the listener
window.addEventListener('load',   listener, false);
document.addEventListener('scroll', listener, false);
