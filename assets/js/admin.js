/**
 * Javascript functions for the admin interface for Food and Drink Menu
 *
 * @package Food and Drink Menu
 */

/*
 * When the page loads
 */
jQuery(document).ready(function ($) {

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

});


/**
 * Update map in Menu Item source preview
 * @since 1.0
 */
function fdm_update_source_map() {

	// Hide the map panel if no source is input
	if ( jQuery('#fdm_item_source_address').val() === '' ) {
		jQuery('.fdm-source-map').hide();

	// Show the map panel and update map
	} else {
		jQuery('.fdm-source-map').empty();
		jQuery('.fdm-source-map').fadeIn('fast');
		jQuery('.fdm-source-map').append('<img src="//maps.google.com/maps/api/staticmap?markers=size:normal|color:blue|' + encodeURI(jQuery('#fdm_item_source_address').val()) + '&amp;zoom=' + encodeURI(jQuery('#fdm_item_source_zoom').val()) + '&amp;size=300x300&amp;scale=2&amp;sensor=false&amp;visual_refresh=true">');
	}
}