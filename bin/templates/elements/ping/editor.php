	
<form method="POST" action="<?= url('ping', 'push') ?>" enctype="multipart/form-data" id="ping-editor">
	<?php if (isset($target) && $target): ?>
		<input type="hidden" name="target" value="<?= $target ?>">
	<?php endif; ?>

	<div class="padded add-ping">
		<div class="row l10">
			<div class="span l1 desktop-only" style="text-align: center">
				<img src="<?= $sso->getUser($authUser->id)->getAvatar(64) ?>" style="width: 100%; border: solid 1px #777; border-radius: 3px;">
			</div>
			<div class="span l9">
				<textarea name="content" id="new-ping-content" placeholder="Message to broadcast..."></textarea>

				<div class="spacer" style="height: 10px"></div>

				<div class="row l5 m4 s4 fluid">

					<div class="span l1 m1 s1" data-lysine-view="file-upload-preview" >
						<div style="text-align: center; height: 100%; border: solid 1px #DDD; border-radius: 3px; overflow: hidden; position: relative">
							<img style="vertical-align: middle" data-lysine-src="{{source}}" class="upload-preview">
							<input type="hidden" name="media[]" value="" data-for="id">
							<a class="remove-media" href="#" style="color: #FFF; font-weight: bold; font-size: 1.8em; position: absolute; top: -5px; right: 5px; text-shadow: 0 0 8px rgba(0, 0, 0, .7); line-height: 1em; ">&times;</a>
						</div>
					</div>
				</div>
			</div>
		</div>


		<div class="spacer" style="height: 10px"></div>

		<div class="row l10" id="poll-dialog" style="display: none">
			<div class="span l1"></div>
			<div class="span l9">
				<div data-lysine-view="poll-create-option">
					<div class="row l5 m4 s4 fluid">
						<div class="span l4 m3 s3">
							<input type="text" name="poll[]" placeholder="Option..." style="width: 100%; border: none; border-bottom: solid 1px #ccc; padding: 3px;">
						</div>
						<div class="span l1 m1 s1">
							<a href="#remove-poll" class="poll-create-remove">Remove</a>
						</div>
					</div>
				</div>

				<div class="row l5 m4 s4 fluid">
					<div class="span l4 m3 s3">
						<a href="#add-poll" id="poll-create-add">Add option</a>
					</div>
				</div>
			</div>

			<div class="spacer" style="height: 10px"></div>
		</div>

		<div>
			<div class="row l10"><!--
				--><div class="span l1">
					<!--Just a spacer-->
				</div><!--
				--><div class="span l4">
					<input type="file" id="ping_media" style="display: none">
					<img src="<?= spitfire\core\http\URL::asset('img/camera.png') ?>" id="ping_media_selector" style="vertical-align: middle; height: 24px; opacity: .5; margin: 0 5px;">
					<img src="<?= spitfire\core\http\URL::asset('img/poll.png') ?>" id="ping_poll" style="vertical-align: middle; height: 24px; opacity: .3; margin: 0 5px;">
				</div><!--
				--><div class="span l5" style="text-align: right">
					<span id="new-ping-character-count">250</span>
					<input type="submit" value="Ping!" id="send-ping">
				</div><!--
				--></div>
		</div>
	</div>
</form>



<script type="text/javascript" src="<?= spitfire\core\http\URL::asset('js/queue.js') ?>"></script>
<script type="text/javascript">

(function () {

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

}());

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


depend(['m3/core/request', 'm3/core/array/iterate', 'm3/core/lysine'], function (request, iterate, lysine) {

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
					source: '<?= \spitfire\core\http\URL::asset('img/video.png') ?>',
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

			request('<?= url('media', 'upload')->setExtension('json') ?>', fd)
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
</script>