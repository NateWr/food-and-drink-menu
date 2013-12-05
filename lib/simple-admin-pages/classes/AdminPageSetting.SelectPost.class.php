<?php

/**
 * Register, display and save a selection with a drop-down list of any post type
 *
 * This setting accepts the following arguments in its constructor function.
 *
 * $args = array(
 *		'id'			=> 'setting_id', 	// Unique id
 *		'title'			=> 'My Setting', 	// Title or label for the setting
 *		'description'	=> 'Description', 	// Help text description
 *		'blank_option'	=> true, 			// Whether or not to show a blank option
 *		'args'			=> array();			// Arguments to pass to WordPress's get_post() function
 * );
 *
 * @since 1.0
 * @package Simple Admin Pages
 */

class sapAdminPageSettingSelectPost_1_0 extends sapAdminPageSetting_1_0 {

	public $sanitize_callback = 'sanitize_text_field';

	// Whether or not to display a blank option
	public $blank_option = true;

	/**
	 * An array of arguments accepted by get_posts().
	 * See: http://codex.wordpress.org/Template_Tags/get_posts
	 */
	public $args = array();

	/**
	 * Display this setting
	 * @since 1.0
	 */
	public function display_setting() {

		$this->args['posts_per_page'] = -1;
		$post_list = new WP_Query( $this->args );

		?>

			<select name="<?php echo $this->id; ?>" id="<?php echo $this->id; ?>">

				<?php if ( $this->blank_option === true ) : ?>
					<option></option>
				<?php endif; ?>

				<?php
					while( $post_list->have_posts() ) :
						$post_list->the_post();
				?>

					<option value="<?php echo esc_attr( get_the_ID() ); ?>"<?php if( $this->value == get_the_ID() ) : ?> selected="selected"<?php endif; ?>><?php echo esc_html( get_the_title( get_the_ID() ) ); ?></option>

				<?php endwhile; ?>

			</select>

		<?php

		$this->display_description();

	}

}
