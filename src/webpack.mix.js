const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/*.js', 'public/js');
mix.js('resources/js/components/Category.js', 'public/js/components')
mix.js('resources/js/components/ModalFormOne.js', 'public/js/components')
    .react();
    mix.sass('resources/sass/template.scss', 'public/css');