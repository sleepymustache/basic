var gulp = require('gulp'),
    sass = require('gulp-ruby-sass'),
    minifycss = require('gulp-minify-css'),
    addsrc = require('gulp-add-src'),
    concat = require('gulp-concat'),
    sourcemaps = require('gulp-sourcemaps');

var sassFolder = 'scss',
    mainSassFile = sassFolder + '/main.scss',
    cssFolder = 'css';

gulp.task('styles', function () {
  return gulp.src(mainSassFile)
      .pipe(sass({sourcemap: true, sourcemapPath: '../' + sassFolder}).
        on('error', function (err) {
          console.error("Error", err.message);
        }))
      .pipe(gulp.dest(cssFolder))
      .pipe(concat('main.min.css'))
      .pipe(minifycss())
      .pipe(gulp.dest(cssFolder));
});

gulp.task('watch', function () {
  gulp.watch([sassFolder + '/**/*.scss'], ['styles']);
});

gulp.task('default', ['watch'], function () {});