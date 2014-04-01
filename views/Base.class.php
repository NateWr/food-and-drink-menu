<?php

/**
 * Base class
 *
 * @since 1.1
 */

// Load library classes
require_once( FDM_PLUGIN_DIR . '/views/Style.class.php' );
require_once( FDM_PLUGIN_DIR . '/views/View.class.php' );
require_once( FDM_PLUGIN_DIR . '/views/View.Item.class.php' );
require_once( FDM_PLUGIN_DIR . '/views/View.Menu.class.php' );
require_once( FDM_PLUGIN_DIR . '/views/View.Section.class.php' );

class fdmBase {

	public $id = null;

	// Collect errors during processing
	public $errors = array();


	/**
	 * Initialize the class
	 * @since 1.1
	 */
	public function __construct( $args ) {

		// Parse the values passed
		$this->parse_args( $args );

	}

	/**
	 * Parse the arguments passed in the construction and assign them to
	 * internal variables.
	 * @since 1.1
	 */
	public function parse_args( $args ) {
		foreach ( $args as $key => $val ) {
			switch ( $key ) {

				case 'id' :
					$this->{$key} = esc_attr( $val );

				default :
					$this->{$key} = $val;

			}
		}
	}

	/**
	 * Set an error
	 * @since 1.0
	 */
	public function set_error( $error ) {
		$this->errors[] = array_merge(
			$error,
			array(
				'class'		=> get_class( $this ),
				'id'		=> $this->id,
				'backtrace'	=> debug_backtrace()
			)
		);
	}

}
