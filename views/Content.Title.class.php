<?php

/**
 * Menu Item title
 *
 * @since 1.1
 */

class fdmContentTitle extends fdmContent {

	public $value = null;

	/**
	 * Render the content's HTML markup
	 */
	public function render() {

		if ( isset( $this->value ) ) :
		?>

		<p class="fdm-item-title"><?php echo $this->value; ?></p>

		<?php
		endif;

	}

}
