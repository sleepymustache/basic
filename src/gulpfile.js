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
  var args = Array.prototype.slice.call(arguments);

  notify.onError({
    title: 'Compile Error',
    message: '<%= error.message %>'
  }).apply(this, args);

  this.emit('end');
}

/**
 * Runs by default
 */
gulp.task('default', ['images', 'scripts', 'styles', 'watch'], function () {});

/**
 * Compresses image files for production
 */
gulp.task('images', function () {
  gulp.src(imageFolder + '/*')
    .on('error', handleErrors)
    .pipe(imagemin())
    .pipe(gulp.dest(buildImageFolder));
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
 * Compiles SCSS to CSS and minifies CSS
 */
gulp.task('styles', function () {
 var sassOptions = {
    'sourcemap': true,
    'style': 'compressed'
  };

  return sass('scss/**/*.scss', sassOptions)
    .on('error', handleErrors)
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
