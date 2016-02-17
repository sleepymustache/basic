// Gulp plugins
var gulp = require('gulp');
var imagemin = require('gulp-imagemin');
var livereload = require('gulp-livereload');
var notify = require('gulp-notify');
var sass = require('gulp-sass');
var sourcemaps = require('gulp-sourcemaps');
var uglify = require('gulp-uglify');

// Source Folders
var imageFolder = 'src/images';
var jsFolder = 'src/js';
var sassFolder = 'src/scss';

// Build Folders
var buildCssFolder = 'src/build/css';
var buildImageFolder = 'src/build/img';
var buildJsFolder = 'src/build/js';

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
    .pipe(gulp.dest(buildImageFolder))
    .pipe(livereload());
});

/**
 * Minifies JS files for production
 */
gulp.task('scripts', function () {
  gulp.src(jsFolder + '/*.js')
    .on('error', handleErrors)
    .pipe(uglify())
    .pipe(gulp.dest(buildJsFolder))
    .pipe(livereload());
});

/**
 * Compiles SCSS to CSS and minifies CSS
 */
gulp.task('styles', function () {
  return gulp.src(sassFolder + '/**/*.scss')
    .pipe(sourcemaps.init())
    .pipe(sass({
      outputStyle: 'compressed'
    })
    .on('error', handleErrors))
    .pipe(sourcemaps.write('./', {
      includeContent: true,
      sourceRoot: './'
    }))
    .pipe(gulp.dest(buildCssFolder))
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
