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
const zip         = require('gulp-zip');

// The dev URL for browserSync
const devUrl      = 'http://basic.local.com';

// Source Folders
const baseDir     = 'src';
const imageFiles  = baseDir + '/images/**/*.{png,gif,jpg}';
const jsFiles     = baseDir + '/js/**/*.{js,jsx}';
const sassFiles   = baseDir + '/scss/**/*.scss';

// Build Folders
const buildFolder =      'dist';
const buildCssFolder   = buildFolder + '/css';
const buildImageFolder = buildFolder + '/images';
const buildJsFolder    = buildFolder + '/js';

// Application State
const state = {
  // Shouldn't try to minify JS if there are errors
  shouldMinify: true
};

/**
 * Handles errors with notifications
 */
const handleErrors = function () {
  const args = Array.prototype.slice.call(arguments);

  notify.onError({
    title:   '<%= error.name %>',
    message: '<%= error.message %>'
  }).apply(this, args);
};

/**
 * Handles the deleting of watched files
 * @param {object} event
 */
const fileDeleter = function (event) {
  const del = require('del');
  const path = require('path');

  if (event.type === 'deleted') {
    const filePathFromSrc = path.relative(path.resolve(baseDir), event.path);
    const destFilePath = path.resolve(buildFolder, filePathFromSrc);
    del.sync(destFilePath);
  }
};

/**
 * Lints the source
 */
gulp.task('eslint', function () {
  state.shouldMinify = true;

  return gulp.src([jsFiles])
    .pipe(eslint())
    .pipe(plumber())
    .pipe(eslint.format())
    .pipe(eslint.failAfterError())
    .on('error', notify.onError((args) => {
      state.shouldMinify = false;
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
 * Runs by default
 */
gulp.task('build', [
  'scripts',
  'copy',
  'images',
  'styles'
]);

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
  if (!state.shouldMinify) return gulp;

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
    '!' + baseDir + '/app/tests/**'
  ], { nodir: true, dot: true })
    .pipe(plumber({errorHandler: handleErrors}))
    .pipe(gulp.dest(buildFolder))
    .pipe(browserSync.stream());
});

/**
 * Watches for changes in files and does stuff
 */
gulp.task('watch', ['copy', 'images', 'styles', 'scripts'], () => {
  const imageWatcher = gulp.watch([imageFiles], ['images']);
  const copyWatcher = gulp.watch([
    baseDir + '/**',
    '!' + sassFiles,
    '!' + imageFiles,
    '!' + jsFiles
  ], { dot: true }, ['copy']);

  gulp.watch([jsFiles], ['scripts']);
  gulp.watch([sassFiles],  ['styles']);

  copyWatcher.on('change', fileDeleter);
  imageWatcher.on('change', fileDeleter);

  browserSync.init({
    proxy: devUrl,
    notify: false
  });
});

gulp.task('cleanup', [], function () {

});

gulp.task('zip', ['copy', 'images', 'styles', 'scripts'], () => {
  var today = new Date();

  gulp.src('dist/**/*')
    .pipe(zip(
      today.getFullYear().toString() + "-" +
      today.getMonth().toString() + "-" +
      today.getDay().toString() + "_" +
      today.getHours().toString() +
      today.getMinutes().toString() +
      '-dist.zip'
    ))
    .pipe(gulp.dest('./'));
});
