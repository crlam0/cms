let mix = require('laravel-mix');

mix
    .setPublicPath('theme/')
    .setResourceRoot('../')
    .js('theme/assets/js/vendor.js', 'js')
    .js('theme/assets/js/main.js', 'js')
    .sass('theme/assets/sass/full.scss', 'css')
/*  
    .copyDirectory('theme/assets/images', 'theme/images')
    .copyDirectory('theme/assets/fonts', 'theme/fonts') 
 */
    .options({
       processCssUrls: true
    })
    .version();

