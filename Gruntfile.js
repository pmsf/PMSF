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
                    'static/dist/css/mobile.built.css': 'static/sass/mobile.scss',
                    'static/dist/css/statistics.built.css': 'static/css/statistics.css',
                    'static/dist/css/status.built.css': 'static/sass/status.scss'
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
            dev: {
                files: {
                    'static/dist/js/app.min.js': 'static/js/app.js',
                    'static/dist/js/map.min.js': 'static/js/map.js',
                    'static/dist/js/map.common.min.js': 'static/js/map.common.js',
                    'static/dist/js/mobile.min.js': 'static/js/mobile.js',
                    'static/dist/js/stats.min.js': 'static/js/stats.js',
                    'static/dist/js/statistics.min.js': 'static/js/statistics.js',
                    'static/dist/js/status.min.js': 'static/js/status.js',
                    'static/dist/js/serviceWorker.min.js': 'static/js/serviceWorker.js'
                }
            }
        },
        obfuscator: {
            options: {
                compact: true,
                controlFlowFlattening: true,
                controlFlowFlatteningThreshold: 1,
                deadCodeInjection: false,
                deadCodeInjectionThreshold: 0.2,
                debugProtection: true,
                debugProtectionInterval: true,
                disableConsoleOutput: true,
                identifierNamesGenerator: 'mangled',
                log: false,
                renameGlobals: false,
                rotateStringArray: true,
                selfDefending: true,
                stringArray: true,
                stringArrayEncoding: 'rc4',
                stringArrayThreshold: 1,
                transformObjectKeys: true,
                unicodeEscapeSequence: false
            },
            prod: {
                files: {
                    'static/dist/js/app.min.js' :'static/js/app.js',
                    'static/dist/js/map.min.js':'static/js/map.js',
                    'static/dist/js/map.common.min.js' : 'static/js/map.common.js',
                    'static/dist/js/mobile.min.js':'static/js/mobile.js',
                    'static/dist/js/stats.min.js':'static/js/stats.js',
                    'static/dist/js/statistics.min.js':'static/js/statistics.js',
                    'static/dist/js/status.min.js':'static/js/status.js',
                    'static/dist/js/serviceWorker.min.js':'static/js/serviceWorker.js'
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
                    'static/dist/data/searchmarkerstyle.min.json': 'static/data/searchmarkerstyle.json',
                    'static/dist/data/weather.min.json': 'static/data/weather.json',
                    'static/dist/locales/de.min.json': 'static/locales/de.json',
                    'static/dist/locales/fr.min.json': 'static/locales/fr.json',
                    'static/dist/locales/it.min.json': 'static/locales/it.json',
                    'static/dist/locales/jp.min.json': 'static/locales/jp.json',
                    'static/dist/locales/ko.min.json': 'static/locales/ko.json',
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
                    'static/dist/css/mobile.min.css': 'static/dist/css/mobile.built.css',
                    'static/dist/css/statistics.min.css': 'static/dist/css/statistics.built.css',
                    'static/dist/css/status.min.css': 'static/dist/css/status.built.css'
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
                    'index.php': 'pre-index.php'
                }
            }
        },
        cacheBust: {
            options: {
                assets: ['static/dist/**/*.css', 'static/dist/**/*.json', 'static/dist/**/*.js', '!static/dist/**/*built*']
            },
            taskName: {
                files: [{
                    src: ['index.php']
                }]
            }
        }
    });

    grunt.registerTask('js-build', ['newer:obfuscator']);
    grunt.registerTask('js-dev', ['babel:dev']);
    grunt.registerTask('css-build', ['newer:sass', 'newer:cssmin']);
    grunt.registerTask('js-lint', ['newer:eslint']);
    grunt.registerTask('json', ['newer:minjson']);
    grunt.registerTask('php-lint', ['newer:phplint']);
    grunt.registerTask('html-build', ['htmlmin', 'cacheBust']);

    grunt.registerTask('build', ['clean', 'js-build', 'css-build', 'json', 'html-build']);
    grunt.registerTask('dev', ['clean', 'js-dev', 'css-build', 'json', 'html-build']);
    grunt.registerTask('lint', ['js-lint', 'php-lint']);
    grunt.registerTask('default', ['build', 'watch']);

};
