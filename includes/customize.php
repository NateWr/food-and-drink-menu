<?php if ( !defined( 'ABSPATH' ) ) exit;
/**
 * Functions to add the menu layout manager to the Customizer
 *
 * @since 1.5
 */

/**
 * Initialize the callbacks to turn the Customizer into a "blank slate"
 *
 * This is the first of a series of callbacks which remove all registered
 * panels, sections and controls from the Customizer. This is only done when
 * the customizer is loaded with a special query arg from the Menu editing
 * screen.
 *
 * @see https://github.com/xwp/wp-customizer-blank-slate
 * @param $components Components that have been loaded
 * @since 1.5
 */
function fdm_customize_init_blank_state( $components ) {

	if ( empty( $_GET['fdm_menu'] ) ) {
		return $components;
	}

	// Reset the customize register actions
	add_action( 'wp_loaded', 'fdm_customize_reset_register', 1 );

	// Remove all registered components
	$components = array();
	return $components;
}
add_filter( 'customize_loaded_components', 'fdm_customize_init_blank_state' );

/**
 * Prevent other constructs from being registered and register only those we
 * want registered in our instance of the Customizer
 *
 * @since 1.5
 */
function fdm_customize_reset_register() {

	global $wp_customize;

	// Prevent anything from hooking in to register controls
	remove_all_actions( 'customize_register' );

	// Register just the things we need
	$wp_customize->register_panel_type( 'WP_Customize_Panel' );
	$wp_customize->register_section_type( 'WP_Customize_Section' );

	// Register our Customizer controls
	add_action( 'customize_register', 'fdm_customize_register' );
}

/**
 * Register the Customizer controls
 *
 * @since 1.5
 */
function fdm_customize_register( $wp_customize ) {

	include_once( FDM_PLUGIN_DIR . '/includes/class-customize-menu-group.php' );
	$wp_customize->register_control_type( 'FDM_WP_Customize_Menu_Group' );

	$wp_customize->add_panel(
		'fdm-menu',
		array(
			'title' => _x( 'Food and Drink Menu Layout', 'Name of the Customizer panel for managing the menu layout.', 'food-and-drink-menu' ),
			'description' => __( 'This panel allows you to build your menu layout. Add sections to columns, reorder dishes within the sections and rename the sections as needed.', 'food-and-drink-menu' ),
			'active_callback' => 'fdm_customize_is_menu_post',
		)
	);

	$wp_customize->add_section(
		'fdm-menu-column-1-section',
		array(
			'title' => __( 'First Column', 'food-and-drink-menu' ),
			'panel' => 'fdm-menu',
		)
	);

	$wp_customize->add_setting(
		'fdm-menu-column-1',
		array(
			'sanitize_callback' => '__return_true', // @todo implement sanitization
			'type' => 'fdm-menu-column',
			'transport' => 'postMessage',
		)
	);

	$wp_customize->add_control(
		new FDM_WP_Customize_Menu_Group(
			$wp_customize,
			'fdm-menu-column-1',
			array(
				'label'     => __( 'First Column', 'food-and-drink-menu' ),
				'section'   => 'fdm-menu-column-1-section',
				'setting'   => 'fdm-menu-column-1',
			)
		)
	);

	$wp_customize->add_section(
		'fdm-menu-column-2-section',
		array(
			'title' => __( 'Second Column', 'food-and-drink-menu' ),
			'panel' => 'fdm-menu',
		)
	);

	$wp_customize->add_setting(
		'fdm-menu-column-2',
		array(
			'sanitize_callback' => '__return_true', // @todo implement sanitization
			'type' => 'fdm-menu-column',
			'transport' => 'postMessage',
		)
	);

	$wp_customize->add_control(
		new FDM_WP_Customize_Menu_Group(
			$wp_customize,
			'fdm-menu-column-2',
			array(
				'label'     => __( 'Second Column', 'food-and-drink-menu' ),
				'section'   => 'fdm-menu-column-2-section',
				'setting'   => 'fdm-menu-column-2',
			)
		)
	);
}
add_action( 'customize_register', 'fdm_customize_register' );

/**
 * Callback function to determine in the page currently being displayed is a
 * Menu post type
 *
 * @since 1.5
 */
function fdm_customize_is_menu_post() {
	return is_singular( FDM_MENU_POST_TYPE );
}

/**
 * Enqueue assets for the customizer
 *
 * @since 1.5
 */
function fdm_customize_enqueue_control_assets() {

	if ( empty( $_GET['fdm_menu'] ) ) {
		return;
	}

	// In versions prior to WP 4.7, it's possible that this dependency is
	// missing. Fixed in https://core.trac.wordpress.org/ticket/38107
	global $wp_version;
	if ( version_compare( $wp_version, 4.7, '<' ) ) {
		wp_enqueue_script( 'wp-util' );
	}

	wp_enqueue_script( 'fdm-customize-control', FDM_PLUGIN_URL . '/assets/js/customize.js', array( 'customize-controls' ), 1.5 );




			// Pass settings to the script
// 			global $wp_customize;
// 			wp_localize_script(
// 				'content-layout-control-js',
// 				'CLC_Control_Settings',
// 				array(
// 					'root' 	=> home_url( rest_get_url_prefix() ),
// 					'nonce'	=> wp_create_nonce( 'wp_rest' ),
// 				)
// 			);
}
add_action( 'customize_controls_enqueue_scripts', 'fdm_customize_enqueue_control_assets' );
