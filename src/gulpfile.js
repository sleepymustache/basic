// Gulp plugins
var gulp = require('gulp');
var imagemin = require('gulp-imagemin');
var livereload = require('gulp-livereload');
var sass = require('gulp-ruby-sass');
var uglify = require('gulp-uglify');

// Source Folders
var imageFolder = 'images';
var jsFolder = 'js';
var mainSassFile = 'main.scss';
var sassFolder = 'scss'

// Build Folders
var buildCssFolder = 'build/css';
var buildImageFolder = 'build/img';
var buildJsFolder = 'build/js';

/**
 * Compiles SCSS to CSS and minifies CSS
 */
gulp.task('styles', function () {
  var sassOptions = {
    'sourcemapPath': '../' + sassFolder,
    'style': 'compressed'
  };

  return gulp.src(sassFolder + '/' + mainSassFile)
    .pipe(sass(sassOptions).
      on('error', function (err) {
        console.error("Error", err.message);
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

/**
 * Minifies JS files for production
 */
gulp.task('scripts', function () {
  gulp.src(jsFolder + '/*.js')
    .pipe(uglify())
    .pipe(gulp.dest(buildJsFolder));
});

/**
 * Compresses image files for production
 */
gulp.task('images', function () {
  gulp.src(imageFolder + '/*')
    .pipe(imagemin())
    .pipe(gulp.dest(buildImageFolder));
})

/**
 * Runs by default
 */
gulp.task('default', ['images', 'scripts', 'styles', 'watch'], function () {});