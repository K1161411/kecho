// gulp 安裝命令
// @lastdate 2016-04-13 12:31:03
//
// npm install gulp autoprefixer strftime gulp-less gulp-autoprefixer gulp-concat gulp-jade gulp-jshint gulp-uglify gulp-jshint gulp-coffee gulp-header gulp-rename gulp-sourcemaps gulp-clean-css gulp-imports gulp-livereload gulp-imports gulp-watch --save-dev
//
//
// 初始化gulp插件
// --------------------------------------------
var gulp = require('gulp');
var less = require('gulp-less');
var cleanCss = require('gulp-clean-css');
var sourcemaps = require('gulp-sourcemaps');
var prefix = require('gulp-autoprefixer');
// var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var strftime = require('strftime');
var header = require('gulp-header');
var rename = require('gulp-rename');
var livereload = require('gulp-livereload');
var imports = require('gulp-imports');
//
//
// mod module
// --------------------------------------------
var gulp_date_now = strftime('%F %T'); // mod current time
var gulp_comment_banner = '/*! ---- * <%= date %> ---- */\n\n'; // mod ASCII banner
//
//
// 编译压缩后台相关样式与脚本
// --------------------------------------------

// 加载后台所有需要压缩js文件
var theme_script_files = [
    'themes/ryu/_source/js/app.js',
];
// 压缩并在/Public/js下生成后台admin.min.js文件
gulp.task('theme_script_module', function() {
    gulp
        .src(theme_script_files)
        .pipe(imports())
        .pipe(header(gulp_comment_banner, {
            date: gulp_date_now
        }))
        .pipe(gulp.dest('themes/ryu/assets/js/'))
        // .pipe(uglify())
        // .pipe(header(gulp_comment_banner, {
        //     date: gulp_date_now
        // }))
        // .pipe(rename({
        //     suffix: '.min'
        // }))
        // .pipe(gulp.dest('themes/ryu/assets/js/'))
    ;
});


// 加载后台所有需要编译的less或者css文件
var theme_style_files = [
    'themes/ryu/_source/less/app.less',
];
// 压缩并在/Public/css下生成后台admin.min.css文件
gulp.task('theme_style_module', function() {
    gulp
        .src(theme_style_files)
        .pipe(less())
        .pipe(prefix('last 2 version', 'ie 8', 'ie 9'))
        .pipe(sourcemaps.init())
        .pipe(rename('app.css'))
        .pipe(header(gulp_comment_banner, {
            date: gulp_date_now
        }))
        .pipe(sourcemaps.write())
        .pipe(gulp.dest('themes/ryu/assets/css/'))
        // .pipe(cleanCss())
        // .pipe(rename({
        //     suffix: '.min'
        // }))
        // .pipe(gulp.dest('themes/ryu/assets/css/'))
        // .pipe(livereload())
    ;
});



//
//
// 监听
// --------------------------------------------
gulp.task('watching', function() {
    // TASK
    gulp.watch([
        theme_script_files,
    ], ['theme_script_module']);
    gulp.watch([
        theme_style_files,
        'themes/ryu/_source/less/**/*.less',
    ], ['theme_style_module']);
    // LIVERELOAD
    livereload.listen();
    gulp.watch([
        theme_style_files,
        theme_script_files,
        'themes/ryu/_source/less/**/*.less',
    ], function(event) {
        livereload.changed(event.path);
    });
});
//
//
// 运行
// --------------------------------------------
gulp.task('default', [
    // 后台
    'theme_script_module',
    'theme_style_module',
    // 前台
    // 'home_script_module',
    // 'home_style_module',
    // 监听
    'watching'
]);