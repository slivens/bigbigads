'use strict';

// sass compile
var gulp = require('gulp');
var sass = require('gulp-sass');
var sourcemaps = require('gulp-sourcemaps');
var prettify = require('gulp-prettify');
var minifyCss = require("gulp-minify-css");
var rename = require("gulp-rename");
var uglify = require("gulp-uglify");
var rtlcss = require("gulp-rtlcss");  
var connect = require('gulp-connect');
var concat = require('gulp-concat');
var clean = require('gulp-clean');
var del = require('del');
var runSequence = require('run-sequence');
const rev = require('gulp-rev');
const revCollector = require('gulp-rev-collector');
const jshint = require('gulp-jshint');
const gulpsync = require('gulp-sync')(gulp);

var config = {
    script:{
        target:'./app/js/'
    },
    mode:"production"
}
//*** Localhost server tast
gulp.task('localhost', function() {
  connect.server();
});

gulp.task('localhost-live', function() {
  connect.server({
    livereload: true
  });
});

//*** SASS compiler task
gulp.task('sass', function () {
  // bootstrap compilation
	gulp.src('./src/sass/bootstrap.scss').pipe(sass()).pipe(gulp.dest('./assets/global/plugins/bootstrap/css/'));

  // select2 compilation using bootstrap variables
	//gulp.src('./assets/global/plugins/select2/sass/select2-bootstrap.min.scss').pipe(sass({outputStyle: 'compressed'})).pipe(gulp.dest('./assets/global/plugins/select2/css/'));

  // global theme stylesheet compilation
    gulp.src('./src/sass/global/*.scss').pipe(sourcemaps.init()).pipe(sass()).pipe(sourcemaps.write('./')).pipe(gulp.dest('./assets/global/css'));
    gulp.src('./src/sass/apps/*.scss').pipe(sourcemaps.init()).pipe(sass()).pipe(sourcemaps.write('./')).pipe(gulp.dest('./assets/apps/css'));
    gulp.src('./src/sass/pages/*.scss').pipe(sourcemaps.init()).pipe(sass()).pipe(sourcemaps.write('./')).pipe(gulp.dest('./assets/pages/css'));

  // theme layouts compilation
    gulp.src('./src/sass/layouts/layout/*.scss').pipe(sourcemaps.init()).pipe(sass()).pipe(sourcemaps.write('./')).pipe(gulp.dest('./assets/layouts/layout/css'));
  gulp.src('./src/sass/layouts/layout/themes/*.scss').pipe(sourcemaps.init()).pipe(sass()).pipe(sourcemaps.write('./')).pipe(gulp.dest('./assets/layouts/layout/css/themes'));


  gulp.src('./src/sass/layouts/layout3/*.scss').pipe(sourcemaps.init()).pipe(sass()).pipe(sourcemaps.write('./')).pipe(gulp.dest('./assets/layouts/layout3/css'));
  gulp.src('./src/sass/layouts/layout3/themes/*.scss').pipe(sourcemaps.init()).pipe(sass()).pipe(sourcemaps.write('./')).pipe(gulp.dest('./assets/layouts/layout3/css/themes'));
});

//*** SASS watch(realtime) compiler task
gulp.task('sass:watch', function () {
	gulp.watch('./src/sass/**/*.scss', ['sass']);
});

//*** CSS & JS minify task
gulp.task('minify', function () {
    // css minify 
    gulp.src(['./assets/apps/css/*.css', '!./assets/apps/css/*.min.css']).pipe(minifyCss()).pipe(rename({suffix: '.min'})).pipe(gulp.dest('./assets/apps/css/'));

    gulp.src(['./assets/global/css/*.css','!./assets/global/css/*.min.css']).pipe(minifyCss()).pipe(rename({suffix: '.min'})).pipe(gulp.dest('./assets/global/css/'));
    gulp.src(['./assets/pages/css/*.css','!./assets/pages/css/*.min.css']).pipe(minifyCss()).pipe(rename({suffix: '.min'})).pipe(gulp.dest('./assets/pages/css/'));    
    
    gulp.src(['./assets/layouts/**/css/*.css','!./assets/layouts/**/css/*.min.css']).pipe(rename({suffix: '.min'})).pipe(minifyCss()).pipe(gulp.dest('./assets/layouts/'));
    gulp.src(['./assets/layouts/**/css/**/*.css','!./assets/layouts/**/css/**/*.min.css']).pipe(rename({suffix: '.min'})).pipe(minifyCss()).pipe(gulp.dest('./assets/layouts/'));

    gulp.src(['./assets/global/plugins/bootstrap/css/*.css','!./assets/global/plugins/bootstrap/css/*.min.css']).pipe(minifyCss()).pipe(rename({suffix: '.min'})).pipe(gulp.dest('./assets/global/plugins/bootstrap/css/'));

    //js minify
    gulp.src(['./assets/apps/scripts/*.js','!./assets/apps/scripts/*.min.js']).pipe(uglify()).pipe(rename({suffix: '.min'})).pipe(gulp.dest('./assets/apps/scripts/'));
    gulp.src(['./assets/global/scripts/*.js','!./assets/global/scripts/*.min.js']).pipe(uglify()).pipe(rename({suffix: '.min'})).pipe(gulp.dest('./assets/global/scripts'));
    gulp.src(['./assets/pages/scripts/*.js','!./assets/pages/scripts/*.min.js']).pipe(uglify()).pipe(rename({suffix: '.min'})).pipe(gulp.dest('./assets/pages/scripts'));
    gulp.src(['./assets/layouts/**/scripts/*.js','!./assets/layouts/**/scripts/*.min.js']).pipe(uglify()).pipe(rename({suffix: '.min'})).pipe(gulp.dest('./assets/layouts/'));

});

gulp.task('clean', function() {
        return gulp.src('./app/js', {read:false}).pipe(clean());
})

gulp.task('script',  function() {
    var target = 'bigbigads.js';

    if (config.mode === "develop") {
        gulp.src(['./src/js/**/*.js', '!./src/js/standalone/**/*.js']).pipe(sourcemaps.init()).pipe(concat(target)).pipe(sourcemaps.write('./')).pipe(gulp.dest('./app/js/'));
        gulp.src(['./src/js/standalone/**/*.js']).pipe(gulp.dest('./app/js/'));
        gulp.src(['./src/index.html']).pipe(gulp.dest('./app/'));
    } else {
        gulp.src(['./src/js/**/*.js', '!./src/js/standalone/**/*.js']).pipe(concat(target)).pipe(gulp.dest('./app/js/')).pipe(uglify()).pipe(gulp.dest('./app/js/'));
        gulp.src(['./src/js/standalone/**/*.js'])
            .pipe(uglify())
            .pipe(rev())
            .pipe(gulp.dest(config.script.target))
            .pipe(rev.manifest())
            .pipe(gulp.dest('./app'));

    }
    // gulp.src(['./app/js/' + target]).pipe(uglify()).pipe(rename({suffix:'.min'})).pipe(gulp.dest('./app/js/'));
});

//*** HTML formatter task
gulp.task('prettify', function() {
  	
  	gulp.src('./**/*.html').
  	  	pipe(prettify({
    		indent_size: 4, 
    		indent_inner_html: true,
    		unformatted: ['pre', 'code']
   		})).
   		pipe(gulp.dest('./'));
});

gulp.task('lint', function()  {
    gulp.src('./src/js/**/*.js')
        .pipe(jshint())
        .pipe(jshint.reporter('default'));
    gulp.src('./app/js/main.js')
        .pipe(jshint())
        .pipe(jshint.reporter('default'));

});

//对JS与CSS生成版本号，防止缓存;源文件必须与目标文件分开，否则将只能替换一次
gulp.task('rev',  function() {
    return gulp.src(['./app/*.json', './src/index.html']).pipe(revCollector({
                replaceReved:true
                }))
                .pipe(gulp.dest('./app'));
});

gulp.task('script:watch', function() {
    gulp.watch(['./src/js/**/*.js'], ['lint', 'script']);
});

gulp.task('watch', ['sass:watch', 'script:watch']);

gulp.task('production', ['clean'], function(cb) {
    config.mode = "production";
    return runSequence(['sass'], ['script'], ['minify'], ['rev'],  cb);
});
gulp.task('develop', ['sass', 'script']);
