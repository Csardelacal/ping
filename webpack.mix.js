let mix = require("laravel-mix");

mix
	.js('resources/assets/js/index.js', 'public/js/index.js')
	.setPublicPath('./public');