/* global wp, jQuery */

/**
 * Secondary panel for customizer controls
 *
 * @since 1.5
 */
(function( api, $ ) {

	api.fdm = api.fdm || {};

	/**
	 * View: Section for selection in Secondary Panel
	 *
     * List sections
	 *
	 * @since 1.5
	 */
	api.fdm.SecondaryPanelSectionView = wp.Backbone.View.extend( {

		template: wp.template( 'fdm-secondary-panel-section' ),

	} );


}( wp.customize, jQuery ) );
