'use strict';

module.exports = function(grunt) {

	// Project configuration.
	grunt.initConfig({

		// Load grunt project configuration
		pkg: grunt.file.readJSON('package.json'),

		// Configure less CSS compiler
		less: {
			build: {
				options: {
					ieCompat: true
				},
				files: {
					'assets/css/admin.css': 'assets/css/less/admin.less',
				}
			},
		},

		// Configure JSHint
		jshint: {
			test: {
				src: 'assets/js/src/**/*.js'
			}
		},

		// Concatenate scripts
		concat: {
			build: {
				files: {
					'assets/js/admin.js': [
						'assets/js/src/menu-item-prices.js',
						'assets/js/src/menu-organizer.js',
					],
				}
			}
		},

		// Watch for changes on some files and auto-compile them
		watch: {
			less: {
				files: 'assets/css/less/**/*.less',
				tasks: ['less'],
			},
			js: {
				files: 'assets/js/src/**/*.js',
				tasks: ['jshint', 'concat'],
			}
		},

		// Create a .pot file
		makepot: {
			target: {
				options: {
					domainPath: 'languages',
					potHeaders: {
	                    poedit: true,
	                    'x-poedit-keywordslist': true
	                },
					processPot: function( pot, options ) {
						pot.headers['report-msgid-bugs-to'] = 'https://themeofthecrop.com';
						return pot;
					},
					type: 'wp-plugin',
				}
			}
		},

		// Build a package for distribution
		compress: {
			main: {
				options: {
					archive: 'food-and-drink-menu-<%= pkg.version %>.zip'
				},
				files: [
					{
						src: [
							'*', '**/*',
							'!food-and-drink-menu-<%= pkg.version %>.zip',
							'!.*', '!Gruntfile.js', '!package.json', '!node_modules', '!node_modules/**/*',
							'!assets/css/less', '!assets/css/less/**/*',
							'!assets/js/src', '!assets/js/src/**/*',
						],
						dest: '/food-and-drink-menu',
					}
				]
			}
		}
	});

	grunt.loadNpmTasks('grunt-contrib-compress');
	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-contrib-jshint');
	grunt.loadNpmTasks('grunt-contrib-less');
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-wp-i18n');

	grunt.registerTask('default', ['watch']);
	grunt.registerTask('build', ['less', 'jshint', 'concat', 'makepot']);
	grunt.registerTask('package', ['build', 'compress']);

};
