let mix = require("laravel-mix");

mix
	.js('resources/assets/js/feed/index.js', 'public/js/feed/index.js')
	.js('resources/assets/js/app.js', 'public/js/app.js')
	.js('resources/assets/js/activity/index.js', 'public/js/activity/index.js')
	.js('resources/assets/js/ping/detail.js', 'public/assets/js/ping/detail.js')
	.js('resources/assets/js/user/show.js', 'public/js/user/show.js')
	.sass('resources/assets/css/app.scss', 'css/app.css')
	.setPublicPath('./public');
