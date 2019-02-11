var gulp = require('gulp');
var del = require('del');
var less = require('gulp-less');
var cssmin = require('gulp-clean-css');
var rename = require('gulp-rename');

gulp.task('requirements', async() => {
    gulp.src('bower_components/jquery/dist/jquery.min.js')
        .pipe(gulp.dest('public/static'));
    gulp.src('bower_components/bootstrap/dist/css/bootstrap.min.css')
        .pipe(gulp.dest('public/static'));
    await gulp.src('bower_components/bootstrap/dist/js/bootstrap.min.js')
        .pipe(gulp.dest('public/static'));
});

gulp.task('less',async()=>{
    await gulp.src('src/less/main.less').pipe(less()).pipe(cssmin()).pipe(rename('main.min.css'))
    .pipe(gulp.dest('public/static'));
});

gulp.task('views', async() => {
    await gulp.src(['src/views/**/*','!src/views/config*.php']).pipe(gulp.dest('public'));
});

gulp.task('clean', async() => {
    await del('public/**/*');
});

gulp.task('build', gulp.parallel('requirements','views','less'));
gulp.task('clean-build', gulp.series('clean', gulp.parallel('requirements','views','less')));