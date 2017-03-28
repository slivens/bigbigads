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
const gulpsync = require('gulp-sync')(gulp);
const htmlmin = require('gulp-htmlmin');

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
      const global = gulp.src('./src/sass/global/*.scss').pipe(sass()).pipe(rev()).pipe(minifyCss()).pipe(gulp.dest('./assets/global/css')).pipe(rev.manifest('global.json')).pipe(gulp.dest('./assets/'));
      const app = gulp.src('./src/sass/apps/*.scss').pipe(sass()).pipe(rev()).pipe(minifyCss()).pipe(gulp.dest('./assets/apps/css')).pipe(rev.manifest('apps.json')).pipe(gulp.dest('./assets/'));
      const pages = gulp.src('./src/sass/pages/*.scss').pipe(sass()).pipe(rev()).pipe(minifyCss()).pipe(gulp.dest('./assets/pages/css')).pipe(rev.manifest('pages.json')).pipe(gulp.dest('./assets/'));

      const layout = (gulp.src('./src/sass/layouts/layout3/*.scss').pipe(sass()).pipe(rev()).pipe(minifyCss()).pipe(gulp.dest('./assets/layouts/layout3/css')).pipe(rev.manifest('layouts-layout3.json')).pipe(gulp.dest('./assets/')));
      const theme = gulp.src('./src/sass/layouts/layout3/themes/*.scss').pipe(sass()).pipe(rev()).pipe(minifyCss()).pipe(gulp.dest('./assets/layouts/layout3/css/themes')).pipe(rev.manifest('layouts-layout3-theme.json')).pipe(gulp.dest('./assets/'));
      return mergeStream(global, app, pages, layout, theme);
    } else {
      // bootstrap compilation
        gulp.src('./src/sass/bootstrap.scss').pipe(sass()).pipe(gulp.dest('./assets/global/plugins/bootstrap/css/'));

      // select2 compilation using bootstrap variables
        //gulp.src('./assets/global/plugins/select2/sass/select2-bootstrap.min.scss').pipe(sass({outputStyle: 'compressed'})).pipe(gulp.dest('./assets/global/plugins/select2/css/'));

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

//*** CSS & JS minify task（TODO:这个命令在WEB APP中没用了，现在临时用来生成前端页面的脚本，最终将被抛弃，前端页面使用laravel的前端工具生成)
// gulp.task('minify', function () {
//     // css minify 
//     gulp.src(['./assets/apps/css/*.css', '!./assets/apps/css/*.min.css']).pipe(minifyCss()).pipe(rename({suffix: '.min'})).pipe(gulp.dest('./static'));

//     gulp.src(['./assets/global/css/*.css','!./assets/global/css/*.min.css']).pipe(minifyCss()).pipe(rename({suffix: '.min'})).pipe(gulp.dest('./static'));
//     gulp.src(['./assets/pages/css/*.css','!./assets/pages/css/*.min.css']).pipe(minifyCss()).pipe(rename({suffix: '.min'})).pipe(gulp.dest('./static'));    
    
//     gulp.src(['./assets/layouts/**/css/*.css','!./assets/layouts/**/css/*.min.css']).pipe(rename({suffix: '.min'})).pipe(minifyCss()).pipe(gulp.dest('./static'));
//     gulp.src(['./assets/layouts/**/css/**/*.css','!./assets/layouts/**/css/**/*.min.css']).pipe(rename({suffix: '.min'})).pipe(minifyCss()).pipe(gulp.dest('./static'));

//     gulp.src(['./assets/global/plugins/bootstrap/css/*.css','!./assets/global/plugins/bootstrap/css/*.min.css']).pipe(minifyCss()).pipe(rename({suffix: '.min'})).pipe(gulp.dest('./static'));

//     //js minify
//     gulp.src(['./src/js/standalone/*.js']).pipe(uglify()).pipe(rename({suffix: '.min'})).pipe(gulp.dest('./js'));
//    // gulp.src(['./assets/global/scripts/*.js','!./assets/global/scripts/*.min.js']).pipe(uglify()).pipe(rename({suffix: '.min'})).pipe(gulp.dest('./assets/global/scripts'));
//   //  gulp.src(['./assets/pages/scripts/*.js','!./assets/pages/scripts/*.min.js']).pipe(uglify()).pipe(rename({suffix: '.min'})).pipe(gulp.dest('./assets/pages/scripts'));
//    // gulp.src(['./assets/layouts/**/scripts/*.js','!./assets/layouts/**/scripts/*.min.js']).pipe(uglify()).pipe(rename({suffix: '.min'})).pipe(gulp.dest('./assets/layouts/'));

// });

gulp.task('clean', function() {
        return gulp.src(['./app/js', './assets/*.json'], {read:false}).pipe(clean());
})

gulp.task('script',  function() {
    var target = 'bigbigads.js';

    if (config.mode === "develop") {
        gulp.src(['./src/js/**/*.js', '!./src/js/standalone/**/*.js']).pipe(sourcemaps.init()).pipe(concat(target)).pipe(sourcemaps.write('./')).pipe(gulp.dest('./app/js/'));
        gulp.src(['./src/js/standalone/**/*.js']).pipe(gulp.dest('./app/js/'));
        gulp.src(['./src/index.html']).pipe(gulp.dest('./app/'));
    } else {
        gulp.src(['./src/js/**/*.js', '!./src/js/standalone/**/*.js']).pipe(concat(target)).pipe(gulp.dest('./app/js/')).pipe(uglify()).pipe(gulp.dest('./app/js/'));
        return gulp.src(['./src/js/standalone/**/*.js'])
            .pipe(uglify())
            .pipe(rev())
            .pipe(gulp.dest(config.script.target))
            .pipe(rev.manifest())
            .pipe(gulp.dest('./assets'));

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
        .pipe(jshint())
        .pipe(jshint.reporter('default'));
    gulp.src('./app/js/main.js')
        .pipe(jshint())
        .pipe(jshint.reporter('default'));

});

//对JS与CSS生成版本号，防止缓存;源文件必须与目标文件分开，否则将只能替换一次
gulp.task('rev',  function() {
    return gulp.src(['./assets/*.json',  './src/index.html']).pipe(revCollector({
                replaceReved:true
                }))
                .pipe(htmlmin({
                    collapseWhitespace:true
                }))
                .pipe(gulp.dest('./app'));
});


gulp.task('script:watch', function() {
    gulp.watch(['./src/js/**/*.js'], ['lint', 'script']);
});

gulp.task('watch', ['sass:watch', 'script:watch']);

gulp.task('config-product', function() {
    config.mode = "production";
})

/**
 * 网站简单处理，统一生成压缩后的目标文件，不打版本
 */
gulp.task('front-production', function() {
      // global theme stylesheet compilation
      const global = gulp.src('./src/sass/global/*.scss').pipe(sass()).pipe(minifyCss()).pipe(gulp.dest('./dist/global/css'));
      const app = gulp.src('./src/sass/apps/*.scss').pipe(sass()).pipe(minifyCss()).pipe(gulp.dest('./dist/apps/css'));
      const pages = gulp.src('./src/sass/pages/*.scss').pipe(sass()).pipe(minifyCss()).pipe(gulp.dest('./dist/pages/css'));
      const layout = gulp.src('./src/sass/layouts/layout3/*.scss').pipe(sass()).pipe(minifyCss()).pipe(gulp.dest('./dist/layouts/layout3/css'));
      const theme = gulp.src('./src/sass/layouts/layout3/themes/*.scss').pipe(sass()).pipe(minifyCss()).pipe(gulp.dest('./dist/layouts/layout3/css/themes'));

    return gulp.src(['./src/js/standalone/**/*.js'])
        .pipe(uglify())
        .pipe(gulp.dest('./dist/'));
})


gulp.task('production', gulpsync.sync([["config-product", 'clean'], ['sass', 'script'], 'rev', 'front-production']));
gulp.task('develop', ['sass', 'script', 'front-production']);
