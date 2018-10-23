const mix = require('laravel-mix');

if (mix.inProduction()) {
    mix.version()
}

mix.webpackConfig({
    output: {
        publicPath: '/backend/', // 设置默认打包目录
        chunkFilename: `js/[name].${mix.inProduction() ? '[chunkhash].' : ''}js` // 路由懒加载的时候打包出来的js文件
    }
});

mix.js('resources/assets/backend/js/app.js', 'public/backend/js') // 打包后台js
    .sass('resources/assets/backend/sass/app.scss', 'public/backend/css') // 打包后台css
    .extract(['vue', 'iview', 'axios']) // 提取依赖库
    .setResourceRoot('/back/') // 设置资源目录
    .setPublicPath('public/back'); // 设置 mix-manifest.json 目录