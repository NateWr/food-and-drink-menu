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

		// Define the HTML element to use
		$html_element = 'li';
		if ( $this->singular ) {
			$html_element = 'div';
		}
		
		ob_start();
		
		$template = fdm_find_template( 'menu-item' );
		if ( $template ) {
			include( $template );
		}
		
		$output = ob_get_clean();
		return $output;

	}
	
	/**
	 * Load item data
	 * @since 1.1
	 * @todo This duplicates code in View.Menu.class.php. That file should use
	 * this class to retrieve the meta data
	 */
	public function load_item() {
	
		if ( !isset( $this->id ) ) {
			return;
		}
		
		$item = get_post( $this->id );
		
		$data = array(
			'id' 		=> array( 'id' => $item->ID ),
			'title' 	=> array( 'value' => $item->post_title ),
			'content' 	=> array( 'value' => apply_filters('the_content', $item->post_content) ),
		);

		$image = wp_get_attachment_image_src( get_post_thumbnail_id( $item->ID ), 'fdm-item-thumb' );
		if ( isset( $image[0] ) ) {
			$data['image'] = array(
				'url'	=> $image[0],
				'title'	=> $item->post_title
			);
		}

		if ( !get_option( 'fdm-disable-price' ) ) {
			$data['price'] = array(
				'value' => get_post_meta( $item->ID, 'fdm_item_price', true )
			);
		}

		$data = apply_filters( 'fdm_item_data', $data );
		
		$this->parse_args( $data );
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
