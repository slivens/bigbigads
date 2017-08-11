var path = require('path');
var webpack = require('webpack');
var ExtractTextPlugin = require('extract-text-webpack-plugin');
var ManifestPlugin = require('webpack-manifest-plugin');
var isProduction = process.env.NODE_ENV === 'production';

module.exports = {
    devtool: isProduction ? false : 'source-map',
    entry: {
        home: ['./resources/assets/js/home.js'],
        mobile: ['./resources/assets/js/mobile.js'],
        pay: ['./resources/assets/js/pay.js'],
        stripe: ['./resources/assets/js/stripe.js'],
        plan: ['./resources/assets/js/plan.js'],
        product: ['./resources/assets/js/product.js'],
        vendor: ['jquery', 'swiper', 'bootstrap', 'moment', 'js-url']
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
                    options: {
                        sourceMap: isProduction ? false : true
                    }
                }, {
                    loader: 'sass-loader',
                    options: {
                        sourceMap: isProduction ? false : true
                    }
                }],
                fallback: "style-loader"
            }),
        }, {
            test: /\.(png|jpg|svg|gif|eot|woff|woff2|ttf)$/,
            loader: "file-loader"
        }, {
            test: /\.(js|vue)$/,
            loader: 'eslint-loader',
            enforce: 'pre',
            options: {
                formatter: require('eslint-friendly-formatter'),
                failOnWarning:true,
                failOnError: true
            }
        }, {
            test: /\.js$/,
            loader: 'babel-loader',
            exclude: /node_modules/,
            query: {
                compact: false
            }
        }, {
            test: /\.vue$/,
            loader: 'vue-loader',
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
        }),
        new webpack.DefinePlugin({
            'process.env': {
                NODE_ENV: isProduction ? '"production"' : '"development"'
            }
        })
    ],
    resolve: {
        alias: {
            'vue$': 'vue/dist/vue.common.js',
        }
    },
}
