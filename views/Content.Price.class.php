<?php

/**
 * Menu Item price
 *
 * @since 1.1
 */

class fdmContentPrice extends fdmContent {

	public $value = null;

	/**
	 * Render the content's HTML markup
	 */
	public function render() {

		if ( isset( $this->value ) ) :
		?>

		<div class="fdm-item-price-wrapper">
			<span class="fdm-item-price"><?php echo $this->value; ?></span>
		</div>

		<?php
		endif;

	}

}
