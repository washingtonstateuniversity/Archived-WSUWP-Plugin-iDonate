var gulp = require('gulp'),
	phpcs = require('gulp-phpcs'),
	sourcemaps = require('gulp-sourcemaps'),
	less = require('gulp-less'),
	LessAutoprefix = require('less-plugin-autoprefix'),
	autoprefix = new LessAutoprefix({
		cascade: false,
		browsers: ['> 1%', 'ie 8-11', 'Firefox ESR'],
	}),
	rename = require('gulp-rename');

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
		.pipe(gulp.dest('./css'));
});

gulp.task('default', ['phpcs', 'less'], function() {});
