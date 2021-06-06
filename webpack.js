const webpack = require('webpack')
let webpackConfig = require('@nextcloud/webpack-vue-config');
const { VueLoaderPlugin } = require('vue-loader');

webpackConfig = {
    ...webpackConfig,
    devtool: 'inline-cheap-module-source-map',
    watchOptions: {
        ignored: /node_modules/,
    }
};

module.exports = webpackConfig