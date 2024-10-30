<?php

namespace CommentsWP\Admin\Pages\Dashboard\Cards;

use CommentsWP\Admin\Pages\Dashboard\Cards\Feature\HasMenu;
use CommentsWP\Admin\Pages\Dashboard\Cards\Feature\CardMenuItem;
use CommentsWP\Admin\Pages\Dashboard\Cards\Templates\CardSmallDuo;

/**
 * Class PingTrackbacksNumberCard.
 *
 * @since 1.0.0
 */
class PostsCommentsNumberCard extends CardSmallDuo {

	use HasMenu;

	/**
	 * URL to card documentation article.
	 *
	 * @since 1.1.0
	 */
	const DOC_URL = 'https://commentswp.com/docs/posts-with-without-comments-card/';

	/**
	 * Set some card-specific options.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$data = [
			'title_one' => esc_html__( 'With Comments', 'commentswp' ),
			'title_two' => esc_html__( 'No Comments', 'commentswp' ),
		];

		parent::__construct( $data );

		$this->register_menu(
			( new CardMenuItem( $this->get_class_name() ) )
				->url( static::DOC_URL, true )
				->label( __( 'Learn More', 'commentswp' ) )
		);
	}

	/**
	 * Do all calculations to generate card's data.
	 *
	 * @since 1.0.0
	 */
	protected function generate_data() {

		global $wpdb;

		$build_where_period = commentswp()->get( 'admin' )->dashboard->filters->build_sql_where_period( 'posts' );

		$sql = "SELECT 
				COUNT($wpdb->posts.ID) AS total,
				(
					SELECT COUNT(DISTINCT({$wpdb->comments}.comment_post_ID))
					FROM $wpdb->comments
					LEFT JOIN $wpdb->posts AS p ON p.ID = {$wpdb->comments}.comment_post_ID
					WHERE {$wpdb->comments}.comment_type = 'comment'
					  AND {$wpdb->comments}.comment_approved = '1'
					  AND p.post_type = 'post'
					  AND p.post_status = 'publish'
				) AS with_comments
			FROM $wpdb->posts
			WHERE $wpdb->posts.post_type = 'post'
			  AND $wpdb->posts.post_status = 'publish'
			  $build_where_period";

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.NotPrepared
		$data = $wpdb->get_row( $sql, ARRAY_A );

		if ( $data['with_comments'] > $data['total'] ) {
			$data['with_comments'] = $data['total'];
		}

		$data['no_comments'] = $data['total'] - $data['with_comments'];

		$this->data = array_map( 'absint', $data );
	}

	/**
	 * Render the 1st part.
	 *
	 * @since 1.0.0
	 */
	protected function render_one() {

		$url = add_query_arg(
			[
				'orderby' => 'comment_count',
				'order'   => 'desc',
			],
			admin_url( 'edit.php' )
		);

		$percent = '0';

		if ( $this->data['total'] !== 0 ) {
			$percent = round( $this->data['with_comments'] * 100 / $this->data['total'] ) . '%';
		}
		?>

		<div class="commentswp-card-content-icon">
			<div class="icon">
				<a href="<?php echo esc_url( $url ); ?>"
					title="<?php esc_attr_e( 'Posts with comments', 'commentswp' ); ?>">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
						<path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" />
					</svg>
				</a>
			</div>
		</div>
		<div class="commentswp-card-content-data">
			<div class="label">
				<?php echo esc_html( $this->title( 'one' ) ); ?>
			</div>
			<div class="value"
				x-data="{
					with_comments_total:<?php echo (int) $this->data['with_comments']; ?>,
					with_comments_percent:'<?php echo esc_html( $percent ); ?>',
					value:'',
				}"
				x-init="value = with_comments_percent"
				x-text="value"
				@click.self="value = (value === with_comments_percent) ? with_comments_total : with_comments_percent">
			</div>
		</div>

		<?php
	}

	/**
	 * Render the 2nd part.
	 *
	 * @since 1.0.0
	 */
	protected function render_two() {

		$url = add_query_arg(
			[
				'orderby' => 'comment_count',
				'order'   => 'asc',
			],
			admin_url( 'edit.php' )
		);

		$percent = '0';

		if ( $this->data['total'] !== 0 ) {
			$percent = round( $this->data['no_comments'] * 100 / $this->data['total'] ) . '%';
		}
		?>

		<div class="commentswp-card-content-icon">
			<div class="icon">
				<a href="<?php echo esc_url( $url ); ?>"
					title="<?php esc_attr_e( 'Posts without comments', 'commentswp' ); ?>">
					<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
					</svg>
				</a>
			</div>
		</div>
		<div class="commentswp-card-content-data">
			<div class="label">
				<?php echo esc_html( $this->title( 'two' ) ); ?>
			</div>
			<div class="value"
				x-data="{
					no_comments_total:<?php echo (int) $this->data['no_comments']; ?>,
					no_comments_percent:'<?php echo esc_html( $percent ); ?>',
					value:'',
				}"
				x-init="value = no_comments_percent"
				x-text="value"
				@click.self="value = (value === no_comments_percent) ? no_comments_total : no_comments_percent">
			</div>
		</div>

		<?php
	}
}
