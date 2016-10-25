/**
 * Handle the Menu Organizer on the Menu editing page
 *
 *  @since 1.5
 */

var fdmMenuOrganizer = fdmMenuOrganizer || {};

jQuery( function ( $ ) {

	if ( !$( '#fdm-menu-organizer' ).length ) {
		return;
	}

	/**
	 * Column slugs used by the menu organizer
	 *
	 * @param array
	 * @since 1.5
	 */
	fdmMenuOrganizer.columns = ['fdm_menu_column_one', 'fdm_menu_column_two'];

	/**
	 * Initialize the menu organizer
	 *
	 * @since 1.5
	 */
	fdmMenuOrganizer.init = function() {

		$( '.fdm-sortable-sections' ).sortable( {
			connectWith: '.fdm-sortable-sections',
			placeholder: 'fdm-menu-sections-placeholder',
			delay: 150,
			handle: '.fdm-title',
			update: fdmMenuOrganizer.sectionsUpdated,
		} );

		for ( var i in fdmMenuOrganizer.columns ) {
			var column = fdmMenuOrganizer.columns[i];
			var ids = $( '#' + column ).val();
			if ( ids ) {
				ids = ids.split(',').filter(Boolean);
				for ( var s in ids ) {
					$( '#fdm-menu-sections-list > [data-term-id="' + ids[s] + '"]' )
						.appendTo( '#' + column + '_list' );
				}
			}
		}
	};

	/**
	 * Update the sections values
	 *
	 * @since 1.5
	 */
	fdmMenuOrganizer.sectionsUpdated = function( event, ui ) {

		function getIds( $list ) {
			var ids = [];
			$list.each( function() {
				ids.push( $(this).data( 'term-id' ) );
			} );
			return ids;
		}

		for ( var i in fdmMenuOrganizer.columns ) {
			var column = fdmMenuOrganizer.columns[i];
			$( '#' + column ).val( getIds( $( '#' + column + '_list > li' ) ) );
		}
	};

	fdmMenuOrganizer.init();
} );
