let mix = require("laravel-mix");

mix
	.js('resources/assets/js/feed/index.js', 'public/js/feed/index.js')
	.js('resources/assets/js/app.js', 'public/js/app.js')
	.sass('resources/assets/css/app.scss', 'css/app.css')
	.setPublicPath('./public');