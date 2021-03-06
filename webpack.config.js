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
        welcome: ['./resources/assets/js/welcome.js'],
        extension: ['./resources/assets/js/extension.js'],
        methodology: ['./resources/assets/js/methodology.js'],
        login: ['./resources/assets/js/login.js'],
        vendor: ['jquery', 'swiper', 'bootstrap', 'moment', 'js-url']
    },
    output: {
        path: path.resolve(__dirname, './public/dist'),
        publicPath: '/dist/',
        filename: !isProduction ? '[name].js' : '[name]-[hash].js',
        chunkFilename: !isProduction ? '[name].js' : '[name]-[chunkhash].js',
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
            loader: [
                "file-loader",
                {
                    loader: "image-webpack-loader",
                    options: {
                        bypassOnDebug: !isProduction
                    }
                }
            ]
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
        }, {
            test: /\.json$/,
            loader: 'json-loader'
        }]
    },
    plugins: [
         new webpack.DefinePlugin({
            PRODUCTION: JSON.stringify(isProduction),
            LOCALE: JSON.stringify('en'), // 当前Locale
            DEFAULT_LOCALE: JSON.stringify('en') //默认Locale
         }),
        new webpack.optimize.CommonsChunkPlugin({
            name: ['vendor']
        }),
        new ExtractTextPlugin(!isProduction ? '[name].css' : '[name]-[hash].css'),
        new ManifestPlugin({
            fileName: 'rev-manifest.json', // 该名称不可以改，Laravel 5.3需要引用该名称的文件
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
