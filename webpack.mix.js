let mix = require("laravel-mix");

mix
	.js('resources/assets/js/feed/index.js', 'js/feed/index.js')
	.js('resources/assets/js/app.js', 'js/app.js')
	.js('resources/assets/js/activity/index.js', 'js/activity/index.js')
	.js('resources/assets/js/ping/detail.js', 'assets/js/ping/detail.js')
	.js('resources/assets/js/user/show.js', 'js/user/show.js')
	.sass('resources/assets/css/app.scss', 'css/app.css')
	.version()
	.setPublicPath('./public');
