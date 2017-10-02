var path = require('path');
var webpack = require('webpack');
var ExtractTextPlugin = require('extract-text-webpack-plugin');
var HtmlWebpackPlugin = require('html-webpack-plugin');
var ManifestPlugin = require('webpack-manifest-plugin');
var isProduction = process.env.NODE_ENV === 'production';

module.exports = {
    devtool: isProduction ? false : 'source-map',
    entry: {
        bundle:['./src/index.js'],
        // search:['./src/pages/search/search.js'],
        // owner:['./src/pages/owner/owner.js'],
        // analysis:['./src/pages/analysis/analysis.js'],
        // "owner-search":['./src/pages/owner-search/owner-search.js'],
        // "owner-analysis":['./src/pages/owner-analysis/owner-analysis.js'],
        // ranking:['./src/pages/ranking/ranking.js'],
        // profile:['./src/pages/profile/profile.js'],
        // plans:['./src/pages/plans/plans.js'],
        vendor:[
            'bootstrap', 
            // 'bootstrap-switch',
            // 'bootstrap-hover-dropdown',
            'jquery-slimscroll',
            'sweetalert',
            'angular', 
            'angular-sanitize', 
            'angular-touch', 
            'angular-busy', 
            'angular-ui-router', 
            'oclazyload', 
            'angular-resource', 
            'angular-ui-bootstrap',
            'angular-sweetalert',
            'moment'
            ]
    },
    output: {
        path: path.resolve(__dirname, './app'),
        filename: !isProduction ? '[name].js' : 'js/[name]-[hash:10].js', // 使用chunkhash会导致一个问题:a.js引用b.js，生成a-1.js,b-1.js, b.js变化而a.js没变化，生成a-1.js, b-2.js。用户从自己的缓存讲出a-1.js，去引用b-1.js。
        chunkFilename: !isProduction ? '[name].js' : 'js/[name]-[hash:10].js',
    },
    module: {
        rules:[{
            test:/\.css$/,
            use:ExtractTextPlugin.extract({
                use:'css-loader',
                options: {
                    sourceMap: !isProduction
                }
            })
        },{
            test:/\.scss$/,
            use:ExtractTextPlugin.extract({
                use:[{
                    loader:'css-loader',
                    options: {
                        sourceMap: !isProduction
                    }
                }, {
                    loader:'sass-loader',
                    options: {
                        sourceMap: !isProduction
                    }
                }]
                }),
        }, {
            test:/\.(png|jpg|gif)$/,
            loader:"url-loader?limit=10000&name=/images/[name].[ext]"
        }, {
            test:/\.(svg|eot|ttf|woff|woff2)$/,
            loader:"file-loader"
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
            test: /\.html$/,
            loader: 'html-loader'
        }, {
            test: /\.json$/,
            loader: 'json-loader'
        }
        ]
    },
	externals:{
			'jquery':'window.jQuery'
		},
    plugins:[
         new webpack.optimize.CommonsChunkPlugin({
            name:['vendor']
            }),
         new ExtractTextPlugin({
            filename: !isProduction ? '[name].css' : '[name]-[hash:10].css',
            allChunks: true // 重要：出于性能考虑，css与js是分开的两个文件；当通过import实现lazyload时，没有加该参数，则加载进来的js文件是不带样式的，而加了该参数则能解决此问题。
            }),
         new ManifestPlugin({
            fileName:'manifest.json',
            baseName:'/app/'
         }),
         new HtmlWebpackPlugin({
            title: "bigbigads",
            path: __dirname + '/app',
            chunks: ['vendor', 'bundle'],
            xhtml: true,
            template: 'src/index.html'
         })
    ]
}
