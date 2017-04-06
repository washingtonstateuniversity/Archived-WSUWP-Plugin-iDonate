<?php
/*
Plugin Name: WSUWP Plugin iDonate
Version: 0.0.20
Description: A plugin that is used to select funds to pass to the iDonate online giving solution
Author: washingtonstateuniversity, blairlierman
Author URI: https://foundation.wsu.edu/
Plugin URI: https://github.com/washingtonstateuniversity/WSUWP-Plugin-iDonate
Text Domain: wsuwp-plugin-idonate
Domain Path: /languages
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// The core plugin class.
require dirname( __FILE__ ) . '/includes/class-wsuwp-plugin-idonate.php';

add_action( 'after_setup_theme', 'WSUWP_Plugin_iDonate' );
/**
 * Start things up.
 *
 * @return \WSUWP_Plugin_iDonate
 */
function WSUWP_Plugin_iDonate() {
	return WSUWP_Plugin_iDonate::get_instance();
}
