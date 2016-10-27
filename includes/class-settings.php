<?php

if ( !defined( 'ABSPATH' ) )
	exit;

/**
 * Class to register all settings in the settings panel
 */
class fdmSettings {

	public function __construct() {

		// Call when plugin is initialized on every page load
		add_action( 'init', array( $this, 'load_settings_panel' ) );

		// Add filters on the menu style so we can apply the setting option
		add_filter( 'fdm_menu_args', array( $this, 'set_style' ) );
		add_filter( 'fdm_shortcode_menu_atts', array( $this, 'set_style' ) );
		add_filter( 'fdm_shortcode_menu_item_atts', array( $this, 'set_style' ) );

	}

	/**
	 * Get the theme supports options for this plugin
	 *
	 * This mimics the core get_theme_support function, except it automatically
	 * looks up this plugin's feature set and searches for features within
	 * those settings.
	 *
	 * @param string $feature The feature support to check
	 * @since 1.5
	 */
	public function get_theme_support( $feature ) {

		$theme_support = get_theme_support( 'food-and-drink-menu' );

		if ( !is_array( $theme_support ) ) {
			return apply_filters( 'fdm_get_theme_support_' . $feature, false, $theme_support );
		}

		$theme_support = $theme_support[0];

		if ( isset( $theme_support[$feature] ) ) {
			return apply_filters( 'fdm_get_theme_support_' . $feature, $theme_support[$feature], $theme_support );
		}

		return apply_filters( 'fdm_get_theme_support_' . $feature, false, $theme_support );
	}

	/**
	 * Load the admin settings page
	 * @since 1.1
	 * @sa https://github.com/NateWr/simple-admin-pages
	 */
	public function load_settings_panel() {

		require_once( FDM_PLUGIN_DIR . '/lib/simple-admin-pages/simple-admin-pages.php');

		// Insantiate the Simple Admin Library so that we can add a settings page
		$sap = sap_initialize_library(
			array(
				'version'		=> '2.0.a.7', // Version of the library
				'lib_url'		=> FDM_PLUGIN_URL . '/lib/simple-admin-pages/', // URL path to sap library
			)
		);

		// Create a page for the options under the Settings (options) menu
		$sap->add_page(
			'submenu', 				// Admin menu which this page should be added to
			array(					// Array of key/value pairs matching the AdminPage class constructor variables
				'id'			=> 'food-and-drink-menu-settings',
				'title'			=> __( 'Settings', 'food-and-drink-menu' ),
				'menu_title'	=> __( 'Settings', 'food-and-drink-menu' ),
				'description'	=> '',
				'capability'	=> 'manage_options',
				'parent_menu'	=> 'edit.php?post_type=fdm-menu'
			)
		);

		// Create a section to choose a default style
		$sap->add_section(
			'food-and-drink-menu-settings',	// Page to add this section to
			array(								// Array of key/value pairs matching the AdminPageSection class constructor variables
				'id'			=> 'fdm-style-settings',
				'title'			=> __( 'Style', 'food-and-drink-menu' ),
				'description'	=> __( 'Choose what style you would like to use for your menu.', 'food-and-drink-menu' )
			)
		);
		global $fdm_controller;
		$options = array();
		foreach( $fdm_controller->styles as $style ) {
			$options[$style->id] = $style->label;
		}
		$sap->add_setting(
			'food-and-drink-menu-settings',
			'fdm-style-settings',
			'select',
			array(
				'id'			=> 'fdm-style',
				'title'			=> __( 'Style', 'food-and-drink-menu' ),
				'description'	=> __( 'Choose the styling for your menus.', 'food-and-drink-menu' ),
				'blank_option'	=> false,
				'options'		=> $options
			)
		);

		// Create a section to disable specific features
		$sap->add_section(
			'food-and-drink-menu-settings',	// Page to add this section to
			array(								// Array of key/value pairs matching the AdminPageSection class constructor variables
				'id'			=> 'fdm-enable-settings',
				'title'			=> __( 'Disable Features', 'food-and-drink-menu' ),
				'description'	=> __( 'Choose what features of the menu items you wish to disable and hide from the admin interface.', 'food-and-drink-menu' )
			)
		);
		$sap->add_setting(
			'food-and-drink-menu-settings',
			'fdm-enable-settings',
			'toggle',
			array(
				'id'			=> 'fdm-disable-price',
				'title'			=> __( 'Price', 'food-and-drink-menu' ),
				'label'			=> __( 'Disable all pricing options for menu items.', 'food-and-drink-menu' )
			)
		);

		// Create a section for advanced options
		$sap->add_section(
			'food-and-drink-menu-settings',	// Page to add this section to
			array(								// Array of key/value pairs matching the AdminPageSection class constructor variables
				'id'			=> 'fdm-advanced-settings',
				'title'			=> __( 'Advanced Options', 'food-and-drink-menu' )
			)
		);
		$sap->add_setting(
			'food-and-drink-menu-settings',
			'fdm-advanced-settings',
			'text',
			array(
				'id'			=> 'fdm-item-thumb-width',
				'title'			=> __( 'Menu Item Photo Width', 'food-and-drink-menu' ),
				'description'	=> sprintf(
					esc_html__( 'The width in pixels of menu item thumbnails. Leave this field empty to preserve the default (600x600). After changing this setting, you may need to %sregenerate your thumbnails%s.', 'food-and-drink-menu' ),
					'<a href="http://doc.themeofthecrop.com/plugins/food-and-drink-menu/user/faq#image-sizes">',
					'</a>'
				),
			)
		);
		$sap->add_setting(
			'food-and-drink-menu-settings',
			'fdm-advanced-settings',
			'text',
			array(
				'id'			=> 'fdm-item-thumb-height',
				'title'			=> __( 'Menu Item Photo Height', 'food-and-drink-menu' ),
				'description'	=> sprintf(
					esc_html__( 'The height in pixels of menu item thumbnails. Leave this field empty to preserve the default (600x600). After changing this setting, you may need to %sregenerate your thumbnails%s.', 'food-and-drink-menu' ),
					'<a href="http://doc.themeofthecrop.com/plugins/food-and-drink-menu/user/faq#image-sizes">',
					'</a>'
				),
			)
		);

		// Create filter so addons can modify the settings page or add new pages
		$sap = apply_filters( 'fdm_settings_page', $sap );

		// Backwards compatibility when the sap library went to version 2
		$sap->port_data(2);

		// Register all admin pages and settings with WordPress
		$sap->add_admin_menus();
	}

	/**
	 * Set the style of a menu or menu item before rendering
	 * @since 1.1
	 */
	public function set_style( $args ) {

		$settings = get_option( 'food-and-drink-menu-settings' );
		if ( !$settings['fdm-style'] ) {
			$args['style'] = 'base';
		} else {
			$args['style'] = $settings['fdm-style'];
		}

		return $args;
	}

}
