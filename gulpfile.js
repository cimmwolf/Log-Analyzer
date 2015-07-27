var gulp = require('gulp');
var less = require('gulp-less');
var autoprefixer = require('gulp-autoprefixer');
var minifyCss = require('gulp-minify-css');
var coffee = require('gulp-coffee');
var uglify = require('gulp-uglify');

gulp.task('less', function () {
    return gulp.src('./less/style.less')
        .pipe(less({
            paths: ['./vendor/twbs/bootstrap/less']
        }))
        .pipe(autoprefixer({
            browsers: ['last 2 versions'],
            cascade: false
        }))
        .pipe(minifyCss())
        .pipe(gulp.dest('./css'));
});

gulp.task('coffee', function () {
    gulp.src('./coffee/*.coffee')
        .pipe(coffee())
        .pipe(uglify())
        .pipe(gulp.dest('./js/'))
});

gulp.task('default', ['less', 'coffee'], function () {
    // place code for your default task here
});