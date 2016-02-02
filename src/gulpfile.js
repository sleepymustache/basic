// Gulp plugins
var gulp = require('gulp');
var imagemin = require('gulp-imagemin');
var livereload = require('gulp-livereload');
var notify = require('gulp-notify');
var sass = require('gulp-ruby-sass');
var sourcemaps = require('gulp-sourcemaps');
var uglify = require('gulp-uglify');

// Source Folders
var imageFolder = 'images';
var jsFolder = 'js';
var mainSassFile = 'main.scss';
var sassFolder = 'scss';

// Build Folders
var buildCssFolder = 'build/css';
var buildImageFolder = 'build/img';
var buildJsFolder = 'build/js';

function handleErrors() {
  console.log('handling error');
  var args = Array.prototype.slice.call(arguments);
  notify.onError({
    title: 'Compile Error',
    message: '<%= error.message %>'
  }).apply(this, args);
  this.emit('end'); // Keep gulp from hanging on this task
}

function buildScript(file, watch) {
  var props = {
    entries: ['./' + jsFolder + '/' + file],
    debug : true,
    transform:  [reactify]
  };

  // watchify() if watch requested, otherwise run browserify() once
  var bundler = watch ? watchify(browserify(props)) : browserify(props);

  function rebundle() {
    var stream = bundler.bundle();
    return stream
      .on('error', handleErrors)
      .pipe(source(file))
      .pipe(buffer())
      //.pipe(uglify())
      .pipe(gulp.dest('./' + buildJsFolder + '/'))
      .pipe(livereload());
  }

  // listen for an update and run rebundle
  bundler.on('update', function() {
    rebundle();
    console.log('Rebundle...');
  });

  // run it once the first time buildScript is called
  return rebundle();
}

/**
 * Compiles SCSS to CSS and minifies CSS
 */
gulp.task('styles', function () {
 var sassOptions = {
    'sourcemap': true,
    'style': 'compressed'
  };

  return sass('scss/**/*.scss', sassOptions)
    .on('error', function (err) {
        console.error("Error", err.message);
    })
    .pipe(sourcemaps.init({debug: true}))
    .pipe(sourcemaps.write('./', {
      includeContent: true,
      sourceRoot: './'
    }))
    .pipe(gulp.dest('./' + buildCssFolder))
    .pipe(livereload());
});

/**
 * Watches for changes in files and does stuff
 */
gulp.task('watch', function () {
  var server = livereload.listen();
  gulp.watch([jsFolder + '/**/*.js'], ['scripts']);
  gulp.watch([sassFolder + '/**/*.scss'], ['styles']);
  gulp.watch([imageFolder + '/**/*'], ['images']);
});

/**
 * Minifies JS files for production
 */
gulp.task('scripts', function () {
  gulp.src(jsFolder + '/*.js')
    .pipe(uglify())
    .on('error', handleErrors)
    .pipe(gulp.dest(buildJsFolder))
    .pipe(livereload());
});

/**
 * Compresses image files for production
 */
gulp.task('images', function () {
  gulp.src(imageFolder + '/*')
    .on('error', handleErrors)
    .pipe(imagemin())
    .pipe(gulp.dest(buildImageFolder));
})

/**
 * Runs by default
 */
gulp.task('default', ['images', 'scripts', 'styles', 'watch'], function () {});