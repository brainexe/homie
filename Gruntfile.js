module.exports = function(grunt) {
    grunt.loadNpmTasks('grunt-angular-gettext');

    grunt.initConfig({
        nggettext_extract: {
            pot: {
                files: {
                    'lang/template.pot': ['assets/templates/*.html', 'templates/*.html']
                }
            }
        },
        nggettext_compile: {
            all: {
                files: {
                    'assets/lang/translations.js': ['lang/*.po']
                }
            },
        },
    });
}
