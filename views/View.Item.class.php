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
		$elements[] = 'title';
		if ( $this->content ) {
			$elements[] = 'content';
		}
		if ( isset( $this->image ) ) {
			$elements[] = 'image';
			$classes[] = 'fdm-item-has-image';
		}
		if ( isset( $this->price ) && $this->price ) {
			$elements[] = 'price';
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
		$elements = apply_filters( 'fdm_menu_item_elements', $elements, $this );
		$elements_order = apply_filters( 'fdm_menu_item_elements_order', $elements_order, $this );
		$classes = apply_filters( 'fdm_menu_item_classes', $classes, $this );

		// Capture output
		ob_start();
		$template = fdm_find_template( 'menu-item', $this );
		if ( $template ) {
			include( $template );
		}
		$output = ob_get_clean();

		return apply_filters( 'fdm_menu_item_output', $output, $this );

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
				$this->image = array(
					'url'	=> $image[0],
					'title'	=> $this->title
				);
			}
		}

		if ( !get_option( 'fdm-disable-price' ) ) {
			$this->price = array(
				'value' => get_post_meta( $this->id, 'fdm_item_price', true )
			);
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

		$this->title = array( 'value' => $this->post->post_title );
		$this->content = array( 'value' => apply_filters('the_content', $this->post->post_content) );
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
