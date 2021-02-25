let mix = require('laravel-mix');

mix
    .setPublicPath('theme/')
    .setResourceRoot('../')
    .js('theme/assets/js/main.js', 'js')
    .sass('theme/assets/sass/main.scss', 'css')
    .copyDirectory('theme/assets/images', 'theme/images')
//    .copyDirectory('theme/assets/fonts', 'theme/fonts') 
    .options({
       processCssUrls: true
    })
    .version();

if ( !mix.inProduction() ) {
    mix
        .webpackConfig({devtool: 'source-map'})
        .sourceMaps();
}

