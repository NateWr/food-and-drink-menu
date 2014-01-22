<?php

/**
 * @package Food and Drink Menu
 *
  Plugin Name: Food and Drink Menu
  Plugin URI: http://themeofthecrop.com
  Description: Create a menu for restaurants, cafes, bars and eateries and display it in templates, posts, pages and widgets.
  Version: 1.0.2
  Author: Nate Wright
  Author URI: https://github.com/NateWr
  License: GPLv2 or later
 *
 */

// Exit early if called directly.
defined( 'WP_PLUGIN_URL' ) or die( 'Restricted access' );

// Common strings
define( 'FDM_TEXTDOMAIN', 'fdmdomain' );
define( 'FDM_PLUGIN_DIR', dirname( plugin_basename( __FILE__ ) ) );
define( 'FDM_PLUGIN_URL', WP_PLUGIN_URL . '/' . FDM_PLUGIN_DIR );
define( 'FDM_PLUGIN_FNAME', plugin_basename( __FILE__ ) );
define( 'FDM_UPGRADE_URL', 'http://themeofthecrop.com/?utm_medium=Plugin%20Upgrade%20Link&utm_campaign=Food%20and%20Drink%20Menu' );
define( 'FDM_TEMPLATE_DIR', 'fdm-templates' );
define( 'FDM_VERSION', 1 );
define( 'FDM_MENU_POST_TYPE', 'fdm-menu' );
define( 'FDM_MENUITEM_POST_TYPE', 'fdm-menu-item' );


// Load the admin page classes
require_once('lib/simple-admin-pages/simple-admin-pages.php');

// Load the plugin functions
require_once 'functions.php';

// Support internationalization
load_plugin_textdomain( FDM_TEXTDOMAIN, false, FDM_PLUGIN_DIR . '/languages/' );

// Register the plugin on init and flush the rewrite once activated
add_action('init', 'fdm_plugin_init');
register_activation_hook( __FILE__, 'fdm_rewrite_flush' );

// Add the thumbnail size for menu items
if ( !$fdm_config_thumb_width = get_option( 'fdm-item-thumb-width' ) ) {
	$fdm_config_thumb_width = 600;
}
if ( !$fdm_config_thumb_height = get_option( 'fdm-item-thumb-height' ) ) {
	$fdm_config_thumb_height = 600;
}
add_image_size( 'fdm-item-thumb', intval( $fdm_config_thumb_width ), intval( $fdm_config_thumb_height ), true );

// Register the widget
require_once( 'widgets/WidgetMenu.class.php' );
add_action( 'widgets_init', create_function( '', 'return register_widget( "fdmWidgetMenu" );' ) );
