var gulp = require('gulp');
var del = require('del');
var less = require('gulp-less');
var cssmin = require('gulp-clean-css');
var rename = require('gulp-rename');
var uglify = require('gulp-uglify');
var concat = require('gulp-concat');
var htmlmin = require('gulp-htmlmin');

gulp.task('requirements', async() => {
    //jquery
    gulp.src('bower_components/jquery/dist/jquery.min.js')
        .pipe(gulp.dest('public/static'));
    //toastr
    gulp.src('bower_components/toastr/toastr.min.js')
        .pipe(gulp.dest('public/static'));
    gulp.src('bower_components/toastr/toastr.min.css')
        .pipe(gulp.dest('public/static'));
    //font-awesome and awesome-checkbox
    gulp.src('src/static/font-awesome-4.7.0/css/font-awesome.min.css')
        .pipe(gulp.dest('public/static'));
    gulp.src('src/static/font-awesome-4.7.0/fonts/*')
        .pipe(gulp.dest('public/static/fonts'));
    gulp.src('node_modules/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css').pipe(cssmin())
        .pipe(gulp.dest('public/static'));
    //animate.css
    gulp.src('node_modules/animate.css/animate.min.css')
        .pipe(gulp.dest('public/static'));
    //crypto
    gulp.src('bower_components/crypto-js/crypto-js.js')
        .pipe(uglify())
        .pipe(gulp.dest('public/static'));
    //moment & tempusdominus
    gulp.src(['bower_components/moment/moment.js','bower_components/moment/locale/zh-cn.js'])
        .pipe(concat('moment.min.js'))
        .pipe(uglify())
        .pipe(gulp.dest('public/static'));
    gulp.src('src/static/tempusdominus/*.js')
        .pipe(gulp.dest('public/static'));
    gulp.src('src/static/tempusdominus/tempusdominus.css')
        .pipe(cssmin())
        .pipe(rename('tempusdominus.min.css'))
        .pipe(gulp.dest('public/static'));
    //bootstrap
    gulp.src('bower_components/bootstrap/dist/css/bootstrap.min.css')
        .pipe(gulp.dest('public/static'));
    gulp.src('bower_components/bootstrap/dist/css/bootswatch-materia.min.css')
        .pipe(gulp.dest('public/static'));
    //canvas_nest
    gulp.src('node_modules/canvas-nest.js/dist/canvas-nest.js')
        .pipe(gulp.dest('public/static'));
    //pjax
    gulp.src('node_modules/jquery-pjax/jquery.pjax.js')
        .pipe(gulp.dest('public/static'));
    await gulp.src('bower_components/bootstrap/dist/js/bootstrap.min.js')
        .pipe(gulp.dest('public/static'));
});

gulp.task('less',async()=>{
    gulp.src('src/less/main.less').pipe(less()).pipe(cssmin()).pipe(rename('main.min.css'))
        .pipe(gulp.dest('public/static'));
    await gulp.src('src/less/views/admin/main.less').pipe(less()).pipe(cssmin()).pipe(rename('admin.min.css'))
        .pipe(gulp.dest('public/static'));
});

gulp.task('views', async() => {
    gulp.src('src/static/*.js').pipe(uglify()).pipe(gulp.dest('public/static'));
    gulp.src('src/views/**/.htaccess').pipe(gulp.dest('public'));
    var htmloptions = {
        collapseWhitespace:true,
        collapseBooleanAttributes:true,
        removeComments:true,
        removeEmptyAttributes:false,
        removeScriptTypeAttributes:false,
        removeStyleLinkTypeAttributes:false,
        minifyJS:true,
        minifyCSS:true
    };
    gulp.src('src/views/index.default.php').pipe(htmlmin(htmloptions)).pipe(gulp.dest('public'));
    gulp.src('src/views/index.error.php').pipe(htmlmin(htmloptions)).pipe(gulp.dest('public'));
    await gulp.src(['src/views/**/*', '!src/views/index.default.php','!src/views/index.error.php']).pipe(gulp.dest('public'));
});

gulp.task('clean', async() => {
    await del('public/**/*');
});

gulp.task('watch',function(){
    gulp.watch('src/less/**/*.less',gulp.series('less'));
    gulp.watch('src/views/**/*', gulp.series('views'));
    gulp.watch('src/static/**/*', gulp.series('requirements'));
    gulp.watch('node_modules/**/*', gulp.series('requirements'));
    gulp.watch('bower_components/**/*',gulp.series('requirements'));
});

gulp.task('build', gulp.parallel('requirements','views','less'));
gulp.task('clean-build', gulp.series('clean', gulp.parallel('requirements','views','less')));