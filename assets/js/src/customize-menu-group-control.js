/* global wp, jQuery */
/* exported fdm */

/**
 * Defines the menu group customizer control
 *
 * @since 1.5
 */
(function( api, $ ) {

	api.fdm = api.fdm || {};

	/**
	 * Menu Group (column) customizer control
	 *
	 * @class
	 * @augments wp.customize.Control
	 * @augments wp.customize.Class
	 */
	api.fdm.MenuGroupControl = wp.customize.Control.extend({
		/**
		 * Current post_id being controlled
		 *
		 * @since 0.1
		 */
		post_id: 0,

		/**
		 * Object hash of post setting values
		 *
		 * @since 0.1
		 */
		edited_posts: {},

		/**
		 * Load and render the control settings
		 *
		 * @abstract
		 * @since 0.1
		 */
		ready: function() {
			var control = this;
		},
	});

	// Register this control with the fdm_menu_group control type
	api.controlConstructor.fdm_menu_group = api.fdm.MenuGroupControl;

}( wp.customize, jQuery ) );
