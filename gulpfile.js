/* global require */

let gulp = require('gulp');
let autoPrefix = require('autoprefixer');
let cssNano = require('cssnano');
let htmlMin = require('gulp-htmlmin');
let postCssHtml = require('gulp-html-postcss');

gulp.task('compress-components', function() {
    return gulp.src('src/components/*-*.html')
        .pipe(htmlMin({
            removeComments: true,
            preventAttributesEscaping: true,
            collapseWhitespace: true,
            minifyJS: true,
        }))
        .pipe(postCssHtml([
            autoPrefix,
            cssNano({safe: true}),
        ]))
        .pipe(gulp.dest('components'));
});

gulp.task('publish', ['compress-components'], function() {
    return gulp.src([
        'bower_components/iron-*/*.html',
        'bower_components/paper-*/*.html',
        'bower_components/google-*/*.html',
        'bower_components/gold-*/*.html',
        'bower_components/neon-*/*.html',
        'bower_components/platinum-*/*.html',
        'bower_components/polymer/*.html',
    ], {base: 'bower_components'})
        .pipe(htmlMin({
            removeComments: true,
            preventAttributesEscaping: true,
            collapseWhitespace: true,
            minifyJS: true,
            minifyCSS: true,
        }))
        .pipe(gulp.dest('bower_components'));
});
