<?php

/**
 * Add a widget to display a menu item
 *
 * @since 1.0
 * @package Food and Drink Menu
 */
class fdmWidgetMenuItem extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 * @since 1.0
	 */
	function __construct() {

		parent::__construct(
			'fdm_widget_menu_item',
			__('Food and Drink Menu Item', 'food-and-drink-menu'),
			array( 'description' => __( 'Display a single item from your food and drink menu.', 'food-and-drink-menu' ), )
		);

	}

	/**
	 * Print the widget content
	 * @since 1.0
	 */
	public function widget( $args, $instance ) {

		// Get the settings
		$atts = array( 
			'id' => null,
			'layout' => 'classic'
		);
		if( isset( $instance['id'] ) ) {
			$atts['id'] = $instance['id'];
		}

		// Print the widget's HTML markup
		echo $args['before_widget'];
		if( isset( $instance['title'] ) ) {
			$title = apply_filters( 'widget_title', $instance['title'] );
			echo $args['before_title'] . $title . $args['after_title'];
		}
		echo fdm_menu_item_shortcode( $atts );
		echo $args['after_widget'];

	}

	/**
	 * Print the form to configure this widget in the admin panel
	 * @since 1.0
	 */
	public function form( $instance ) {
	
		$id = null;
		if ( isset( $instance['id'] ) ) {
			$id = $instance['id'];
		}

		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"> <?php _e( 'Title' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text"<?php if ( isset( $instance['title'] ) ) : ?> value="<?php echo esc_attr( $instance['title'] ); ?>"<?php endif; ?>>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'id' ); ?>"> <?php _e( 'Menu Item' ); ?></label>
			<select id="<?php echo $this->get_field_id( 'id' ); ?>" name="<?php echo $this->get_field_name( 'id' ); ?>">

				<?php

				$items = new WP_Query( array(
						'posts_per_page' 	=> -1,
						'post_type' 		=> 'fdm-menu-item'
					)
				);

				while( $items->have_posts() ) :
					$items->next_post();

				?>

				<option value="<?php echo $items->post->ID; ?>"<?php if ( $items->post->ID == $id ) : ?> selected<?php endif; ?>>
					<?php echo esc_attr( $items->post->post_title ); ?>
				</option>

				<?php

				endwhile;

				// Reset the loop so we don't interfere with normal template functions
				wp_reset_postdata();

				?>
			</select>
		</p>

		<?php

	}

	/**
	 * Sanitize and save the widget form values.
	 * @since 1.0
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = array();
		if ( !empty( $new_instance['id'] ) ) {
			$instance['id'] = intval( $new_instance['id'] );
		}
		if ( !empty( $new_instance['title'] ) ) {
			$instance['title'] = strip_tags( $new_instance['title'] );
		}

		return $instance;

	}

}
