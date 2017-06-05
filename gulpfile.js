const elixir = require('laravel-elixir');
const critical = require('critical');
var gulp    = require('gulp'),
    htmlmin = require('gulp-htmlmin');
require('laravel-elixir-vue-2');

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

elixir(mix => {
    mix.sass('app.scss')
       .webpack('app.js');
});

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
