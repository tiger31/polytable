'use strict';

const { VueLoaderPlugin } = require('vue-loader');

module.exports = {
    mode: 'development',
    entry: {
        profile : './js/vue/profile.js',
        countdown : './bundle.js'
    },
    output: {
        publicPath: "/dist/",
        chunkFilename: "[id].chunk.js"
    },
    module: {
        rules: [
            {
                test: /\.vue$/,
                loader: 'vue-loader',
                options: {
                    loaders: {
                        // https://vue-loader.vuejs.org/guide/scoped-css.html#mixing-local-and-global-styles
                        css: ['vue-style-loader', {
                            loader: 'css-loader',
                        }],
                    },
                    cacheBusting: true,
                },
            },
            {
                test: /\.css$/,
                use: [ 'vue-style-loader','css-loader' ]
            },
            {
                test: /\.scss$/,
                use: [ 'vue-style-loader','css-loader','sass-loader' ]
            }
        ]
    },
    plugins: [
        new VueLoaderPlugin()
    ]
};