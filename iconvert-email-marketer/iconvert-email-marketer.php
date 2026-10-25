<?php
/**
 * Plugin Name: iConvert Email Marketer
 * Description: The complete email marketing toolkit built right into WordPress, featuring a familiar block builder, ready-made templates, and powerful automations
 * Version: 1.2.1
 * Author: ExtendThemes
 * Text Domain: iconvert-email-marketer
 * License: GPL-3.0+
 * License URI: https://www.gnu.org/licenses/gpl-3.0.txt
 * Requires PHP: 7.4
 * Requires at least: 6.5
 */


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


// skip loading free version if the iConvert Email Marketer PRO is active
if ( ! function_exists( 'iconvertem_is_free_and_pro_already_active' ) ) {

	function iconvertem_is_free_and_pro_already_active( $base_path ) {
		$free_plugin_base_name = 'iconvert-email-marketer/iconvert-email-marketer.php';
		$pro_plugin_base_name  = 'iconvert-email-marketer-pro/iconvert-email-marketer.php';

		$current_basename = plugin_basename( $base_path );
		$is_free          = $current_basename === $free_plugin_base_name;

		$pro_is_active = false;
		if ( $is_free ) {
			$active_plugins = get_option( 'active_plugins' );
			$pro_is_active  = in_array( $pro_plugin_base_name, $active_plugins );
		}

		return $is_free && $pro_is_active;
	}
}

if ( iconvertem_is_free_and_pro_already_active( __FILE__ ) ) {
	return;
}


if ( defined( 'ICONVERTEM_VERSION' ) ) {
	return;
}


use  IconvertEmailMarketer\App\Core\Frontend\Application;
use  IconvertEmailMarketer\App\Core\Structure\EditorLoader;
use  IconvertEmailMarketer\App\Core\Admin\Application as AdminApplication;



define( 'ICONVERTEM_VERSION', '1.2.1' );
define( 'ICONVERTEM_BUILD_NUMBER', '57' );
define( 'ICONVERTEM_PATH', plugin_dir_path( __FILE__ ) );
define( 'ICONVERTEM_URL', plugin_dir_url( __FILE__ ) );
define( 'ICONVERTEM_ENTRY_FILE', __FILE__ );
define( 'ICONVERTEM_PAGE_FILE', __FILE__ );
define( 'ICONVERTEM_PAGE_ID', 'iconvertem' );


if ( ! defined( 'ICONVERTEM_API_URL' ) ) {
	define( 'ICONVERTEM_API_URL', 'https://mail-templates.iconvert.pro/wp-json/email-builder/v1' );
}


if ( ! defined( 'ICONVERTEM_RECOVERY_DELAY_MULTIPLIER' ) ) {
	define( 'ICONVERTEM_RECOVERY_DELAY_MULTIPLIER', 3600 );
}


require_once __DIR__ . '/vendor/autoload.php';

// HELPERS

require_once( ICONVERTEM_PATH . '/App/Helpers/Registry.php' );
require_once( ICONVERTEM_PATH . '/App/Helpers/WPHelpers.php' );
require_once( ICONVERTEM_PATH . '/App/Helpers/BlockHelpers.php' );



new EditorLoader();

if ( ! ( defined( 'ICONVERTEM_SKIP_PRO' ) && ICONVERTEM_SKIP_PRO ) && file_exists( ICONVERTEM_PATH . '/pro/index.php' ) ) {

	require_once ICONVERTEM_PATH . '/pro/index.php';
}

if ( is_admin() ) {

	//load the admin/private code
	AdminApplication::boot();
} else {
	//load the frontend/public code
	Application::boot();
}


\IconvertEmailMarketer\Bootstrap::load();
