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
