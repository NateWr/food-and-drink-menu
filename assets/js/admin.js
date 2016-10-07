/**
 * Allow prices to be added and deleted on the Menu Item editing page
 *
 *  @since 1.5
 */
jQuery( function ( $ ) {
	var $prices = $( '#fdm-input-prices' );

	if ( !$prices.length ) {
	   return;
	}

	// Re-usable function to remove a price entry field
	function removePrice() {
		$( this ).closest( '.fdm-input-control' ).remove();
		return false;
	}

	// Add a price entry field
	$( '#fdm-price-add', $prices ).click( function( e ) {
		var $price_input = $prices.find( '.fdm-input-control' ).last(),
			$new_price_input = $price_input.clone();

		$new_price_input.find( 'input[name="fdm_item_price[]"]' ).val( '' );

		$price_input.after( $new_price_input );

		// Reset click handlers
		$( '.fdm-input-delete', $prices ).off()
			.click( removePrice );

		return false;
	} );

	// Remove a price entry field
	$( '.fdm-input-delete', $prices ).click( removePrice );

} );

/**
 * Handle the Menu Organizer on the Menu editing page
 *
 *  @since 1.5
 */
jQuery( function ( $ ) {

	/**
	 * Add a menu section in the menu organizer
	 * @since 1.0
	 */
	$('#fdm-menu-organizer a').click(function() {

		// Add
		if ($(this).parent().parent().parent().hasClass('fdm-options')) {
			if ($(this).parent().parent().parent().siblings('.fdm-added').children('ul')) {
				$(this).parent().parent().parent().siblings('.fdm-added').children('ul').append($(this).parent());

				// Remove menu section from the other selection panel
				var term_id = $(this).data('termid');
				$('#fdm-menu-organizer .fdm-options ul li a').each(function() {
					if ($(this).data('termid') == term_id) {
						$(this).parent().remove();
					}
				});
			}

		// Remove
		} else if ($(this).parent().parent().parent().hasClass('fdm-added')) {
			if ($('#fdm-menu-organizer .fdm-options ul')) {
				$('#fdm-menu-organizer .fdm-options ul').append($(this).parent());
			}
		}

		// Update hidden fields with column content values
		var column_one = '';
		$('#fdm-menu-column-one .fdm-added ul li a').each(function() {
			column_one = column_one + $(this).data('termid') + ',';
		});
		$('#fdm_menu_column_one').val(column_one);
		var column_two = '';
		$('#fdm-menu-column-two .fdm-added ul li a').each(function() {
			column_two = column_two + $(this).data('termid') + ',';
		});
		$('#fdm_menu_column_two').val(column_two);

		return false;

	});

	/**
	 * Load appropriate menu sections when the menu organizer loads
	 * @since 1.0
	 */
	if ($('#fdm-menu-organizer').length !== 0) {
		var ids = [$('#fdm_menu_column_one').val(), $('#fdm_menu_column_two').val()];
		for (var i = 0; i < ids.length; i++) {
			if (ids[i] !== '') {
				ids[i] = ids[i].split(',');
				ids[i].pop();
				var target = 'one';
				if (i > 0) {
					target = 'two';
				}
				for (var t = 0; t < ids[i].length; t++) {
					$( '#fdm-menu-column-' + target + ' .fdm-options ul li a[data-termid="' + ids[i][t] + '"]' ).trigger( 'click' );
				}
			}
		}
	}
} );
