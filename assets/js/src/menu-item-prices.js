/**
 * Allow prices to be added and deleted on the Menu Item editing page and
 * list table
 *
 *  @since 1.5
 */
jQuery( function ( $ ) {
	var $prices = $( '.fdm-input-prices' );

	if ( !$prices.length ) {
		return;
	}

	// Open the price editing panel in the menu item list table
	$( '.fdm-item-list-price' ).click( function( e ) {
		var $target = $( e.target );

		if ( $target.hasClass( 'fdm-item-price-edit' ) ) {
			$(this).addClass( 'is-visible' );
			return false;
		}
	} );

	// Re-usable function to remove a price entry field
	function removePrice( e ) {
		priceChanged( e );
		$( this ).closest( '.fdm-input-control' ).remove();
		return false;
	}

	// Re-usable function to signal when prices have changed.
	// Only used on menu items list table.
	function priceChanged( e ) {
		var $form = $( e.target ).closest( '.fdm-item-price-form' );

		if ( !$form.length ) {
			return;
		}

		$( '.fdm-item-price-save', $form ).removeAttr( 'disabled' );
	}

	$prices.click( function( e ) {
		var $target = $( e.target ),
			$price_panel = $(this),
			$price_input = $price_panel.find( '.fdm-input-control' ).last(),
			$new_price_input = $price_input.clone();

		if ( !$target.hasClass( 'fdm-price-add' ) ) {
			return;
		}

		$new_price_input.find( 'input[data-name="fdm_item_price"], input[name="fdm_item_price[]"]' ).val( '' );
		$price_input.after( $new_price_input );
		$new_price_input.find( 'input' ).focus();

		$( '.fdm-input-delete', $price_panel ).off()
			.click( removePrice );
		$( 'input[data-name="fdm_item_price"], input[name="fdm_item_price[]"]', $price_panel ).off()
			.keyup( priceChanged );

		return false;
	} );

	// Remove a price entry field
	$( '.fdm-input-delete', $prices ).click( removePrice );

	// Enable the update price button on the menu item list whenever a price
	// has changed.
	$( 'input[data-name="fdm_item_price"], input[name="fdm_item_price[]"]', $prices ).keyup( priceChanged );

	// Save price changes (only on menu item list table)
	var $submit = $( '.fdm-item-price-save' );
	if ( $submit.length ) {
		$submit.click( function( e ) {
			var $button = $(this),
				$spinner = $button.siblings( '.spinner' ),
				$price_wrapper = $button.closest( '.fdm-item-list-price'),
				$price_summary = $price_wrapper.find( '.fdm-item-price-summary' ),
				menu_item_id = $price_wrapper.data( 'menu-item-id' ),
				$price_inputs = $price_wrapper.find( 'input[data-name="fdm_item_price"], input[name="fdm_item_price[]"]' ),
				prices = [],
				$message = $price_wrapper.find( '.fdm-item-price-message' ),
				params;

			if ( !menu_item_id ) {
				return false;
			}

			$button.attr( 'disabled', 'disabled' );
			$spinner.css( 'visibility', 'visible' );
			$message.empty();

			$price_inputs.each( function() {
				prices.push( $(this).val() );
			} );

			params = {
				id: menu_item_id,
				prices: prices,
				action: 'fdm-menu-item-price',
				nonce: fdmSettings.nonce,
			};

			// Allow third-party addons to hook in and add data
			$price_wrapper.trigger( 'save-item-price.fdm', params );

			$.post(
				ajaxurl,
				params,
				function( r ) {

					$button.removeAttr( 'disabled' );
					$spinner.css( 'visibility', 'hidden' );

					if ( typeof r === 'undefined' || typeof r.success === 'undefined' ) {
						$message.html( fdmSettings.i18n.undefined_error );
						return;
					}

					if ( r.success === false ) {
						if ( typeof r.data === 'undefined' || typeof r.data.msg === 'undefined' ) {
							$message.html( fdmSettings.i18n.undefined_error );
						} else {
							$message.html( r.data.msg );
						}
						return;
					}

					if ( typeof r.data.price_summary !== 'undefined' ) {
						$price_summary.html( r.data.price_summary );
					}
					$price_wrapper.removeClass( 'is-visible' );
				}
			);

			return false;
		} );
	}

} );
