let mix = require("laravel-mix");

mix
	.js('resources/assets/js/feed/index.js', 'public/assets/js/feed/index.js')
	.setPublicPath('./public');