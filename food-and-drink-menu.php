<?php
/**
 * Plugin Name: Food and Drink Menu
 * Plugin URI: http://themeofthecrop.com
 * Description: Create a menu for restaurants, cafes, bars and eateries and display it in templates, posts, pages and widgets.
 * Version: 1.4.2
 * Author: Nate Wright
 * Author URI: https://github.com/NateWr
 *
 * Text Domain: fdmdomain
 * Domain Path: /languages/
 */
if ( ! defined( 'ABSPATH' ) )
	exit;

class fdmFoodAndDrinkMenu {

	/**
	 * Initialize the plugin and register hooks
	 */
	public function __construct() {
		// Common strings
		define( 'FDM_TEXTDOMAIN', 'fdmdomain' );
		define( 'FDM_PLUGIN_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
		define( 'FDM_PLUGIN_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );
		define( 'FDM_PLUGIN_FNAME', plugin_basename( __FILE__ ) );
		define( 'FDM_UPGRADE_URL', 'http://themeofthecrop.com/?utm_medium=Plugin%20Upgrade%20Link&utm_campaign=Food%20and%20Drink%20Menu' );
		define( 'FDM_TEMPLATE_DIR', 'fdm-templates' );
		define( 'FDM_VERSION', 3 );
		define( 'FDM_MENU_POST_TYPE', 'fdm-menu' );
		define( 'FDM_MENUITEM_POST_TYPE', 'fdm-menu-item' );

		// Load template functions
		require_once( FDM_PLUGIN_DIR . '/functions.php' );

		// Call when plugin is initialized on every page load
		add_action( 'init', array( $this, 'load_config' ) );
		add_action( 'init', array( $this, 'load_textdomain' ) );

		// Load custom post types
		require_once( FDM_PLUGIN_DIR . '/cpts.php' );
		$this->cpts = new fdmCustomPostTypes();

		// Load settings
		require_once( FDM_PLUGIN_DIR . '/settings.php' );
		$this->settings = new fdmSettings();

		// Call when the plugin is activated
		register_activation_hook( __FILE__, array( $this, 'rewrite_flush' ) );

		// Load admin assets
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );

		// Register the widget
		add_action( 'widgets_init', array( $this, 'register_widgets' ) );

		// Order the menu items by menu order in the admin interface
		add_filter( 'pre_get_posts', array( $this, 'admin_order_posts' ) );

		// Append menu and menu item content to a post's $content variable
		add_filter( 'the_content', array( $this, 'append_to_content' ) );

		// Add links to plugin listing
		add_filter('plugin_action_links', array( $this, 'plugin_action_links' ), 10, 2);
	}

	/**
	 * Flush the rewrite rules when this plugin is activated to update with
	 * custom post types
	 * @since 1.1
	 */
	public function rewrite_flush() {
		$this->cpts->load_cpts();
		flush_rewrite_rules();
	}

	/**
	 * Load the plugin's configuration settings and default content
	 * @since 1.1
	 */
	public function load_config() {

		$settings = get_option( 'food-and-drink-menu-settings' );

		// Add a thumbnail size for menu items
		if ( !$fdm_config_thumb_width = $settings['fdm-item-thumb-width'] ) {
			$fdm_config_thumb_width = 600;
		}
		if ( !$fdm_config_thumb_height = $settings['fdm-item-thumb-height'] ) {
			$fdm_config_thumb_height = 600;
		}
		add_image_size( 'fdm-item-thumb', intval( $fdm_config_thumb_width ), intval( $fdm_config_thumb_height ), true );

		// Define supported styles
		fdm_load_view_files();
		$this->styles = array(
			'base' => new fdmStyle(
				array(
					'id'	=> 'base',
					'label'	=> __( 'Base formatting only', FDM_TEXTDOMAIN ),
					'css'	=> array(
						'base' => FDM_PLUGIN_URL . '/assets/css/base.css'
					)
				)
			),
			'classic' => new fdmStyle(
				array(
					'id'	=> 'classic',
					'label'	=> __( 'Classic style', FDM_TEXTDOMAIN ),
					'css'	=> array(
						'base' => FDM_PLUGIN_URL . '/assets/css/base.css',
						'classic' => FDM_PLUGIN_URL . '/assets/css/classic.css'
					)
				)
			),
			'none' => new fdmStyle(
				array(
					'id'	=> 'none',
					'label'	=> __( 'Don\'t load any CSS styles', FDM_TEXTDOMAIN ),
					'css'	=> array( )
				)
			),
		);
		$this->styles = apply_filters( 'fdm_styles', $this->styles );

	}

	/**
	 * Load the plugin textdomain for localistion
	 * @since 1.1
	 */
	public function load_textdomain() {
		load_plugin_textdomain( FDM_TEXTDOMAIN, false, plugin_basename( dirname( __FILE__ ) ) . "/languages/" );
	}

	/**
	 * Register the widgets
	 * @since 1.1
	 */
	public function register_widgets() {
		require_once( FDM_PLUGIN_DIR . '/widgets/WidgetMenu.class.php' );
		register_widget( 'fdmWidgetMenu' );
		require_once( FDM_PLUGIN_DIR . '/widgets/WidgetMenuItem.class.php' );
		register_widget( 'fdmWidgetMenuItem' );
	}

	/**
	 * Print the menu on menu post type pages
	 * @since 1.1
	 */
	function append_to_content( $content ) {
		global $post;

		if ( !is_main_query() || !in_the_loop() || ( FDM_MENU_POST_TYPE !== $post->post_type && FDM_MENUITEM_POST_TYPE !== $post->post_type ) ) {
			return $content;
		}

		// We must disable this filter while we're rendering the menu in order to
		// prevent it from falling into a recursive loop with each menu item's
		// content.
		remove_action( 'the_content', array( $this, 'append_to_content' ) );

		fdm_load_view_files();

		$args = array(
			'id'	=> $post->ID,
			'show_content'	=> true
		);
		if ( FDM_MENUITEM_POST_TYPE == $post->post_type ) {
			$args['singular'] = true;
		}
		$args = apply_filters( 'fdm_menu_args', $args );

		// Initialize and render the view
		if ( FDM_MENU_POST_TYPE == $post->post_type ) {
			$menu = new fdmViewMenu( $args );
		} else {
			$menu = new fdmViewItem( $args );
		}
		$content = $menu->render();

		// Restore this filter
		add_action( 'the_content', array( $this, 'append_to_content' ) );

		return $content;

	}

	/**
	 * Enqueue the admin-only CSS and Javascript
	 * @since 1.0
	 * @todo only enqueue these on relevant pages
	 */
	public function enqueue_admin_assets() {
		wp_enqueue_script( 'fdm-admin', FDM_PLUGIN_URL . '/assets/js/admin.js', array( 'jquery' ), '1.0', true );
		wp_enqueue_style( 'fdm-admin', FDM_PLUGIN_URL . '/assets/css/admin.css', array(), '1.0' );

		// Backwards compatibility for old admin icons
		global $wp_version;
		if ( $wp_version < 3.8 ) {
			wp_enqueue_style( 'fdm-admin-compat-3.8', FDM_PLUGIN_URL . '/assets/css/admin-compat-3.8.css', array(), '1.0' );
		}
	}

	/**
	 * Order the menu items by menu order in the admin interface
	 * @since 1.0
	 */
	public function admin_order_posts( $query ) {

		// Check that we're on the right screen
		if( ( is_admin() && $query->is_admin ) && $query->get( 'post_type' ) == 'fdm-menu-item' ) {

			// Don't override an existing orderby setting. This prevents other
			// orderby options from breaking.
			if ( !$query->get ( 'orderby' ) ) {
				$query->set( 'orderby', 'menu_order' );
				$query->set( 'order', 'ASC' );
			}
		}

		return $query;
	}

	/**
	 * Add links to the plugin listing on the installed plugins page
	 * @since 1.0
	 */
	public function plugin_action_links( $links, $plugin ) {

		if ( $plugin == FDM_PLUGIN_FNAME ) {

			$links['help'] = '<a href="' . FDM_PLUGIN_URL . '/docs" title="' . __( 'View the help documentation for Food and Drink Menu', FDM_TEXTDOMAIN ) . '">' . __( 'Help', FDM_TEXTDOMAIN ) . '</a>';

			if ( !defined( 'FDMP_VERSION' ) ) {
				$links['upgrade'] = '<a href="' . FDM_UPGRADE_URL . '" title="' . __( 'Upgrade to Food and Drink Pro', FDM_TEXTDOMAIN ) . '">' . __( 'Upgrade', FDM_TEXTDOMAIN ) . '</a>';
			}
		}

		return $links;

	}

}

$fdm_controller = new fdmFoodAndDrinkMenu();
