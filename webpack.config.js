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
        path: path.resolve(__dirname, './public/dist'),
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
                }],
                fallback:"style-loader"
            }),
        }, {
            test: /\.(png|jpg|svg|gif|eot|woff|woff2|ttf)$/,
            loader: "file-loader"
        }]
    },
    plugins: [
        new webpack.optimize.CommonsChunkPlugin({
            name: ['vendor']
        }),
        new ExtractTextPlugin('[name].css'),
        new ManifestPlugin({
            fileName: 'manifest.json',
            baseName: '/'
        }),
        new webpack.ProvidePlugin({
            $: 'jquery',
            jQuery: 'jquery',
            'window.jQuery': 'jquery'
        })
    ],

}
