<?php

/**
 * Simple Admin Pages
 *
 * This is a very small utility library to easily add new admin pages to the
 * WordPress admin interface. It simply collects WordPress' useful Settings API
 * into reuseable classes.
 *
 * Created by Nate Wright
 *
 * @version 1.1.x This is a special release for a suite of products from Theme
 *	of the Crop (http://themeofthecrop.com) to ensure a smooth upgrade when
 *	the Food and Drink Menu upgrades its version of the library. This release
 *	ensures that this version of the library's files are loaded even if another
 *	version is loaded elsewhere.
 * @since 1.0
 * @package Simple Admin Pages
 * @license GNU GPL 2 or later
 */

/**
 * Always load the library files attached to this copy of SAP
 *
 * This fixes a compatibility bug if two versions of the library are being
 * loaded by different plugins/themes. However, versions prior to 2.0 do not
 * include this and can cause compatibility issues.
 */
require_once( 'classes/Library.class.php' );

/**
 * Initialize the appropriate version of the libary.
 *
 * This function should remain backwards compatible at all times, so that the
 * initialization function from version 1.0 will still be able to initialize
 * the library appropriately for version 2.0. This way, if two plugins exist
 * with different versions, the plugin creator will still be able to initialize
 * their library.
 *
 * @since 1.0
 */
if ( !function_exists( 'sap_initialize_library' ) ) {

	function sap_initialize_library( $args = array() ) {

		// Exit early if no version was provided
		if ( !isset( $args['version'] ) ) {
			return null;
		}

		// Set the textdomain for translation
		if ( !defined( 'SAP_TEXTDOMAIN' ) ) {
			define( 'SAP_TEXTDOMAIN', 'sapdomain' );
		}

		$lib_class_name = 'sapLibrary_' . str_replace( '.', '_', $args['version'] );

		return new $lib_class_name( $args );
	}

}
