/* global wp, jQuery */

/**
 * Secondary panel for customizer controls
 *
 * @since 1.5
 */
(function( api, $ ) {

	api.fdm = api.fdm || {};

	/**
	 * View: Secondary Panel
	 *
     * A slide-out panel used to select additional sections and items. Manages
	 * sub-views, displaying them in response to load and close events.
	 *
	 * @since 1.5
	 */
	api.fdm.SecondaryPanelView = wp.Backbone.View.extend( {

		template: wp.template( 'fdm-secondary-panel' ),

		id: 'fdm-secondary-panel',

		events: {
			'click .fdm-close': 'close',
		},

		initialize: function( options ) {
			this.listenTo( this, 'load-secondary-panel.fdm', this.load );
			this.listenTo( this, 'close-secondary-panel.fdm', this.close );
		},

		/**
		 * Load a new panel
		 *
		 * @param view wp.Backbone.view View to load
		 * @since 1.5
		 */
		load: function( view, control ) {

			$( 'body' ).addClass( 'fdm-secondary-open' );

			if ( this.views.first( '.fdm-secondary-content' ) ) {
				this.sendClose();
			}

			this.views.set( '.fdm-secondary-content', view );

			this.views.first( 'fdm-secondary-content' ).$el
				.find( 'label, input, a, button' ).first()
				.focus();

			delete this.control;
			this.control = control;
		},

		/**
		 * Close the panel
		 *
		 * @since 1.5
		 */
		close: function() {
			this.sendClose();
			$( 'body' ).removeClass( 'fdm-secondary-open' );
		},

		/**
		 * Send an event to the attached control
		 *
		 * @param string name Event name to trigger
		 * @param object data Optional data to pass with the event
		 * @since 1.5
		 */
		send: function( name, data ) {
			this.control.container.trigger( name, data );
		},

		/**
		 * Convenience function to send an event to close the panel to the
		 * attached control
		 *
		 * @since 1.5
		 */
		sendClose: function() {
			this.send( 'secondary-panel-closed.fdm', this.views.first( '.fdm-secondary-content' ) );
		}

	} );

	// Initialize the secondary panel
	api.fdm.secondary_panel = new api.fdm.SecondaryPanelView();
	$( '.wp-full-overlay' ).append( api.fdm.secondary_panel.render().el );

}( wp.customize, jQuery ) );
