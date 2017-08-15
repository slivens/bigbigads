'use strict';

// sass compile
const gulp = require('gulp');
const sass = require('gulp-sass');
const sourcemaps = require('gulp-sourcemaps');
const prettify = require('gulp-prettify');
const minifyCss = require("gulp-minify-css");
const rename = require("gulp-rename");
const uglify = require("gulp-uglify");
const rtlcss = require("gulp-rtlcss");  
const connect = require('gulp-connect');
const concat = require('gulp-concat');
const clean = require('gulp-clean');
const del = require('del');
const mergeStream = require('merge-stream');
const runSequence = require('run-sequence');
const rev = require('gulp-rev');
const revCollector = require('gulp-rev-collector');
const jshint = require('gulp-jshint');
const eslint = require('gulp-eslint');
const gulpsync = require('gulp-sync')(gulp);
const htmlmin = require('gulp-htmlmin');
const strip = require('gulp-strip-comments');

var config = {
    script:{
        target:'./app/js/'
    },
    mode:"develop"
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
    if (config.mode === "production") {
        // var tasks = [];
      // global theme stylesheet compilation
      const global = gulp.src('./src/sass/global/*.scss').pipe(sass()).pipe(minifyCss()).pipe(gulp.dest('./assets/global/css')).pipe(gulp.dest('./assets/'));
      const app = gulp.src('./src/sass/apps/*.scss').pipe(sass()).pipe(minifyCss()).pipe(gulp.dest('./assets/apps/css')).pipe(gulp.dest('./assets/'));
      const pages = gulp.src('./src/sass/pages/*.scss').pipe(sass()).pipe(minifyCss()).pipe(gulp.dest('./assets/pages/css')).pipe(gulp.dest('./assets/'));

      const layout = (gulp.src('./src/sass/layouts/layout3/*.scss').pipe(sass()).pipe(rev()).pipe(minifyCss()).pipe(gulp.dest('./assets/layouts/layout3/css')).pipe(rev.manifest('layouts-layout3.json')).pipe(gulp.dest('./assets/')));
      const theme = gulp.src('./src/sass/layouts/layout3/themes/*.scss').pipe(sass()).pipe(rev()).pipe(minifyCss()).pipe(gulp.dest('./assets/layouts/layout3/css/themes')).pipe(rev.manifest('layouts-layout3-theme.json')).pipe(gulp.dest('./assets/'));
      return mergeStream(global, app, pages, layout, theme);
    } else {
      // bootstrap compilation
      gulp.src('./src/sass/bootstrap.scss').pipe(sass()).pipe(gulp.dest('./assets/global/plugins/bootstrap/css/'));

      // global theme stylesheet compilation
      gulp.src('./src/sass/global/*.scss').pipe(sourcemaps.init()).pipe(sass()).pipe(sourcemaps.write('./')).pipe(gulp.dest('./assets/global/css'));
      gulp.src('./src/sass/apps/*.scss').pipe(sourcemaps.init()).pipe(sass()).pipe(sourcemaps.write('./')).pipe(gulp.dest('./assets/apps/css'));
      gulp.src('./src/sass/pages/*.scss').pipe(sourcemaps.init()).pipe(sass()).pipe(sourcemaps.write('./')).pipe(gulp.dest('./assets/pages/css'));

      // theme layouts compilation
      // gulp.src('./src/sass/layouts/layout/*.scss').pipe(sourcemaps.init()).pipe(sass()).pipe(sourcemaps.write('./')).pipe(gulp.dest('./assets/layouts/layout/css'));
      // gulp.src('./src/sass/layouts/layout/themes/*.scss').pipe(sourcemaps.init()).pipe(sass()).pipe(sourcemaps.write('./')).pipe(gulp.dest('./assets/layouts/layout/css/themes'));

      gulp.src('./src/sass/layouts/layout3/*.scss').pipe(sourcemaps.init()).pipe(sass()).pipe(sourcemaps.write('./')).pipe(gulp.dest('./assets/layouts/layout3/css'));
      gulp.src('./src/sass/layouts/layout3/themes/*.scss').pipe(sourcemaps.init()).pipe(sass()).pipe(sourcemaps.write('./')).pipe(gulp.dest('./assets/layouts/layout3/css/themes'));
  }
});

//*** SASS watch(realtime) compiler task
gulp.task('sass:watch', function () {
	gulp.watch('./src/sass/**/*.scss', ['sass']);
});

gulp.task('clean', function() {
        return gulp.src(['./app/js', './app/*.css', './assets/*.json', './app/*.json', './app/**/*.html'], {read:false}).pipe(clean());
})

gulp.task('script',  function() {
    var target = 'bigbigads.js';

    if (config.mode === "develop") {
        gulp.src(['./src/js/**/*.js', '!./src/js/standalone/**/*.js']).pipe(sourcemaps.init()).pipe(concat(target)).pipe(sourcemaps.write('./')).pipe(gulp.dest('./app/js/'));
        gulp.src(['./src/js/standalone/**/*.js']).pipe(gulp.dest('./app/js/'));
        gulp.src(['./src/index.html']).pipe(gulp.dest('./app/'));
    } else {
        return gulp.src(['./src/js/**/*.js', '!./src/js/standalone/**/*.js']).pipe(concat(target)).pipe(gulp.dest('./app/js/')).pipe(uglify()).pipe(gulp.dest('./app/js/'));

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
    gulp.src(['./src/js/**/*.js', '!./src/js/standalone/**/*.js'])
        .pipe(eslint())
        .pipe(eslint.format())
    gulp.src('./app/js/standalone/**/*.js')
        .pipe(eslint())
        .pipe(eslint.format())
        .pipe(eslint.failAfterError())
});

//压缩HTML和打版本
gulp.task('html',  function() {
    if (config.mode === "develop") {
        gulp.src(['./src/404.html'])
                    .pipe(gulp.dest('./app/'));
        gulp.src(['./src/components/**/*.html'])
                    .pipe(gulp.dest('./app/components'));
        gulp.src(['./src/views/**/*.html'])
                    .pipe(gulp.dest('./app/views'));
        gulp.src(['./src/tpl/**/*.html'])
                    .pipe(gulp.dest('./app/tpl'));
        return gulp.src(['./app/manifest.json','./src/index.html']).pipe(revCollector({
                    replaceReved:true
                    }))
                    .pipe(gulp.dest('./app'));
    } else {
        const htmlOptions = {
                        removeComments: true,
                        collapseWhitespace:true,
                        minifyJS: true,
                        minifyCss: true
                    };
        gulp.src(['./src/404.html'])
                    .pipe(strip())
                    .pipe(htmlmin(htmlOptions))
                    .pipe(gulp.dest('./app/'));
        gulp.src(['./src/components/**/*.html'])
                    .pipe(strip())
                    .pipe(htmlmin(htmlOptions))
                    .pipe(gulp.dest('./app/components'));
        gulp.src(['./src/views/**/*.html'])
                    .pipe(strip())
                    .pipe(htmlmin(htmlOptions))
                    .pipe(gulp.dest('./app/views'));
        gulp.src(['./src/tpl/**/*.html'])
                    .pipe(strip())
                    .pipe(htmlmin(htmlOptions))
                    .pipe(gulp.dest('./app/tpl'));
        return gulp.src(['./assets/*.json',  './app/manifest.json','./src/index.html']).pipe(revCollector({
                    replaceReved:true
                    }))
                    .pipe(strip())
                    .pipe(htmlmin(htmlOptions))
                    .pipe(gulp.dest('./app'));
    }
});

gulp.task('script:watch', function() {
    gulp.watch(['./src/js/**/*.js'], ['lint', 'script']);
});

gulp.task('html:watch', function() {
    gulp.watch(['./src/**/*.html'], ['html']);
});

gulp.task('watch', ['sass:watch', 'script:watch', 'html:watch']);

gulp.task('config-product', function() {
    config.mode = "production";
})


gulp.task('production', gulpsync.sync([["config-product"], ['sass', 'script'], 'html']));
gulp.task('develop', gulpsync.sync([['sass', 'script'], 'html']));
