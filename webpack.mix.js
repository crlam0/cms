let mix = require('laravel-mix');

mix
    .setPublicPath('theme/')
    .setResourceRoot('theme/')
    .js('theme/assets/js/full.js', 'js')
    .sass('theme/assets/sass/full.scss', 'css')
    .copyDirectory('theme/assets/images', 'theme/images')
    .copyDirectory('theme/assets/fonts', 'theme/fonts')
    .options({
       processCssUrls: false
    })
    .version();

