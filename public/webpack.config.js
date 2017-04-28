var path = require('path');
var webpack = require('webpack');

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
            'angular-sweetalert'
            ]
    },
    output: {
        path: path.resolve(__dirname, './app/js'),
        filename:'[name].js',
        chunkFilename:"[name].js"
    },
    module: {
        loaders: [
        ]
    },
	externals:{
			'jquery':'window.jQuery'
		},
    plugins:[
         new webpack.optimize.CommonsChunkPlugin({name:'vendor'}),
    ]
}
