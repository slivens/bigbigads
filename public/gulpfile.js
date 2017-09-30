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
const critical = require('critical');
const penthouse = require('penthouse')
const fs = require('fs')
const cheerio = require('cheerio')

var config = {
    script:{
        target:'./app/js/'
    },
    mode:"develop"
}

function readFilePromise (filepath, encoding) {
  return new Promise((resolve, reject) => {
    fs.readFile(filepath, encoding, (err, content) => {
      if (err) {
        return reject(err)
      }
      resolve(content)
    })
  })
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
        return gulp.src(['./app/**/*.map', './app/**/*.js', './app/*.css', './assets/*.json', './app/*.json', './app/**/*.html'], {read:false}).pipe(clean());
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
    gulp.src(['./src/js/**/*.js'])
        .pipe(eslint())
        .pipe(eslint.format())
        // .pipe(eslint.failAfterError())
});


// gulp.task('rev', function() {
//     return gulp.src(['./app/manifest.json', './app/js/bundle*.js']).pipe(revCollector({
//                     replaceReved:true
//                     }))
//                     .pipe(gulp.dest('./app/js/'));
// })

// gulp.task('rev:watch', function() {
//     return gulp.watch(['./src/js/**/*.js'], ['rev']);
// });

// gulp.task('html:watch', function() {
//     gulp.watch(['./src/**/*.html'], ['html']);
// });

// gulp.task('watch', ['rev:watch', 'html:watch']);

gulp.task('config-product', function() {
    config.mode = "production";
})

gulp.task('critical', async function(cb) {
    let revData = await readFilePromise('app/manifest.json')
    let revs = JSON.parse(revData)
    return penthouse({
        url: 'http://bigbigads.dev/app/', // can also use file:/// protocol for local files
        // cssString: 'body { color; red }', // the original css to extract critcial css from
        css: [
            'node_modules/bootstrap/dist/css/bootstrap.min.css', 
            'app/' + revs['bundle.css'], 
            'app/' + revs['search.css'], 
            'assets/global/plugins/select2/css/select2.min.css',
            'assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css'
            ], // path to original css file on disk
        // OPTIONAL params
        width: 1300, // viewport width
        height: 900, // viewport height
        forceInclude: [ // selectors to keep
            '.keepMeEvenIfNotSeenInDom',
            /^\.regexWorksToo/
        ],
        propertiesToRemove: [
            '(.*)transition(.*)',
            'cursor',
            'pointer-events',
            '(-webkit-)?tap-highlight-color',
            '(.*)user-select'
        ],
        timeout: 60000, // ms; abort critical CSS generation after this timeout
        strict: false, // set to true to throw on CSS errors (will run faster if no errors)
        maxEmbeddedBase64Length: 1000, // characters; strip out inline base64 encoded resources larger than this
        userAgent: 'Penthouse Critical Path CSS Generator', // specify which user agent string when loading the page
        renderWaitTime: 100, // ms; render wait timeout before CSS processing starts (default: 100)
        blockJSRequests: false, // set to false to load (external) JS (default: true)
        customPageHeaders: {
            'Accept-Encoding': 'identity' // add if getting compression errors like 'Data corrupted'
        },
        screenshots: {
            // turned off by default
            basePath: './', // absolute or relative; excluding file extension
            type: 'jpeg', // jpeg or png, png default
            quality: 20 // only applies for jpeg type
            // -> these settings will produce homepage-before.jpg and homepage-after.jpg
        },
        htmltag: 'app'
    })
    .then(async function(res) {
        // console.log(res)
        // use the critical css
        fs.writeFileSync('app/critical.css', res.formattedCss)
        fs.writeFileSync('app/static.html', res.html)
    })
    .catch(err => {
        console.log("error", err)
        // handle the error
    })   
})

gulp.task("test", async function() {
    let css = await readFilePromise('app/critical.css')
    let criticalHtml = await readFilePromise('app/static.html')
    let orig = await readFilePromise('app/index.html')       
    let $ = cheerio.load(orig)
    $('style').html(css)
    $('#server-render').html(criticalHtml)
    fs.writeFileSync('app/index.html', $.html())
})

// gulp.task('production', gulpsync.sync([["config-product"], 'html']));
// gulp.task('develop', gulpsync.sync(['html']));
