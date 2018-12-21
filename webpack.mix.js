let mix = require('laravel-mix');

mix
    .setPublicPath('theme/')
    .setResourceRoot('theme/')
    .options({ processCssUrls: false })
    .js('theme/assets/js/full.js', 'js')
    .sass('theme/assets/sass/full.scss', 'css')
    .version();
