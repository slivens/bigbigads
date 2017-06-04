var path = require('path');
var webpack = require('webpack');
var ExtractTextPlugin = require('extract-text-webpack-plugin');
var ManifestPlugin = require('webpack-manifest-plugin');

module.exports = {
    entry: {
        home: ['./resources/assets/js/home.js'],
        vendor:['jquery', 'swiper', 'bootstrap']
    },
    output: {
        path: path.resolve(__dirname, './public/js'),
        filename: '[name].js',
    },
    module: {
        rules: [{
            test: /\.css$/,
            use: ExtractTextPlugin.extract({
                use: 'css-loader'
            })
        }, {
            test: /\.scss$/,
            use: ExtractTextPlugin.extract({
                use: [{
                    loader: 'css-loader',
                }, {
                    loader: 'sass-loader',
                }]
            }),
        }, {
            test: /\.(png|jpg|svg|gif)$/,
            loader: "url-loader?limit=10000&name=/images/[name].[ext]"
        }]
    },
    plugins: [
        new webpack.optimize.CommonsChunkPlugin({
            name: ['vendor']
        }),
        new ExtractTextPlugin('bundle-[chunkhash:10].css'),
        new ManifestPlugin({
            fileName: 'manifest.json',
            baseName: '/app/'
        }),
        new webpack.ProvidePlugin({
            $: 'jquery',
            jQuery: 'jquery',
            'window.jQuery': 'jquery'
        })
    ],

}
