var fs   = require('fs');
var glob = require('glob');
var exec = require('child_process').exec;

module.exports = function (grunt) {
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
    grunt.loadNpmTasks('grunt-concurrent');

    grunt.config('env', grunt.option('env') || process.env.ENVIRONMENT || 'development');
    const isProduction = grunt.config('env') === 'production';

    var locales = glob.sync('*.po', {cwd:'lang/'}).map(function(filename) {
        return filename.replace('.po', '');
    });

    function registerExecTask(name, command) {
        grunt.registerTask(name, function () {
            var done = this.async();
            exec(command, {shell: '/bin/bash'}, function(error, stdout, stderr) {
                done();
                if (error) {
                    console.error(`exec error: ${error}`);
                    return;
                }
                console.log(stdout);
            });
        });
    }

    var defaultTasks = [
        'clean',
        'compile_lang',
        'copy',
        'lodash',
        'uglify',
        'htmlmin',
        'sass',
        'cssmin',
        'manifest'
    ];

    if (isProduction) {
        defaultTasks.push('uniqueify');
        defaultTasks.push('compress');
    }

    grunt.registerTask('build', defaultTasks);
    grunt.registerTask('buildAll', ['bower', 'build']);
    grunt.registerTask('default', ['build']);

    grunt.registerTask('extract_lang', ['php_gettext_extract', 'nggettext_extract', 'pot_merge', 'exec:potMerge']);
    grunt.registerTask('compile_lang', ['nggettext_compile', 'po2mo']);

    registerExecTask('bower', 'bower update --production');
    registerExecTask('lodash', `
        TASKS=$(grep -hoP "lodash\\.\\K(\\w*)" assets/js -r | sort | uniq | paste -s -d, -);
        CACHE_FILE=./cache/lodash_generated;
        TARGET_FILE=./bower_components/lodash/dist/lodash.custom.js
        touch $CACHE_FILE;
        CACHED=$(<$CACHE_FILE);
        if [ "$CACHED" = "$TASKS" ]; then
            echo -ne "Already generated ($CACHED)";
        else
            echo -ne "Generating $TASKS (cached: $CACHED)";
            node ./node_modules/lodash-cli/bin/lodash --output $TARGET_FILE --development include=$TASKS;
            echo $TASKS > $CACHE_FILE;
        fi
        echo -ne "...$(($(stat -c%s $TARGET_FILE) / 1000)) kb"
    `);
    registerExecTask('php_gettext_extract', 'xgettext --from-code=utf-8 -o lang/pot/php.pot --add-comments --keyword=translate $(find src vendor/brainexe -name "*.php")');
    registerExecTask('pot_merge', 'msgcat --use-first lang/pot/frontend.pot lang/pot/php.pot > lang/pot/all.pot');

    grunt.registerTask('console', function (command) {
        var done = this.async();
        command = 'php console ' + command;

        exec(command, function (stdErr, stdout) {
            console.log(stdout);
            if (stdErr) {
                console.err(stdErr);
            }
            done();
        });
    });

    grunt.initConfig({
        concurrent: {
            options: {
                limit: 100
            },
            prepare: ['clean', 'lodash', 'compile_lang'],
            minify: ['copy', 'uglify:app', 'uglify:vendor', 'htmlmin', ['sass', 'cssmin']],
        },
        nggettext_extract: {
            pot: {
                files: {
                    'lang/pot/frontend.pot': [
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
                tasks: ['lodash', 'uglify'],
                options: {
                    livereload: true
                }
            },
            sass: {
                files: ['assets/**/*.{sass,scss}'],
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
                        src: ['**/*.ico', '**/*.png', '**/*.jpg', '**/*.mp3', '*.json'],
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
            'cache/css/**'
        ],
        cssmin: {
            app: {
                options: {
                    sourceMap: true,
                    keepSpecialComments: 0,
                    sourceMapName: 'web/appcss.map',
                    level: {
                        2: {
                            all: true,
                        }
                    }
                },
                files: {
                    'web/app.css': [
                        'cache/css/**/*.css',
                        'bower_components/rickshaw/rickshaw.css',
                        'bower_components/ui-select/dist/select.min.css',
                        'bower_components/angular-bootstrap-colorpicker/css/colorpicker.min.css',
                        'bower_components/angular-loading-bar/build/loading-bar.min.css'
                    ]
                }
            }
        },
        htmlmin: {
            templates: {
                options: {
                    removeComments: true,
                    collapseWhitespace: true,
                    minifyCSS: true
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
                    compress: {
                        unsafe:         true,
                        unsafe_comps:   true,
                        unsafe_math:    true,
                        angular:        true,
                        pure_getters:   true,
                        hoist_funs:     true,
                        hoist_vars:     true,
                        collapse_vars:  true,
                        reduce_vars:    true,
                        keep_fargs:     false,
                        pure_funcs:     isProduction ? ['console.debug'] : [],
                        global_defs: {
                            LANG_FILES: JSON.stringify(
                                locales.reduce(function(all, locale) {
                                    all[locale] = '/lang/' + locale + '.json';
                                    return all;
                                }, {})
                            ),
                            DEBUG: !isProduction
                        }
                    },
                    mangle: isProduction ? {
                        toplevel: true,
                        regex: '.*'
                    } : false,
                    mangleProperties: {
                        regex: /^(_.+|[A-Z_]{2,}$)/
                    },
                    enclose: true,
                    sourceMap: isProduction,
                    sourceMapName: 'web/appjs.map',
                    sourceMapIncludeSources: true
                },
                files: {
                    'web/app.js': [
                        'assets/js/**/*.js'
                    ]
                }
            },
            vendor: {
                options: {
                    compress:       false,
                    mangle:         false,
                    sourceMap:      isProduction,
                    sourceMapName:  'web/vendorjs.map',
                    enclose:        {}
                },
                files: {
                    'web/vendor.js': [
                        'bower_components/angular/angular.min.js',
                        'bower_components/angular-route/angular-route.min.js',
                        'bower_components/angular-gettext/dist/angular-gettext.min.js',
                        'bower_components/angular-sanitize/angular-sanitize.min.js',
                        'bower_components/angular-bootstrap/ui-bootstrap-tpls.min.js',
                        'bower_components/angular-native-dragdrop/draganddrop.min.js',
                        'bower_components/angular-cache/dist/angular-cache.min.js',
                        'bower_components/angular-bootstrap-colorpicker/js/bootstrap-colorpicker-module.min.js',
                        'bower_components/ng-sortable/dist/ng-sortable.min.js',
                        'bower_components/ui-select/dist/select.min.js',
                        'bower_components/sockjs-client/dist/sockjs.min.js',
                        'bower_components/angular-loading-bar/build/loading-bar.min.js',
                        'bower_components/lodash/dist/lodash.custom.js',

                        // needed for sensor module
                        'bower_components/rickshaw/vendor/d3.min.js',
                        'bower_components/rickshaw/rickshaw.min.js'
                    ]
                }
            }
        },
        manifest: {
            generate: {
                cwd: 'web/',
                options: {
                    network:        ['*'],
                    fallback:       ['/', '/templates/offline.html'],
                    exclude:        ['manifest.appcache'],
                    preferOnline:   true,
                    basePath:       'web',
                    timestamp:      false,
                    verbose:        false,
                    master:         ['index.html'],
                },
                src: [
                    '**/*.{html,js,json,css,jpg,png,woff,woff2,ttf,svg,eot,ico}'
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
                    style: isProduction ? 'compressed' : 'expanded',
                    sourcemap: 'none',
                    cacheLocation: 'cache/sass/',
                    update: true,
                    stopOnError: true
                },
                files: [{
                    expand: true,
                    cwd: 'assets/sass/',
                    src: ['**/*.sass'],
                    dest: 'cache/css/',
                    ext: '.css'
                }]
            }
        },
        po2mo: locales.reduce(function(all, locale) {
            all[locale] = {
                src: 'lang/' + locale + '.po',
                dest: 'cache/lang/' + locale + '/LC_MESSAGES/messages.mo'
            };

            return all;
        }, {}),
        uniqueify: {
             static: {
                 options: {
                     replaceSrc: ['web/*.{html,appcache,js}']
                 },
                 files: [{
                     cwd: 'web/',
                     src: ['**/*.{js,css,ico,appcache,json,mp3}']
                 }]
             },
             html: {
                 options: {
                     replaceSrc: ['web/*.{js,appcache}', 'web/**/*.html']
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
