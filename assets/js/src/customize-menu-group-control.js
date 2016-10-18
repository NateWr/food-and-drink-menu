/* global wp, jQuery */

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
		 * Group number this control handles
		 *
		 * @since 1.5
		 */
		group_number: 0,

		/**
		 * Reference to Menu Section views in this control
		 *
		 * @since 1.5
		 */
		menu_sections: null,

		/**
		 * Current post_id being controlled
		 *
		 * @since 1.5
		 */
		post_id: 0,

		/**
		 * Object hash of post setting values
		 *
		 * @since 1.5
		 */
		edited_posts: {},

		/**
		 * Load and render the control settings
		 *
		 * @abstract
		 * @since 1.5
		 */
		ready: function() {
			var control = this;

			control.group_number = this.id.replace( /^\D+/g, '');

			_.bindAll( control, 'onPageRefresh' );
			api.previewer.bind( 'previewer-reset.fdm', control.onPageRefresh );
		},

		/**
		 * Store and clear the menu section details when a page is changed
		 *
		 * @since 1.5
		 */
		onPageRefresh: function( data ) {
			var list = $( '.fdm-section-list', this.selector ).empty(),
				group;

			this.reset();

			if ( typeof data == 'undefined' || data === null ) {
				this.post_id = 0;
				return;
			}

			var control = this;

			this.post_id = data.ID;

			group = data.groups[control.group_number];
			control.menu_sections = [];
			for ( var section_id in group ) {
				control.menu_sections[section_id] = new api.fdm.MenuSectionView({
					id: section_id,
					title: group[section_id].title,
					description: group[section_id].description,
					collection: new Backbone.Collection( group[section_id].items ),
				});
				list.append( control.menu_sections[section_id].render().el );
			}
		},

		/**
		 * Reset sections and destroy associated views
		 *
		 * @since 1.5
		 */
		reset: function() {

			if ( typeof this.menu_sections == 'undefined' || this.menu_sections === null ) {
				return;
			}

			for ( var key in this.menu_sections ) {
				this.menu_sections[key].remove();
			}
		}
	});

	// Register this control with the fdm_menu_group control type
	api.controlConstructor.fdm_menu_group = api.fdm.MenuGroupControl;

}( wp.customize, jQuery ) );
