<?php

namespace CommentsWP\Admin\Pages\Dashboard\Cards;

use CommentsWP\Admin\Pages\Dashboard\Cards\Feature\HasMenu;
use CommentsWP\Admin\Pages\Dashboard\Cards\Feature\CardMenuItem;
use CommentsWP\Admin\Pages\Dashboard\Cards\Templates\CardSmallDuo;

/**
 * Class TopLevelRepliesNumberCard.
 *
 * @since 1.0.0
 */
class TopLevelRepliesNumberCard extends CardSmallDuo {

	use HasMenu;

	/**
	 * URL to card documentation article.
	 *
	 * @since 1.1.0
	 */
	const DOC_URL = 'https://commentswp.com/docs/top-level-replies-comments-card/';

	/**
	 * Set some card-specific options.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$data = [
			'title_one' => esc_html__( 'Top Level', 'commentswp' ),
			'title_two' => esc_html__( 'Replies', 'commentswp' ),
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

		$build_where_period = commentswp()->get( 'admin' )->dashboard->filters->build_sql_where_period();

		$data = $wpdb->get_row(
			"SELECT
       			COUNT(*) as total,
				COUNT(
					IF(comment_parent = 0, 1, null)
				) AS top_level,
				COUNT(
					IF(comment_parent > 0, 1, null)
				) AS replies
			FROM $wpdb->comments
			WHERE comment_type = 'comment'
				$build_where_period;",
			ARRAY_A
		);

		$data = array_map( 'absint', $data );

		$this->data = (object) $data;
	}

	/**
	 * Render the 1st part.
	 *
	 * @since 1.0.0
	 */
	protected function render_one() {

		$url = add_query_arg(
			[
				'comment_type' => 'comment',
				'parent'       => 0,
			],
			admin_url( 'edit-comments.php' )
		);

		$percent = '0';

		if ( $this->data->total !== 0 ) {
			$percent = round( $this->data->top_level * 100 / $this->data->total ) . '%';
		}
		?>

		<div class="commentswp-card-content-icon">
			<div class="icon">
				<a href="<?php echo esc_url( $url ); ?>"
					title="<?php esc_attr_e( 'See Top Level Comments', 'commentswp' ); ?>">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
						<path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
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
					top_level_total:<?php echo (int) $this->data->top_level; ?>,
					top_level_percent:'<?php echo esc_html( $percent ); ?>',
					value:'',
				}"
				x-init="value = top_level_percent"
				x-text="value"
				@click.self="value = (value === top_level_percent) ? top_level_total : top_level_percent">
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

		$url = add_query_arg( 'comment_type', 'comment', admin_url( 'edit-comments.php' ) );

		$percent = '0';

		if ( $this->data->total !== 0 ) {
			$percent = round( $this->data->replies * 100 / $this->data->total ) . '%';
		}
		?>

		<div class="commentswp-card-content-icon">
			<div class="icon">
				<a href="<?php echo esc_url( $url ); ?>"
					title="<?php esc_attr_e( 'See All Comments', 'commentswp' ); ?>">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
						<path d="M9 2a2 2 0 00-2 2v8a2 2 0 002 2h6a2 2 0 002-2V6.414A2 2 0 0016.414 5L14 2.586A2 2 0 0012.586 2H9z" />
						<path d="M3 8a2 2 0 012-2v10h8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z" />
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
					replies_total:<?php echo (int) $this->data->replies; ?>,
					replies_percent:'<?php echo esc_html( $percent ); ?>',
					value:'',
				}"
				x-init="value = replies_percent"
				x-text="value"
				@click.self="value = (value === replies_percent) ? replies_total : replies_percent">
			</div>
		</div>

		<?php
	}
}
