<?php

/**
 * Menu Item content
 *
 * @since 1.1
 */

class fdmContentContent extends fdmContent {

	public $value = null;

	/**
	 * Render the content's HTML markup
	 */
	public function render() {

		if ( isset( $this->value ) && trim( $this->value ) ) :
		?>

		<div class="fdm-item-content">
			<?php echo $this->value; ?>
		</div>

		<?php
		endif;

	}

}
