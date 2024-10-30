<?php

namespace CommentsWP\Admin;

use CommentsWP\Helpers\URL;
use CommentsWP\Helpers\Assets;
use CommentsWP\Admin\Pages\AllComments;
use CommentsWP\Admin\Pages\Dashboard\Dashboard;

/**
 * Class Admin.
 *
 * @since 1.0.0
 */
class Admin {

	/**
	 * Predefined admin page slug.
	 *
	 * @since 1.0.0
	 */
	const SLUG = 'comments_page_commentswp';

	/**
	 * AdminBar instance.
	 *
	 * @since 1.0.0
	 *
	 * @var AdminBar
	 */
	public $admin_bar;

	/**
	 * Dashboard instance.
	 *
	 * @since 1.0.0
	 *
	 * @var Dashboard
	 */
	public $dashboard;

	/**
	 * AllComments instance.
	 *
	 * @since 1.0.0
	 *
	 * @var AllComments
	 */
	public $all_comments;

	/**
	 * Admin constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param AdminBar    $admin_bar    The AdminBar menu items.
	 * @param Dashboard   $dashboard    The Dashboard page.
	 * @param AllComments $all_comments The All Comments page.
	 */
	public function __construct(
		AdminBar $admin_bar,
		Dashboard $dashboard,
		AllComments $all_comments
	) {

		$this->admin_bar    = $admin_bar;
		$this->dashboard    = $dashboard;
		$this->all_comments = $all_comments;
	}

	/**
	 * Init the class logic.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		if (
			is_admin() &&
			isset( $_SERVER['REQUEST_URI'] ) &&
			$_SERVER['REQUEST_URI'] === '/wp-admin/admin.php?page=commentswp-' . Dashboard::PAGE_SLUG
		) {
			wp_safe_redirect( $this->dashboard->get_admin_url() );
			exit;
		}

		$this->hooks();

		$this->ajax();
	}

	/**
	 * All the hooks go here.
	 *
	 * @since 1.0.0
	 */
	protected function hooks() {

		add_action( 'admin_menu', [ $this, 'register_admin_menu' ] );

		add_filter( 'script_loader_tag', [ $this, 'defer_enqueued_assets' ], 10, 3 );

		add_filter( sprintf( '%splugin_action_links_%s', is_multisite() ? 'network_admin_' : '', plugin_basename( COMMENTSWP_PLUGIN_FILE ) ), [ $this, 'add_plugin_actions_links' ] );

		$this->admin_bar->hooks();
		$this->all_comments->hooks();
	}

	/**
	 * Show some plugin links on the Plugins page.
	 *
	 * @since 1.0.0
	 *
	 * @param string[] $links The current plugin action links.
	 *
	 * @return string[]
	 */
	public function add_plugin_actions_links( $links ) {

		$dashboard = sprintf(
			'<a href="%s">%s</a>',
			esc_url( $this->dashboard->get_admin_url() ),
			esc_html__( 'Dashboard', 'commentswp' )
		);

		return array_merge( [ $dashboard ], $links );
	}

	/**
	 * All the ajax-related hooks go here.
	 *
	 * @since 1.0.0
	 */
	protected function ajax() {

		if ( ! wp_doing_ajax() ) {
			return;
		}

		if ( ! $this->has_access() ) {
			return;
		}

		do_action( 'commentswp_admin_ajax' );
	}

	/**
	 * Register plugin admin menu.
	 *
	 * @since 1.0.0
	 */
	public function register_admin_menu() { // phpcs:ignore WPForms.PHP.HooksMethod.InvalidPlaceForAddingHooks

		$hook_suffix = add_comments_page(
			esc_html__( 'Comments Dashboard', 'commentswp' ),
			esc_html__( 'Dashboard', 'commentswp' ),
			'moderate_comments',
			'commentswp-' . Dashboard::PAGE_SLUG,
			[ $this, 'render' ],
			-42
		);

		if ( $hook_suffix ) {
			add_action( "admin_print_styles-$hook_suffix", [ $this, 'enqueue_assets' ] );
		}
	}

	/**
	 * Render the content of the admin area.
	 *
	 * @since 1.0.0
	 */
	public function render() {

		$url = URL::add_utm( 'https://commentswp.com/', 'Admin Page', 'Logo' );

		echo '<div id="commentswp" class="wrap">';

		echo '<h1>';
		echo '<a href="' . esc_url( $url ) . '" target="_blank">
				<img src="' . esc_url( Assets::url( 'images/logo.svg' ) ) . '" class="logo" alt="" />
			</a>';
		echo '<span>CommentsWP</span>';
		echo '</h1>';

		$this->dashboard->render();

		echo '</div>';
	}

	/**
	 * Check if user has access to the plugin admin area and its various parts.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function has_access() {

		$access = false;

		if ( current_user_can( 'moderate_comments' ) ) {
			$access = true;
		}

		return apply_filters( 'commentswp_admin_has_access', $access );
	}

	/**
	 * Add "defer" attribute to certain scripts.
	 *
	 * @since 1.0.0
	 *
	 * @param string $tag    Generated HTML tag.
	 * @param string $handle Unique handle of the script.
	 * @param string $src    URL to the script.
	 *
	 * @return string
	 */
	public function defer_enqueued_assets( $tag, $handle, $src ) {

		if ( $handle === 'commentswp-alpinejs' ) {
			// phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedScript
			$tag = '<script defer type="text/javascript" src="' . esc_url( $src ) . '"></script>';
		}

		return $tag;
	}

	/**
	 * All CSS and JS assets are enqueued here.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_assets() {
		/*
		 * CSS.
		 */
		wp_enqueue_style(
			'commentswp-pure-grid',
			Assets::url( 'libs/pure-grid.min.css' ),
			[],
			'2.1.0'
		);

		wp_enqueue_style(
			'commentswp-admin',
			Assets::url( 'css/commentswp-admin.css', true ),
			[ 'commentswp-pure-grid' ],
			Assets::ver()
		);

		/*
		 * JavaScript.
		 */
		wp_enqueue_script(
			'commentswp-alpinejs',
			Assets::url( 'libs/alpinejs.min.js' ),
			[],
			'3.10.2',
			false
		);

		wp_enqueue_script(
			'commentswp-admin',
			Assets::url( 'js/commentswp-admin.js', true ),
			[ 'commentswp-alpinejs', 'jquery' ],
			Assets::ver(),
			false
		);
	}

	/**
	 * Helper function to determine if loading a plugin-related admin page.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug Slug identifier for a specific admin page.
	 * @param string $view Slug identifier for a specific admin page view ("subpage").
	 *
	 * @return bool
	 */
	public function is_page( $slug = '', $view = '' ) {

		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		// Check against basic requirements.
		if (
			! is_admin() ||
			empty( $_REQUEST['page'] ) ||
			strpos( $_REQUEST['page'], 'commentswp' ) === false
		) {
			return false;
		}

		// Check against page slug identifier.
		if ( ! empty( $slug ) && 'commentswp-' . $slug !== $_REQUEST['page'] ) {
			return false;
		}

		// Check against sublevel page view.
		if (
			! empty( $view ) &&
			( empty( $_REQUEST['view'] ) || $view !== $_REQUEST['view'] )
		) {
			return false;
		}
		// phpcs:enable

		return true;
	}
}
