
module.exports = function(grunt) {
    grunt.loadNpmTasks('grunt-angular-gettext');

    grunt.registerTask('extract_lang', ['nggettext_extract']);
    grunt.registerTask('compile_lang', ['nggettext_compile']);

    grunt.initConfig({
        nggettext_extract: {
            pot: {
                files: {
                    'lang/template.pot': [
                        'assets/templates/*.html',
                        'assets/templates/*/*.html',
                        'assets/js/*.js',
                        'assets/js/*/*.js',
                        'assets/js/*/*/*.js',
                        'templates/*.html'
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
        }
    });
};
