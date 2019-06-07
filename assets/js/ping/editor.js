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




depend(function () {

	/**
	 * This little listener makes sure to display the amount of characters left for
	 * the user to type in
	 */
	var listener = function () {

		var self = this;

		setTimeout(function () {
			var height = Math.min(self.scrollHeight, 400);
			var length = self.value.length;

			self.style.height = height + 'px';
			document.querySelector('#new-ping-character-count').innerHTML = 250 - length;
		}, 1);

	};

	document.querySelector('#new-ping-content').addEventListener('keypress', listener, false);
	document.querySelector('.add-ping').addEventListener('click', function () {
		document.querySelector('#new-ping-content').focus();
	}, false);


	depend(['m3/core/request'], function (request) {
		document.getElementById('ping-editor').addEventListener('submit', function (e) {
			request(this.action.trim('/') + '.json', new FormData(this))
			.then(function (resp) {
				//Clean up the editor and refresh the pings on the page.
				window.location.reload();
			});

			e.stopPropagation();
			e.preventDefault();
		});
	});


	depend(['m3/core/request', 'm3/core/array/iterate', 'm3/core/lysine', 'queue'], function (request, iterate, lysine, Queue) {

		var mediaLimit = 4;

		/*
		 * The forms used for media input
		 */
		var form = {
			input: document.getElementById('ping_media'),
			ui: document.getElementById('ping_media_selector')
		};

		var queue = new Queue();
		var uploads = [];
		var locked = false;

		queue.onProgress = function () {
			//Disable the post ping button
			document.getElementById('send-ping').setAttribute('disabled', 'disabled');
		};

		queue.onComplete = function () {
			//Enable the post ping button
			document.getElementById('send-ping').removeAttribute('disabled');
		};

		form.ui.addEventListener('click', function () {
			form.input.click();
		});

		form.input.addEventListener('change', function (e) {
			var files = e.target.nodeName.toLowerCase() === 'input' ? e.target.files : null;

			iterate(files, function (e) {
				var job = queue.job();

				if (e.size > 25 * 1024 * 1024) {
					//Needs a better error
					alert('Files must be smaller than 25MB');
					job.complete();
					return;
				}

				var v = new lysine.view('file-upload-preview');

				if (e.type.substring(0, 5) === 'image') {

					var reader = new FileReader();

					reader.onload = function (e) {
						v.setData({
							source: e.target.result,
							id: null
						});

					};

					reader.readAsDataURL(e);
				} else {
					v.setData({
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
					document.getElementById('ping_media_selector').style.display = 'none';
				}

				var fd = new FormData();
				fd.append('file', e);

				request('/media/upload.json', fd)
						  .then(function (response) {
							  var json = JSON.parse(response);
							  v.set('id', json.id + ':' + json.secret);

							  job.complete();
						  })
						  .catch(function (error) {
							  alert('Error uploading file. Please retry');
							  v.destroy();
						  });
			});

		});


		depend(['m3/core/delegate'], function (delegate) {
			delegate('click', function (e) {
				return e.classList.contains('remove-media');
			}, function (event, element) {
				element.parentNode.parentNode.parentNode.removeChild(element.parentNode.parentNode);
				uploads.pop();
				locked = false;
			});
		});
	});

	depend(['m3/core/lysine'], function (Lysine) {

		var addOption = function () {

			var v = new Lysine.view('poll-create-option');

			v.getHTML().addEventListener('click', function (e) {
				e.stopPropagation();
			})
			v.getHTML().querySelector('.poll-create-remove').addEventListener('click', function (v) {
				return function (e) {
					v.destroy();
					e.stopPropagation();
				}
			}(v))
		};

		document.getElementById('ping_poll').addEventListener('click', function (e) {
			for (var i = 0; i < 3; i++) {
				addOption();
			}

			document.getElementById('poll-dialog').style.display = 'block';
			document.getElementById('ping_poll').style.display = 'none';
			e.preventDefault();
			e.stopPropagation();
		});

		document.getElementById('poll-create-add').addEventListener('click', function (e) {
			addOption();
		});
	});

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

		console.log(src.width + 'x' + src.height);

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
});