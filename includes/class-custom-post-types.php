<?php
/**
 * Class to handle all custom post type definitions for Food and Drink Menu
 */

if ( !defined( 'ABSPATH' ) )
	exit;

class fdmCustomPostTypes {

	/**
	 * Array of menu item taxonomies
	 *
	 * @param array
	 * @since 1.5
	 */
	public $menu_item_taxonomies = array();

	public function __construct() {

		// Call when plugin is initialized on every page load
		add_action( 'init', array( $this, 'load_cpts' ) );
		add_action( 'admin_menu', array( $this, 'load_cpt_admin_menu' ) );

		// Handle metaboxes
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_meta' ) );

		// Add columns and filters to the admin list of menu items
		add_filter( 'manage_fdm-menu-item_posts_columns', array( $this, 'menu_item_posts_columns' ) );
		add_filter( 'manage_edit-fdm-menu-item_sortable_columns', array( $this, 'menu_item_posts_sortable_columns' ) );
		add_action( 'pre_get_posts', array( $this, 'menu_item_posts_orderby' ) );
		add_action( 'manage_fdm-menu-item_posts_custom_column', array( $this, 'menu_item_posts_columns_content' ), 10, 2 );
		add_action( 'restrict_manage_posts', array( $this, 'menu_item_posts_filters' ) );
		add_filter( 'parse_query', array( $this, 'menu_item_posts_filter_query' ) );

		// Add columns and filters to the admin list of menus
		add_filter( 'manage_fdm-menu_posts_columns', array( $this, 'menu_posts_columns' ) );
		add_action( 'manage_fdm-menu_posts_custom_column', array( $this, 'menu_posts_columns_content' ), 10, 2 );

		// Process price changes from the menu item list table
		add_action( 'wp_ajax_nopriv_fdm-menu-item-price' , array( $this , 'ajax_nopriv' ) );
		add_action( 'wp_ajax_fdm-menu-item-price', array( $this, 'ajax_menu_item_price' ) );

		// Allow menus to opt for a page template if desired
		add_filter( 'theme_' . FDM_MENU_POST_TYPE . '_templates', array( $this, 'add_menu_templates' ), 10, 3 );
		add_filter( 'template_include', array( $this, 'load_menu_template' ), 99 );

	}

	/**
	 * Initialize custom post types
	 * @since 1.1
	 */
	public function load_cpts() {

		// Define the menu taxonomies
		$menu_taxonomies = array();

		// Create filter so addons can modify the taxonomies
		$menu_taxonomies = apply_filters( 'fdm_menu_taxonomies', $menu_taxonomies );

		// Define the menu custom post type
		$args = array(
			'labels' => array(
				'name' => __( 'Menus', 'food-and-drink-menu' ),
				'singular_name' => __( 'Menu', 'food-and-drink-menu' ),
				'add_new' => __( 'Add Menu', 'food-and-drink-menu' ),
				'add_new_item' => __( 'Add New Menu', 'food-and-drink-menu' ),
				'edit' => __( 'Edit', 'food-and-drink-menu' ),
				'edit_item' => __( 'Edit Menu', 'food-and-drink-menu' ),
				'new_item' => __( 'New Menu', 'food-and-drink-menu' ),
				'view' => __( 'View', 'food-and-drink-menu' ),
				'view_item' => __( 'View Menu', 'food-and-drink-menu' ),
				'search_items' => __( 'Search Menus', 'food-and-drink-menu' ),
				'not_found' => __( 'No Menu found', 'food-and-drink-menu' ),
				'not_found_in_trash' => __( 'No Menu found in Trash', 'food-and-drink-menu' ),
				'parent' => __( 'Parent Menu', 'food-and-drink-menu' )
			),
			'menu_icon' => 'data:image/svg+xml;base64,PHN2ZyB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHdpZHRoPSIzMiIgaGVpZ2h0PSIzMiIgdmlld0JveD0iMCAwIDMyIDMyIj4KPHBhdGggZmlsbD0icmdiYSgyNDAsMjQ1LDI1MCwwLjYpIiBkPSJNNyAwYy0zLjMxNCAwLTYgMy4xMzQtNiA3IDAgMy4zMSAxLjk2OSA2LjA4MyA0LjYxNiA2LjgxMmwtMC45OTMgMTYuMTkxYy0wLjA2NyAxLjA5OCAwLjc3OCAxLjk5NiAxLjg3OCAxLjk5NmgxYzEuMSAwIDEuOTQ1LTAuODk4IDEuODc4LTEuOTk2bC0wLjk5My0xNi4xOTFjMi42NDYtMC43MjkgNC42MTYtMy41MDIgNC42MTYtNi44MTIgMC0zLjg2Ni0yLjY4Ni03LTYtN3pNMjcuMTY3IDBsLTEuNjY3IDEwaC0xLjI1bC0wLjgzMy0xMGgtMC44MzNsLTAuODMzIDEwaC0xLjI1bC0xLjY2Ny0xMGgtMC44MzN2MTNjMCAwLjU1MiAwLjQ0OCAxIDEgMWgyLjYwNGwtMC45ODIgMTYuMDA0Yy0wLjA2NyAxLjA5OCAwLjc3OCAxLjk5NiAxLjg3OCAxLjk5NmgxYzEuMSAwIDEuOTQ1LTAuODk4IDEuODc4LTEuOTk2bC0wLjk4Mi0xNi4wMDRoMi42MDRjMC41NTIgMCAxLTAuNDQ4IDEtMXYtMTNoLTAuODMzeiI+PC9wYXRoPgo8L3N2Zz4=',
			'public' => true,
			'rewrite' => array( 'slug' => 'menu' ),
			'supports' => array(
				'title',
				'editor',
				'revisions',
			),
			'taxonomies' => array_keys( $menu_taxonomies )
		);

		// Create filter so addons can modify the arguments
		$args = apply_filters( 'fdm_menu_args', $args );

		// Add an action so addons can hook in before the menu is registered
		do_action( 'fdm_menu_pre_register' );

		// Register the menu
		register_post_type( 'fdm-menu', $args );

		// Add an action so addons can hook in after the menu is registered
		do_action( 'fdm_menu_post_register' );

		// Create menu sections (desserts, entrees, etc)
		$this->menu_item_taxonomies['fdm-menu-section'] = array(
			'labels' 	=> array(
				'name' => __( 'Menu Sections', 'food-and-drink-menu' ),
				'singular_name' => __( 'Menu Section', 'food-and-drink-menu' ),
				'search_items' => __( 'Search Menu Sections', 'food-and-drink-menu' ),
				'all_items' => __( 'All Menu Sections', 'food-and-drink-menu' ),
				'parent_item' => __( 'Menu Section', 'food-and-drink-menu' ),
				'parent_item_colon' => __( 'Menu Section:', 'food-and-drink-menu' ),
				'edit_item' => __( 'Edit Menu Section', 'food-and-drink-menu' ),
				'update_item' => __( 'Update Menu Section', 'food-and-drink-menu' ),
				'add_new_item' => __( 'Add New Menu Section', 'food-and-drink-menu' ),
				'new_item_name' => __( 'Menu Section', 'food-and-drink-menu' ),
				'no_terms' => __( 'No menu sections', 'food-and-drink-menu' ),
				'items_list_navigation' => __( 'Menu sections list navigation', 'food-and-drink-menu' ),
				'items_list' => __( 'Menu sections list', 'food-and-drink-menu' ),
				'archives' => __( 'Menu Archives', 'food-and-drink-menu' ),
				'insert_into_item' => __( 'Insert into menu', 'food-and-drink-menu' ),
				'uploaded_to_this_item' => __( 'Uploaded to this menu', 'food-and-drink-menu' ),
				'filter_items_list' => __( 'Filter menu list', 'food-and-drink-menu' ),
				'item_list_navigation' => __( 'Menu list navigation', 'food-and-drink-menu' ),
				'items_list' => __( 'Menu list', 'food-and-drink-menu' ),
			),
		);

		// Create filter so addons can modify the taxonomies
		$this->menu_item_taxonomies = apply_filters( 'fdm_menu_item_taxonomies', $this->menu_item_taxonomies );

		// Register taxonomies
		foreach( $this->menu_item_taxonomies as $id => $taxonomy ) {
			register_taxonomy(
				$id,
				'',
				$taxonomy
			);
		}

		// Define the Menu Item custom post type
		$args = array(
			'labels' => array(
				'name' => __( 'Menu Items', 'food-and-drink-menu' ),
				'singular_name' => __( 'Menu Item', 'food-and-drink-menu' ),
				'add_new' => __( 'Add Menu Item', 'food-and-drink-menu' ),
				'add_new_item' => __( 'Add New Menu Item', 'food-and-drink-menu' ),
				'edit' => __( 'Edit', 'food-and-drink-menu' ),
				'edit_item' => __( 'Edit Menu Item', 'food-and-drink-menu' ),
				'new_item' => __( 'New Menu Item', 'food-and-drink-menu' ),
				'view' => __( 'View', 'food-and-drink-menu' ),
				'view_item' => __( 'View Menu Item', 'food-and-drink-menu' ),
				'search_items' => __( 'Search Menu Items', 'food-and-drink-menu' ),
				'not_found' => __( 'No Menu Item found', 'food-and-drink-menu' ),
				'not_found_in_trash' => __( 'No Menu Item found in Trash', 'food-and-drink-menu' ),
				'parent' => __( 'Parent Menu Item', 'food-and-drink-menu' ),
				'featured_image' => __( 'Item Photo', 'food-and-drink-menu' ),
				'set_featured_image' => __( 'Set item photo', 'food-and-drink-menu' ),
				'remove_featured_image' => __( 'Remove item photo', 'food-and-drink-menu' ),
				'use_featured_image' => __( 'Use as item photo', 'food-and-drink-menu' ),
				'archives' => __( 'Menu Item Archives', 'food-and-drink-menu' ),
				'insert_into_item' => __( 'Insert into menu item', 'food-and-drink-menu' ),
				'uploaded_to_this_item' => __( 'Uploaded to this menu item', 'food-and-drink-menu' ),
				'filter_items_list' => __( 'Filter menu items list', 'food-and-drink-menu' ),
				'item_list_navigation' => __( 'Menu items list navigation', 'food-and-drink-menu' ),
				'items_list' => __( 'Menu items list', 'food-and-drink-menu' ),
			),
			'menu_position' => 5,
			'show_in_menu' => 'edit.php?post_type=' . FDM_MENU_POST_TYPE,
			'public' => true,
			'rewrite' => array( 'slug' => 'menu-item' ),
			'supports' => array(
				'title',
				'editor',
				'thumbnail',
				'revisions',
				'page-attributes'
			),
			'taxonomies' => array_keys( $this->menu_item_taxonomies )
		);

		// Create filter so addons can modify the arguments
		$args = apply_filters( 'fdm_menu_item_args', $args );

		// Add an action so addons can hook in before the menu is registered
		do_action( 'fdm_menu_item_pre_register' );

		// Register the menu item post type
		register_post_type( 'fdm-menu-item', $args );

		// Add an action so addons can hook in after the menu is registered
		do_action( 'fdm_menu_item_post_register' );

	}

	/**
	 * Add submenu items to the menu
	 *
	 * @since 1.5
	 */
	public function load_cpt_admin_menu() {

		// Remove the Add Menu item
		remove_submenu_page(
			'edit.php?post_type=' . FDM_MENU_POST_TYPE,
			'post-new.php?post_type=' . FDM_MENU_POST_TYPE
		);

		// Add any menu item taxonomies
		foreach( $this->menu_item_taxonomies as $id => $taxonomy ) {
			add_submenu_page(
				'edit.php?post_type=' . FDM_MENU_POST_TYPE,
				$taxonomy['labels']['name'],
				$taxonomy['labels']['name'],
				isset( $taxonomy['capabilities'] ) ? $taxonomy['capabilities']['edit_terms'] : 'edit_posts',
				'edit-tags.php?taxonomy=' . $id . '&post_type=' . FDM_MENUITEM_POST_TYPE
			);
		}
	}

	/**
	 * Add metaboxes to specify custom post type data
	 * @since 1.0
	 */
	public function add_meta_boxes() {

		$meta_boxes = array(

			// Add a menu organizer
			'fdm_menu_layout' => array (
				'id'		=>	'fdm_menu_layout',
				'title'		=> esc_html__( 'Menu Layout', 'food-and-drink-menu' ),
				'callback'	=> array( $this, 'show_menu_organizer' ),
				'post_type'	=> 'fdm-menu',
				'context'	=> 'normal',
				'priority'	=> 'core'
			),

			// Add a menu footer WYSIWYG editor
			'fdm_menu_footer' => array (
				'id'		=>	'fdm_menu_footer',
				'title'		=> esc_html__( 'Menu Footer', 'food-and-drink-menu' ),
				'callback'	=> array( $this, 'show_menu_footer' ),
				'post_type'	=> 'fdm-menu',
				'context'	=> 'normal',
				'priority'	=> 'default'
			),

			// Add a box that shows menu shortcode
			'fdm_menu_shortcode' => array (
				'id'		=>	'fdm_menu_shortcode',
				'title'		=> esc_html__( 'Menu Shortcode', 'food-and-drink-menu' ),
				'callback'	=> array( $this, 'show_menu_shortcode' ),
				'post_type'	=> 'fdm-menu',
				'context'	=> 'side',
				'priority'	=> 'default'
			),

			// Add a box that shows menu item shortcode
			'fdm_menu_item_shortcode' => array (
				'id'		=>	'fdm_menu_item_shortcode',
				'title'		=> esc_html__( 'Menu Item Shortcode', 'food-and-drink-menu' ),
				'callback'	=> array( $this, 'show_menu_item_shortcode' ),
				'post_type'	=> 'fdm-menu-item',
				'context'	=> 'side',
				'priority'	=> 'default'
			),

		);

		// Add menu item price metabox
		$settings = get_option( 'food-and-drink-menu-settings' );
		if ( !$settings['fdm-disable-price'] ) {
			$meta_boxes['fdm_menu_item_price'] = array (
				'id'		=>	'fdm_item_price',
				'title'		=> esc_html__( 'Price', 'food-and-drink-menu' ),
				'callback'	=> array( $this, 'show_item_price' ),
				'post_type'	=> 'fdm-menu-item',
				'context'	=> 'side',
				'priority'	=> 'default'
			);
		}

		// Add a page template metabox for WP versions before 4.7 when custom
		// post type templates were introduced
		if ( version_compare( get_bloginfo( 'version' ), '4.7-beta-1', '<' ) ) {
			$meta_boxes['fdm_menu_template'] = array(
				'id' => 'fdm_menu_template',
				'title' => esc_html__( 'Page Template', 'food-and-drink-menu' ),
				'callback' => array( $this, 'show_page_template' ),
				'post_type' => 'fdm-menu',
				'context' => 'side',
				'priority' => 'default',
			);
		}

		// Create filter so addons can modify the metaboxes
		$meta_boxes = apply_filters( 'fdm_meta_boxes', $meta_boxes );

		// Create the metaboxes
		foreach ( $meta_boxes as $meta_box ) {
			add_meta_box(
				$meta_box['id'],
				$meta_box['title'],
				$meta_box['callback'],
				$meta_box['post_type'],
				$meta_box['context'],
				$meta_box['priority']
			);
		}
	}


	/**
	 * Print the Menu Item price metabox HTML
	 *
	 * @param WP_Post|null $singular_post Only appears on the menu item editing screen
	 * @since 1.0
	 */
	public function show_item_price( $singular_post = null ) {

		// Retrieve values for this if it exists
		global $post;
		$prices = (array) get_post_meta( $post->ID, 'fdm_item_price' );

		// Always add at least one price input field
		if ( empty( $prices ) ) {
			$prices = array( '' );
		}

		// If we don't have a $post, this is being printed in the quick edit
		// panel. Input fields can't have name attributes because it messes with
		// the menu item filter forms.
		// @see https://github.com/NateWr/food-and-drink-menu/issues/35
		$input_attrs = '';
		if ( !is_null( $singular_post ) && is_a( $singular_post, 'WP_POST' ) ) {
			$input_attrs = ' name="fdm_item_price[]"';
		} else {
			$input_attrs = ' data-name="fdm_item_price"';
		}

		?>

			<div class="fdm-input-controls fdm-input-side-panel" data-menu-item-id="<?php echo $post->ID; ?>">
				<div class="fdm-input-prices fdm-input-group">
					<?php foreach( $prices as $key => $price ) : ?>
						<div class="fdm-input-control">
							<label for="fdm_item_price" class="screen-reader-text">
								<?php echo __( 'Price', 'food-and-drink-menu' ); ?>
							</label>
							<input type="text"<?php echo $input_attrs; ?> value="<?php echo esc_attr( $price ); ?>">
							<a href="#" class="fdm-input-delete">
								<?php esc_html_e( 'Remove this price' ); ?>
							</a>
						</div>
					<?php endforeach; ?>
					<div class="fdm-input-group-add">
						<a href="#" class="fdm-price-add">
							<?php esc_html_e( 'Add Price' ); ?>
						</a>
					</div>
				</div>

				<?php do_action( 'fdm_show_item_price' ); ?>

			</div>

		<?php
	}

	/**
	 * Print the Menu footer HTML
	 * @since 1.0
	 */
	public function show_menu_footer() {

		// Retrieve existing settings
		global $post;
		$footer = get_post_meta( $post->ID, 'fdm_menu_footer_content', true );

		wp_editor(
			$footer,
			'fdm_menu_footer_content',
			array(
				'textarea_rows' => 5
			)
		);
	}

	/**
	 * Print the Menu organizer HTML
	 * @since 1.0
	 */
	public function show_menu_organizer() {

		// Retrieve existing settings
		global $post;
		$column_one = get_post_meta( $post->ID, 'fdm_menu_column_one', true );
		$column_two = get_post_meta( $post->ID, 'fdm_menu_column_two', true );

		// Retrieve sections and store in HTML lists
		$sections = get_terms( 'fdm-menu-section', array( 'hide_empty' => false ) );
		foreach( $sections as $section ) {
			$alt_title = get_post_meta( $post->ID, 'fdm_menu_section_' . $section->term_id, true );
			if ( $alt_title ) {
				$section->name = $alt_title;
			}
		}
		?>

			<input type="hidden" id="fdm_menu_column_one" name="fdm_menu_column_one" value="<?php esc_attr_e( $column_one ); ?>">
			<input type="hidden" id="fdm_menu_column_two" name="fdm_menu_column_two" value="<?php esc_attr_e( $column_two ); ?>">

			<p><?php echo __( 'Drag-and-drop Menu Sections into columns on your menu.', 'food-and-drink-menu' ); ?></p>

			<div id="fdm-menu-organizer">
				<div class="fdm-column">
					<h3>
						<?php esc_html_e( 'Available Sections', 'food-and-drink-menu' ); ?>
					</h3>
					<?php if ( empty( $sections ) ) : ?>
						<div class="fdm-no-sections">
							<?php
								printf(
									__( "You don't have any Menu Sections yet. When you create Menu Items, you should %sassign them to Menu Sections%s.", 'food-and-drink-menu' ),
									'<a href="' . esc_url( 'http://doc.themeofthecrop.com/plugins/food-and-drink-menu/user/getting-started/create-menu' ) . '" target="_blank">',
									'</a>'
								);
							?>
						</div>

					<?php else : ?>
						<ul id="fdm-menu-sections-list" class="fdm-sortable-sections">
							<?php foreach( $sections as $section ) : ?>
								<li data-term-id="<?php esc_attr_e( $section->term_id ); ?>">
									<div class="fdm-title">
										<span class="fdm-term-count"><?php esc_html_e( $section->count ); ?></span>
										<span class="fdm-term-name"><?php esc_html_e( $section->name ); ?></span>
									</div>
									<a href="#" class="fdm-edit-section-name">
										<span class="screen-reader-text">
											<?php esc_html_e( 'Edit Section Name', 'food-and-drink-menu' ); ?>
										</span>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>
				<div class="fdm-column">
					<h3>
						<?php esc_html_e( 'First Column', 'food-and-drink-menu' ); ?>
					</h3>
					<ul id="fdm_menu_column_one_list" class="fdm-sortable-sections fdm-sections-added"></ul>
				</div>
				<div class="fdm-column">
					<h3>
						<?php esc_html_e( 'Second Column', 'food-and-drink-menu' ); ?>
					</h3>
					<ul id="fdm_menu_column_two_list" class="fdm-sortable-sections fdm-sections-added"></ul>
				</div>

				<p class="description">
					<?php esc_html_e( 'Hint: Leave the second column empty to display the menu in a single column.', 'food-and-drink-menu' ); ?>
				</p>
			</div>

		<?php

	}

	/**
	 * Print the metabox for selecting a page template for menus
	 *
	 * @since 1.5
	 */
	public function show_page_template() {

		$templates = array(
			'' => esc_html__( 'Default Menu Template', 'food-and-drink-menu' ),
		);

		$templates = $this->add_menu_templates( $templates );

		global $post;
		$selected = get_post_meta( $post->ID, '_wp_page_template', true );
		?>

			<select name="_wp_page_template">
				<?php foreach( $templates as $val => $label ) : ?>
					<option value="<?php esc_attr_e( $val ); ?>"<?php if ( $selected == $val ) : ?> selected<?php endif; ?>><?php esc_html_e( $label ); ?></option>
				<?php endforeach; ?>
			</select>
			<p>
				<?php esc_html_e( 'Choose any of your theme templates to display your menu.', 'food-and-drink-menu' ); ?>
			</p>

		<?php
	}

	/**
	 * Print the menu shortcode HTML on the edit page for easy reference
	 * @since 1.0
	 * @note We also add the fdm_nonce field here for security
	 */
	public function show_menu_shortcode() {

		// Retrieve post ID
		global $post;

		// Check if menu is ready to be displayed
		$status = get_post_status( $post->ID );

		// Show advisory note when shortcode isn't ready
		if ( $status != 'publish' ) {

			?>

			<p><?php echo __( 'Once this menu is published, look here to find the shortcode you will use to display this menu in any post or page.', 'food-and-drink-menu' ); ?></p>

			<?php

		// Show the shortcode
		} else {

			?>

				<p><?php echo __( 'Copy and paste the snippet below into any post or page in order to display this menu.', 'food-and-drink-menu' ); ?></p>
				<div class="fdm-menu-shortcode">[fdm-menu id=<?php echo $post->ID; ?>]</div>

			<?php

		}

		// Add an nonce here for security
		?>
		<input type="hidden" name="fdm_nonce" value="<?php echo wp_create_nonce( basename( __FILE__ ) ); ?>">
		<?php
	}

	/**
	 * Print the menu shortcode HTML on the edit page for easy reference
	 * @since 1.0
	 * @note We also add the fdm_nonce field here for security
	 */
	public function show_menu_item_shortcode() {

		// Retrieve post ID
		global $post;

		// Check if menu is ready to be displayed
		$status = get_post_status( $post->ID );

		// Show advisory note when shortcode isn't ready
		if ( $status != 'publish' ) {

			?>

			<p><?php echo __( 'Once this menu is published, look here to find the shortcode you will use to display this menu item in any post or page.', 'food-and-drink-menu' ); ?></p>

			<?php

		// Show the shortcode
		} else {

			?>

				<p><?php echo __( 'Copy and paste the snippet below into any post or page in order to display this menu.', 'food-and-drink-menu' ); ?></p>
				<div class="fdm-menu-shortcode">[fdm-menu-item id=<?php echo $post->ID; ?>]</div>

			<?php

		}

		// Add an nonce here for security
		?>
		<input type="hidden" name="fdm_nonce" value="<?php echo wp_create_nonce( basename( __FILE__ ) ); ?>">
		<?php
	}

	/**
	 * Save the metabox data from menu items and menus
	 * @since 1.0
	 */
	public function save_meta( $post_id ) {

		// Verify nonce
		if ( !isset( $_POST['fdm_nonce'] ) || !wp_verify_nonce( $_POST['fdm_nonce'], basename( __FILE__ ) ) ) {
			return $post_id;
		}

		// Check autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		// Check permissions
		if ( !current_user_can( 'edit_page', $post_id ) ) {
			return $post_id;
		} elseif ( !current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		// Array of values to fetch and store
		$meta_ids = array();

		global $post;

		// Define Menu Item data
		if ( FDM_MENUITEM_POST_TYPE == $post->post_type ) {

			$meta_ids['fdm_item_price'] = 'sanitize_text_field';

		}

		// Define Menu organizer metadata
		if ( FDM_MENU_POST_TYPE == $post->post_type ) {

			$meta_ids['fdm_menu_column_one'] = 'sanitize_text_field';
			$meta_ids['fdm_menu_column_two'] = 'sanitize_text_field';
			$meta_ids['fdm_menu_footer_content'] = 'wp_kses_post';

			// Custom section names for each menu
			$sections = array_filter(
				array_merge(
					isset( $_POST['fdm_menu_column_one'] ) ? explode( ',', $_POST['fdm_menu_column_one'] ) : array(),
					isset( $_POST['fdm_menu_column_two'] ) ? explode( ',', $_POST['fdm_menu_column_two'] ) : array()
				)
			);
			foreach( $sections as $section_id ) {
				if ( isset( $_POST['fdm_menu_section_' . absint( $section_id )] ) ) {
					$meta_ids['fdm_menu_section_' . absint( $section_id )] = 'sanitize_text_field';
				}
			}

			// Save menu templates for WP versions before 4.7
			if ( version_compare( get_bloginfo( 'version' ), '4.7-beta-1', '<' ) ) {
				$meta_ids['_wp_page_template'] = 'sanitize_file_name';
			}
		}

		// Create filter so addons can add new data
		$meta_ids = apply_filters( 'fdm_save_meta', $meta_ids );

		// Save the metadata
		foreach ($meta_ids as $meta_id => $sanitize_callback) {

			if ( $meta_id == 'fdm_item_price' ) {
				// If an out-of-date copy of Food and Drink Menu Pro is running,
				// we need to coerce the $POST value into an array.
				if ( isset( $_POST[$meta_id] ) && !is_array( $_POST[$meta_id] ) ) {
					$_POST[$meta_id] = array( $_POST[$meta_id] );
				}

				delete_post_meta( $post_id, $meta_id );
				$new = isset( $_POST[$meta_id] ) ? array_map( $sanitize_callback, $_POST[$meta_id] ) : array();
				foreach( $new as $new_entry ) {
					if ( $new_entry !== '' ) {
						add_post_meta( $post_id, $meta_id, $new_entry );
					}
				}
			} else {
				$cur = get_post_meta( $post_id, $meta_id, true );
				$new = isset( $_POST[$meta_id] ) ? call_user_func( $sanitize_callback, $_POST[$meta_id] ) : '';
				if ( $new && $new != $cur ) {
					update_post_meta( $post_id, $meta_id, $new );
				} elseif ( $new == '' && $cur ) {
					delete_post_meta( $post_id, $meta_id, $cur );
				}
			}
		}
	}

	/**
	 * Add the menu section column header to the admin list of menu items
	 * @since 1.4
	 */
	public function menu_item_posts_columns( $columns ) {
		return array(
			'cb'		=> '<input type="checkbox" />',
			'title'		=> __( 'Title' ),
			'price'		=> __( 'Price', 'food-and-drink-menu' ),
			'sections'	=> __( 'Sections', 'food-and-drink-menu' ),
			'shortcode'	=> __( 'Shortcode', 'food-and-drink-menu' ),
			'date'		=> __( 'Date' ),
		);
	}

	/**
	 * Make new column headers sortable
	 * @since 1.4
	 */
	public function menu_item_posts_sortable_columns( $columns ) {
		$columns['price'] = 'price';

		return $columns;
	}

	/**
	 * Modify query rules to sort on new columns
	 * @since 1.4
	 */
	public function menu_item_posts_orderby( $query ) {

		if ( !is_admin() ) {
			return;
		}

		$orderby = $query->get( 'orderby' );

		if ( $orderby == 'price' ) {
			$query->set( 'orderby', 'meta_value' );
			$query->set( 'meta_key', 'fdm_item_price' );
		}

	}

	/**
	 * Add the menu sections to the admin list of menu items
	 * @since 1.4
	 */
	public function menu_item_posts_columns_content( $column, $post ) {

		if ( $column == 'price' ) {
			$prices = (array) get_post_meta( $post, 'fdm_item_price' );
			if ( !empty( $prices ) ) {
				?>
				<div class="fdm-item-list-price" data-menu-item-id="<?php echo absint( $post ); ?>">
					<div class="fdm-item-price-summary">
						<?php
							echo join(
								apply_filters( 'fdm_prices_separator', _x( '/', 'Separator between multiple prices.', 'food-and-drink-menu' ) ),
								$prices
							);
						?>
					</div>
					<div class="fdm-item-price-actions">
						<a href="#" class="fdm-item-price-edit">
							<?php esc_html_e( 'Edit Price', 'food-and-drink-menu' ); ?>
						</a>
					</div>
					<div class="fdm-item-price-form">
						<?php $this->show_item_price(); ?>
						<div class="fdm-item-price-buttons">
							<span class="spinner"></span>
							<button class="fdm-item-price-save button" disabled="disabled">
								<?php esc_html_e( 'Update Price', 'food-and-drink-menu' ); ?>
							</button>
						</div>
						<div class="fdm-item-price-message"></div>
					</div>
				</div>
				<?php
			}
		}

		if ( $column == 'sections' ) {
			$terms = wp_get_post_terms( $post, 'fdm-menu-section' );
			$output = array();
			foreach( $terms as $term ) {
				$output[] = '<a href="' . admin_url( 'edit-tags.php?action=edit&taxonomy=fdm-menu-section&tag_ID=' . $term->term_taxonomy_id . '&post_type=fdm-menu-item' ) . '">' . $term->name . '</a>';
			}

			echo join( __( ', ', 'Separator in list of Menu Sections', 'food-and-drink-menu' ), $output );
		}

		if ( $column == 'shortcode' ) {
			echo '[fdm-menu-item id=' . $post . ']';
		}
	}

	/**
	 * Add a filter to view by menu section on the admin list of menu items
	 * @since 1.4
	 */
	public function menu_item_posts_filters() {

		if ( !is_admin() ) {
			return;
		}

		$screen = get_current_screen();
		if ( is_object( $screen ) && $screen->post_type == 'fdm-menu-item' ) {

			$terms = get_terms( 'fdm-menu-section' );

			if ( !empty( $terms ) ) : ?>
				<select name="section">
					<option value=""><?php _e( 'All sections', 'food-and-drink-menu' ); ?></option>

					<?php foreach( $terms as $term ) : ?>
					<option value="<?php echo esc_attr( $term->term_id ); ?>"<?php if( !empty( $_GET['section'] ) && $_GET['section'] == $term->term_id ) : ?> selected="selected"<?php endif; ?>><?php echo esc_attr( $term->name ); ?></option>
					<?php endforeach; ?>

					<option value="-1"><?php _e( 'Unassigned items', 'food-and-drink-menu' ); ?></option>
				</select>
			<?php endif;
		}
	}

	/**
	 * Apply selected filters to the admin list of menu items
	 * @since 1.4
	 */
	public function menu_item_posts_filter_query( $query ) {

		if ( !is_admin() || ( !empty( $query->query['post_type'] ) && $query->query['post_type'] !== FDM_MENUITEM_POST_TYPE ) || !function_exists( 'get_current_screen' ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( is_object( $screen ) && $screen->post_type == FDM_MENUITEM_POST_TYPE && !empty( $_GET['section'] ) ) {
			$section = (int) $_GET['section'];

			// Get menu items not assigned to any section
			if ( $section === -1 ) {
				$terms = get_terms( 'fdm-menu-section', array( 'fields' => 'ids' ) );
				$query->query_vars['tax_query'] = array(
					array(
						'taxonomy'	=> 'fdm-menu-section',
						'field'		=> 'id',
						'terms'		=> $terms,
						'operator'	=> 'NOT IN',
					)
				);

			// Get menu items from a specific section
			} else {
				$query->query_vars['tax_query'] = array(
					array(
						'taxonomy'	=> 'fdm-menu-section',
						'field'		=> 'id',
						'terms'		=> $section,
					)
				);
			}
		}
	}

	/**
	 * Add the menu sections column header to the admin list of menus
	 * @since 1.4
	 */
	public function menu_posts_columns( $columns ) {
		return array(
			'cb'		=> '<input type="checkbox" />',
			'title'		=> __( 'Title' ),
			'sections'	=> __( 'Sections', 'food-and-drink-menu' ),
			'shortcode'	=> __( 'Shortcode', 'food-and-drink-menu' ),
			'date'		=> __( 'Date' ),
		);
	}

	/**
	 * Add the menu sections to the admin list of menu items
	 * @since 1.4
	 */
	public function menu_posts_columns_content( $column, $post ) {

		if ( $column == 'shortcode' ) {
			echo '[fdm-menu id=' . $post . ']';
		}

		if ( $column == 'sections' ) {
			$post_meta = get_post_meta( $post );

			$col1 = !empty( $post_meta['fdm_menu_column_one'] ) ? array_filter( explode( ',', $post_meta['fdm_menu_column_one'][0] ) ) : array();
			$col2 = !empty( $post_meta['fdm_menu_column_two'] ) ? array_filter( explode( ',', $post_meta['fdm_menu_column_two'][0] ) ) : array();

			if ( !empty( $col1 ) || !empty( $col2 ) ) :
				$terms = get_terms( 'fdm-menu-section', array( 'include' => array_merge( $col1, $col2 ), 'fields' => 'id=>name' ) );
				?>

				<div class="fdm-cols">
					<div class="fdm-col">
						<?php foreach( $col1 as $id ) : ?>
							<?php if ( isset( $terms[ $id ] ) ) : ?>
								<a href="<?php echo esc_url( admin_url( 'edit-tags.php?action=edit&taxonomy=fdm-menu-section&tag_ID=' . $id . '&post_type=' . FDM_MENUITEM_POST_TYPE ) ); ?>">
									<?php echo $terms[ $id ]; ?>
								</a>
							<?php endif; ?>
						<?php endforeach; ?>
					</div>
					<div class="fdm-col">
						<?php foreach( $col2 as $id ) : ?>
							<?php if ( isset( $terms[ $id ] ) ) : ?>
								<a href="<?php echo esc_url( admin_url( 'edit-tags.php?action=edit&taxonomy=fdm-menu-section&tag_ID=' . $id . '&post_type=' . FDM_MENUITEM_POST_TYPE ) ); ?>">
									<?php echo $terms[ $id ]; ?>
								</a>
							<?php endif; ?>
						<?php endforeach; ?>
					</div>
				</div>

			<?php endif;
		}
	}

	/**
	 * Respond to unauthenticated ajax requests
	 *
	 * @since 1.5
	 */
	public function ajax_nopriv() {

		wp_send_json_error(
			array(
				'error' => 'loggedout',
				'msg' => sprintf( __( 'You have been logged out. Please %slogin again%s.', 'food-and-drink-menu' ), '<a href="' . wp_login_url( admin_url( 'edit.php?post_type=' . FDM_MENUITEM_POST_TYPE ) ) . '">', '</a>' ),
			)
		);
	}

	/**
	 * Respond to ajax requests with updated menu item prices
	 *
	 * @since 1.5
	 */
	public function ajax_menu_item_price() {

		// Authenticate request
		if ( !check_ajax_referer( 'fdm-admin', 'nonce' ) || !current_user_can( 'edit_posts' ) ) {
			$this->ajax_nopriv();
		}

		$id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
		if ( !get_post_type( $id ) ) {
			wp_send_json_error(
				array(
					'error'		=> 'menu_item_not_found',
					'msg'		=> __( 'This menu item could not be found. Please reload the page and try again.', 'food-and-drink-menu' ),
				)
			);
		}

		$prices = isset( $_POST['prices'] ) && is_array( $_POST['prices'] ) ? array_filter( array_map( 'sanitize_text_field', $_POST['prices'] ) ) : array();

		delete_post_meta( $id, 'fdm_item_price' );
		foreach( $prices as $price ) {
			add_post_meta( $id, 'fdm_item_price', $price );
		}

		$response = array(
			'id' => $id,
			'prices' => $prices,
			'price_summary' => join(
				apply_filters( 'fdm_prices_separator', _x( '/', 'Separator between multiple prices.', 'food-and-drink-menu' ) ),
				$prices
			),
		);

		$response = apply_filters( 'fdm_ajax_menu_item_price', $response, $id, $prices );

		wp_send_json_success( $response );
	}

	/**
	 * Add page templates to the list of available templates
	 *
	 * @param array $post_templates
	 * @param WP_Theme $wp_theme
	 * @param WP_Post|null $post
	 * @since 1.5
	 */
	public function add_menu_templates( $post_templates, $wp_theme = null, $post = null ) {

		$page_template = get_page_template();
		if ( !empty( $page_template )  ) {
			$post_templates['page.php'] = esc_html__( 'Default Page Template', 'food-and-drink-menu' );
		}

		$page_templates = get_page_templates();
		if ( $page_templates ) {
			$page_templates = array_flip( $page_templates );
			foreach( $page_templates as $file => $label ) {
				$post_templates[$file] = sprintf(
					esc_html__( 'Page Template: %s', 'food-and-drink-menu' ),
					$label
				);
			}
		}

		return $post_templates;
	}


	/**
	 * Optionally load a page template instead of the singular menu template
	 *
	 * @param string $template Requested file
	 * @since 1.5
	 */
	public function load_menu_template( $template ) {

		if ( is_singular( FDM_MENU_POST_TYPE ) ) {
			$new_template = get_post_meta( get_the_ID(), '_wp_page_template', true );
			$new_template_file = locate_template( $new_template );
			if ( $new_template_file  ) {
				return $new_template_file;
			}
		}

		return $template;
	}
}
