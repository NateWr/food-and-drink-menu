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

	public $style = 'base';

	public $layout = 'classic';

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

		if ( !count( $this->items ) ) {
			$this->get_items();
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
	 */
	public function get_sections() {
		if ( !count( $this->groups ) ) {
			return;
		}

		foreach( $this->groups as $group ) {
			foreach( $group as $section_id ) {
				$section_query = new WP_Query( array(
					'post_type'      	=> 'fdm-menu-item',
					'posts_per_page' 	=> -1,
					'order'				=> 'ASC',
					'orderby'			=> 'menu_order',
					'tax_query'     	=> array(
						array(
							'taxonomy' => 'fdm-menu-section',
							'field'    => 'id',
							'terms'    => $section_id,
						),
					),
				));
				if ( count( $section_query->posts ) ) {
					$this->data['items'][$section_id] = $section_query->posts;
					$section = get_term( $section_id, 'fdm-menu-section' );
					$data = array(
							'id'			=> $section_id,
							'title'			=> $section->name,
							'value'		=> $section->description
					);
					foreach( $section_query->posts as $post ) {
						$data['posts'][] = $post->ID;
					}
					$this->sections[$data['id']] = new fdmViewSection( apply_filters( 'fdm_section_data', $data ) );
				}
			}
		}
	}

	/**
	 * Define the menu items for this menu
	 */
	public function get_items() {
		if ( !count( $this->sections) ) {
			return;
		}

		if ( !count( $this->data['items'] ) ) {
			return;
		}

		foreach ( $this->data['items'] as $section ) {
			foreach ( $section as $item ) {
				if ( isset( $this->items[$item->ID] ) ) {
					continue;
				}
				$data = array(
					'id' 		=> array( 'id' => $item->ID ),
					'title' 	=> array( 'value' => $item->post_title ),
					'content' 	=> array( 'value' => apply_filters('the_content', $item->post_content) ),
				);

				$image = wp_get_attachment_image_src( get_post_thumbnail_id( $item->ID ), 'fdm-item-thumb' );
				if ( isset( $image[0] ) ) {
					$data['image'] = array(
						'url'	=> $image[0],
						'title'	=> $item->post_title
					);
				}

				if ( !get_option( 'fdm-disable-price' ) ) {
					$data['price'] = array(
						'value' => get_post_meta( $item->ID, 'fdm_item_price', true )
					);
				}

				$data = apply_filters( 'fdm_item_data', $data );

				$this->items[$data['id']['id']] = $data;
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
		if ( !count( $this->groups ) || !count( $this->sections ) || !count( $this->items ) ) {
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
				$classes = $this->section_classes( $s, $section );
				?>

				<ul<?php echo fdm_format_classes( $classes ); ?>>
					<?php echo $this->sections[$section]->render(); ?>

					<?php
					foreach ( $this->sections[$section]->posts as $item_id ) {
						$item = new fdmViewItem( $this->items[$item_id] );
						$item->render();
					}
					?>

				</ul>

				<?php
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
