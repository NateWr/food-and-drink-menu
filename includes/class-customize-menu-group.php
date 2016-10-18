<?php
/**
 * Customizer Menu Group Control Class
 *
 * A control that contains the logic for editing a Food and Drink Menu group
 * (column) which contains Menu Sections.
 *
 * @see WP_Customize_Control
 * @since 1.5
 */
class FDM_WP_Customize_Menu_Group extends WP_Customize_Control {
	/**
	 * Control type
	 *
	 * @since 1.5
	 */
	public $type = 'fdm_menu_group';

	/**
	 * Translatable strings
	 *
	 * @since 1.5
	 */
	public $i18n = array();

	/**
	 * Initialize
	 *
	 * @since 1.5
	 */
	public function __construct( $manager, $id, $args = array() ) {
		parent::__construct( $manager, $id, $args );
		$this->setup_i18n( $args );

		// To render the control templates, the customizer manager creates
		// a fake instantiation of each control with an id of `temp` and
		// then calls print_template on it. As a result, any hooks added in
		// the construct function will be hooked twice.
		if ( $this->id  == 'temp' ) {
			return;
		}

		add_action( 'customize_controls_print_footer_scripts', array( $this, 'load_control_template' ) );
		add_action( 'customize_update_content_layout', array( $this, 'save_to_post_content' ), 10, 2 );
	}

	/**
	 * Initialize translatable strings
	 *
	 * @since 1.5
	 * @param array $args List of arguments this control was instantiated with.
	 */
	public function setup_i18n( $args = array() ) {

		$i18n = array(
			'add_section' => __( 'Add Section', 'food-and-drink-menu' ),
		);

		if ( isset( $args ) && is_array( $args ) && isset( $args['i18n'] ) ) {
			$i18n = array_merge( $i18n, $args['i18n'] );
		}

		$this->i18n = apply_filters( 'fdm_customize_menu_group_i18n', $i18n, $this, $args );
	}

	/**
	 * Render a JS template for the content of the media control.
	 *
	 * This adds a range input below the media control.
	 *
	 * @since 1.5
	 */
	public function content_template() {
		?>

		<ul class="fdm-section-list"></ul>

		<div class="buttons">
			<a href="#" class="fdm-add-section button-secondary">
				{{ data.i18n.add_section }}
			</a>
		</div>

		<?php
	}

	/**
	 * Refresh the parameters passed to the JavaScript via JSON.
	 *
	 * @since 3.4.0
	 */
	public function to_json() {
		parent::to_json();

		$this->json['i18n'] = $this->i18n;
	}

	/**
	 * Active callback to determine whether control should be visible
	 *
	 * @return bool
	 * @since 1.5
	 */
	public function active_callback() {
		return fdm_customize_is_menu_post();
	}

	/**
	 * Print control templates for use in Backbone Views
	 *
	 * @return array
	 * @since 1.5
	 */
	public function load_control_template() {
		?>
		<script type="text/html" id="tmpl-fdm-menu-group"><?php include( FDM_PLUGIN_DIR . '/js/templates/menu-group.js' ); ?></script>
		<?php
	}

	/**
	 * Save values as post meta and render post_content
	 *
	 * @since 1.5
	 */
	public function save_to_post_content( $value, $setting ) {

		if ( !is_array( $value ) ) {
			return;
		}

		// @todo implement custom capability
		if ( !current_user_can( 'manage_options' ) ) {
			return;
		}
	}
}
