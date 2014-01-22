<ul<?php echo fdm_format_classes( $this->classes ); ?>>
	<li class="fdm-section-header">
		<h3><?php echo $this->title; ?></h3>

		<?php if ( $this->description ) : ?>
		<p><?php echo $this->description; ?></p>
		<?php endif; ?>

	</li>
	<?php echo $this->print_items(); ?>
</ul>