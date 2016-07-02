var fs   = require('fs');
var glob = require('glob');

module.exports = function (grunt) {
    grunt.config('env', grunt.option('env') || process.env.ENVIRONMENT || 'development');
    var isProduction = grunt.config('env') == 'production';

    var locales = glob.sync('*.po', {cwd:'lang/'}).map(function(filename) {
        return filename.replace('.po', '');
    });

    grunt.loadNpmTasks('grunt-angular-gettext');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-contrib-clean');
    grunt.loadNpmTasks('grunt-contrib-htmlmin');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-compress');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-sass');
    grunt.loadNpmTasks('grunt-manifest');
    grunt.loadNpmTasks('grunt-exec');
    grunt.loadNpmTasks('grunt-po2mo');
    grunt.loadNpmTasks('grunt-uniqueify');

    grunt.registerTask('extract_lang', ['php_gettext_extract', 'nggettext_extract', 'pot_prepare', 'exec:potMerge']);
    grunt.registerTask('compile_lang', ['nggettext_compile', 'po2mo']);

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

    grunt.registerTask('php_gettext_extract', function () {
        var done = this.async();
        var exec = require('child_process').exec;
        exec('xgettext --from-code=utf-8 -o lang/pot/php.pot --keyword=translate $(find src vendor/brainexe -name *.php)', function(err, stdout, stderr) {
            done();
        });
    });

    grunt.registerTask('pot_prepare', function () {
        var done = this.async();

        var potStream = fs.createWriteStream('lang/pot/all.pot', {flags: 'w'});

        var child = grunt.util.spawn({
            cmd: 'msgcat',
            args: ['--use-first', 'lang/pot/frontend.pot', 'lang/pot/php.pot']
        }, function (err, out) {
            done();
        });
        child.stdout.pipe(potStream);
        child.stderr.pipe(process.stderr);
    });

    grunt.registerTask('pot_merge', function () {
        var done = this.async();

        var potStream = fs.createWriteStream('lang/pot/all.pot', {flags: 'w'});

        var child = grunt.util.spawn({
            cmd: 'msgmerge',
            args: ['lang/de_DE.po', 'lang/pot/all.pot']
        }, function (err, out) {
            done();
        });
        child.stdout.pipe(potStream);
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
        }, function (err, out) {
            done();
        });
        child.stdout.pipe(process.stdout);
        child.stderr.pipe(process.stderr);
    });

    var defaultTasks = [
        'clean',
        'compile_lang',
        'copy:index',
        'copy:static',
        'uglify',
        'htmlmin',
        'sass',
        'cssmin',
    ];

    if (isProduction) {
        defaultTasks.push('manifest');
        defaultTasks.push('uniqueify');
        defaultTasks.push('compress');
    }

    grunt.registerTask('build', defaultTasks);
    grunt.registerTask('buildAll', ['bower', 'build']);
    grunt.registerTask('default', ['build']);

    grunt.initConfig({
        nggettext_extract: {
            pot: {
                files: {
                    'lang/pot/frontend.pot': [
                        'assets/templates/**/*.html',
                        'assets/js/**/*.js',
                        'assets/**/*.html',
                        'cache/translation_token.html'
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
            sass: {
                files: ['assets/**/*.sass', 'assets/**/*.scss'],
                tasks: ['sass', 'cssmin'],
                options: {
                    livereload: true
                }
            },
            templates: {
                files: ['assets/**/*.html'],
                tasks: ['htmlmin'],
                options: {
                    livereload: true
                }
            },
            po: {
                files: ['lang/*.po'],
                tasks: ['compile_lang'],
                options: {
                    livereload: true
                }
            },
            php: {
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
                    {
                        expand: true,
                        flatten: true,
                        src: ['vendor/brainexe/core/scripts/index.php'],
                        dest: 'web/'
                    }
                ]
            },
            static: {
                files: [
                    {
                        expand: true,
                        src: ['**/*.ico', '**/*.png', '**/*.jpg', '*.json'],
                        cwd: 'assets/',
                        dest: 'web/'
                    },
                    {
                        expand: true,
                        src: ['*'],
                        cwd: 'bower_components/bootstrap-sass/assets/fonts/bootstrap/',
                        dest: 'web/fonts/'
                    }
                ]
            }
        },
        clean: [
            'web/**',
            'assets/cache/**'
        ],
        cssmin: {
            app: {
                options: {
                    semanticMerging: false
                },
                files: {
                    'web/app.css': [
                        'assets/**/*.css',
                        'bower_components/rickshaw/rickshaw.css',
                        'bower_components/ui-select/dist/select.min.css',
                        'bower_components/angular-bootstrap-colorpicker/css/colorpicker.min.css'
                    ]
                }
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
                    sourceMap: true,
                    sourceMapIncludeSources: true,
                    sourceMapName: 'web/app.map'
                },
                files: {
                    'web/app.js': [
                        'assets/js/app.js',
                        'assets/js/util/**/*.js',
                        'assets/js/models/**/*.js',
                        'assets/js/controllers/**/*.js',
                        'assets/js/listeners/**/*.js'
                    ]
                }
            },
            vendor: {
                options: {
                    compress: false,
                    mangle: false,
                    sourceMap: true,
                    sourceMapIncludeSources: true,
                    sourceMapName: 'web/vendor.map'
                },
                files: {
                    'web/vendor.js': [
                        'bower_components/angular/angular.min.js',
                        'bower_components/angular-route/angular-route.min.js',
                        'bower_components/angular-gettext/dist/angular-gettext.min.js',
                        'bower_components/angular-sanitize/angular-sanitize.min.js',
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
                    fallback: ['/index.html'],
                    exclude: ['manifest.appcache'],
                    preferOnline: true,
                    basePath: 'web',
                    timestamp: false,
                    verbose: false,
                    master: ['index.html']
                },
                src: [
                    '**/*.{html,js,json,css,jpg,png,woff,woff2,ttf,svg,eot,ico}',
                ],
                dest: 'web/manifest.appcache'
            }
        },
        compress: {
            main: {
                cwd: 'web/',
                options: {
                    mode: 'gzip'
                },
                files: [
                    {
                        expand: true,
                        src: ['web/**/*.{js,json,css,html,map,appcache}'],
                        rename: function(dest, matchedSrcPath) {
                            return './' + matchedSrcPath + '.gz';
                        }
                    }
                ]
            }
        },
        exec: {
            install: {
                command: function () {
                    return [
                        'composer install -o',
                        'grunt bower',
                        'bower install',
                        'php console cc'
                    ].join(' && ');
                }
            },
            potMerge: {
                command: function () {
                    return locales.map(function(locale) {
                        return 'msgmerge lang/' + locale + '.po lang/pot/all.pot -U';
                    }).join(' && ');
                }
            }
        },
        sass: {
            dist: {
                options: {
                    style: 'expanded',
                    sourcemap: 'none',
                    cacheLocation: 'cache/sass/'
                },
                files: [{
                    expand: true,
                    cwd: 'assets/',
                    src: ['**/*.sass'],
                    dest: 'assets/cache/',
                    ext: '.css'
                }]
            }
        },
        po2mo: locales.map(function(locale) {
            return {
                src: 'lang/' + locale + '.po',
                dest: 'cache/lang/' + locale + '/LC_MESSAGES/messages.mo'
            }
        }),
        uniqueify: {
             static: {
                 options: {
                     replaceSrc: ['web/*.{html,appcache}']
                 },
                 files: [{
                     cwd: 'web/',
                     src: ['**/*.{js,css,ico,appcache}']
                 }]
             },
             html: {
                 options: {
                     replaceSrc: ['web/*.{js,appcache}', 'web/*/**/*.html']
                 },
                 files: [{
                     cwd: 'web/',
                     src: ['*/**/*.html']
                 }]
             },
             map: {
                 options: {
                     replaceSrc: ['web/*.{js,css}']
                 },
                 files: [{
                     cwd: 'web/',
                     src: ['*.map']
                 }]
             }
        }
    });
};
