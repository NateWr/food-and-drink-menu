<?php

/**
 * Class for any section view requested on the front end.
 *
 * @since 1.1
 */

class fdmViewSection extends fdmView {

	public $title = '';
	public $description = '';

	// Full menu object to capture the section's post data
	public $menu = null;

	/**
	 * Initialize the class
	 * @since 1.1
	 */
	public function __construct( $args ) {

		// Parse the values passed
		$this->parse_args( $args );
	}

	/**
	 * Render the view and enqueue required stylesheets
	 * @since 1.1
	 */
	public function render() {

		if ( !isset( $this->id ) ) {
			return;
		}

		// Gather data if it's not already set
		$this->load_section();

		if ( !isset( $this->items ) || ( is_array( $this->items ) && !count( $this->items ) ) ) {
			return;
		}

		// Define the classes for this section
		$this->set_classes();

		// Capture output
		ob_start();
		$template = $this->find_template( 'menu-section' );
		if ( $template ) {
			include( $template );
		}
		$output = ob_get_clean();

		return apply_filters( 'fdm_menu_section_output', $output, $this );
	}

	/**
	 * Print the menu items in this section
	 *
	 * @note This just cleans up the template file a bit
	 * @since 1.1
	 */
	public function print_items() {
		$output = '';
		if ( isset( $this->items ) && is_array( $this->items ) ) {
			foreach ( $this->items as $item ) {
				$output .= $item->render();
			}
		}
		return $output;
	}

	/**
	 * Load section data
	 * @since 1.1
	 */
	public function load_section() {

		if ( !isset( $this->id ) ) {
			return;
		}

		// Make sure the section has posts before we load the data.
		$items = new WP_Query( array(
			'post_type'      	=> 'fdm-menu-item',
			'posts_per_page' 	=> -1,
			'order'				=> 'ASC',
			'orderby'			=> 'menu_order',
			'tax_query'     	=> array(
				array(
					'taxonomy' => 'fdm-menu-section',
					'field'    => 'term_id',
					'terms'    => $this->id,
				),
			),
		));
		if ( !count( $items->posts ) ) {
			return;
		}

		// We go ahead and store all the posts data now to save on db calls
		$this->items = array();
		foreach( $items->posts as $item ) {
			$this->items[] = new fdmViewItem(
				array(
					'id' => $item->ID,
					'post' => $item
				)
			);
		}

		if ( !$this->title ) {
			$section = get_term( $this->id, 'fdm-menu-section' );
			$this->title = $section->name;
			$this->slug = $section->slug;
			$this->description = $section->description;
		}

		// Load any custom title that has been set for display in this menu
		if ( isset( $this->menu ) && get_class( $this->menu ) == 'fdmViewMenu' ) {
			$menu_post_meta = get_post_meta( $this->menu->id );

			if ( isset( $menu_post_meta['fdm_menu_section_' . $this->id ] ) ) {
				$this->title = $menu_post_meta['fdm_menu_section_' . $this->id ][0];
			}
		}

		do_action( 'fdm_load_section', $this );

	}

	/**
	 * Set the menu section css classes
	 * @since 1.1
	 */
	public function set_classes( $classes = array() ) {
		$classes = array_merge(
			$classes,
			array(
				'fdm-section',
				'fdm-sectionid-' . $this->id,
				'fdm-section-' . $this->slug,
			)
		);

		// Order of this section appearing on this menu
		if ( isset( $this->order ) ) {
			$classes[] = 'fdm-section-' . $this->order;
		}

		$this->classes = apply_filters( 'fdm_menu_section_classes', $classes, $this );
	}

}
