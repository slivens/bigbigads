var path = require('path');
var webpack = require('webpack');
var ExtractTextPlugin = require('extract-text-webpack-plugin');
var ManifestPlugin = require('webpack-manifest-plugin');

module.exports = {
    entry: {
        bundle:['./src/js/standalone/main.js', './src/js/standalone/directives.js'],
        search:['./src/js/search/search.js'],
        ranking:['./src/js/ranking/ranking.js'],
        profile:['./src/js/profile/profile.js'],
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
        filename:'js/[name]-[chunkhash:10].js',
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
                }, {
                    loader:'sass-loader',
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
         new ExtractTextPlugin('[name]-[chunkhash:10].css'),
         new ManifestPlugin({
            fileName:'manifest.json',
            baseName:'/app/'
         })
    ]
}
