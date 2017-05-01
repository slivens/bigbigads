var path = require('path');
var webpack = require('webpack');
var ExtractTextPlugin = require('extract-text-webpack-plugin');
var ManifestPlugin = require('webpack-manifest-plugin');

module.exports = {
    entry: {
        bundle:['./src/js/standalone/main.js', './src/js/standalone/directives.js'],
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
            'ocLazyLoad', 
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
         new ExtractTextPlugin('bundle-[chunkhash:10].css'),
         new ManifestPlugin({
            fileName:'manifest.json',
            baseName:'/app/'
         })
    ]
}
