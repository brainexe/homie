
module.exports = function(grunt) {
    grunt.loadNpmTasks('grunt-angular-gettext');
    grunt.loadNpmTasks('grunt-contrib-watch');

    grunt.registerTask('extract_lang', ['nggettext_extract']);
    grunt.registerTask('compile_lang', ['nggettext_compile']);

    grunt.registerTask('console', function() {
        var args = arguments;
        var task = Object.keys(args).map(function (key) {
            return args[key];
        });

        task = task.join(':');
        var child = grunt.util.spawn({
            cmd: 'php',
            args: ['console', task],
            stdio: 'inherit'
        });
        child.stdout.pipe(process.stdout);
        child.stderr.pipe(process.stderr);
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
                tasks: ['console:assets:dump'],
                options: {
                    livereload: true
                }
            },
            lang: {
                files: ['lang/*.po'],
                tasks: ['compile_lang', 'console:assets:dump'],
                options: {
                    livereload: true
                }
            },
            src: {
                files: ['src/**'],
                tasks: ['console:cc'],
                options: {
                    debounceDelay: 3000
                }
            }
        }
    });
};
