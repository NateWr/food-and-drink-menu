/* global wp, jQuery */

/**
 * Initialize the customizer preview
 *
 * @since 1.5
 */
(function( api, $ ) {

	api.fdm = api.fdm || {};

	/**
	 * Namespace for handling functionality in the preview pane
	 *
	 * @since 1.5
	 */
	api.fdm.preview = {};

	/**
	 * An object defining the menu that is currently being previewed.
	 *
	 * @since 1.5
	 */
	api.fdm.preview.current = {};

	/**
	 * Set or update the menu that is currently being previewed.
	 *
	 * @since 1.5
	 */
	api.fdm.preview.setCurrent = function() {
		if ( typeof fdm_previewed_item === 'undefined' ) {
			api.fdm.preview.current = null;
		} else {
			api.fdm.preview.current = fdm_previewed_item;
		}
	};

	/**
	 * Broadcast the details of the current menu being previewed.
	 *
	 * @since 1.5
	 */
	api.fdm.preview.sendCurrent = function() {
		api.fdm.preview.setCurrent();
		api.preview.send( 'previewer-reset.fdm', api.fdm.preview.current );
	};

	/**
	 * Bind to events which occur in the preview pane.
	 *
	 * @since 1.5
	 */
	$( function() {

		// Send updated menu data to the control pane
		api.preview.bind( 'active', function() {
			api.fdm.preview.sendCurrent();
		});

	} );

}( wp.customize, jQuery ) );
