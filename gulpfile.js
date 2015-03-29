var gulp = require('gulp'),
    sass = require('gulp-sass'),
    autoprefixer = require('gulp-autoprefixer'),
    browserSync = require('browser-sync'),
    ghPages = require('gulp-gh-pages'),
    minifyCSS = require('gulp-minify-css'),
    reload = browserSync.reload;


gulp.task('sass', function () {
    gulp.src('assets/sass/main.scss')
        .pipe(sass({
            errLogToConsole: true
        }))
        .pipe(autoprefixer())
        .pipe(gulp.dest('www/wp-content/themes/rp-starter-theme/css'))
        .pipe(reload({stream: true}));
});

gulp.task('minify', function() {
    return gulp.src('www/wp-content/themes/rp-starter-theme/css/**/.css')
        .pipe(minifyCSS())
        .pipe(gulp.dest('www/wp-content/themes/rp-starter-theme/css'))
});

gulp.task('deploy', function() {
    return gulp.src('www/wp-content/themes/**/*')
        .pipe(ghPages({
            branch: 'staging'
        }));
});

gulp.task('deploy-production', ['minify'], function() {
    return gulp.src('www/wp-content/themes/**/*')
        .pipe(ghPages({
            branch: 'production'
        }));
});

gulp.task('default', ['sass'], function() {

    browserSync({
        proxy: "http://letsgetlost.dev:8080",
        xip: true
    });

    gulp.watch('assets/sass/**/*', ['sass']);
    gulp.watch("www/**/*.php").on('change', reload);
    gulp.watch("www/**/*.twig").on('change', reload);
});