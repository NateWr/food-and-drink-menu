<?php

/**
 * Menu Item image
 *
 * @since 1.1
 */

class fdmContentImage extends fdmContent {

	public $url = null;

	public $title = '';

	/**
	 * Render the content's HTML markup
	 */
	public function render() {

		if ( isset( $this->url ) ) :
		?>

		<img class="fdm-item-image" src="<?php echo esc_attr( $this->url ); ?>" title="<?php echo esc_attr( $this->title ); ?>" alt="<?php echo esc_attr( $this->title ); ?>">

		<?php
		endif;

	}

}
