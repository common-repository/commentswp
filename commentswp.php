<?php
/**
 * Plugin Name:       CommentsWP
 * Plugin URI:        https://commentswp.com
 * Description:       A beautifully helpful dashboard for all your comments.
 * Requires at least: 5.5
 * Requires PHP:      7.2
 * Author:            Slava Abakumov
 * Author URI:        https://ovirium.com
 * Version:           1.1.0
 * Text Domain:       commentswp
 * License:           GPLv2 or later
 */

use CommentsWP\Plugin;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WPForms.Comments.PHPDocDefine.MissPHPDoc
define( 'COMMENTSWP_VERSION', '1.1.0' );
define( 'COMMENTSWP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'COMMENTSWP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'COMMENTSWP_PLUGIN_FILE', __FILE__ );
// phpcs:enable

/**
 * One function to rule them all.
 *
 * @since 1.0.0
 *
 * @return Plugin
 */
function commentswp() {

	require_once __DIR__ . '/vendor/autoload.php';

	return Plugin::instance();
}

add_action(
	'plugins_loaded',
	static function () {
		commentswp();

		/**
		 * By this time CommentsWP has been loaded.
		 *
		 * @since 1.0.0
		 */
		do_action( 'commentswp_loaded' );
	}
);


