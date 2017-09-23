var path = require('path');
var webpack = require('webpack');
var ExtractTextPlugin = require('extract-text-webpack-plugin');
var ManifestPlugin = require('webpack-manifest-plugin');
var isProduction = process.env.NODE_ENV === 'production';

module.exports = {
    devtool: isProduction ? false : 'source-map',
    entry: {
        bundle:['./src/index.js', './src/directives.js'],
        search:['./src/pages/search/search.js'],
        analysis:['./src/pages/analysis/analysis.js'],
        "owner-search":['./src/pages/owner-search/owner-search.js'],
        "owner-analysis":['./src/pages/owner-analysis/owner-analysis.js'],
        ranking:['./src/pages/ranking/ranking.js'],
        profile:['./src/pages/profile/profile.js'],
        vendor:[
            'bootstrap', 
            'bootstrap-switch',
            'bootstrap-hover-dropdown',
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
    },
    module: {
        rules:[{
            test:/\.css$/,
            use:ExtractTextPlugin.extract({
                use:'css-loader'
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
            test:/\.(png|jpg|svg|gif)$/,
            loader:"url-loader?limit=10000&name=/images/[name].[ext]"
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
         new ExtractTextPlugin(!isProduction ? '[name].css' : '[name]-[hash:10].css'),
         new ManifestPlugin({
            fileName:'manifest.json',
            baseName:'/app/'
         })
    ]
}
