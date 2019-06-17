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




depend(['m3/core/parent', 'm3/core/delegate', 'm3/core/request', 'm3/core/array/iterate', 'm3/core/lysine', 'queue'], function (parent, delegate, request, iterate, Lysine, Queue) {

	/**
	 * This little listener makes sure to display the amount of characters left for
	 * the user to type in. This way, the user is constantly informed if they need
	 * to start shortening their message down.
	 */
	var listener = function () {

		setTimeout(function () {
			document.querySelectorAll('.new-ping-content').forEach(function (self) {
				var height = Math.min(self.scrollHeight, 400);
				var length = self.value.length;
				var form = parent(self, function (e) { return e.tagName.toLowerCase() === 'form';});

				self.style.height = height + 'px';
				form.querySelector('.new-ping-character-count').innerHTML = 250 - length;
			});
		}, 1);

	};

	document.addEventListener('keypress', listener, false);
	
	/**
	 * This listener "catches" the user's intention to 
	 */
	delegate('submit', function (e) { return e.classList.contains('ping-editor'); }, function (e, found) {
		request(found.action.trim('/') + '.json', new FormData(found))
		.then(function (resp) {
			//Clean up the editor and refresh the pings on the page.
			window.location.reload();
		})
		.catch(console.log);

		e.stopPropagation();
		e.preventDefault();
	});

	//BROKEN


	/*
	 * Delegates the listening of the onload event of the image loading functions,
	 * this way we don't have to inline the code, which looks awful.
	 */
	document.body.addEventListener('load', function (e) {
		var src = e.target;

		if (src.tagName !== 'IMG' || !src.classList.contains('upload-preview')) {
			return;
		}

		src.parentNode.style.height = src.parentNode.clientWidth + 'px';

		if (src.width > src.height) {
			var mw = src.parentNode.clientWidth;
			src.style.width = mw * (src.width / src.height) + 'px';
			src.style.height = mw + 'px';
		} else {
			var mw = src.parentNode.clientWidth;
			var h = mw * (src.height / src.width);
			src.style.height = h + 'px';
			src.style.width = mw + 'px';
			src.style.marginTop = -(h - mw) / 2 + 'px';
		}
	}, true);
	
	return function (data) {
		if (!data.target) { data.target = null; }
		if (!data.irt) { data.irt = null; }
		
		var view = new Lysine.view('ping-editor');
		view.setData(data);
		
		
		var mediaLimit = 4;
		var queue = new Queue();
		var uploads = [];
		var locked = false;

		queue.onProgress = function () {
			//Disable the post ping button
			view.getHTML().querySelector('.send-ping').setAttribute('disabled', 'disabled');
		};

		queue.onComplete = function () {
			//Enable the post ping button
			view.getHTML().querySelector('.send-ping').removeAttribute('disabled');
		};

		view.on('.ping_media_selector', 'click', function () {
			view.getHTML().querySelector('.ping_media').click();
		});

		view.on('.ping_media', 'change', function (e) {
			var files = e.target.nodeName.toLowerCase() === 'input' ? e.target.files : null;
			var uid = undefined;
			var v = undefined;

			iterate(files, function (e) {
				var job = queue.job();

				if (e.size > 25 * 1024 * 1024) {
					//Needs a better error
					alert('Files must be smaller than 25MB');
					job.complete();
					return;
				}

				if (e.type.substring(0, 5) === 'image') {

					var reader = new FileReader();

					reader.onload = function (e) {
						uid = parseInt(Math.random() * 1000);
						
						v = view.sub('media').push({
							uid : uid,
							source: e.target.result,
							id: null
						});

					};

					reader.readAsDataURL(e);
				} else {
					uid = parseInt(Math.random() * 1000);
					
					v = view.sub('media').push({
						uid : uid,
						source: '/assets/img/video.png',
						id: null
					});

					if (uploads.length > 0) {
						throw 'Videos can only be uploaded on their own.';
					}

					locked = true;
				}

				uploads.push({
					view: v
				});

				if (uploads.length >= mediaLimit) {
					locked = true;
				}

				/*
				 * If we have completed the maximum number of uploads, the system will
				 * stop accepting further uploads.
				 */
				if (locked) {
					view.getHTML().getElementById('ping_media_selector').style.display = 'none';
				}

				var fd = new FormData();
				fd.append('file', e);

				request('/ping/media/upload.json', fd)
					.then(function (response) {
						var json = JSON.parse(response);
						v.set('id', json.id + ':' + json.secret);

						job.complete();
					})
					.catch(function (error) {
						alert('Error uploading file. Please retry');
						console.log(error);
						//v.destroy();
					});
			});

		});
		
		view.sub('media').on('.remove-media', 'click', function (event, view) {
			view.destroy();
			locked = false;
		});
		

		var addOption = function () {
			view.sub('poll').push({value : '', id: parseInt(Math.random() * 1000 )});
		};

		view.on('.ping_poll', 'click', function (e) {
			for (var i = 0; i < 3; i++) {
				addOption();
			}

			view.find('.poll-dialog').style.display = 'block';
			view.find('.ping_poll').style.display = 'none';
			e.preventDefault();
			e.stopPropagation();
		});

		view.on('.poll-create-add', 'click', function (e) {
			addOption();
		});
		
		view.sub('poll').on('.poll-create-remove', 'click', function (e, v) {
			v.destroy();
		});
		
		
		return view;
	};
});