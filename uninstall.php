<?php
/**
 * Uninstall CommentsWP.
 *
 * @since 1.0.0
 */

// phpcs:disable WordPress.DB.DirectDatabaseQuery

// Exit if accessed directly.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Load plugin file.
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/commentswp.php';

// Disable Action Schedule Queue Runner.
if ( class_exists( 'ActionScheduler_QueueRunner' ) ) {
	ActionScheduler_QueueRunner::instance()->unhook_dispatch_async_request();
}

global $wpdb;

// Delete all the plugin settings.
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE 'commentswp\_%'" );

// Remove any transients we've left behind.
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '\_transient\_commentswp\_%'" );
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '\_site\_transient\_commentswp\_%'" );
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '\_transient\_timeout\_commentswp\_%'" );
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '\_site\_transient\_timeout\_commentswp\_%'" );
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '\_commentswp\_transient\_%'" );

// Unschedule all plugin ActionScheduler actions.
// Don't use commentswp() because 'tasks' in core are registered on `init` hook,
// which is not executed on uninstallation.
( new CommentsWP\Tasks\Tasks() )->cancel_all();
