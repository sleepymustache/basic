// Gulp plugins
const browserSync = require('browser-sync').create();
const eslint      = require('gulp-eslint');
const gulp        = require('gulp');
const imagemin    = require('gulp-imagemin');
const notify      = require('gulp-notify');
const plumber     = require('gulp-plumber');
const sass        = require('gulp-sass');
const sourcemaps  = require('gulp-sourcemaps');
const webpack     = require('webpack-stream');

const devUrl      = 'http://basic.local.com';

// Source Folders
const baseDir     = './src';
const imageFiles  = baseDir + '/images/**/*.{png,gif,jpg}';
const jsFiles     = baseDir + '/js/**/*.{js,jsx}';
const sassFiles   = baseDir + '/scss/**/*.scss';

// Build Folders
const buildCssFolder   = 'build/css';
const buildImageFolder = 'build/images';
const buildJsFolder    = 'build/js';

// Flags
const flags = {
  shouldMinify: true
};

const handleErrors = function () {
  const args = Array.prototype.slice.call(arguments);

  notify.onError({
    title:   '<%= error.name %>',
    message: '<%= error.message %>'
  }).apply(this, args);
};

/**
 * Lints the source
 */
gulp.task('eslint', function () {
  flags.shouldMinify = true;

  return gulp.src([jsFiles])
    .pipe(eslint())
    .pipe(plumber())
    .pipe(eslint.format())
    .pipe(eslint.failAfterError())
    .on('error', notify.onError((args) => {
      flags.shouldMinify = false;
      return handleErrors(args);
    }));
});

/**
 * Runs by default
 */
gulp.task('default', [
  'scripts',
  'copy',
  'images',
  'styles'
], () => {
  browserSync.init({
    proxy: devUrl,
    notify: false
  });
});

/**
 * Compresses image files for production
 */
gulp.task('images', () => {
  gulp.src(imageFiles)
    .pipe(plumber({errorHandler: handleErrors}))
    .pipe(imagemin())
    .pipe(gulp.dest(buildImageFolder))
    .pipe(browserSync.stream());
});

/**
 * Minifies JS files for production
 */
gulp.task('scripts', ['eslint'], () => {
  if (!flags.shouldMinify) return gulp;

  return gulp.src(baseDir + '/js/main.js')
    .pipe(plumber({errorHandler: handleErrors}))
    .pipe(webpack(require('./webpack.config.js')))
    .pipe(gulp.dest(buildJsFolder))
    .pipe(browserSync.stream());
});

/**
 * Compiles SCSS to CSS and minifies CSS
 */
gulp.task('styles', () => {
  gulp.src(sassFiles)
    .pipe(plumber({errorHandler: handleErrors}))
    .pipe(sourcemaps.init())
    .pipe(sass({
      outputStyle: 'compressed'
    }))
    .pipe(sourcemaps.write('./', {
      includeContent: true,
      sourceRoot: './'
    }))
    .pipe(gulp.dest(buildCssFolder))
    .pipe(browserSync.stream());
});

/**
 * Copy the html files to the build directory
 */
gulp.task('copy', function () {
  return gulp.src([
    baseDir + '/**',
    '!' + sassFiles,
    '!' + imageFiles,
    '!' + jsFiles,
    '!src/app/tests/**'
  ], { dot: true })
    .pipe(plumber({errorHandler: handleErrors}))
    .pipe(gulp.dest('build'))
    .pipe(browserSync.stream());
});

/**
 * Watches for changes in files and does stuff
 */
gulp.task('watch', ['copy', 'images', 'styles', 'scripts'], () => {
  gulp.watch([
    baseDir + '/**',
    '!' + sassFiles,
    '!' + imageFiles,
    '!' + jsFiles
  ], {dot: true}, ['copy']);
  gulp.watch([jsFiles],    ['scripts']);
  gulp.watch([sassFiles],  ['styles']);
  gulp.watch([imageFiles], ['images']);

  browserSync.init({
    proxy: devUrl,
    notify: false
  });
});
