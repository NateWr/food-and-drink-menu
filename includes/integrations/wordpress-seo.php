<?php defined( 'ABSPATH' ) || die;
/**
 * Functions to integrate with the SEO by Yoast plugin
 *
 * @see https://wordpress.org/plugins/wordpress-seo
 * @since 1.5
 */

/**
 * Remove the SEO score columns from the list tables for Menus and Menu Items
 *
 * @param array $columns Key/value store of all columns
 * @since 1.5
 */
function fdm_yoast_remove_list_table_columns( $columns ) {

	global $post;

	if ( $post->post_type !== FDM_MENU_POST_TYPE && $post->post_type !== FDM_MENUITEM_POST_TYPE ) {
		return $columns;
	}

	if ( apply_filters( 'fdm_yoast_remove_list_table_columns', true ) ) {
		unset( $columns['wpseo-score'] );
		unset( $columns['wpseo-score-readability'] );
		unset( $columns['wpseo-title'] );
		unset( $columns['wpseo-metadesc'] );
		unset( $columns['wpseo-focuskw'] );
	}

	return $columns;
}
add_filter ( 'manage_edit-fdm-menu_columns', 'fdm_yoast_remove_list_table_columns' );
add_filter ( 'manage_edit-fdm-menu-item_columns', 'fdm_yoast_remove_list_table_columns' );
