<?php

/**
 * Class for any menu view requested on the front end.
 *
 * @since 1.1
 */

class fdmViewMenu extends fdmView {

	public $groups = array();

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
	 * Render the view and enqueue required stylesheets
	 * @since 1.1
	 */
	public function render() {

		if ( !isset( $this->id ) ) {
			return;
		}

		$this->get_groups();
		if ( !count( $this->groups ) ) {
			return;
		}

		// Add any dependent stylesheets or javascript
		$this->enqueue_assets();

		// Add css classes to the menu list
		$this->classes = $this->menu_classes();

		$this->c = 0; // Columns count
		$this->s = 0; // Section count

		ob_start();
		$template = fdm_find_template( 'menu', $this );
		if ( $template ) {
			include( $template );
		}
		$output = ob_get_clean();

		return apply_filters( 'fdm_menu_output', $output, $this );
	}

	/**
	 * Print the sections of a menu group
	 *
	 * @note This just cleans up the template a bit
	 * @since 1.1
	 */
	public function print_group_sections() {

		$output = '';

		foreach ( $this->groups as $group ) {
			foreach ( $group as $section_id ) {

				$section = new fdmViewSection(
					array(
						'id' => $section_id,
						'order' => $this->s
					)
				);

				$output .= $section->render();

				$this->s++;

			}
		}

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
	public function column_classes( $classes = array() ) {
		$classes = array_merge(
			$classes,
			array(
				'fdm-column',
				'fdm-column-' . $this->c
			)
		);

		// Add a last column class
		if ( $this->c == ( count( $this->groups ) - 1 ) ) {
			$classes[] = 'fdm-column-last';
		}

		// Increment the column counter
		$this->c++;

		return apply_filters( 'fdm_menu_column_classes', $classes, $this );
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
