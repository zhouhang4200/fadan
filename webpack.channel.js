const mix = require('laravel-mix');

if (mix.inProduction()) {
    mix.version()
}

mix.webpackConfig({
    output: {
        publicPath: '/channel/', // 设置默认打包目录
        chunkFilename: `js/[name].${mix.inProduction() ? '[chunkhash].' : ''}js` // 路由懒加载的时候打包出来的js文件
    }
});

mix.js('resources/assets/channel/js/app.js', 'public/channel/js') // 打包js
    // .sass('resources/assets/channel/sass/app.scss', 'public/channel/css') // 打包css
    // .less('resources/assets/channel/less/theme.less', 'public/channel/css', {
    //     javascriptEnabled: true
    // })
    .extract(['vue', 'axios']) // 提取依赖库
    .setResourceRoot('/channel/') // 设置资源目录
    .setPublicPath('public/channel'); // 设置 mix-manifest.json 目录