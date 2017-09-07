let mix = require('laravel-mix');

mix.options({
	extractVueStyles: true,
});

mix.js('resources/assets/js/app.js', 'public/js')
   .sass('resources/assets/scss/app.scss', 'public/css')
   .sass('resources/assets/scss/forums.scss', 'public/css');
