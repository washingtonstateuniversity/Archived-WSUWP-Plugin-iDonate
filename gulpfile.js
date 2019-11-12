var gulp = require('gulp'),
	phpcs = require('gulp-phpcs'),
	sourcemaps = require('gulp-sourcemaps'),
	less = require('gulp-less'),
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
		.pipe(
			rename(function(path) {
				path.basename = path.basename.replace('-src', '');
			})
		)
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
	gulp.watch('./css/**/*.less', ['less']);
	gulp.watch('./includes/**/*.js');
	gulp.watch('./includes/**/*.php', ['phpcs']);
});

gulp.task('default', ['phpcs', 'less'], function() {});

gulp.task('serve', ['default', 'watch'], function() {});
