module.exports = function (grunt) {
    const sass = require('node-sass');

    // load plugins as needed instead of up front
    require('jit-grunt')(grunt);
    require('phplint').gruntPlugin(grunt);

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        sass: {
            options: {
                implementation: sass
            },
            dist: {
                files: {
                    'static/dist/css/app.built.css': 'static/sass/main.scss',
                    'static/dist/css/mobile.built.css': 'static/sass/mobile.scss'
                }
            }
        },
        eslint: {
            src: ['static/js/*.js', '!js/vendor/**/*.js']
        },
        babel: {
            options: {
                sourceMap: true,
                presets: ['env']
            },
            prod: {
                files: {
                    'static/dist/js/app.built.js': 'static/js/app.js',
                    'static/dist/js/map.built.js': 'static/js/map.js',
                    'static/dist/js/map.common.built.js': 'static/js/map.common.js',
                    'static/dist/js/mobile.built.js': 'static/js/mobile.js',
                    'static/dist/js/stats.built.js': 'static/js/stats.js',
                    'static/dist/js/serviceWorker.built.js': 'static/js/serviceWorker.js'
                }
            },
            dev: {
                files: {
                    'static/dist/js/app.min.js': 'static/js/app.js',
                    'static/dist/js/map.min.js': 'static/js/map.js',
                    'static/dist/js/map.common.min.js': 'static/js/map.common.js',
                    'static/dist/js/mobile.min.js': 'static/js/mobile.js',
                    'static/dist/js/stats.min.js': 'static/js/stats.js',
                    'static/dist/js/serviceWorker.min.js': 'static/js/serviceWorker.js'
                }
            }
        },
        javascript_obfuscator: {
            options: {
                compact: true,
                controlFlowFlattening: false,
                controlFlowFlatteningThreshold: 1,
                deadCodeInjection: false,
                deadCodeInjectionThreshold: 0.2,
                debugProtection: false,
                debugProtectionInterval: false,
                disableConsoleOutput: false,
                domainLock: [],
                identifierNamesGenerator: 'hexadecimal',
                identifiersPrefix: '',
                inputFileName: '',
                log: false,
                renameGlobals: false,
                reservedNames: [],
                reservedStrings: [],
                rotateStringArray: true,
                seed: 0,
                selfDefending: true,
                sourceMap: false,
                sourceMapBaseUrl: '',
                sourceMapFileName: '',
                sourceMapMode: 'separate',
                splitStrings: false,
                splitStringsChunkLength: 10,
                stringArray: true,
                stringArrayEncoding: 'base64',
                stringArrayThreshold: 0.75,
                target: 'browser',
                transformObjectKeys: true,
                unicodeEscapeSequence: false
            },
            obfuscator: {
                files: {
                    'static/dist/js/app.min.js' :'static/js/app.js',
                    'static/dist/js/map.min.js':'static/js/map.js',
                    'static/dist/js/map.common.min.js' : 'static/js/map.common.js',
                    'static/dist/js/mobile.min.js':'static/js/mobile.js',
                    'static/dist/js/stats.min.js':'static/js/stats.js',
                    'static/dist/js/serviceWorker.min.js':'static/js/serviceWorker.js'
                }
            }
        },
        uglify: {
            options: {
                banner: '/*\n <%= pkg.name %> <%= grunt.template.today("yyyy-mm-dd") %> \n*/\n',
                sourceMap: true,
                compress: {
                    unused: false
                }
            },
            prod: {
                files: {
                    'static/dist/js/app.min.js': 'static/dist/js/app.built.js',
                    'static/dist/js/map.min.js': 'static/dist/js/map.built.js',
                    'static/dist/js/map.common.min.js': 'static/dist/js/map.common.built.js',
                    'static/dist/js/mobile.min.js': 'static/dist/js/mobile.built.js',
                    'static/dist/js/stats.min.js': 'static/dist/js/stats.built.js',
                    'static/dist/js/serviceWorker.min.js': 'static/dist/js/serviceWorker.built.js'
                }
            }
        },
        minjson: {
            build: {
                files: {
                    'static/dist/data/cries.min.json': 'static/data/cries.json',
                    'static/dist/data/pokemon.min.json': 'static/data/pokemon.json',
                    'static/dist/data/moves.min.json': 'static/data/moves.json',
                    'static/dist/data/mapstyle.min.json': 'static/data/mapstyle.json',
                    'static/dist/data/questtype.min.json': 'static/data/questtype.json',
                    'static/dist/data/rewardtype.min.json': 'static/data/rewardtype.json',
                    'static/dist/data/conditiontype.min.json': 'static/data/conditiontype.json',
                    'static/dist/data/items.min.json': 'static/data/items.json',
                    'static/dist/data/grunttype.min.json': 'static/data/grunttype.json',
                    'static/dist/data/searchmarkerstyle.min.json': 'static/data/searchmarkerstyle.json',
                    'static/dist/data/weather.min.json': 'static/data/weather.json',
                    'static/dist/locales/de.min.json': 'static/locales/de.json',
                    'static/dist/locales/fr.min.json': 'static/locales/fr.json',
                    'static/dist/locales/it.min.json': 'static/locales/it.json',
                    'static/dist/locales/jp.min.json': 'static/locales/jp.json',
                    'static/dist/locales/ko.min.json': 'static/locales/ko.json',
                    'static/dist/locales/pl.min.json': 'static/locales/pl.json',
                    'static/dist/locales/pt_br.min.json': 'static/locales/pt_br.json',
                    'static/dist/locales/ru.min.json': 'static/locales/ru.json',
                    'static/dist/locales/sp.min.json': 'static/locales/sp.json',
                    'static/dist/locales/zh_cn.min.json': 'static/locales/zh_cn.json',
                    'static/dist/locales/zh_tw.min.json': 'static/locales/zh_tw.json',
                    'static/dist/locales/zh_hk.min.json': 'static/locales/zh_hk.json'
                }
            }
        },
        clean: {
            build: {
                src: 'static/dist'
            }
        },
        watch: {
            options: {
                interval: 1000,
                spawn: true
            },
            js: {
                files: ['static/js/**/*.js'],
                options: {livereload: true},
                tasks: ['js-lint', 'js-build']
            },
            json: {
                files: ['static/data/*.json', 'static/locales/*.json'],
                options: {livereload: true},
                tasks: ['json']
            },
            css: {
                files: '**/*.scss',
                options: {livereload: true},
                tasks: ['css-build']
            }
        },
        cssmin: {
            options: {
                banner: '/*\n <%= pkg.name %> <%= grunt.template.today("yyyy-mm-dd") %> \n*/\n'
            },
            build: {
                files: {
                    'static/dist/css/app.min.css': 'static/dist/css/app.built.css',
                    'static/dist/css/mobile.min.css': 'static/dist/css/mobile.built.css'
                }
            }
        },
        phplint: {
            files: ['**.php', '**/*.php', '!node_modules/**']
        },
        htmlmin: {
            dist: {
                options: {
                    removeComments: true,
                    collapseWhitespace: true
                },
                files: {
                    'index.php': 'pre-index.php',
                    'user.php': 'pre-user.php'
                }
            }
        },
        cacheBust: {
            options: {
                assets: ['static/dist/**/*.css', 'static/dist/**/*.json', 'static/dist/**/*.js', '!static/dist/**/*built*']
            },
            taskName: {
                files: [{
                    src: ['index.php', 'user.php']
                }]
            }
        }
    });

    grunt.registerTask('js-build', ['babel:prod', 'newer:uglify']);
    grunt.registerTask('js-dev', ['babel:dev']);
    grunt.registerTask('js-obfuscator', ['newer:javascript_obfuscator']);
    grunt.registerTask('css-build', ['newer:sass', 'newer:cssmin']);
    grunt.registerTask('js-lint', ['newer:eslint']);
    grunt.registerTask('json', ['newer:minjson']);
    grunt.registerTask('php-lint', ['newer:phplint']);
    grunt.registerTask('html-build', ['htmlmin', 'cacheBust']);

    grunt.registerTask('build', ['clean', 'js-build', 'css-build', 'json', 'html-build']);
    grunt.registerTask('dev', ['clean', 'js-dev', 'css-build', 'json', 'html-build']);
    grunt.registerTask('obfuscate', ['clean', 'js-obfuscator', 'css-build', 'json', 'html-build']);
    grunt.registerTask('lint', ['js-lint', 'php-lint']);
    grunt.registerTask('default', ['build', 'watch']);

};
