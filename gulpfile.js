var gulp = require('gulp'),
    sass = require('gulp-sass'),
    browserSync = require('browser-sync'),
    reload = browserSync.reload;


gulp.task('sass', function () {
    gulp.src('assets/sass/main.scss')
        .pipe(sass({
            errLogToConsole: true
        }))
        .pipe(gulp.dest('www/wp-content/themes/rp-starter-theme/css'))
        .pipe(reload({stream: true}));
});

gulp.task('default', ['sass'], function() {

    browserSync({
        proxy: "http://letsgetlost.dev:8080"
    });

    gulp.watch('assets/sass/**/*', ['sass']);
    gulp.watch("www/**/*.php").on('change', reload);
    gulp.watch("www/**/*.twig").on('change', reload);
});