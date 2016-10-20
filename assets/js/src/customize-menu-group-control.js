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
		 * Object of menu posts and their edited values
		 *
		 * @since 1.5
		 */
		edited_posts: null,

		/**
		 * Load and render the control settings
		 *
		 * @abstract
		 * @since 1.5
		 */
		ready: function() {
			var control = this;

			control.group_number = this.id.replace( /^\D+/g, '');
			control.edited_posts = {};

			_.bindAll( control, 'onPageRefresh', 'updateSetting', 'sortingComplete', 'getSection', 'updatePreview' );
			api.previewer.bind( 'previewer-reset.fdm', control.onPageRefresh );
			control.container.on( 'menu-section-updated.fdm', control.updateSetting );
		},

		/**
		 * Store and clear the menu section details when a page is changed
		 *
		 * @since 1.5
		 */
		onPageRefresh: function( data ) {
			var group;

			var control = this;

			control.reset();

			if ( typeof data == 'undefined' || data === null ) {
				control.post_id = 0;
				return;
			}

			control.post_id = data.ID;

			group = data.groups[control.group_number];
			control.menu_sections = [];
			for ( var i in group ) {
				control.menu_sections.push(
					new api.fdm.MenuSectionView({
						id: group[i].id,
						title: group[i].title,
						description: group[i].description,
						collection: new Backbone.Collection( group[i].items ),
						control: control,
					})
				);
			}

			control.renderSections();

			control.updatePreview();
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
		},

		/**
		 * Render the section views
		 *
		 * @since 1.5
		 */
		renderSections: function() {
			var list = $( '.fdm-section-list', this.selector ).empty();

			_.each( this.menu_sections, function( section_view ) {
				list.append( section_view.render().el );
			} );

			list.sortable( {
				placeholder: 'fdm-section-list-placeholder',
				delay: '150',
				handle: '.header',
				update: this.sortingComplete,
			} );
		},

		/**
		 * Update the setting value
		 *
		 * Store the setting when saving or during full page reloads of the
		 * preview.
		 *
		 * @since 1.5
		 */
		updateSetting: function() {
			var control = this;


			if ( typeof this.edited_posts[control.post_id] === 'undefined' ) {
				this.edited_posts[control.post_id] = {
					id: this.post_id,
					group: this.id,
				};
			}

			this.edited_posts[control.post_id].sections = this.generateCurrentSetting();

			this.setting( [] ); // Clear it to ensure the change gets noticed
			this.setting( this.edited_posts );
			this.updatePreview();
		},

		/**
		 * Compile the current setting values
		 *
		 * Loops through the group details to compile setting values to be saved
		 * for the current post.
		 *
		 * @since 1.5
		 */
		generateCurrentSetting: function() {
			var setting = [];

			for ( var i in this.menu_sections ) {
				setting.push( {
					'id': this.menu_sections[i].id,
					'title': this.menu_sections[i].title,
					'description': this.menu_sections[i].description,
				} );
			}

			return setting;
		},

		/**
		 * Triggered when a menu section has been resorted via drag-and-drop
		 *
		 * @since 1.5
		 */
		sortingComplete: function( event, ui ) {
			var menu_sections = [],
				control = this;
			$( '.fdm-section-list > li', this.selector ).each( function() {
				menu_sections.push( control.getSection( $( this ).attr('id' ) ) );
			} );
			this.menu_sections = menu_sections;
			this.updateSetting();
		},

		/**
		 * Retrieve a section by it's ID
		 *
		 * @since 1.5
		 */
		getSection: function( id ) {
			return _.find( this.menu_sections, function( section ) { return section.id === id; } );
		},

		/**
		 * Send an event to the preview to refresh the menu view
		 *
		 * @since 1.5
		 */
		updatePreview: function() {
			var settings = wp.customize.get(),
				data = {};
			for ( var i in settings ) {
				if ( i.substring(0, 15 ) == 'fdm-menu-column' && typeof settings[i][this.post_id] !== 'undefined' ) {
					data[i] = settings[i][this.post_id];
				}
			}
			data.id = this.post_id;
			wp.customize.previewer.send( 'refresh-menu.fdm', data );
		}
	});

	// Register this control with the fdm_menu_group control type
	api.controlConstructor.fdm_menu_group = api.fdm.MenuGroupControl;

}( wp.customize, jQuery ) );
