"use strict";

module.exports = function (gulp, $, browserSync) {
  var strip = require('gulp-strip-comments');

  gulp.task('js-min', function () {
    return gulp.src(['./dist/js/*.js', '!./dist/js/lift.js']).pipe($.uglify()).pipe(strip()) // .pipe($.rename({
    //     suffix: '.min'
    // }))
    .pipe(gulp.dest('./dist/js'));
  });
};