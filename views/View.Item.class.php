<?php

/**
 * Class for any item view requested on the front end.
 *
 * @since 1.1
 */

class fdmViewItem extends fdmView {

	// The elements to display for this item
	public $elements = array( 'title' );

	/**
	 * Render the view and enqueue required stylesheets
	 * @since 1.1
	 */
	public function render() {

		if ( !isset( $this->id ) ) {
			return;
		}
		
		// Define css classes to add to this menu item
		$classes = array( 'fdm-item' );
		
		// Register elements to display
		$elements[] = 'title';
		if ( $this->content ) {
			$elements[] = 'content';
		}
		if ( isset( $this->image ) ) {
			$elements[] = 'image';
			$classes[] = 'fdm-item-has-image';
		}
		if ( isset( $this->price ) && $this->price ) {
			$elements[] = 'price';
			$classes[] = 'fdm-item-has-price';
		}
		
		// Define the order to print the elements' HTML
		$elements_order = array(
			'image',
			'title',
			'price',
			'content'
		);

		// Filter the elements and classes
		$elements = apply_filters( 'fdm_menu_item_elements', $elements, $this );
		$elements_order = apply_filters( 'fdm_menu_item_elements_order', $elements_order, $this );
		$classes = apply_filters( 'fdm_menu_item_classes', $classes, $this );
		?>
		
				<li<?php echo fdm_format_classes( $classes ); ?>>
					<?php do_action( 'fdm_menu_item_before', $this ); ?>
					<div class="fdm-item-panel">

					<?php
						// Loop through all the elements that have
						// been defined and call the function attached to each
						// element.
						foreach( $elements_order as $element ) {
							if ( in_array( $element, $elements ) ) {
								$class = $this->content_map[$element];
								if ( class_exists( $class ) ) {
									$content = new $class( $this->{$element} );
									$content->render();
								}
							}
						}
					?>

						<div class="clearfix"></div>
					</div>

					<?php do_action( 'fdm_menu_item_after', $this ); ?>

				</li>
	
		<?php
		
	}

}
