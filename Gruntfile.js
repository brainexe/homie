
module.exports = function(grunt) {
    grunt.loadNpmTasks('grunt-angular-gettext');
    grunt.loadNpmTasks('grunt-contrib-watch');

    grunt.registerTask('extract_lang', ['nggettext_extract']);
    grunt.registerTask('compile_lang', ['nggettext_compile']);

    grunt.registerTask('console', function(task) {
        var child = grunt.util.spawn({
            cmd: 'php',
            args: ['console', task],
            stdio: 'inherit'
        });
        child.stdout.pipe(process.stdout);
        child.stderr.pipe(process.stderr);

        console.log('php console ' +  task);
    });

    grunt.initConfig({
        nggettext_extract: {
            pot: {
                files: {
                    'lang/template.pot': [
                        'assets/templates/**/*.html',
                        'assets/js/**/*.js',
                        'templates/**/*.html'
                    ]
                }
            }
        },
        nggettext_compile: {
            all: {
                files: {
                    'assets/lang/translations.js': ['lang/*.po']
                }
            }
        },
        watch: {
            assets: {
                files: ['assets/**'],
                tasks: ['console:cc']
            },
            src: {
                files: ['src/**'],
                tasks: ['console:cc']
            }
        }
    });
};
