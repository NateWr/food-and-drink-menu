<?php

/**
 * Base class for any view requested on the front end.
 *
 * @since 1.1
 */

class fdmView extends fdmBase {

	// Map types of content to the template which will render them
	public $content_map = array(
		'title'		=> 'content/title',
		'content'	=> 'content/content',
		'price'		=> 'content/price',
		'image'		=> 'content/image'
	);
	
	// Menu layout type default
	public $layout = 'classic';

	public $style = 'base';

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

	/**
	 * Load a template file for views
	 *
	 * First, it looks in the current theme's /fdm-templates/ directory. Then it
	 * will check a parent theme's /fdm-templates/ directory. If nothing is found
	 * there, it will retrieve the template from the plugin directory.

	 * @since 1.1
	 * @param string template Type of template to load (eg - menu, menu-item)
	 */
	function find_template( $template ) {

		$locations = array(
			get_stylesheet_directory() . '/' . FDM_TEMPLATE_DIR . '/',
			get_template_directory() . '/' . FDM_TEMPLATE_DIR . '/',
			FDM_PLUGIN_DIR . '/' . FDM_TEMPLATE_DIR . '/'
		);

		if ( isset( $this->layout ) && $this->layout != 'classic' ) {
			$template .= '-' . $this->layout;
		}

		foreach ( $locations as $loc ) {
			if ( file_exists( $loc . $template . '.php' ) ) {
				return $loc . $template . '.php';
			}
		}

		return false;
	}

}
