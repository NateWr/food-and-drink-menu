'use strict';

module.exports = function(grunt) {

	// Project configuration.
	grunt.initConfig({

		// Load grunt project configuration
		pkg: grunt.file.readJSON('package.json'),

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
					'assets/js/customize.js': [
						'assets/js/src/customize-menu-group-control.js',
					],
				}
			}
		},

		// Watch for changes on some files and auto-compile them
		watch: {
			js: {
				files: 'assets/js/src/**/*.js',
				tasks: ['jshint', 'concat'],
			}
		}
	});

	// Load tasks
	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-contrib-jshint');
	grunt.loadNpmTasks('grunt-contrib-watch');

	// Default task(s).
	grunt.registerTask('default', ['watch']);

};
