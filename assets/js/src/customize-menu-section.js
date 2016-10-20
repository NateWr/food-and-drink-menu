/* global wp, jQuery */

/**
 * Menu Section control models, views and collections
 *
 * @since 1.5
 */
(function( api, $ ) {

	api.fdm = api.fdm || {};

	/**
	 * View: Menu Section
	 *
	 * This view renders a MenuSectionCollection for modifying the title/desc
	 * and adding, removing and re-ordering menu items.
	 *
	 * @since 1.5
	 */
	api.fdm.MenuSectionView = wp.Backbone.View.extend({

		template: wp.template( 'fdm-menu-section' ),

		tagName: 'li',

		className: 'fdm-customize-section',

		events: {
			'click .fdm-toggle-component-form': 'toggleForm',
			'blur [data-fdm-setting-link]': 'updateSection',
			'click .fdm-remove-menu-section': 'removeSection',
		},

		initialize: function( options ) {
			// Store reference to control
			_.extend( this, _.pick( options, 'control', 'title', 'description' ) );

			this.listenTo(this.collection, 'add remove reset', this.render);
		},

		render: function() {
			wp.Backbone.View.prototype.render.apply( this );

			var list = this.$el.find( '.fdm-menu-item-list' ).empty();
			this.collectionViews = [];
			this.collection.each( function( model ) {
				this.collectionViews[model.get('id')] = new api.fdm.MenuSectionItemView( { model: model, control: this.control } );
				list.append( this.collectionViews[model.get('id')].render().el );
			}, this );

			return this;
		},

		/**
		 * Remove collection views when this view is removed
		 *
		 * @since 1.5
		 */
		remove: function() {
			wp.Backbone.View.prototype.remove.apply( this );
			for( var key in this.collectionViews ) {
				this.collectionViews[key].remove();
			}
		},

		/**
		 * Open and close the section form
		 *
		 * @since 1.5
		 */
		toggleForm: function() {
			$( '.control, .footer', this.$el ).toggleClass( 'is-open' );
		},

		/**
		 * Update menu section setting in the parent control whenever the
		 * title or description has changed
		 *
		 * @since 1.5
		 */
		updateSection: function( event ) {
			var target = $( event.target ),
				setting = target.data( 'fdm-setting-link' ),
				val = target.val(),
				atts = {};

			if ( this[setting] === val ) {
				return;
			}

			this[setting] = val;

			this.control.container.trigger( 'menu-section-updated.fdm' );
		},

		removeSection: function() {
			console.log('remove menu section', this);
		}
	});

	/**
	 * View: Summary of Menu Item when it appears in the Menu Section View
	 *
	 * @since 1.5
	 */
	api.fdm.MenuSectionItemView = wp.Backbone.View.extend({

		template: wp.template( 'fdm-menu-section-item' ),

		tagName: 'li',

		events: {
			'click .fdm-remove-menu-item': 'removeItem',
		},

		initialize: function( options ) {
			// Store reference to control
			_.extend( this, _.pick( options, 'control' ) );

			this.listenTo(this.collection, 'add remove reset', this.render);
		},

		removeItem: function() {
			console.log('remove menu item', this);
		}
	});

}( wp.customize, jQuery ) );
