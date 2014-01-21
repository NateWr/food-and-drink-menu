<?php

/**
 * Base class for any view requested on the front end.
 *
 * @since 1.1
 */

class fdmView extends fdmBase {

	// Map types of content to the class which will render them
	public $content_map = array(
		'title'		=> 'fdmContentTitle',
		'content'	=> 'fdmContentContent',
		'price'		=> 'fdmContentPrice',
		'image'		=> 'fdmContentImage'
	);

	/**
	 * Initialize the class
	 * @since 1.1
	 */
	public function __construct( $args ) {

		// Parse the values passed
		$this->parse_args( $args );
		
		// Filter the content map so addons can customize what and how content
		// is output. Filters are specific to each view, so for this base view
		// you would use the filter 'fdm_content_map_fdmView'
		$this->content_map = apply_filters( 'fdm_content_map_' . get_class( $this ), $this->content_map );

	}

	/**
	 * Render the view and enqueue required stylesheets
	 *
	 * @note This function should always be overridden by an extending class
	 * @since 1.1
	 */
	public function render() {
		$this->set_error(
			array( 
				'type'		=> 'render() called on wrong class'
			)
		);
	}

}
