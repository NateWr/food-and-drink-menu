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

		// Refresh the menu's HTML code
		api.preview.bind( 'refresh-menu.fdm', function( data ) {

			$.ajax( {
				url: fdm_preview_config.rest_url + 'food-and-drink-menu/1.0/menu',
				method: 'POST',
				beforeSend: function( xhr ) {
					xhr.setRequestHeader( 'X-WP-Nonce', fdm_preview_config.nonce );
				},
				data: data
			} )
				.done( function( r ) {
					var $menu;

					if ( r === false ) {
						return;
					}

					$menu = $( r.html );
					$menu.attr( 'data-fdm-menu-preview', r.id );

					$( '[data-fdm-menu-preview="' + r.id + '"]' ).replaceWith( $menu );
				}
			);
		});

	} );

}( wp.customize, jQuery ) );
