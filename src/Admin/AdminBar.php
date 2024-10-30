<?php

namespace CommentsWP\Admin;

use WP_Admin_Bar;
use CommentsWP\Helpers\Assets;
use CommentsWP\Admin\Pages\Dashboard\Dashboard;

/**
 * Class AdminBar.
 *
 * @since 1.0.0
 */
class AdminBar {

	/**
	 * Register plugin AdminBar menu items.
	 *
	 * @since 1.0.0
	 */
	public function hooks() {

		add_action( 'admin_bar_menu', [ $this, 'add_items' ] );
	}

	/**
	 * Add plugin AdminBar menu items.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Admin_Bar $admin_bar AdminBar instance.
	 */
	public function add_items( WP_Admin_Bar $admin_bar ) {

		if ( ! current_user_can( 'moderate_comments' ) ) {
			return;
		}

		$admin_bar->add_menu(
			[
				'id'     => 'commentswp-dashboard',
				'parent' => 'comments',
				'group'  => null,
				'title'  => esc_html__( 'Dashboard', 'commentswp' ),
				'href'   => admin_url( 'edit-comments.php?page=commentswp-' ) . Dashboard::PAGE_SLUG,
				'meta'   => [
					'title' => esc_html__( 'CommentsWP Dashboard', 'commentswp' ),
				],
			]
		);
	}
}
