var gulp = require('gulp');
var sass = require('gulp-sass');

var src_path = {
    style: 'sass/'
};

var dist_path = {
    style: 'css/'
};

gulp.task('styles', function() {
    gulp.src(src_path.style + '**/*')
        .pipe(sass().on('error', sass.logError))
        .pipe(gulp.dest(dist_path.style))
});

//Watch task
gulp.task('watch',function() {
    gulp.watch(src_path.style + '**/*',['styles']);
});
