<?php

/**
 * Class for any item view requested on the front end.
 *
 * @since 1.1
 */

class fdmViewItem extends fdmView {

	// The elements to display for this item
	public $elements = array( 'title' );

	// Whether or not this is printed on its own or part of a menu
	public $singular = false;

	/**
	 * Render the view and enqueue required stylesheets
	 *
	 * @since 1.1
	 */
	public function render() {

		if ( !isset( $this->id ) ) {
			return;
		}

		// Gather data if it's not already set
		if ( !isset( $this->title ) ) {
			$this->load_item();
		}

		// Define css classes to add to this menu item
		$classes = array( 'fdm-item' );

		// Register elements to display
		// Each element is referenced by its variable name (key) and location
		// in the menu item where we want to print it (header, body or footer)
		$elements['title'] = 'body';
		if ( $this->content ) {
			$elements['content'] = 'body';
		}
		if ( isset( $this->image ) ) {
			$elements['image'] = 'body';
			$classes[] = 'fdm-item-has-image';
		}
		if ( isset( $this->price ) && $this->price ) {
			$elements['price'] = 'body';
			$classes[] = 'fdm-item-has-price';
		}

		// Define the order to print the elements' HTML
		$elements_order = array(
			'image',
			'title',
			'price',
			'content'
		);

		// Filter the elements and classes
		$this->elements = apply_filters( 'fdm_menu_item_elements', $elements, $this );
		$this->elements_order = apply_filters( 'fdm_menu_item_elements_order', $elements_order, $this );
		$this->classes = apply_filters( 'fdm_menu_item_classes', $classes, $this );

		// Add any dependent stylesheets or javascript
		$this->enqueue_assets();

		// Capture output
		ob_start();
		$template = $this->find_template( 'menu-item' );
		if ( $template ) {
			include( $template );
		}
		$output = ob_get_clean();

		return apply_filters( 'fdm_menu_item_output', $output, $this );

	}

	/**
	 * Print each of the menu item elements in the defined order
	 *
	 * @note This function just provides us with a cleaner template
	 * @since 1.1
	 */
	public function print_elements( $location ) {
	
		$output = '';
		
		foreach( $this->elements_order as $element ) {
			if ( isset( $this->elements[$element] ) && $this->elements[$element] == $location ) {

				// Load the template for this content type
				$template = $this->find_template( $this->content_map[$element] );

				ob_start();
				if ( $template ) {
					include( $template );
				}
				$element_output = ob_get_clean();

				$output .= apply_filters( 'fdm_element_output_' . $element, $element_output, $this );
			}
		}
		return $output;
	}

	/**
	 * Load item data
	 * @since 1.1
	 */
	public function load_item() {

		if ( !isset( $this->id ) ) {
			return;
		}

		// If no title is set, we need to gather the core post data first
		if ( !isset( $this->title ) ) {
			$this->get_data_from_post();
		}

		if ( !isset( $this->image ) ) {
			$image = wp_get_attachment_image_src( get_post_thumbnail_id( $this->id ), 'fdm-item-thumb' );
			if ( isset( $image[0] ) ) {
				$this->image = $image[0];
			}
		}

		if ( !get_option( 'fdm-disable-price' ) ) {
			$this->price = get_post_meta( $this->id, 'fdm_item_price', true );
		}

		do_action( 'fdm_load_item', $this );
	}

	/**
	 * Retrieves data from a post object if it exists or calls the db for it if
	 * not.
	 *
	 * @note This only retrieves core post data, not metadata. @sa load_item()
	 * @since 1.1
	 */
	public function get_data_from_post() {
		if ( !isset( $this->post ) ) {
			$this->post = get_post( $this->id );
		}

		$this->title = $this->post->post_title;
		$this->content = apply_filters('the_content', $this->post->post_content);
	}

	/**
	 * Check if this view is of a single item
	 * @since 1.1
	 */
	public function is_singular() {
		if ( isset( $this->singular ) && $this->singular === true ) {
			return true;
		}
		return false;
	}

}
