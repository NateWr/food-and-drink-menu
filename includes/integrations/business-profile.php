<?php defined( 'ABSPATH' ) || die;
/**
 * Functions to integrate with the Business Profile plugin
 *
 * @see https://wordpress.org/plugins/business-profile/
 * @since 1.5
 */

/**
 * Add an option to select a menu in Business Profile settings
 *
 * @param sapLibrary $sap Simple Admin Pages library instance
 * @since 1.5
 */
function fdm_bp_add_menu_setting( $sap ) {

	$sap->add_setting(
		'bpfwp-settings',
		'bpfwp-contact',
		'post',
		array(
			'id'           => 'menu',
			'title'        => __( 'Menu', 'business-profile' ),
			'description'  => __( 'Select your main restaurant menu. Google may display this in your restaurant listing.', 'business-profile' ),
			'blank_option' => true,
			'args'         => array(
				'post_type'      => FDM_MENU_POST_TYPE,
				'posts_per_page' => -1,
				'post_status'    => 'publish',
			),
		)
	);

	return $sap;
}
add_filter( 'bpfwp_settings_page', 'fdm_bp_add_menu_setting' );

/**
 * Register the menu component in the Business Profile contact card
 *
 * @param array $components List component callback functions to print details
 * @since 1.5
 */
function fdm_bp_add_menu_callback( $components ) {
	$components['menu'] = 'fdm_bp_print_menu_schema';
	return $components;
}
add_filter( 'bpwfwp_component_callbacks', 'fdm_bp_add_menu_callback' );

/**
 * Print the menu schema details in the Business Profile contact card
 *
 * @param int $location A post ID if this is for a specific location
 * @since 1.5
 */
function fdm_bp_print_menu_schema( $location = false ) {

	$menu = bpfwp_setting( 'menu', $location );
	if ( empty( $menu ) ) {
		return;
	}

	?>

	<meta itemprop="menu" itemtype="http://schema.org/menu" content="<?php echo esc_url( get_permalink( $menu ) ); ?>">

	<?php
}
