<?php

/**
 * Class for any section view requested on the front end.
 *
 * @since 1.1
 */

class fdmViewSection extends fdmView {

	/**
	 * Initialize the class
	 * @since 1.1
	 */
	public function __construct( $args ) {

		// Parse the values passed
		$this->parse_args( $args );
	}

	/**
	 * Render the view and enqueue required stylesheets
	 * @since 1.1
	 */
	public function render() {
	?>

				<li class="fdm-section-header">
					<h3><?php echo $this->title; ?></h3>

					<?php if ( isset( $this->value ) && trim( $this->value ) ) : ?>
					<p><?php echo $this->value; ?></p>
					<?php endif; ?>

				</li>

	<?php
	}

}
