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

			<?php echo $this->print_elements(); ?>
		
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