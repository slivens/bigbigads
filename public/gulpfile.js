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


gulp.task('clean', function() {
        return gulp.src(['./app/**/*.js', './app/*.css', './assets/*.json', './app/*.json', './app/**/*.html'], {read:false}).pipe(clean());
})

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
    gulp.src('./src/js/standalone/main.js')
        .pipe(eslint())
        .pipe(eslint.format())
        // .pipe(eslint.failAfterError())
});

//压缩HTML和打版本
gulp.task('html',  function() {
    if (config.mode === "develop") {
        gulp.src(['./src/404.html'])
                    .pipe(gulp.dest('./app/'));
        // gulp.src(['./src/components/**/*.html'])
        //             .pipe(gulp.dest('./app/components'));
        // gulp.src(['./src/pages/**/*.html'])
        //             .pipe(gulp.dest('./app/views'));
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
        // gulp.src(['./src/components/**/*.html'])
        //             .pipe(strip())
        //             .pipe(htmlmin(htmlOptions))
        //             .pipe(gulp.dest('./app/components'));
        // gulp.src(['./src/pages/**/*.html'])
        //             .pipe(strip())
        //             .pipe(htmlmin(htmlOptions))
        //             .pipe(gulp.dest('./app/views'));
        return gulp.src(['./assets/*.json',  './app/manifest.json','./src/index.html']).pipe(revCollector({
                    replaceReved:true
                    }))
                    .pipe(strip())
                    .pipe(htmlmin(htmlOptions))
                    .pipe(gulp.dest('./app'));
    }

    gulp.src(['./src/data/**/*']).pipe(gulp.dest('./app/data/'));
});
gulp.task('rev', function() {
    return gulp.src(['./app/manifest.json', './app/js/bundle*.js']).pipe(revCollector({
                    replaceReved:true
                    }))
                    .pipe(gulp.dest('./app/js/'));
})

gulp.task('rev:watch', function() {
    return gulp.watch(['./src/js/**/*.js'], ['rev']);
});

gulp.task('html:watch', function() {
    gulp.watch(['./src/**/*.html'], ['html']);
});

gulp.task('watch', ['rev:watch', 'html:watch']);

gulp.task('config-product', function() {
    config.mode = "production";
})


gulp.task('production', gulpsync.sync([["config-product"], ['rev'], 'html']));
gulp.task('develop', gulpsync.sync(['html']));
