const mix = require('laravel-mix');
const path = require("path");

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

mix
    .webpackConfig({
        resolve: {
            alias: {
                '@components': path.resolve(__dirname, 'resources', "js/components"),
                '@pages': path.resolve(__dirname, 'resources', "js/pages"),
            },
        },
    })
    .js('resources/js/app.js', 'public/vue/js')
    .js('resources/js/vendorApp.js', 'public/vue/js')
    .js('resources/js/deliveryHeadApp.js', 'public/vue/js')
    .js('resources/js/deliveryBoyApp.js', 'public/vue/js')
    .vue()
    .postCss("resources/css/app.css", "public/vue/css", [
        require("tailwindcss")
    ]);
