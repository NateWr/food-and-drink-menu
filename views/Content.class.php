<?php

/**
 * Base class for any content type that will be printed on the front end.
 *
 * @since 1.1
 */

class fdmContent extends fdmBase {

	/**
	 * Render the content's HTML markup
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
