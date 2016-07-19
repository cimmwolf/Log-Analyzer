var gulp = require('gulp');
var less = require('gulp-less');
var autoprefixer = require('gulp-autoprefixer');
var cssnano = require('gulp-cssnano');
var coffee = require('gulp-coffee');
var uglify = require('gulp-uglify');

gulp.task('default', ['less', 'coffee'], function () {
    gulp.src('dist/js/*.js')
        .pipe(uglify({mangle: false}))
        .pipe(gulp.dest('dist/js'));
    return gulp.src('dist/css/*.css')
        .pipe(cssnano())
        .pipe(gulp.dest('dist/css'));
});

gulp.task('less', function () {
    return gulp.src('src/less/style.less')
        .pipe(less({
            paths: ['vendor/twbs/bootstrap/less']
        }))
        .pipe(autoprefixer({
            browsers: ['last 2 versions'],
            cascade: false
        }))
        .pipe(gulp.dest('dist/css'));
});

gulp.task('coffee', function () {
    gulp.src('src/coffee/*.coffee')
        .pipe(coffee())
        .pipe(gulp.dest('dist/js/'))
});