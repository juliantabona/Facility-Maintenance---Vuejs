const mix = require('laravel-mix');

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

mix.js('resources/js/app.js', 'public/js')
   // Import Wookie.js for Ecommerce Store 
   .scripts([
      'resources/js/_extras/js/jquery-plugins/jquery.elevateZoom-3.0.8.min.js'
   ], 'public/js/extra.js').version()
   .copy('resources/js/_extras/css/themes/wookie-theme.css', 'public/css/wookie-store-theme.css')
   .sass('resources/sass/app.scss', 'public/css');