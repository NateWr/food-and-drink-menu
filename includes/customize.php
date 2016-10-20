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
		'fdm-menu-column-0-section',
		array(
			'title' => __( 'First Column', 'food-and-drink-menu' ),
			'panel' => 'fdm-menu',
		)
	);

	$wp_customize->add_setting(
		'fdm-menu-column-0',
		array(
			'sanitize_callback' => 'fdm_customize_sanitize_menu_group',
			'type' => 'fdm_menu_group',
			'transport' => 'postMessage',
		)
	);

	$wp_customize->add_control(
		new FDM_WP_Customize_Menu_Group(
			$wp_customize,
			'fdm-menu-column-0',
			array(
				'label'     => __( 'First Column', 'food-and-drink-menu' ),
				'section'   => 'fdm-menu-column-0-section',
				'setting'   => 'fdm-menu-column-0',
			)
		)
	);

	$wp_customize->add_section(
		'fdm-menu-column-1-section',
		array(
			'title' => __( 'Second Column', 'food-and-drink-menu' ),
			'panel' => 'fdm-menu',
		)
	);

	$wp_customize->add_setting(
		'fdm-menu-column-1',
		array(
			'sanitize_callback' => 'fdm_customize_sanitize_menu_group',
			'type' => 'fdm_menu_group',
			'transport' => 'postMessage',
		)
	);

	$wp_customize->add_control(
		new FDM_WP_Customize_Menu_Group(
			$wp_customize,
			'fdm-menu-column-1',
			array(
				'label'     => __( 'Second Column', 'food-and-drink-menu' ),
				'section'   => 'fdm-menu-column-1-section',
				'setting'   => 'fdm-menu-column-1',
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
 * Enqueue assets for the customize pane
 *
 * @since 1.5
 */
function fdm_customize_enqueue_control_assets() {
	wp_enqueue_script( 'fdm-customize-control', FDM_PLUGIN_URL . '/assets/js/fdm-customize-control.js', array( 'customize-controls', 'backbone', 'wp-util', 'wp-backbone' ), 1.5, true );
	wp_enqueue_style( 'fdm-customize-control', FDM_PLUGIN_URL . '/assets/css/customize.css', array(), 1.5 );
}
add_action( 'customize_controls_enqueue_scripts', 'fdm_customize_enqueue_control_assets' );

/**
 * Enqueue assets for the preview pane
 *
 * @since 1.5
 */
function fdm_customize_enqueue_preview_assets() {

	// Enqueue assets and data for which the current post is needed
	add_action( 'wp_footer', 'fdm_customize_load_preview_data', 1 );
}
add_action( 'customize_preview_init', 'fdm_customize_enqueue_preview_assets' );

/**
 * Load data about the menu being viewed in the preview pane
 *
 * @since 1.5
 */
function fdm_customize_load_preview_data() {

	if ( !fdm_customize_is_menu_post() ) {
		return;
	}

	$menu = new fdmViewMenu(
		array(
			'id' => get_the_ID(),
			'show_title' => true,
			'show_content' => true,
		)
	);
	$menu->get_menu_post();

	$return = array(
		'ID' => $menu->id,
		'title' => $menu->title,
		'content' => $menu->content,
		'footer' => $menu->footer,
		'post_type' => $menu->post->post_type,
	);

	$menu->get_groups();
	foreach( $menu->groups as $group_i => $group ) {
		foreach( $group as $section_id ) {

			$section = new fdmViewSection( array( 'id' => $section_id, 'menu' => $menu ) );
			$section->load_section();

			$section_array = array(
				'id' => $section_id,
				'title' => $section->title,
				'description' => $section->description,
			);

			foreach( $section->items as $item ) {
				$section_array['items'][] = array(
					'ID' => $item->id,
					'title' => $item->post->post_title,
				);
			}

			if ( !isset( $return['groups'][$group_i] ) ) {
				$return['groups'][$group_i] = array();
			}

			$return['groups'][$group_i][] = $section_array;
		}
	}

	wp_enqueue_script( 'fdm-customize-preview', FDM_PLUGIN_URL . '/assets/js/fdm-customize-preview.js', array( 'customize-preview' ), 1.5, true );
	wp_localize_script( 'fdm-customize-preview', 'fdm_previewed_item', $return );
}

/**
 * Sanitize values for the FDM_WP_Customize_Menu_Group control setting
 *
 * @since 1.5
 */
function fdm_customize_sanitize_menu_group( $value ) {

	if ( empty( $value ) ) {
		return array();
	}

	$sanitized = array();

	foreach( $value as $post ) {

		$post_id = isset( $post['id'] ) ? absint( $post['id'] ) : 0;
		if ( get_post_status( $post_id ) === false || empty( $post['group'] ) || empty( $post['sections'] ) ) {
			continue;
		}

		if ( $post['group'] != 'fdm-menu-column-0' && $post['group'] != 'fdm-menu-column-1' ) {
			continue;
		}

		$sanitized_post = array(
			'id' => $post_id,
			'group' => $post['group'],
			'sections' => array(),
		);

		foreach( $post['sections'] as $section ) {

			$section_id = absint( $section['id'] );
			if ( !term_exists( $section_id, 'fdm-menu-section' ) ) {
				error_log( 'continue 3' );
				continue;
			}

			$sanitized_post['sections'][] = array(
				'id' => $section_id,
				'title' => empty( $section['title'] ) ? '' : sanitize_text_field( $section['title'] ),
				'description' => empty( $section['description'] ) ? '' : sanitize_text_field( $section['description'] ),
			);
		}

		$sanitized[] = $sanitized_post;
	}

	return $sanitized;
}
