const mix = require('laravel-mix');

if (mix.inProduction()) {
    mix.version();
}

mix.webpackConfig({
    output: {
        publicPath: '/channel-pc/', // 设置默认打包目录
        chunkFilename: `js/[name].${mix.inProduction() ? '[chunkhash].' : ''}js` // 路由懒加载的时候打包出来的js文件
    }
});

mix.js('resources/assets/channel-pc/js/app.js', 'public/channel-pc/js') // 打包js
    .sass('resources/assets/channel-pc/sass/app.scss', 'public/channel-pc/css') // 打包css
    // .less('resources/assets/channel/less/theme.less', 'public/channel/css', {
    //     javascriptEnabled: true
    // })
    .extract(['vue', 'axios', 'vant']) // 提取依赖库
    .setResourceRoot('/channel-pc/') // 设置资源目录
    .setPublicPath('public/channel-pc'); // 设置 mix-manifest.json 目录