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


depend(['queue', 'm3/core/array/iterate' ], function (Queue, iterate) {
	
	
	
	var UploadTarget = function (maxSize) {
		
		this.queue = new Queue();
		this.limit = maxSize;
		
		
		this.upload = undefined;
		this.preview = undefined;
	};
	
	UploadTarget.prototype = {
		
		put : function (files) {
			var slf = this;

			iterate(files, function (e) {
				var job = slf.queue.job();
				var meta = undefined;
				
				var fd = new FormData();
				console.log(e);
				fd.append('file', e);
				
				/*
				 * Check the file size of thegiven file does not exceed the defined 
				 * amount
				 */
				if (e.size > slf.limit) {
					alert('Files must be smaller than ' + Math.round(slf.limit / 1024 / 1024) + 'MB');
					job.complete();
					return;
				}
				
				/*
				 * In the case of images, we can read the image and pass it to the 
				 * component requesting the upload.
				 */
				if (e.type.substring(0, 5) === 'image') {
					var reader = new FileReader();
					
					reader.onload = function (e) {
						meta = slf.preview(e.target.result, e.type);
						slf.upload(fd, meta, e.type, function () { job.complete(); });
					};

					reader.readAsDataURL(e);
				} 
				else {
					meta = slf.preview('/assets/img/video.png', e.type);
					slf.upload(fd, meta, e.type, function () { job.complete(); });
				}

				
			});
		},
		onupload : function (upload) { this.upload = upload; },
		onpreview : function (preview) { this.preview = preview; }
		
	};
	
	return UploadTarget;
});
