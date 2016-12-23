module.exports = function(grunt) {
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        stylelint: {
            src: [ "css/wsuwp-plugin-idonate-src.css" ]
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

        postcss: {
            options: {
                map: true,
                diff: false,
                processors: [
                    require( "autoprefixer" )( {
                        browsers: [ "> 1%", "ie 8-11", "Firefox ESR" ]
                    } )
                ]
            },
            dist: {
                src: "css/wsuwp-plugin-idonate-src.css",
                dest: "css/wsuwp-plugin-idonate.css"
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
                    "known-properties": 1,                 // Okay to ignore in the case of known unknowns.
                    "duplicate-background-images": 2,
                    "duplicate-properties": 2,
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
                files: [ "css/wsuwp-plugin-idonate-src.css" ],
                tasks: [ "default" ],
                option: {
                    livereload: 8000
                }
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

    grunt.loadNpmTasks( "grunt-phpcs" );
    grunt.loadNpmTasks( "grunt-contrib-watch" );
    grunt.loadNpmTasks( "grunt-contrib-connect" );
    grunt.loadNpmTasks( "grunt-postcss" );
    grunt.loadNpmTasks( "grunt-contrib-csslint" );
    grunt.loadNpmTasks( "grunt-stylelint" );

    // Default task(s).
    grunt.registerTask( "default", ["phpcs", "stylelint", "postcss", "csslint" ] );

    grunt.registerTask( "serve", [ "connect", "watch" ] );
};
