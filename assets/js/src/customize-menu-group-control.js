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

			_.bindAll( control, 'onPageRefresh', 'renderSections', 'updateSetting', 'sortingComplete', 'getSection', 'updatePreview' );
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

			if ( typeof data == 'undefined' || data === null ) {
				control.post_id = 0;
				return;
			}

			control.post_id = data.ID;

			// Initialize data for a new menu
			if ( typeof control.edited_posts[control.post_id] === 'undefined' ) {

				control.edited_posts[control.post_id] = {
					id: control.post_id,
					group: control.id,
					sections: [],
				};

				group = data.groups[control.group_number];
				for ( var i in group ) {
					control.edited_posts[control.post_id].sections.push(
						new api.fdm.MenuSectionView({
							id: group[i].id,
							title: group[i].title,
							description: group[i].description,
							collection: new Backbone.Collection( group[i].items ),
							control: control,
						})
					);
				}
			}

			control.renderSections();
			control.updatePreview();
		},

		/**
		 * Render the section views
		 *
		 * @since 1.5
		 */
		renderSections: function() {
			var control = this,
				list = $( '.fdm-section-list', this.selector ).empty();

			_.each( control.edited_posts[control.post_id].sections, function( section_view ) {
				list.append( section_view.render().el );
			} );

			list.sortable( {
				placeholder: 'fdm-section-list-placeholder',
				delay: '150',
				handle: '.header',
				update: control.sortingComplete,
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
			var control = this,
				settings = wp.customize.get()[this.id] || {};

			if ( typeof settings[control.post_id] === 'undefined' ) {
				settings[control.post_id] = {};
			}

			settings[control.post_id] = {
				id: control.post_id,
				group: control.id,
				sections: control.generateSectionSetting( control.post_id ),
			};

			control.setting( [] ); // Clear it to ensure the change gets noticed
			control.setting( settings );

			control.updatePreview();
		},

		/**
		 * Compile the current section values for saving as a setting
		 *
		 * Loops through the group details to compile setting values to be saved
		 * for the current post.
		 *
		 * @since 1.5
		 */
		generateSectionSetting: function() {
			var control = this,
				setting = [];

			_.each( control.edited_posts[control.post_id].sections, function( section ) {
				setting.push( {
					'id': section.id,
					'title': section.title,
					'description': section.description,
				} );
			} );

			return setting;
		},

		/**
		 * Triggered when a menu section has been resorted via drag-and-drop
		 *
		 * @since 1.5
		 */
		sortingComplete: function( event, ui ) {
			var control = this;

			var sections = [];
			$( '.fdm-section-list > li', control.selector ).each( function( i ) {
				sections.push( control.getSection( $( this ).attr('id' ) ) );
			} );

			control.edited_posts[control.post_id].sections = sections;

			control.updateSetting();
		},

		/**
		 * Retrieve a section by it's ID
		 *
		 * @since 1.5
		 */
		getSection: function( id ) {
			var control = this;
			return _.find( control.edited_posts[control.post_id].sections, function( section ) { return section.id === id; } );
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
