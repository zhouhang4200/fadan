const mix = require('laravel-mix');

// 得到package.json中的参数 --env.admin 转换成 一个对象 {admin: true}
const { env } = require('minimist')(process.argv.slice(2));

// 判断如果是admin那就执行 webpack.admin.js 构建后台项目，构建之后return就不会往下执行了
if (env && env.admin) {
    require(`${__dirname}/webpack.admin.mix.js`);
    return
}

externals = {
    'element-ui': 'Element',
    'axios': 'axios',
    'vue': 'Vue',
    'vuex': 'Vuex',
    'vue-router': 'VueRouter',
    'vue-chartjs': 'VueChartJs',
    'lodash': '_',
};

mix.webpackConfig({
    output: {
        publicPath: '/frontend/v2/', // 设置默认打包目录
        chunkFilename: `js/[name].${mix.inProduction() ? '[chunkhash].' : ''}js` // 路由懒加载的时候打包出来的js文件
    }
});

mix.js('resources/assets/frontend/js/app.js', 'public/frontend/v2/js')
    .sass('resources/assets/frontend/sass/app.scss', 'public/frontend/v2/css')
    .less('resources/assets/frontend/less/theme.less', 'public/frontend/v2/css', {
        javascriptEnabled: true
    })
    .extract(['vue', 'axios', 'element-ui'])
    .setResourceRoot('/frontend/') // 设置资源目录
    .setPublicPath('public/frontend/v2/') // 设置 mix-manifest.json 目录
    .version(); // 生成的文件加上版本号
