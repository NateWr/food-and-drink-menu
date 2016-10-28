<?php if ( !defined( 'ABSPATH' ) ) exit;
/**
 * Handle backwards-compatibility and cross-plugin compatibility issues
 */

/**
 * Load a .mo file for an old textdomain if one exists
 *
 * In versions prior to 1.5, the textdomain did not match the plugin slug. This
 * had to be changed to comply with changes to how translations are managed in
 * the .org repo. This function checks to see if an old translation file exists
 * and loads it if it does, so that people don't lose their translations.
 *
 * Old textdomain: fdmdomain
 *
 * @param string $mofile The translation file being requested
 * @param string $textdomain The textdomain being requested
 * @since 1.5
 */
function fdm_load_old_textdomain( $mofile, $textdomain ) {

	if ( $textdomain === 'food-and-drink-menu' && 0 === strpos( $mofile, WP_LANG_DIR . '/plugins/'  ) && !file_exists( $mofile ) ) {
		$mofile = dirname( $mofile ) . DIRECTORY_SEPARATOR . str_replace( $textdomain, 'fdmdomain', basename( $mofile ) );
	}
	return $mofile;
}
add_filter( 'load_textdomain_mofile', 'fdm_load_old_textdomain', 10, 2 );
