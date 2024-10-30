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
class PingTrackbacksNumberCard extends CardSmallDuo {

	use HasMenu;

	/**
	 * URL to card documentation article.
	 *
	 * @since 1.1.0
	 */
	const DOC_URL = 'https://commentswp.com/docs/pingbacks-trackbacks-card/';

	/**
	 * Set some card-specific options.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$data = [
			'title_one' => esc_html__( 'Pingbacks', 'commentswp' ),
			'title_two' => esc_html__( 'Trackbacks', 'commentswp' ),
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
					IF(comment_type = 'pingback', 1, null)
				) AS pingbacks,
				COUNT(
					IF(comment_type = 'trackback', 1, null)
				) AS trackbacks
			FROM $wpdb->comments
			WHERE comment_type IN ('pingback', 'trackback')
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

		$url = add_query_arg( 'comment_type', 'pingback', admin_url( 'edit-comments.php' ) );

		$percent = '0';

		if ( $this->data->total !== 0 ) {
			$percent = round( $this->data->pingbacks * 100 / $this->data->total ) . '%';
		}
		?>

		<div class="commentswp-card-content-icon">
			<div class="icon">
				<a href="<?php echo esc_url( $url ); ?>"
					title="<?php esc_attr_e( 'See Pingbacks', 'commentswp' ); ?>">
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
					pingbacks_total:<?php echo (int) $this->data->pingbacks; ?>,
					pingbacks_percent:'<?php echo esc_html( $percent ); ?>',
					value:'',
				}"
				x-init="value = pingbacks_percent"
				x-text="value"
				@click.self="value = (value === pingbacks_percent) ? pingbacks_total : pingbacks_percent">
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

		$url = add_query_arg( 'comment_type', 'trackback', admin_url( 'edit-comments.php' ) );

		$percent = '0';

		if ( $this->data->total !== 0 ) {
			$percent = round( $this->data->trackbacks * 100 / $this->data->total ) . '%';
		}
		?>

		<div class="commentswp-card-content-icon">
			<div class="icon">
				<a href="<?php echo esc_url( $url ); ?>"
					title="<?php esc_attr_e( 'See Trackbacks', 'commentswp' ); ?>">
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
					trackbacks_total:<?php echo (int) $this->data->trackbacks; ?>,
					trackbacks_percent:'<?php echo esc_html( $percent ); ?>',
					value:'',
				}"
				x-init="value = trackbacks_percent"
				x-text="value"
				@click.self="value = (value === trackbacks_percent) ? trackbacks_total : trackbacks_percent">
			</div>
		</div>

		<?php
	}
}
