const {mix} = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */
mix.options({
	extractVueStyles: false,
	processCssUrls: true,
	uglify: false,
	purifyCss: false,
	postCss: [require('autoprefixer')],
	clearConsole: true,
});

mix.sass('resources/assets/sass/app.scss', 'public/css/app.css')
	.js('resources/assets/js/app.js', 'public/js/app.js')
	.js('resources/assets/js/payment.js', 'public/js/payment.js')
	.js('resources/assets/js/guest.js', 'public/js/guest.js')
	.js('resources/assets/js/slideshow.js', 'public/js/slideshow.js')
	.copy('resources/vendor/reveal/reveal-theme.css', 'public/css/slideshow.css')
	.copy('resources/vendor/emoji/emoji.css', 'public/css/emoji.css')

if (mix.config.inProduction) {
	mix.version()
}

if (process.env.SYNC === 'on') {
	mix.browserSync('platforma.wnl');
}
