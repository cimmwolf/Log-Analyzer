var gulp = require('gulp');
var less = require('gulp-less');
var autoprefixer = require('autoprefixer');
var cssNano = require('cssnano');
var htmlMin = require('gulp-htmlmin');
var postCss = require('gulp-postcss');
var postCssHtml = require('gulp-html-postcss');

gulp.task('default', ['less'], function () {
    return gulp.src('dist/css/*.css')
        .pipe(postCss([
            autoprefixer,
            cssNano({safe: true})
        ]))
        .pipe(gulp.dest('dist/css'));
});

gulp.task('less', function () {
    return gulp.src('src/less/style.less')
        .pipe(less({
            paths: ['vendor/twbs/bootstrap/less']
        }))
        .pipe(gulp.dest('dist/css'));
});

gulp.task('compress-components', function () {
    return gulp.src('components/*-*.html')
        .pipe(htmlMin({
            removeComments: true,
            preventAttributesEscaping: true,
            collapseWhitespace: true,
            minifyJS: true
        }))
        .pipe(postCssHtml([
            autoprefixer,
            cssNano({safe: true})
        ]))
        .pipe(gulp.dest('components'));
});

gulp.task('publish', ['default', 'compress-components'], function () {
    return gulp.src([
        'bower_components/iron-*/*.html',
        'bower_components/paper-*/*.html',
        'bower_components/google-*/*.html',
        'bower_components/gold-*/*.html',
        'bower_components/neon-*/*.html',
        'bower_components/platinum-*/*.html',
        'bower_components/polymer/*.html'
    ], {base: 'node_modules/@bower_components'})
        .pipe(cache('components'))
        .pipe(htmlMin({
            removeComments: true,
            preventAttributesEscaping: true,
            collapseWhitespace: true,
            minifyJS: true,
            minifyCSS: true
        }))
        .pipe(gulp.dest('bower_components'));
});