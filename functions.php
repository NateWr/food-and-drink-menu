<?php
/**
 * Template tags and shortcodes for use with Food and Drink Menu
 */


/**
 * Create a shortcode to display a menu
 * @since 1.0
 */
function fdm_menu_shortcode( $atts ) {

	// Define shortcode attributes
	$menu_atts = array(
		'id' => null,
		'layout' => 'classic',
		'show_title' => false,
		'show_content' => false,
	);

	// Create filter so addons can modify the accepted attributes
	$menu_atts = apply_filters( 'fdm_shortcode_menu_atts', $menu_atts );

	// Extract the shortcode attributes
	$args = shortcode_atts( $menu_atts, $atts );

	// Render menu
	fdm_load_view_files();
	$menu = new fdmViewMenu( $args );

	return $menu->render();
}
add_shortcode( 'fdm-menu', 'fdm_menu_shortcode' );

/**
 * Create a shortcode to display a menu item
 * @since 1.1
 */
function fdm_menu_item_shortcode( $atts ) {

	// Define shortcode attributes
	$menu_item_atts = array(
		'id' => null,
		'layout' => 'classic',
		'singular' => true
	);

	// Create filter so addons can modify the accepted attributes
	$menu_item_atts = apply_filters( 'fdm_shortcode_menu_item_atts', $menu_item_atts );

	// Extract the shortcode attributes
	$args = shortcode_atts( $menu_item_atts, $atts );

	// Render menu
	fdm_load_view_files();
	$menuitem = new fdmViewItem( $args );

	return $menuitem->render();
}
add_shortcode( 'fdm-menu-item', 'fdm_menu_item_shortcode' );

/**
 * Load files needed for views
 * @since 1.1
 * @note Can be filtered to add new classes as needed
 */
function fdm_load_view_files() {

	$files = array(
		FDM_PLUGIN_DIR . '/views/Base.class.php' // This will load all default classes
	);

	$files = apply_filters( 'fdm_load_view_files', $files );

	foreach( $files as $file ) {
		require_once( $file );
	}

}

/*
 * Assign a globally unique id for each displayed menu
 */
$globally_unique_id = 0;
function fdm_global_unique_id() {
	global $globally_unique_id;
	$globally_unique_id++;
	return 'fdm-menu-' . $globally_unique_id;
}

/**
 * Transform an array of CSS classes into an HTML attribute
 * @since 1.0
 */
function fdm_format_classes($classes) {
	if (count($classes)) {
		return ' class="' . join(" ", $classes) . '"';
	}
}
