<?php

/**
 * Class for any menu view requested on the front end.
 *
 * @since 1.1
 */

class fdmViewMenu extends fdmView {

	public $max_columns = 2;

	public $groups = array();
	public $sections = array();
	public $items = array();

	// Store data to prevent duplicate database calls
	public $data = array();

	/**
	 * Retrieve data if it hasn't been retrieved already
	 * @since 1.1
	 */
	public function get_data() {
		if ( !count( $this->groups ) ) {
			$this->get_groups();
		}

		if ( !count( $this->sections ) ) {
			$this->get_sections();
		}
	}


	/**
	 * Define the groups for this menu and attach section ids to them
	 *
	 * @note Groups can represent columns or other groupings of sections
	 * @since 1.1
	 */
	public function get_groups() {

		$cols = array( 'one', 'two' );
		foreach ( $cols as $key => $col_num ) {
			$col = get_post_meta( $this->id, 'fdm_menu_column_' . $col_num, true );
			if ( trim( $col ) == '' ) {
				continue;
			} else {
				$this->groups[$key] = explode( ",", $col );
				array_pop( $this->groups[$key] );
			}
		}

		$this->groups = apply_filters( 'fdm_group_data', $this->groups );

	}

	/**
	 * Define the sections for this menu
	 * @since 1.1
	 */
	public function get_sections() {
		if ( !count( $this->groups ) ) {
			return;
		}

		foreach( $this->groups as $group ) {
			foreach( $group as $section_id ) {
				$this->sections[$section_id] = new fdmViewSection(
					array(
						'id' => $section_id
					)
				);
			}
		}
	}

	/**
	 * Render the view and enqueue required stylesheets
	 * @since 1.1
	 */
	public function render() {

		if ( !isset( $this->id ) ) {
			return;
		}

		$this->get_data();
		if ( !count( $this->groups ) || !count( $this->sections ) ) {
			return;
		}

		// Add any dependent stylesheets or javascript
		$this->enqueue_assets();

		// Add css classes to the menu list
		$classes = $this->menu_classes();

		// Capture output to return as one string
		// If we print directly here instead of capturing the output, the menu
		// will appear above any other content in the page/post
		ob_start();
		?>

		<ul id="<?php echo fdm_global_unique_id(); ?>"<?php echo fdm_format_classes( $classes ); ?>>

		<?php
		$c = 0; // Columns count
		$s = 0; // Section count
		foreach ( $this->groups as $group ) :
			$classes = $this->column_classes( $c, count( $this->groups ) );
			?>

			<li<?php echo fdm_format_classes( $classes ); ?>>

			<?php
			foreach ( $group as $section ) :
				$this->sections[$section]->order = $s;
				echo $this->sections[$section]->render();

				$s++;
			endforeach;
			?>

			</li>

			<?php
			$c++;
		endforeach;
		?>

		</ul>

		<?php
		$output = ob_get_clean();
		return $output;
	}

	/**
	 * Get the initial menu css classes
	 * @since 1.1
	 */
	public function menu_classes( $classes = array() ) {
		$classes = array_merge(
			$classes,
			array(
				'fdm-menu',
				'fdm-menu-' . $this->id,
				'fdm-columns-' . count( $this->groups ),
				'fdm-layout-' . esc_attr( $this->layout ),
				'clearfix'
			)
		);

		return apply_filters( 'fdm_menu_classes', $classes, $this );
	}

	/**
	 * Get the menu column css classes
	 * @since 1.1
	 */
	public function column_classes( $i, $total, $classes = array() ) {
		$classes = array_merge(
			$classes,
			array(
				'fdm-column',
				'fdm-column-' . $i
			)
		);

		// Add a last column class
		if ( $i == ( $total - 1 ) ) {
			$classes[] = 'fdm-column-last';
		}

		return apply_filters( 'fdm_menu_column_classes', $classes, $this, $i, $total );
	}

	/**
	 * Get the menu section css classes
	 * @since 1.1
	 */
	public function section_classes( $i, $section_id, $classes = array() ) {
		$classes = array_merge(
			$classes,
			array(
				'fdm-section',
				'fdm-section-' . $i,
				'fdm-sectionid-' . $section_id
			)
		);

		return apply_filters( 'fdm_menu_section_classes', $classes, $this, $i, $section_id );
	}

	/**
	 * Enqueue stylesheets
	 */
	public function enqueue_assets() {

		global $fdm_styles;

		foreach ( $fdm_styles as $style ) {
			if ( $this->style == $style->id ) {
				$style->enqueue_assets();
			}
		}
	}

}
