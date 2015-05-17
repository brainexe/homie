module.exports = function (grunt) {
    grunt.loadNpmTasks('grunt-angular-gettext');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-clean');
    grunt.loadNpmTasks('grunt-contrib-htmlmin');
    grunt.loadNpmTasks('grunt-contrib-uglify');

    grunt.registerTask('extract_lang', ['nggettext_extract']);
    grunt.registerTask('compile_lang', ['nggettext_compile']);

    grunt.registerTask('bower', function() {
        var done = this.async();

        var child = grunt.util.spawn({
            cmd: 'bower',
            args: ['update', '--production']
        }, function(err, out) {
            done();
        });
        child.stdout.pipe(process.stdout);
        child.stderr.pipe(process.stderr);
    });

    grunt.registerTask('console', function () {
        var args = arguments;
        var task = Object.keys(args).map(function (key) {
            return args[key];
        });

        var done = this.async();
        task = task.join(':');
        var child = grunt.util.spawn({
            cmd: 'php',
            args: ['console', task],
            stdio: 'inherit'
        }, function(err, out) {
            done();
        });
        child.stdout.pipe(process.stdout);
        child.stderr.pipe(process.stderr);
    });

    grunt.registerTask('build', ['compile_lang', 'clean', 'copy', 'uglify', 'htmlmin', 'concat']);
    grunt.registerTask('buildAll', ['bower', 'build']);
    grunt.registerTask('default', ['build']);

    grunt.initConfig({
        nggettext_extract: {
            pot: {
                files: {
                    'lang/template.pot': [
                        'assets/templates/**/*.html',
                        'assets/js/**/*.js',
                        'assets/**/*.html'
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
                files: ['assets/**', 'lang/*.po'],
                tasks: ['build'],
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
        },
        copy: {
            index: {
                files: [
                    {expand: true, flatten: true, src: ['vendor/brainexe/core/scripts/index.php'], dest: 'web/'}
                ]
            },
            static: {
                files: [
                    {expand: true, src: ['**/*.ico', '**/*.png', '**/*.jpg'], cwd: 'assets/', dest: 'web/', filter: 'isFile'},
                    {expand: true, src: ['**/*.woff', '**/*.woff2'], cwd: 'bower_components/bootstrap', dest: 'web/', filter: 'isFile'}
                ]
            }
        },
        clean: ["web/**"],
        concat: {
            'common.css': {
                src: [
                    'bower_components/bootstrap/dist/css/bootstrap.min.css',
                    'bower_components/rickshaw/rickshaw.css',
                    'bower_components/allmighty-autocomplete/style/autocomplete.css',
                    'bower_components/ui-select/dist/select.min.css',
                    'assets/css/*.css'
                ],
                dest: 'web/common.css',
                nonull: true
            }
        },
        htmlmin: {
            templates: {
                options: {
                    removeComments: true,
                    collapseWhitespace: true
                },
                files: [{
                    expand: true,
                    cwd: 'assets',
                    src: '**/*.html',
                    dest: 'web'
                }]
            }
        },
        uglify: {
            app: {
                options: {
                    beautify: true,
                    compress: false,
                    mangle: false
                },
                files: {
                    'web/app.js': [
                        'assets/js/app.js',
                        'assets/js/util/**/*.js',
                        'assets/js/controllers/**/*.js',
                        'assets/lang/*js'
                    ]
                }
            },
            vendor: {
                options: {
                    compress: false,
                    mangle: false
                },
                files: {
                    'web/vendor.js': [
                        'bower_components/jquery/dist/jquery.min.js',
                        'bower_components/sockjs-client/dist/sockjs.js',
                        'bower_components/angular/angular.min.js',
                        'bower_components/angular-route/angular-route.min.js',
                        'bower_components/angular-gettext/dist/angular-gettext.min.js',
                        'bower_components/angular-sanitize/angular-sanitize.min.js',
                        'bower_components/angular-bootstrap/ui-bootstrap-tpls.min.js',
                        'bower_components/ui-select/dist/select.min.js',
                        'bower_components/allmighty-autocomplete/script/autocomplete.js',

                        // sensor app
                        'bower_components/rickshaw/vendor/d3.v3.js',
                        'bower_components/rickshaw/rickshaw.js',

                        'assets/js/vendor/**/*js'
                    ]
                }
            }
        }
    });
};
