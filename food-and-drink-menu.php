<?php
/**
 * Plugin Name: Food and Drink Menu
 * Plugin URI: http://themeofthecrop.com
 * Description: Create a menu for restaurants, cafes, bars and eateries and display it in templates, posts, pages and widgets.
 * Version: 1.5.2
 * Author: Nate Wright
 * Author URI: https://github.com/NateWr
 *
 * Text Domain: food-and-drink-menu
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
		define( 'FDM_DOMAIN', 'food-and-drink-menu' );
		define( 'FDM_PLUGIN_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
		define( 'FDM_PLUGIN_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );
		define( 'FDM_PLUGIN_FNAME', plugin_basename( __FILE__ ) );
		define( 'FDM_UPGRADE_URL', 'https://themeofthecrop.com/plugins/food-and-drink-menu/?utm_medium=Plugin%20Upgrade%20Link&utm_campaign=Food%20and%20Drink%20Menu' );
		define( 'FDM_TEMPLATE_DIR', 'fdm-templates' );
		define( 'FDM_VERSION', 3 );
		define( 'FDM_MENU_POST_TYPE', 'fdm-menu' );
		define( 'FDM_MENUITEM_POST_TYPE', 'fdm-menu-item' );

		// Load template functions
		require_once( FDM_PLUGIN_DIR . '/includes/template-functions.php' );

		// Call when plugin is initialized on every page load
		add_action( 'init', array( $this, 'load_config' ) );
		add_action( 'init', array( $this, 'load_textdomain' ) );

		// Load custom post types
		require_once( FDM_PLUGIN_DIR . '/includes/class-custom-post-types.php' );
		$this->cpts = new fdmCustomPostTypes();

		// Load settings
		require_once( FDM_PLUGIN_DIR . '/includes/class-settings.php' );
		$this->settings = new fdmSettings();

		// Load integrations with other plugins
		require_once( FDM_PLUGIN_DIR . '/includes/integrations/business-profile.php' );
		require_once( FDM_PLUGIN_DIR . '/includes/integrations/wordpress-seo.php' );

		// Load compatibility sections
		require_once( FDM_PLUGIN_DIR . '/includes/compatibility.php' );

		// Call when the plugin is activated
		register_activation_hook( __FILE__, array( $this, 'rewrite_flush' ) );

		// Load admin assets
		add_action( 'admin_print_scripts-post-new.php', array( $this, 'enqueue_admin_assets' ) );
		add_action( 'admin_print_scripts-post.php', array( $this, 'enqueue_admin_assets' ) );
		add_action( 'admin_print_scripts-edit.php', array( $this, 'enqueue_admin_assets' ) );

		// Register the widget
		add_action( 'widgets_init', array( $this, 'register_widgets' ) );

		// Order the menu items by menu order in the admin interface
		add_filter( 'pre_get_posts', array( $this, 'admin_order_posts' ) );

		// Append menu and menu item content to a post's $content variable
		add_filter( 'the_content', array( $this, 'append_to_content' ) );

		// Add links to plugin listing
		add_filter( 'plugin_action_links', array( $this, 'plugin_action_links' ), 10, 2);

		// Backwards compatibility for new taxonomy term splitting
		// in 4.2
		// https://make.wordpress.org/core/2015/02/16/taxonomy-term-splitting-in-4-2-a-developer-guide/
		add_action( 'split_shared_term', array( $this, 'compat_split_shared_term' ), 10, 4 );
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
					'label'	=> __( 'Base formatting only', 'food-and-drink-menu' ),
					'css'	=> array(
						'base' => FDM_PLUGIN_URL . '/assets/css/base.css'
					)
				)
			),
			'classic' => new fdmStyle(
				array(
					'id'	=> 'classic',
					'label'	=> __( 'Classic style', 'food-and-drink-menu' ),
					'css'	=> array(
						'base' => FDM_PLUGIN_URL . '/assets/css/base.css',
						'classic' => FDM_PLUGIN_URL . '/assets/css/classic.css'
					)
				)
			),
			'none' => new fdmStyle(
				array(
					'id'	=> 'none',
					'label'	=> __( 'Don\'t load any CSS styles', 'food-and-drink-menu' ),
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
		load_plugin_textdomain( 'food-and-drink-menu', false, plugin_basename( dirname( __FILE__ ) ) . "/languages/" );
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
	 */
	public function enqueue_admin_assets() {

		global $post_type;

		if ( $post_type != FDM_MENU_POST_TYPE && $post_type != FDM_MENUITEM_POST_TYPE ) {
			return;
		}

		wp_enqueue_style( 'fdm-admin', FDM_PLUGIN_URL . '/assets/css/admin.css', array(), '1.5.2' );
		wp_enqueue_script( 'fdm-admin', FDM_PLUGIN_URL . '/assets/js/admin.js', array( 'jquery' ), '1.5.2', true );
		wp_localize_script(
			'fdm-admin',
			'fdmSettings',
			array(
				'nonce' => wp_create_nonce( 'fdm-admin' ),
				'i18n' => array(
					'undefined_error' => esc_html( 'An unexpected error occurred. Please reload the page and try again.', 'food-and-drink-menu' ),
				)
			)
		);

		add_action( 'admin_footer', array( $this, 'print_modals' ) );
	}

	/**
	 * Print modals used in the menu editing screens
	 *
	 * @since 1.5
	 */
	public function print_modals() {
		?>

		<div id="fdm-menu-section-modal" class="fdm-modal">
			<div class="fdm-modal-content">
				<div class="field">
					<label for="fdm-menu-section-modal-name">
						<?php esc_html_e( 'Section Name', 'food-and-drink-menu' ); ?>
					</label>
					<input type="text" id="fdm-menu-section-modal-name">
				</div>
				<p class="description">
					<?php
						printf(
							esc_html( 'Enter a unique name for this section when it appears in this menu. The name entered here will only be used for this menu. To change the name of the section in all menus, visit the %sMenu Sections%s list.', 'food-and-drink-menu' ),
							'<a href="' . esc_url( admin_url( '/edit-tags.php?taxonomy=fdm-menu-section&post_type=fdm-menu-item' ) ) . '">',
							'</a>'
						);
					?>
				</p>
				<div class="buttons">
					<a id="fdm-menu-section-modal-save" href="#" class="fdm-save button">
						<?php esc_html_e( 'Update Section Name', 'food-and-drink-menu' ); ?>
					</a>
				</div>
			</div>
		</div>

		<?php
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
				$query->set( 'orderby', array( 'menu_order' => 'ASC', 'post_date' => 'DESC' ) );
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

			$links['help'] = '<a href="http://doc.themeofthecrop.com/plugins/food-and-drink-menu?utm_source=Plugin&utm_medium=Plugin%Help&utm_campaign=Food%20and%20Drink%20Menu" title="' . __( 'View the help documentation for Food and Drink Menu', 'food-and-drink-menu' ) . '">' . __( 'Help', 'food-and-drink-menu' ) . '</a>';

			if ( !defined( 'FDMP_VERSION' ) ) {
				$links['upgrade'] = '<a href="' . FDM_UPGRADE_URL . '" title="' . __( 'Upgrade to Food and Drink Pro', 'food-and-drink-menu' ) . '">' . __( 'Upgrade', 'food-and-drink-menu' ) . '</a>';
			}
		}

		return $links;

	}

	/**
	 * Update menu section term ids when shared terms are split
	 *
	 * Backwards compatibility for new taxonomy term splitting
	 * introduced in 4.2. Shared terms in different taxonomies
	 * were created in versions prior to 4.1 and will be
	 * automatically split in 4.2, with their term ids being
	 * updated. This function will update the term ids used to
	 * link menu sections to menus.
	 *
	 * https://make.wordpress.org/core/2015/02/16/taxonomy-term-splitting-in-4-2-a-developer-guide/
	 *
	 * @since 1.4.3
	 */
	public function compat_split_shared_term( $old_term_id, $new_term_id, $term_taxonomy_id, $taxonomy ) {

		if ( $taxonomy !== 'fdm-menu-section' ) {
			return;
		}

		$posts = new WP_Query( array(
			'post_type' => 'fdm-menu',
			'posts_per_page'	=> 1000,
		) );

		$cols = array( 'one', 'two' );
		while( $posts->have_posts() ) {
			$posts->the_post();

			foreach( $cols as $col ) {
				$updated = false;
				$menu_sections = get_post_meta( get_the_ID(), 'fdm_menu_column_' . $col, true );

				if ( !empty( $menu_sections ) ) {
					$term_ids = explode( ',', $menu_sections );
					foreach( $term_ids as $key => $term_id ) {
						if ( $term_id == $old_term_id ) {
							$term_ids[ $key ] = $new_term_id;
							$updated = true;
						}
					}
				}

				if ( $updated ) {
					update_post_meta( get_the_ID(), 'fdm_menu_column_' . $col, join( ',', $term_ids ) );
				}
			}
		}
	}

}

global $fdm_controller;
$fdm_controller = new fdmFoodAndDrinkMenu();
