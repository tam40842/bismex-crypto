// const SWPrecacheWebpackPlugin = require('sw-precache-webpack-plugin')

const mix = require('laravel-mix');

const autoprefixer = require('autoprefixer')
// const tailwindcss = require('tailwindcss');
const mqpacker = require('mqpacker');
// require('laravel-mix-purgecss');
const path = require('path');
const ImageminPlugin = require('imagemin-webpack-plugin').default

const node_env = (process.env.NODE_ENV === 'development') ? true : false;

const options = {
    enabled: true,

    // Your custom globs are merged with the default globs. If you need to
    // fully replace the globs, use the underlying `paths` option instead.

    extensions: ['html', 'js', 'php', 'vue'],

    // Other options are passed through to Purgecss
    whitelistPatterns: [/language/, /hljs/],

    whitelistPatternsChildren: [/^markdown$/],
}
const postCss = {
    postCss: [
        autoprefixer(),
        mqpacker({
            sort: true
        }),
        // tailwindcss('./tailwind.config.js'),
    ],
    processCssUrls: false,
    extractVueStyles: 'public/css/app.css'
};

const webpackConfig = {
    resolve: {
        extensions: ['.js', '.vue'],
        alias: {
            '@': __dirname + '/vue',
            '@page': __dirname + '/vue/views/pages',
            '@component': __dirname + '/vue/views/components',
            '@media': __dirname + '/vue/media',
            '@layout': __dirname + '/vue/views/layouts',
            '@store': __dirname + '/vue/store',
            '@mixin': __dirname + '/vue/mixins',
            '@plugin': __dirname + '/vue/plugin',
            '@lib': __dirname + '/vue/library',
            '@image': __dirname + '/vue/images',
            '@scss': __dirname + '/vue/view/scss',
            '@http': __dirname + '/vue/http.js',
            '@app': __dirname + '/vue/app.js',
        }
    },
    plugins: [
        // Make sure that the plugin is after any plugins that add images
        // new ImageminPlugin({
        //     disable: node_env, // Disable during development
        //     minFileSize: 100000,
        //     cacheFolder: './.imgCache',
        //     pngquant: {
        //         quality: '95-100'
        //     },
        //     jpegtran:{
        //         progressive: true
        //     },
        //     optipng: {
        //         optimizationLevel: 5
        //     },
        // }),
        // new SWPrecacheWebpackPlugin(
        //     {
        //         cacheId: 'BlockOption',
        //         dontCacheBustUrlsMatching: /\.\w{8}\./,
        //         filename: 'service-worker.js',
        //         minify: true,
        //         navigateFallback: './index.html',
        //         staticFileGlobsIgnorePatterns: [/\.map$/, /asset-manifest\.json$/],
        //     }
        // ),
    ]
}

mix
    .webpackConfig(webpackConfig)
    .js('vue/app.js', 'public/js')
    .sass('vue/views/scss/core.scss', 'public/css')
    .extract([
        'vue',
        'vuex',
        'vue-router',
        'jquery',
        'popper.js',
        'lodash',
        'bootstrap'
        // 'axios',
    ])
    .options(postCss)
    // .purgeCss(node_env ? {} : options)
    .sourceMaps(node_env, 'source-map')
    // .setResourceRoot("../")
    .setPublicPath('public')
    .version();


// if (node_env) {
//     mix.browserSync({
//         proxy: false,
//         port: 3000,
//         server: { baseDir: 'public/' },
//         files: [
//             './public/*.html',
//             './public/**/*.html',
//             './public/assets/images/*.*',
//             './public/assets/css/*.css',
//             './public/assets/js/*.js',
//             './src/**/*.*'
//         ],
//         reload: true,
//         open: false,
//     });
// }

// Full API
// mix.js(src, output);
// mix.react(src, output); <-- Identical to mix.js(), but registers React Babel compilation.
// mix.preact(src, output); <-- Identical to mix.js(), but registers Preact compilation.
// mix.coffee(src, output); <-- Identical to mix.js(), but registers CoffeeScript compilation.
// mix.ts(src, output); <-- TypeScript support. Requires tsconfig.json to exist in the same folder as webpack.mix.js
// mix.extract(vendorLibs);
// mix.sass(src, output);
// mix.less(src, output);
// mix.stylus(src, output);
// mix.postCss(src, output, [require('postcss-some-plugin')()]);
// mix.browserSync('my-site.test');
// mix.combine(files, destination);
// mix.babel(files, destination); <-- Identical to mix.combine(), but also includes Babel compilation.
// mix.copy(from, to);
// mix.copyDirectory(fromDir, toDir);
// mix.minify(file);
// mix.sourceMaps(); // Enable sourcemaps
// mix.version(); // Enable versioning.
// mix.disableNotifications();
// mix.setPublicPath('path/to/public');
// mix.setResourceRoot('prefix/for/resource/locators');
// mix.autoload({}); <-- Will be passed to Webpack's ProvidePlugin.
// mix.webpackConfig({}); <-- Override webpack.config.js, without editing the file directly.
// mix.babelConfig({}); <-- Merge extra Babel configuration (plugins, etc.) with Mix's default.
// mix.then(function () {}) <-- Will be triggered each time Webpack finishes building.
// mix.override(function (webpackConfig) {}) <-- Will be triggered once the webpack config object has been fully generated by Mix.
// mix.dump(); <-- Dump the generated webpack config object to the console.
// mix.extend(name, handler) <-- Extend Mix's API with your own components.
// mix.options({
//   extractVueStyles: false, // Extract .vue component styling to file, rather than inline.
//   globalVueStyles: file, // Variables file to be imported in every component.
//   processCssUrls: true, // Process/optimize relative stylesheet url()'s. Set to false, if you don't want them touched.
//   purifyCss: false, // Remove unused CSS selectors.
//   terser: {}, // Terser-specific options. https://github.com/webpack-contrib/terser-webpack-plugin#options
//   postCss: [] // Post-CSS options: https://github.com/postcss/postcss/blob/master/docs/plugins.md
// });
