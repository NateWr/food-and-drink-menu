<?php if ( $this->title ) : ?>
<h3 class="fdm-menu-title"><?php echo $this->title; ?></h3>
<?php endif; ?>
<?php if ( $this->content ) { echo $this->content; } ?>
<ul id="<?php echo fdm_global_unique_id(); ?>"<?php echo fdm_format_classes( $this->classes ); ?>>

<?php foreach ( $this->groups as $group ) :	?>

	<li<?php echo fdm_format_classes( $this->column_classes() ); ?>>

	<?php echo $this->print_group_section( $group ); ?>

	</li>

<?php endforeach; ?>

</ul>