var gulp = require('gulp'),
	phpcs = require('gulp-phpcs'),
	phpunit = require('gulp-phpunit'),
	sourcemaps = require('gulp-sourcemaps'),
	lesshint = require('gulp-lesshint'),
	less = require('gulp-less'),
	cssmin = require('gulp-cssmin'),
	LessAutoprefix = require('less-plugin-autoprefix'),
	autoprefix = new LessAutoprefix({
		cascade: false,
		browsers: ['> 1%', 'ie 8-11', 'Firefox ESR'],
	}),
	rename = require('gulp-rename'),
	livereload = require('gulp-livereload');

gulp.task('phpcs', function() {
	return (
		gulp
			.src(['./**/*.php', '!vendor/**/*.*', '!node_modules/**/*.*'])
			// Validate files using PHP Code Sniffer
			.pipe(
				phpcs({
					bin: 'vendor/bin/phpcs',
					standard: 'phpcs.ruleset.xml',
				})
			)
			// Log all problems that was found
			.pipe(phpcs.reporter('log'))
			.pipe(phpcs.reporter('fail', { failOnFirst: false }))
			.pipe(livereload())
	);
});

gulp.task('phpunit', function() {
	return gulp
		.src('phpunit.xml')
		.pipe(phpunit('./vendor/bin/phpunit'))
		.pipe(livereload());
});

gulp.task('lesslint', function() {
	return gulp
		.src('./css/**/*.less')
		.pipe(lesshint())
		.pipe(lesshint.reporter())
		.pipe(lesshint.failOnError())
		.pipe(livereload());
});

gulp.task('less', function() {
	return gulp
		.src('./css/**/*.less')
		.pipe(sourcemaps.init())
		.pipe(
			less({
				plugins: [autoprefix],
				strictMath: true,
				outputSourceFiles: false,
			})
		)
		.pipe(cssmin())
		.pipe(
			rename(function(path) {
				path.basename = path.basename.replace('-src', '');
			})
		)
		.pipe(rename({ suffix: '.min' }))
		.pipe(sourcemaps.write('./'))
		.pipe(gulp.dest('./css'))
		.pipe(livereload());
});

gulp.task('watch', function() {
	livereload.listen({
		host: 'localhost',
		port: 8000,
		start: true,
	});
	gulp.watch('./css/**/*.less', ['lesslint', 'less']);
	gulp.watch('./includes/**/*.js');
	gulp.watch('./includes/**/*.php', ['phpcs', 'phpunit']);
});

gulp.task('default', ['phpcs', 'phpunit', 'lesslint', 'less'], function() {});

gulp.task('serve', ['default', 'watch'], function() {});
