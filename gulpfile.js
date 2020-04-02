const { dest, parallel, series, src, watch } = require('gulp');

// Configuration
const devUrl = 'http://localhost:8080'; // The local development URL for BrowserSync
const enableTests = false;              // Set to true to enable tests

// Gulp plugins
const browserSync = require('browser-sync').create();
const eslint      = require('gulp-eslint');
const imagemin    = require('gulp-imagemin');
const notify      = require('gulp-notify');
const plumber     = require('gulp-plumber');
const sass        = require('gulp-sass');
const sourcemaps  = require('gulp-sourcemaps');
const webpack     = require('webpack-stream');
const zip         = require('gulp-zip');

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
const handleErrors = (err) => {
  notify.onError({
    title:   '<%= error.name %>',
    message: '<%= error.message %>'
  })(err);
};

/**
 * Handles the deleting of watched files
 * @param {object} event
 */
const fileDeleter = (event) => {
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
const lint = () => {
  state.shouldMinify = true;

  return src([jsFiles])
    .pipe(eslint())
    .pipe(plumber())
    .pipe(eslint.format())
    .pipe(eslint.failAfterError())
    .on('error', notify.onError((err) => {
      state.shouldMinify = false;
      return handleErrors(err);
    }));
};

/**
 * Compresses image files for production
 */
const images = () => {
  return src(imageFiles)
    .pipe(plumber({ errorHandler: handleErrors }))
    .pipe(imagemin())
    .pipe(dest(buildImageFolder))
    .pipe(browserSync.stream());
};

/**
 * Minifies JS files for production
 */
const scripts = series(lint, (cb) => {
  if (!state.shouldMinify) return cb;

  return src(baseDir + '/js/main.js')
    .pipe(plumber({ errorHandler: handleErrors }))
    .pipe(webpack(require('./webpack.config.js')))
    .pipe(dest(buildJsFolder))
    .pipe(browserSync.stream());
});

/**
 * Compiles SCSS to CSS and minifies CSS
 */
const styles = () => {
  return src(sassFiles)
    .pipe(plumber({ errorHandler: handleErrors }))
    .pipe(sourcemaps.init())
    .pipe(sass({
      outputStyle: 'compressed'
    }))
    .pipe(sourcemaps.write('./', {
      includeContent: true,
      sourceRoot: './'
    }))
    .pipe(dest(buildCssFolder))
    .pipe(browserSync.stream({ watch: '**/*.css' }));
};

/**
 * Copy the html files to the build directory
 */
const copy = () => {
  var files = [
    baseDir + '/**',
    '!' + sassFiles,
    '!' + imageFiles,
    '!' + jsFiles
  ];

  if (!enableTests) {
    files.push('!' + baseDir + '/app/tests/**');
  }

  return src(files, { nodir: true, dot: true })
    .pipe(plumber({errorHandler: handleErrors}))
    .pipe(dest(buildFolder))
    .pipe(browserSync.stream());
};

const build = series((cb) => {
  browserSync.init({
    proxy: devUrl,
    notify: false
  });

  cb();
}, parallel(copy, images, styles, scripts));

const cleanup = (cb) => { cb(); };

/**
 * Watches for changes in files and does stuffF
 */
const develop = parallel(build, () => {
  watch([jsFiles], scripts);
  watch([sassFiles], styles);
  const imageWatcher = watch([imageFiles], images);
  const copyWatcher  = watch([
    baseDir + '/**',
    '!' + sassFiles,
    '!' + imageFiles,
    '!' + jsFiles
  ], { dot: true }, copy);

  copyWatcher.on('change',  fileDeleter);
  imageWatcher.on('change', fileDeleter);
});

const packUp = series(build, () => {
  var today = new Date();

  return src('dist/**/*')
    .pipe(zip(
      today.getFullYear().toString() + '-' +
      today.getMonth().toString() + '-' +
      today.getDay().toString() + '_' +
      today.getHours().toString() +
      today.getMinutes().toString() +
      '-dist.zip'
    ))
    .pipe(dest('./'));
});

//if (process.env.NODE_ENV === 'production') {
exports.cleanup = cleanup;
exports.zip     = packUp;
exports.develop = develop;
exports.build   = build;
exports.default = develop;
exports.copy    = copy;
