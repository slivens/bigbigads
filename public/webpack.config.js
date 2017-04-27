var path = require('path');
var webpack = require('webpack');

module.exports = {
    entry: {
        vendor:[
            'angular', 
            'angular-sanitize', 
            'angular-touch', 
            'angular-busy', 
            'angular-ui-router', 
            'ocLazyLoad', 
            'angular-resource', 
            'angular-ui-bootstrap'
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
    plugins:[
         new webpack.optimize.CommonsChunkPlugin({name:'vendor'})
    ]
}
