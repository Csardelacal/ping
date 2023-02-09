let mix = require("laravel-mix");
require('laravel-mix-tailwind');

mix
	.js('resources/assets/js/feed/index.js', 'js/feed/index.js')
	.js('resources/assets/js/app.js', 'js/app.js')
	.js('resources/assets/js/activity/index.js', 'js/activity/index.js')
	.js('resources/assets/js/ping/detail.js', 'assets/js/ping/detail.js')
	.js('resources/assets/js/user/show.js', 'js/user/show.js')
	.sass('resources/assets/css/app.scss', 'css/app.css')
	/**
	 * Bring in font-awesome so the application can render icons.
	 */
	.copy(
        'node_modules/@fortawesome/fontawesome-free/webfonts',
        'public/webfonts'
    )
	/**
	 * This enables typescript support in vue. Which should allow me to build way
	 * more robust components and modules in the future.
	 */
	.webpackConfig({
		module: {
			rules: [
				{
					test: /\.tsx?$/,
					loader: "ts-loader",
					exclude: /node_modules/,
					options: { 
						appendTsSuffixTo: [/\.vue$/],
						/**
						 * @see https://stackoverflow.com/questions/71569535/npm-run-watch-crashes-after-any-change-in-vue-or-js-files-compiletemplate-now-r
						 */
						transpileOnly:true
					},
				}
			]
		}
	})
	.vue()
	.tailwind()
	.version()
	.setPublicPath('./public');
