// const elixir = require('laravel-elixir');
const gutil = require("gulp-util");
const env = require('gulp-env');
const critical = require('critical');
const webpack = require("webpack");
const webpackConfig = require("./webpack.config.js");
var gulp    = require('gulp'),
    htmlmin = require('gulp-htmlmin');
// require('laravel-elixir-vue-2');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

// elixir(mix => {
//     mix.sass('app.scss')
//        .webpack('app.js');
// });

// Generate & Inline Critical-path CSS
// gulp.task('critical',  function (cb) {
//     critical.generate({
//         inline: true,
// 		extract:true,
//         base: 'public/',
// 		css: ['public/dist/home.css'],
//         src: '../resources/views/index.blade.php',
//         dest: '../resources/views/index_critical.blade.php',
//         width: 1000,
//         height: 480,
//         minify: true
//     });
// });

gulp.task('compress', function() {
    var opts = {
        collapseWhitespace:    true,
        removeAttributeQuotes: true,
        removeComments:        true,
        minifyJS:              true
    };

    return gulp.src('./storage/framework/views/*')
               .pipe(htmlmin(opts))
               .pipe(gulp.dest('./storage/framework/views/'));
});

gulp.task('webpack:build-dev', function(cb) {
    var myConfig = Object.create(webpackConfig);
    webpack(myConfig, function(err, stats) {
		if(err) throw new gutil.PluginError("webpack:build-dev", err);
		gutil.log("[webpack:build-dev]", stats.toString({
			colors: true
		}));
		cb();
        });
})

gulp.task('webpack:build', function(cb) {
	// modify some webpack config options
	var myConfig = Object.create(webpackConfig);
	myConfig.plugins = myConfig.plugins.concat(
		new webpack.DefinePlugin({
            'process.env.NODE_ENV': JSON.stringify('production')
		}),
		new webpack.optimize.UglifyJsPlugin()
	);

	// run webpack
	webpack(myConfig, function(err, stats) {
		if(err) throw new gutil.PluginError("webpack:build", err);
		gutil.log("[webpack:build]", stats.toString({
			colors: true
		}));
		cb();
	});
})
