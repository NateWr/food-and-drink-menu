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
					'assets/css/customize.css': 'assets/css/less/customize.less',
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
					'assets/js/fdm-customize-control.js': [
						'assets/js/src/customize-secondary-panel.js',
						'assets/js/src/customize-menu-section.js',
						'assets/js/src/customize-menu-group-control.js',
					],
					'assets/js/fdm-customize-preview.js': [
						'assets/js/src/customize-preview.js',
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
		}
	});

	// Load tasks
	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-contrib-jshint');
	grunt.loadNpmTasks('grunt-contrib-less');
	grunt.loadNpmTasks('grunt-contrib-watch');

	// Default task(s).
	grunt.registerTask('default', ['watch']);

};
