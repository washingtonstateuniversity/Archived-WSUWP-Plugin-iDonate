module.exports = function(grunt) {
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        stylelint: {
            src: [ "css/wsuwp-plugin-idonate.css"]
        },

        phpcs: {
            plugin: {
                src: './'
            },
            options: {
                bin: "vendor/bin/phpcs --extensions=php --ignore=\"*/vendor/*,*/node_modules/*\"",
                standard: "phpcs.ruleset.xml"
            }
        },

        less: {
            options: {
                strictMath: true,
				sourceMap:true,
				outputSourceFiles: false,
				plugins : [ new (require('less-plugin-autoprefix'))({cascade:false, browsers : [ "> 1%", "ie 8-11", "Firefox ESR"  ]}) ]
            },
            development: {
                files: {
                    'css/wsuwp-plugin-idonate.css': 'css/wsuwp-plugin-idonate-src.less'
                }
            }
        },

        lesslint: {
            src: ['css/*.less'],
            options: {
                csslint: {
                    "fallback-colors": false,              // Unless we want to support IE8
                    "box-sizing": false,                   // Unless we want to support IE7
                    "compatible-vendor-prefixes": false,   // The library on this is older than autoprefixer.
                    "gradients": false,                    // This also applies ^
                    "overqualified-elements": false,       // We have weird uses that will always generate warnings.
                    "ids": false,
                    "regex-selectors": false,
                    "adjoining-classes": false,
                    "box-model": false,
                    "universal-selector": false,
                    "unique-headings": false,
                    "outline-none": false,
                    "floats": false,
                    "font-sizes": false,
                    "important": false,                    // This should be set to 2 one day.
                    "unqualified-attributes": false,       // Should probably be 2 one day.
                    "qualified-headings": false,
                    "known-properties": false,                 // Okay to ignore in the case of known unknowns.
                    "duplicate-background-images": false,  // This should ideally be 2
                    "duplicate-properties": false, // @todo should be 2
                    "star-property-hack": 2,
                    "text-indent": 2,
                    "display-property-grouping": 2,
                    "shorthand": 2,
                    "empty-rules": false,
                    "vendor-prefix": 2,
                    "zero-units": 2,
                    "order-alphabetical": false

                }
            }
        },

        csslint: {
            main: {
                src: [ "css/wsuwp-plugin-idonate.css" ],
                options: {
                    "fallback-colors": false,              // Unless we want to support IE8
                    "box-sizing": false,                   // Unless we want to support IE7
                    "compatible-vendor-prefixes": false,   // The library on this is older than autoprefixer.
                    "gradients": false,                    // This also applies ^
                    "overqualified-elements": false,       // We have weird uses that will always generate warnings.
                    "ids": false,
                    "regex-selectors": false,
                    "adjoining-classes": false,
                    "box-model": false,
                    "universal-selector": false,
                    "unique-headings": false,
                    "outline-none": false,
                    "floats": false,
                    "font-sizes": false,
                    "important": false,                    // This should be set to 2 one day.
                    "unqualified-attributes": false,       // Should probably be 2 one day.
                    "qualified-headings": false,
                    "known-properties": false,                 // Okay to ignore in the case of known unknowns.
                    "duplicate-background-images": false,  // This should ideally be 2
                    "duplicate-properties": false, // @todo should be 2
                    "star-property-hack": 2,
                    "text-indent": 2,
                    "display-property-grouping": 2,
                    "shorthand": 2,
                    "empty-rules": false,
                    "vendor-prefix": 2,
                    "zero-units": 2,
                    "order-alphabetical": false
                }
            }
        },

        watch: {
            styles: {
                files: [ "css/wsuwp-plugin-idonate-src.less", "includes/*.php" ],
                tasks: [ "default" ],
                option: {
                    livereload: 8000
                }
            }
        },

		sed: {
			tabs: {
				path: "css/wsuwp-plugin-idonate.css",
				pattern: "  ",
				replacement: '\t',
				recursive: false
			},
			zeros: {
				path: "css/wsuwp-plugin-idonate.css",
				pattern: '0[.]',
				replacement: '.',
				recursive: false
			},
			squiggleys: {
				path: "css/wsuwp-plugin-idonate.css",
				pattern: '}',
				replacement: '}\r\n',
				recursive: false
			},
			tabspace: {
				path: "css/wsuwp-plugin-idonate.css",
				pattern: '\t ',
				replacement: '\t',
				recursive: false
			},
			nestedspace: {
				path: "css/wsuwp-plugin-idonate.css",
				pattern: '[)] \{',
				replacement: ') {\r\n',
				recursive: false
			},
			addspaceatend: {
				path: "css/wsuwp-plugin-idonate.css",
				pattern: ' [*]/',
				replacement: ' */\n',
				recursive: false
			}							
		},
 
        connect: {
            server: {
                options: {
                    open: true,
                    port: 8000,
                    hostname: "localhost"
                }
            }
        }
    });

	grunt.loadNpmTasks( "grunt-contrib-clean" );
    grunt.loadNpmTasks( "grunt-contrib-watch" );
    grunt.loadNpmTasks( "grunt-contrib-connect" );
    grunt.loadNpmTasks( "grunt-contrib-less" );
    grunt.loadNpmTasks( "grunt-contrib-csslint" );
	grunt.loadNpmTasks( "grunt-lesslint" );
    grunt.loadNpmTasks( "grunt-phpcs" );
	grunt.loadNpmTasks( "grunt-sed" );
    grunt.loadNpmTasks( "grunt-stylelint" );

    // Default task(s).
    grunt.registerTask( "default", [ "lesslint", "less", "sed", "stylelint", "phpcs" ] );
    grunt.registerTask( "serve", [ "connect", "watch" ] );
};
