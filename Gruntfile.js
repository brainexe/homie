module.exports = function (grunt) {
    'use strict';

    grunt.loadNpmTasks('grunt-angular-gettext');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-clean');
    grunt.loadNpmTasks('grunt-contrib-htmlmin');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-compress');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-manifest');
    grunt.loadNpmTasks('grunt-exec');
    grunt.loadNpmTasks('grunt-jslint');

    // todo improve performance: https://www.npmjs.com/package/grunt-parallelize
    grunt.registerTask('extract_lang', ['nggettext_extract']);
    grunt.registerTask('compile_lang', ['nggettext_compile']);

    grunt.registerTask('bower', function () {
        var done = this.async();

        var child = grunt.util.spawn({
            cmd: 'bower',
            args: ['update', '--production']
        }, function (err, out) {
            done();
        });
        child.stdout.pipe(process.stdout);
        child.stderr.pipe(process.stderr);
    });

    grunt.registerTask('websocket', function (option) {
        option = option || 'start';
        var forever = require('forever');

        function start() {
            console.log('start...');
            forever.startDaemon("./nodejs/websocket.js");
        }

        function stop() {
            console.log('stop...');
            forever.stopAll();
        }

        switch (option) {
            case 'start':
                start();
                break;
            case 'stop':
                stop();
                break;
            case 'restart':
                stop();
                start();
                break;
            default:
                throw "use: (start|stop|restart)";
        }
    });
    // todo refactor (merge with websocket)
    grunt.registerTask('messageQueue', function (option) {
        option = option || 'start';
        var forever = require('forever');

        function start() {
            console.log('start...');
            forever.startDaemon("./nodejs/message-queue.js");
        }

        function stop() {
            console.log('stop...');
            forever.stopAll();
        }

        switch (option) {
            case 'start':
                start();
                break;
            case 'stop':
                stop();
                break;
            case 'restart':
                stop();
                start();
                break;
            default:
                throw "use: (start|stop|restart)";
        }
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
        }, function (err, out) {
            done();
        });
        child.stdout.pipe(process.stdout);
        child.stderr.pipe(process.stderr);
    });

    grunt.registerTask('build', ['compile_lang', 'copy', 'uglify', 'htmlmin', 'concat', 'cssmin', 'manifest', 'compress']);
    grunt.registerTask('buildAll', ['bower', 'build']);
    grunt.registerTask('default', ['build']);

    var isProduction = process.env.ENVIRONMENT == 'production';
    grunt.initConfig({
        nggettext_extract: {
            pot: {
                files: {
                    'lang/template.pot': [
                        'assets/templates/**/*.html',
                        'assets/js/**/*.js',
                        'assets/**/*.html'
                    ]
                },
                options: {
                    markerNames: ['_']
                }
            }
        },
        nggettext_compile: {
            all: {
                options: {
                    format: "json"
                },
                files: [
                    {
                        expand: true,
                        dot: true,
                        cwd: "lang",
                        dest: "web/lang",
                        src: ["*.po"],
                        ext: ".json"
                    }
                ]
            }
        },
        watch: {
            js: {
                files: ['assets/**/*.js'],
                tasks: ['uglify:app'],
                options: {
                    livereload: true
                }
            },
            css: {
                files: ['assets/**/*.css'],
                tasks: ['concat', 'cssmin'],
                options: {
                    livereload: true
                }
            },
            // todo po files
            templates: {
                files: ['assets/**/*.html'],
                tasks: ['htmlmin'],
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
                    {
                        expand: true,
                        src: ['**/*.ico', '**/*.png', '**/*.jpg'],
                        cwd: 'assets/',
                        dest: 'web/',
                        filter: 'isFile'
                    },
                    {
                        expand: true,
                        src: ['**/*.woff', '**/*.woff2'],
                        cwd: 'bower_components/bootstrap',
                        dest: 'web/',
                        filter: 'isFile'
                    }
                ]
            }
        },
        clean: ["web/**"],
        concat: {
            'common.css': {
                src: [
                    'bower_components/bootstrap/dist/css/bootstrap.min.css',
                    'bower_components/rickshaw/rickshaw.css',
                    'bower_components/ui-select/dist/select.min.css',
                    'bower_components/angular-bootstrap-colorpicker/css/colorpicker.min.css',
                    //'bower_components/ng-sortable/dist/ng-sortable.style.min.css', // todo
                    'assets/css/**/*.css'
                ],
                dest: 'web/common.css',
                nonull: true
            }
        },
        cssmin: {
            target: {
                files: [{
                    expand: true,
                    cwd: 'web/',
                    src: ['*.css', '!*.min.css'],
                    dest: 'web/',
                    ext: '.min.css'
                }]
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
                    beautify: !isProduction,
                    compress: isProduction ? {
                        unsafe: true,
                        unsafe_comps: true,
                        screw_ie8: true,
                        angular: true,
                        pure_getters: true,
                        hoist_vars: true
                    } : false,
                    mangle: isProduction ? {
                        toplevel: true
                    } : false,
                    sourceMap: isProduction,
                    sourceMapIncludeSources: true,
                    sourceMapName: 'web/app.map'
                },
                files: {
                    'web/app.js': [
                        'assets/js/app.js',
                        'assets/js/util/**/*.js',
                        'assets/js/models/**/*.js',
                        'assets/js/controllers/**/*.js'
                    ]
                }
            },
            vendor: {
                options: {
                    compress: false,
                    mangle: false,
                    sourceMap: isProduction,
                    sourceMapIncludeSources: true,
                    sourceMapName: 'web/vendor.map'
                },
                files: {
                    'web/vendor.js': [
                        'bower_components/angular/angular.min.js',
                        'bower_components/angular-route/angular-route.min.js',
                        'bower_components/angular-gettext/dist/angular-gettext.min.js',
                        'bower_components/angular-sanitize/angular-sanitize.min.js', // todo needed?
                        'bower_components/angular-bootstrap/ui-bootstrap-tpls.min.js',
                        'bower_components/angular-native-dragdrop/draganddrop.js',
                        'bower_components/angular-cache/dist/angular-cache.min.js',
                        'bower_components/angular-bootstrap-colorpicker/js/bootstrap-colorpicker-module.min.js',
                        'bower_components/ng-sortable/dist/ng-sortable.min.js',
                        'bower_components/ui-select/dist/select.min.js',
                        'bower_components/sockjs-client/dist/sockjs.min.js',

                        // needed for sensor module
                        'bower_components/rickshaw/vendor/d3.min.js',
                        'bower_components/rickshaw/rickshaw.min.js',

                        'assets/js/vendor/**/*js'
                    ]
                }
            }
        },
        manifest: {
            generate: {
                cwd: 'web/',
                options: {
                    network: ['*'],
                    fallback: ['/ /index.html'],
                    exclude: ['manifest.appcache'],
                    preferOnline: true,
                    basePath: 'web',
                    verbose: true,
                    timestamp: true,
                    hash: true,
                    master: ['index.html']
                },
                src: [
                    '**/*.html',
                    '**/*.js',
                    '**/*.json',
                    '**/*.css',
                    '**/*.png',
                    '**/*.jpg',
                    '**/*.woff',
                    '**/*.woff2',
                    '**/*.ico'
                ],
                dest: 'web/manifest.appcache'
            }
        },
        compress: {
            main: {
                cwd: 'web/',
                options: {
                    mode: 'gzip',
                    level: 9
                },
                files: [
                    {expand: true, src: ['web/**/*.js'],   dest: '.', ext: '.js.gz'},
                    {expand: true, src: ['web/**/*.json'], dest: '.', ext: '.json.gz'},
                    {expand: true, src: ['web/**/*.html'], dest: '.', ext: '.html.gz'},
                    {expand: true, src: ['web/**/*.css'],  dest: '.', ext: '.css.gz'}
                ]
            }
        },
        exec: {
            install: {
                command: function () {
                    return [
                        'composer install',
                        'grunt bower',
                        'bower install',
                        'php console cc'
                    ].join(' && ');
                }
            }
        },
        jslint: { // configure the task
            // lint your project's server code
            server: {
                src: [
                    'assets/**/*.js'
                ],
                directives: {
                    node: true,
                    todo: true
                }
            },
            options: {
                edition: 'latest', // specify an edition of jslint or use 'dir/mycustom-jslint.js' for own path
                junit: 'out/server-junit.xml', // write the output to a JUnit XML
                log: 'out/server-lint.log',
                jslintXml: 'out/server-jslint.xml',
                errorsOnly: true, // only display errors
                failOnError: false, // defaults to true
                checkstyle: 'out/server-checkstyle.xml' // write a checkstyle-XML
            }
        }
    });
};
