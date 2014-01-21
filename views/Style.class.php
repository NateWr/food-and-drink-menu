<?php

/**
 * Class for any menu style
 *
 * Handles file dependencies, UI selection output, etc.
 *
 * @since 1.1
 */

class fdmStyle extends fdmBase {

	public $id = '';
	public $label = '';
	public $css = array();
	public $js = array();
	public $dependencies = array();
	
	/**
	 * Enqueue the stylesheets and javascript files
	 */
	public function enqueue_assets() {
		foreach( $this->css as $key => $file ) {
			wp_enqueue_style( 'fdm-css-' . $key, $file );
		}
		foreach( $this->js as $file ) {
			wp_enqueue_script('fdm-js-' . $key, $file, array( 'jquery' ), false,  true );
		}
	}

}