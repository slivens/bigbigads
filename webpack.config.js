var path = require('path');
var webpack = require('webpack');
var ExtractTextPlugin = require('extract-text-webpack-plugin');
var ManifestPlugin = require('webpack-manifest-plugin');
function isProduction() {
    if (!process.env.NODE_ENV)
        return true;
    return process.env.NODE_ENV === 'production';
}
module.exports = {
    devtool: isProduction() ? 'cheap-module-source-map' : 'source-map',
    entry: {
        home: ['./resources/assets/js/home.js'],
        app: ['./resources/assets/js/app.js'],
        vendor:['jquery', 'swiper', 'bootstrap', 'moment', 'js-url']
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
                    options:{
                        sourceMap: isProduction() ? false : true
                    }
                }, {
                    loader: 'sass-loader',
                    options: {
                        sourceMap: isProduction() ? false : true
                    }
                }],
                fallback:"style-loader"
            }),
        }, {
            test: /\.(png|jpg|svg|gif|eot|woff|woff2|ttf)$/,
            loader: "file-loader"
        },  { 
            test: /\.vue$/, 
            loader: 'vue-loader'
            }
        ]
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
        new webpack.EnvironmentPlugin({NODE_ENV: 'development', DEBUG:true})
    ],
    resolve: {
      alias: {
          'vue$': 'vue/dist/vue.common.js'
        }
    }
}
