<?php if ( $this->is_singular() ) : ?>
<div class="fdm-menu fdm-menu-item">
<?php endif; ?>

	<?php if ( $this->is_singular() ) : ?>
	<div<?php echo fdm_format_classes( $classes ); ?>>
	<?php else : ?>
	<li<?php echo fdm_format_classes( $classes ); ?>>
	<?php endif; ?>

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

	<?php if ( $this->is_singular() ) : ?>
	</div>
	<?php else : ?>
	</li>
	<?php endif; ?>

<?php if ( $this->is_singular() ) : ?>
</div>
<?php endif; ?>