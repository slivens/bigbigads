// const elixir = require('laravel-elixir');
const gutil = require("gulp-util");
const env = require('gulp-env');
// const critical = require('critical');
const imagemin = require('gulp-imagemin');

const  gulp    = require('gulp');
// const htmlmin = require('gulp-htmlmin');

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

// gulp.task('compress', function() {
//     var opts = {
//         collapseWhitespace:    true,
//         removeAttributeQuotes: true,
//         removeComments:        true,
//         minifyJS:              true
//     };

//     return gulp.src('./storage/framework/views/*')
//                .pipe(htmlmin(opts))
//                .pipe(gulp.dest('./storage/framework/views/'));
// });

gulp.task('minify', function() {
    gulp.src('public/static/images/**/*')
        .pipe(imagemin())
        .pipe(gulp.dest('public/dist/images'))
})
