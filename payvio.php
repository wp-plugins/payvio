<?php

/**
 * @package Payvio
 * @version 0.0.3
 */
/* Plugin Name: Payvio
 * Plugin URI: http://www.payvio.com
 * Description: Alacarte payments through Payvio.
 * Version: 0.0.3
 * Author: Payvio
 * License: GPL2
 */

// Don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'This plugin cannot be called directly.';
	exit;
}

define("PAYVIO_PLUGIN_VERSION", "0.0.1");
define("PAYVIO_PLUGIN_DIR", dirname(__FILE__));
define("PAYVIO_PLUGIN_URL", plugin_dir_url( __FILE__ ));
define('PAYVIO_PLUGIN_IMAGES_DIR', plugin_dir_url(__FILE__) . 'includes/images');
define('PAYVIO_PLUGIN_PVO_JS', plugin_dir_url(__FILE__) . 'includes/pvo.js');
define('WORDPRESS_BASE_URL', get_bloginfo('url'));

// Environment Config
require_once( PAYVIO_PLUGIN_DIR . '/includes/config.php');

// Includes
require_once( PAYVIO_PLUGIN_DIR . '/class.payvio-serviceclient.php');
require_once( PAYVIO_PLUGIN_DIR . '/class.payvio.php');
require_once( PAYVIO_PLUGIN_DIR . '/class.payvio-settings.php');
require_once( PAYVIO_PLUGIN_DIR . '/class.payvio-data.php');
require_once( PAYVIO_PLUGIN_DIR . '/class.payvio-helpers.php');
require_once( PAYVIO_PLUGIN_DIR . '/includes/utility.php');

register_activation_hook( __FILE__, array( 'Payvio', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Payvio', 'deactivate' ) );

// Initiate the plugin on each postback
add_action( 'init', array( 'Payvio', 'init' ) );
add_action( 'wp_footer', array( 'Payvio', 'footer' ) );
if ( is_admin() ) {
	add_action( 'init', array( 'Payvio', 'init_admin' ) );
}

?>