<?php

/**
 * @package Food and Drink Menu
 */

/**
 * Flush the rewrite rules so that the post type URL slug is enabled on
 * activation. See: http://codex.wordpress.org/Function_Reference/register_post_type#Flushing_Rewrite_on_Activation
 * @since 1.0
 */
function fdm_rewrite_flush() {
    fdm_plugin_init();
    flush_rewrite_rules();
}

/**
 * Enqueue the admin-only CSS and Javascript
 * @since 1.0
 */
function fdm_enqueue_admin_scripts() {
	wp_enqueue_script( 'fdm-admin', FDM_PLUGIN_URL . '/assets/js/admin.js', array( 'jquery' ), '1.0', true );
	wp_enqueue_style( 'fdm-admin', FDM_PLUGIN_URL . '/assets/css/admin.css', array(), '1.0' );

	// Backwards compatibility for old admin icons
	global $wp_version;
	if ( $wp_version < 3.8 ) {
		wp_enqueue_style( 'fdm-admin-compat-3.8', FDM_PLUGIN_URL . '/assets/css/admin-compat-3.8.css', array(), '1.0' );
	}

}
add_action( 'admin_enqueue_scripts', 'fdm_enqueue_admin_scripts' );

/**
 * Create all content types and taxonomies, and register the settings page in
 * the admin panel
 * @since 1.0
 */
function fdm_plugin_init() {

	// Define the menu taxonomies
	$menu_taxonomies = array();

	// Create filter so addons can modify the taxonomies
	$menu_taxonomies = apply_filters( 'fdm_menu_taxonomies', $menu_taxonomies );

	// Define the menu custom post type
	$args = array(
		'exclude_from_search' => true,
        'labels' => array(
            'name' => _x( 'Menus', FDM_TEXTDOMAIN ),
            'singular_name' => _x( 'Menu', FDM_TEXTDOMAIN ),
            'add_new' => __( 'Add Menu', FDM_TEXTDOMAIN ),
            'add_new_item' => __( 'Add New Menu', FDM_TEXTDOMAIN ),
            'edit' => __( 'Edit', FDM_TEXTDOMAIN ),
            'edit_item' => __( 'Edit Menu', FDM_TEXTDOMAIN ),
            'new_item' => __( 'New Menu', FDM_TEXTDOMAIN ),
            'view' => __( 'View', FDM_TEXTDOMAIN ),
            'view_item' => __( 'View Menu', FDM_TEXTDOMAIN ),
            'search_items' => __( 'Search Menus', FDM_TEXTDOMAIN ),
            'not_found' => __( 'No Menu found', FDM_TEXTDOMAIN ),
            'not_found_in_trash' => __( 'No Menu found in Trash', FDM_TEXTDOMAIN ),
            'parent' => __( 'Parent Menu', FDM_TEXTDOMAIN )
        ),
        'menu_position' => 15,
        'public' => true,
        'rewrite' => array( 'slug' => 'menu' ),
        'supports' => array(
            'title',
            'editor',
            'revisions'
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

	// Define the menu item taxonomies
	$menu_item_taxonomies = array(

		// Create menu sections (desserts, entrees, etc)
		'fdm-menu-section'	=> array(
			'hierarchy'	=> true,
			'labels' 	=> array(
				'name' => _x( 'Menu Sections', 'taxonomy general name', FDM_TEXTDOMAIN ),
				'singular_name' => _x( 'Menu Section', 'taxonomy singular name', FDM_TEXTDOMAIN ),
				'search_items' => __( 'Search Menu Sections', FDM_TEXTDOMAIN ),
				'all_items' => __( 'All Menu Sections', FDM_TEXTDOMAIN ),
				'parent_item' => __( 'Menu Section', FDM_TEXTDOMAIN ),
				'parent_item_colon' => __( 'Menu Section:', FDM_TEXTDOMAIN ),
				'edit_item' => __( 'Edit Menu Section', FDM_TEXTDOMAIN ),
				'update_item' => __( 'Update Menu Section', FDM_TEXTDOMAIN ),
				'add_new_item' => __( 'Add New Menu Section', FDM_TEXTDOMAIN ),
				'new_item_name' => __( 'Menu Section', FDM_TEXTDOMAIN ),
			)
		)

	);

	// Create filter so addons can modify the taxonomies
	$menu_item_taxonomies = apply_filters( 'fdm_menu_item_taxonomies', $menu_item_taxonomies );

	// Register taxonomies
	foreach( $menu_item_taxonomies as $id => $taxonomy ) {
		register_taxonomy(
			$id,
			'',
			$taxonomy
		);
	}

	// Define the Menu Item custom post type
	$args = array(
		'exclude_from_search' => true,
        'labels' => array(
            'name' => _x( 'Menu Items', FDM_TEXTDOMAIN ),
            'singular_name' => _x( 'Menu Item', FDM_TEXTDOMAIN ),
            'add_new' => __( 'Add Menu Item', FDM_TEXTDOMAIN ),
            'add_new_item' => __( 'Add New Menu Item', FDM_TEXTDOMAIN ),
            'edit' => __( 'Edit', FDM_TEXTDOMAIN ),
            'edit_item' => __( 'Edit Menu Item', FDM_TEXTDOMAIN ),
            'new_item' => __( 'New Menu Item', FDM_TEXTDOMAIN ),
            'view' => __( 'View', FDM_TEXTDOMAIN ),
            'view_item' => __( 'View Menu Item', FDM_TEXTDOMAIN ),
            'search_items' => __( 'Search Menu Items', FDM_TEXTDOMAIN ),
            'not_found' => __( 'No Menu Item found', FDM_TEXTDOMAIN ),
            'not_found_in_trash' => __( 'No Menu Item found in Trash', FDM_TEXTDOMAIN ),
            'parent' => __( 'Parent Menu Item', FDM_TEXTDOMAIN )
        ),
        'menu_position' => 15,
        'public' => true,
        'rewrite' => array( 'slug' => 'menu-item' ),
        'supports' => array(
            'title',
            'editor',
            'thumbnail',
            'revisions',
            'page-attributes'
        ),
        'taxonomies' => array_keys( $menu_item_taxonomies )
    );

	// Add an action so addons can hook in before the menu is registered
	do_action( 'fdm_menu_item_pre_register' );

	// Register the menu item post type
	register_post_type( 'fdm-menu-item', $args );

	// Add an action so addons can hook in after the menu is registered
	do_action( 'fdm_menu_item_post_register' );

	// Define supported styles
	require_once( 'views/Base.class.php' );
	global $fdm_styles;
	$fdm_styles = array(
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
	$fdm_styles = apply_filters( 'fdm_styles', $fdm_styles );

	// Insantiate the Simple Admin Library so that we can add a settings page
	$sap = sap_initialize_library(
		array(
			'version'		=> '1.1', // Version of the library
			'lib_url'		=> FDM_PLUGIN_URL . '/lib/simple-admin-pages/', // URL path to sap library
		)
	);

	// Create a page for the options under the Settings (options) menu
	$sap->add_page(
		'options', 				// Admin menu which this page should be added to
		array(					// Array of key/value pairs matching the AdminPage class constructor variables
			'id'			=> 'food-and-drink-menu-settings',
			'title'			=> __( 'Food and Drink Menu', FDM_TEXTDOMAIN ),
			'menu_title'	=> __( 'Food and Drink Menu', FDM_TEXTDOMAIN ),
			'description'	=> '',
			'capability'	=> 'manage_options'
		)
	);

	// Create a section to choose a default style
	$sap->add_section(
		'food-and-drink-menu-settings',	// Page to add this section to
		array(								// Array of key/value pairs matching the AdminPageSection class constructor variables
			'id'			=> 'fdm-style-settings',
			'title'			=> __( 'Style', FDM_TEXTDOMAIN ),
			'description'	=> __( 'Choose what style you would like to use for your menu.', FDM_TEXTDOMAIN )
		)
	);
	$options = array();
	foreach( $fdm_styles as $style ) {
		$options[$style->id] = $style->label;
	}
	$sap->add_setting(
		'food-and-drink-menu-settings',
		'fdm-style-settings',
		'select',
		array(
			'id'			=> 'fdm-style',
			'title'			=> __( 'Style', FDM_TEXTDOMAIN ),
			'description'	=> __( 'Choose the styling for your menus.', FDM_TEXTDOMAIN ),
			'blank_option'	=> false,
			'options'		=> $options
		)
	);

	// Create a section to disable specific features
	$sap->add_section(
		'food-and-drink-menu-settings',	// Page to add this section to
		array(								// Array of key/value pairs matching the AdminPageSection class constructor variables
			'id'			=> 'fdm-enable-settings',
			'title'			=> __( 'Disable Features', FDM_TEXTDOMAIN ),
			'description'	=> __( 'Choose what features of the menu items you wish to disable and hide from the admin interface.', FDM_TEXTDOMAIN )
		)
	);
	$sap->add_setting(
		'food-and-drink-menu-settings',
		'fdm-enable-settings',
		'toggle',
		array(
			'id'			=> 'fdm-disable-price',
			'title'			=> __( 'Price', FDM_TEXTDOMAIN ),
			'label'			=> __( 'Disable all pricing options for menu items.', FDM_TEXTDOMAIN )
		)
	);

	// Create a section for advanced options
	$sap->add_section(
		'food-and-drink-menu-settings',	// Page to add this section to
		array(								// Array of key/value pairs matching the AdminPageSection class constructor variables
			'id'			=> 'fdm-advanced-settings',
			'title'			=> __( 'Advanced Options', FDM_TEXTDOMAIN )
		)
	);
	$sap->add_setting(
		'food-and-drink-menu-settings',
		'fdm-advanced-settings',
		'text',
		array(
			'id'			=> 'fdm-item-thumb-width',
			'title'			=> __( 'Menu Item Photo Width', FDM_TEXTDOMAIN ),
			'description'	=> __( 'The width in pixels of menu item thumbnails automatically generated by WordPress. Leave this field empty to preserve the default (600).', FDM_TEXTDOMAIN )
		)
	);
	$sap->add_setting(
		'food-and-drink-menu-settings',
		'fdm-advanced-settings',
		'text',
		array(
			'id'			=> 'fdm-item-thumb-height',
			'title'			=> __( 'Menu Item Photo Height', FDM_TEXTDOMAIN ),
			'description'	=> __( 'The height in pixels of menu item thumbnails automatically generated by WordPress. Leave this field empty to preserve the default (600).', FDM_TEXTDOMAIN )
		)
	);

	// Create filter so addons can modify the settings page or add new pages
	$sap = apply_filters( 'fdm_settings_page', $sap );

	// Register all admin pages and settings with WordPress
	$sap->add_admin_menus();

	// Add filters on the menu style so we can apply the setting option
	add_filter( 'fdm_menu_args', 'fdm_set_style' );
	add_filter( 'fdm_shortcode_menu_atts', 'fdm_set_style' );
	add_filter( 'fdm_shortcode_menu_item_atts', 'fdm_set_style' );

}

/**
 * Order the menu items by menu order in the admin interface
 * @since 1.0
 */
function fdm_admin_order_posts( $query ) {

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
add_filter( 'pre_get_posts', 'fdm_admin_order_posts' );

/**
 * Add a metabox to specify custom post type data
 * @since 1.0
 */
function fdm_add_meta_boxes() {

	$meta_boxes = array(

		// Add a menu organizer
		'fdm_menu_layout' => array (
			'id'		=>	'fdm_menu_layout',
			'title'		=> __( 'Menu Layout', FDM_TEXTDOMAIN ),
			'callback'	=> 'fdm_show_menu_organizer',
			'post_type'	=> 'fdm-menu',
			'context'	=> 'normal',
			'priority'	=> 'default'
		),

		// Add a box that shows menu shortcode
		'fdm_menu_shortcode' => array (
			'id'		=>	'fdm_menu_shortcode',
			'title'		=> __( 'Menu Shortcode', FDM_TEXTDOMAIN ),
			'callback'	=> 'fdm_show_menu_shortcode',
			'post_type'	=> 'fdm-menu',
			'context'	=> 'side',
			'priority'	=> 'default'
		)

	);

	// Add menu item price metabox
	if ( !get_option( 'fdm-disable-price' ) ) {
		$meta_boxes['fdm_menu_item_price'] = array (
			'id'		=>	'fdm_item_price',
			'title'		=> __( 'Price', FDM_TEXTDOMAIN ),
			'callback'	=> 'fdm_show_item_price',
			'post_type'	=> 'fdm-menu-item',
			'context'	=> 'side',
			'priority'	=> 'default'
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

	// Remove Attributes metabox from menu organizer
	remove_meta_box( 'pageparentdiv', 'fdm-menu', 'side' );
}
add_action( 'add_meta_boxes', 'fdm_add_meta_boxes' );

/**
 * Print the Menu Item price metabox HTML
 * @since 1.0
 */
function fdm_show_item_price() {

	// Retrieve values for this if it exists
	global $post;
	$price = get_post_meta( $post->ID, 'fdm_item_price', true );

	?>

		<input type="hidden" name="fdm_nonce" value="<?php echo wp_create_nonce( basename( __FILE__ ) ); ?>">

		<div class="fdm-input-controls fdm-input-side-panel">
			<div class="fdm-input-control">
				<label for="fdm_item_price"><?php echo __( 'Price', FDM_TEXTDOMAIN ); ?></label>
				<input type="text" name="fdm_item_price" id="fdm_item_price" value="<?php echo esc_attr( $price ); ?>">
			</div>

			<?php do_action( 'fdm_show_item_price' ); ?>

		</div>

	<?php
}

/**
 * Print the Menu organizer HTML
 * @since 1.0
 */
function fdm_show_menu_organizer() {

	// Retrieve existing settings
	global $post;
	$column_one = get_post_meta( $post->ID, 'fdm_menu_column_one', true );
	$column_two = get_post_meta( $post->ID, 'fdm_menu_column_two', true );

	// Retrieve sections and store in HTML lists
	$terms = get_terms( 'fdm-menu-section', array( 'hide_empty' => false ) );
	$sections_list = '';
	foreach ( $terms as $term ) {
		$sections_list .= '<li><a href="#" data-termid="' . $term->term_id . '">' . $term->name . ' (' . $term->count . ')</a></li>';
	}

	?>

		<input type="hidden" name="fdm_nonce" value="<?php echo wp_create_nonce( basename( __FILE__ ) ); ?>">
		<input type="hidden" id="fdm_menu_column_one" name="fdm_menu_column_one" value="<?php echo $column_one; ?>">
		<input type="hidden" id="fdm_menu_column_two" name="fdm_menu_column_two" value="<?php echo $column_two; ?>">

		<p><?php echo __( 'Click on a Menu Section to add it to this menu.', FDM_TEXTDOMAIN ); ?></p>

		<div id="fdm-menu-organizer">

			<div id="fdm-menu-column-one">
				<div class="fdm-column fdm-options">
					<h4><?php echo __( 'Menu Sections', FDM_TEXTDOMAIN ); ?></h4>
					<ul>
						<?php echo $sections_list; ?>
					</ul>
				</div>
				<div class="fdm-column fdm-added">
					<h4><?php echo __( 'First Column', FDM_TEXTDOMAIN ); ?></h4>
					<ul>
					<!-- List filled with values on page load. see admin.js -->
					</ul>
				</div>
			</div>

			<div id="fdm-menu-column-two">
				<div class="fdm-column fdm-added">
					<h4><?php echo __( 'Second Column', FDM_TEXTDOMAIN ); ?></h4>
					<ul>
					<!-- List filled with values on page load. see admin.js -->
					</ul>
				</div>
				<div class="fdm-column fdm-options">
					<h4><?php echo __( 'Menu Sections', FDM_TEXTDOMAIN ); ?></h4>
					<ul>
						<?php echo $sections_list; ?>
					</ul>
				</div>
			</div>

			<div class="clearfix"></div>

		</div>

		<p><?php echo __( 'Hint: Leave the second column empty to display the menu in a single column.', FDM_TEXTDOMAIN ); ?></p>

	<?php

}

/**
 * Print the menu shortcode HTML on the edit page for easy reference
 * @since 1.0
 */
function fdm_show_menu_shortcode() {

	// Retrieve post ID
	global $post;

	// Check if menu is ready to be displayed
	$status = get_post_status( $post->ID );

	// Show advisory note when shortcode isn't ready
	if ( $status != 'publish' ) {

		?>

		<p><?php echo __( 'Once this menu is published, look here to find the shortcode you will use to display this menu in any post or page.', FDM_TEXTDOMAIN ); ?></p>

		<?php

	// Show the shortcode
	} else {

		?>

			<p><?php echo __( 'Copy and paste the snippet below into any post or page in order to display this menu.', FDM_TEXTDOMAIN ); ?></p>
			<div class="fdm-menu-shortcode">[fdm-menu id=<?php echo $post->ID; ?>]</div>

		<?php

	}

}

/**
 * Save the metabox data from menu items and menus
 * @since 1.0
 */
function fdm_save_meta( $post_id ) {

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

	// Define Menu Item data
	if ( 'fdm-menu-item' == $_POST['post_type'] ) {

		$meta_ids['fdm_item_price'] = 'sanitize_text_field';

	}

	// Define Menu organizer metadata
	if ( 'fdm-menu' == $_POST['post_type'] ) {

		$meta_ids['fdm_menu_column_one'] = 'sanitize_text_field';
		$meta_ids['fdm_menu_column_two'] = 'sanitize_text_field';

	}

	// Create filter so addons can add new data
	$meta_ids = apply_filters( 'fdm_save_meta', $meta_ids );

	// Save the metadata
	foreach ($meta_ids as $meta_id => $sanitize_callback) {
		$cur = get_post_meta( $post_id, $meta_id, true );
		$new = call_user_func( $sanitize_callback, $_POST[$meta_id] );
		if ( $new && $new != $cur ) {
			update_post_meta( $post_id, $meta_id, $new );
		} elseif ( $new == '' && $cur ) {
			delete_post_meta( $post_id, $meta_id, $cur );
		}
	}

}
add_action( 'save_post', 'fdm_save_meta' );

/**
 * Create a shortcode to display a menu
 * @since 1.0
 */
function fdm_menu_shortcode( $atts ) {

	// Define shortcode attributes
	$menu_atts = array(
		'id' => null,
		'layout' => 'classic'
	);

	// Create filter so addons can modify the accepted attributes
	$menu_atts = apply_filters( 'fdm_shortcode_menu_atts', $menu_atts );

	// Extract the shortcode attributes
	$args = shortcode_atts( $menu_atts, $atts );

	// Render menu
	fdm_load_view_files();
	$menu = new fdmViewMenu( $args );
	echo $menu->render();
}
add_shortcode( 'fdm-menu', 'fdm_menu_shortcode' );

/**
 * Create a shortcode to display a menu item
 * @since 1.1
 */
function fdmp_menu_item_shortcode( $atts ) {

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
	echo $menuitem->render();
}
add_shortcode( 'fdm-menu-item', 'fdmp_menu_item_shortcode' );

/**
 * Print the menu on menu post type pages
 * @since 1.1
 */
function fdm_cpt_menu_content( $content ) {
	global $post;

	if ( ( FDM_MENU_POST_TYPE !== $post->post_type && FDM_MENUITEM_POST_TYPE !== $post->post_type )
			|| !is_main_query() ) {
		return $content;
	}

	// We must disable this filter while we're rendering the menu in order to
	// prevent it from falling into a recursive loop with each menu item's
	// content.
	remove_action( 'the_content', 'fdm_cpt_menu_content' );

	fdm_load_view_files();

	$args = array(
		'id'	=> $post->ID
	);
	if ( FDM_MENUITEM_POST_TYPE == $post->post_type ) {
		$args['singular'] = true;
	}
	$args = apply_filters( 'fdm_menu_args', $args );

	if ( FDM_MENU_POST_TYPE == $post->post_type ) {
		$menu = new fdmViewMenu( $args );
		$content .= $menu->render();
	} else {
		$menu = new fdmViewItem( $args );
		$content = $menu->render();
	}

	// Restore this filter
    add_action( 'the_content', 'fdm_cpt_menu_content' );

	return $content;

}
add_filter( 'the_content', 'fdm_cpt_menu_content' );

/**
 * Set the style of a menu or menu item before rendering
 * @since 1.1
 */
function fdm_set_style( $args ) {

	$style = get_option( 'fdm-style' );
	if ( !$style ) {
		$style = 'base';
	}
	$args['style'] = $style;

	return $args;
}

/**
 * Load files needed for views
 * @since 1.1
 */
function fdm_load_view_files() {
	require_once( 'views/Base.class.php' );
}

/**
 * Tranform an array of CSS classes into an HTML attribute
 * @since 1.0
 */
function fdm_format_classes($classes) {
	if (count($classes)) {
		return ' class="' . join(" ", $classes) . '"';
	}
}

/*
 * Assign a globally unique id for each displayed menu
 */
$fdm_global_ids = 0;
function fdm_global_unique_id() {
	global $fdm_global_ids;
	return 'fdm-menu-' . $fdm_global_ids++;
}

/**
 * Add links to the plugin listing on the installed plugins page
 * @since 1.0
 */
function fdm_plugin_action_links( $links, $plugin ) {

    if ( $plugin == FDM_PLUGIN_FNAME ) {

		$links['help'] = '<a href="' . FDM_PLUGIN_URL . '/docs" title="' . __( 'View the help documentation for Food and Drink Menu', FDM_TEXTDOMAIN ) . '">' . __( 'Help', FDM_TEXTDOMAIN ) . '</a>';

		if ( !defined( 'FDMP_VERSION' ) ) {
			$links['upgrade'] = '<a href="' . FDM_UPGRADE_URL . '" title="' . __( 'Upgrade to Food and Drink Pro', FDM_TEXTDOMAIN ) . '">' . __( 'Upgrade', FDM_TEXTDOMAIN ) . '</a>';
		}
    }

    return $links;

}
add_filter('plugin_action_links', 'fdm_plugin_action_links', 10, 2);
