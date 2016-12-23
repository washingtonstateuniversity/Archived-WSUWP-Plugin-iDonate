module.exports = function(grunt) {
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        phpcs: {
            plugin: {
                src: './'
            },
            options: {
                bin: "vendor/bin/phpcs --extensions=php --ignore=\"*/vendor/*,*/node_modules/*\"",
                standard: "phpcs.ruleset.xml"
            }
        },

        watch: {
            styles: {
                files: [ "css/*.css", "src/js/*.js" ],
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

    grunt.loadNpmTasks('grunt-phpcs');
    grunt.loadNpmTasks( "grunt-contrib-watch" );
    grunt.loadNpmTasks( "grunt-contrib-connect" );

    // Default task(s).
    grunt.registerTask('default', ['phpcs']);

    grunt.registerTask( "serve", [ "connect", "watch" ] );
};
