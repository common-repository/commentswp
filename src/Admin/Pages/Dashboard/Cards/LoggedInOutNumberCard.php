<?php

namespace CommentsWP\Admin\Pages\Dashboard\Cards;

use CommentsWP\Admin\Pages\Dashboard\Cards\Feature\HasMenu;
use CommentsWP\Admin\Pages\Dashboard\Cards\Feature\CardMenuItem;
use CommentsWP\Admin\Pages\Dashboard\Cards\Templates\CardSmallDuo;

/**
 * Class LoggedInOutNumberCard.
 *
 * @since 1.0.0
 */
class LoggedInOutNumberCard extends CardSmallDuo {

	use HasMenu;

	/**
	 * URL to card documentation article.
	 *
	 * @since 1.1.0
	 */
	const DOC_URL = 'https://commentswp.com/docs/comments-by-logged-in-out-card/';

	/**
	 * Set some card-specific options.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$data = [
			'title_one' => esc_html__( 'Logged In', 'commentswp' ),
			'title_two' => esc_html__( 'Logged Out', 'commentswp' ),
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
					IF(user_id = 0, 1, null)
				) AS logged_out,
				COUNT(
					IF(user_id > 0, 1, null)
				) AS logged_in
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

		$url = add_query_arg( 'comment_type', 'comment', admin_url( 'edit-comments.php' ) );

		$percent = '0';

		if ( $this->data->total !== 0 ) {
			$percent = round( $this->data->logged_in * 100 / $this->data->total ) . '%';
		}
		?>

		<div class="commentswp-card-content-icon">
			<div class="icon">
				<a href="<?php echo esc_url( $url ); ?>"
					title="<?php esc_attr_e( 'See All Comments', 'commentswp' ); ?>">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
						<path fill-rule="evenodd" d="M3 3a1 1 0 011 1v12a1 1 0 11-2 0V4a1 1 0 011-1zm7.707 3.293a1 1 0 010 1.414L9.414 9H17a1 1 0 110 2H9.414l1.293 1.293a1 1 0 01-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0z" clip-rule="evenodd" />
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
					logged_in_total:<?php echo (int) $this->data->logged_in; ?>,
					logged_in_percent:'<?php echo esc_html( $percent ); ?>',
					value:'',
				}"
				x-init="value = logged_in_percent"
				x-text="value"
				@click.self="value = (value === logged_in_percent) ? logged_in_total : logged_in_percent">
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
				'comment_type' => 'comment',
				'user_id'      => 0,
			],
			admin_url( 'edit-comments.php' )
		);

		$percent = '0';

		if ( $this->data->total !== 0 ) {
			$percent = round( $this->data->logged_out * 100 / $this->data->total ) . '%';
		}
		?>

		<div class="commentswp-card-content-icon">
			<div class="icon">
				<a href="<?php echo esc_url( $url ); ?>"
					title="<?php esc_attr_e( 'See Comments By Logged Out Users', 'commentswp' ); ?>">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
						<path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd" />
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
					logged_out_total:<?php echo (int) $this->data->logged_out; ?>,
					logged_out_percent:'<?php echo esc_html( $percent ); ?>',
					value:'',
				}"
				x-init="value = logged_out_percent"
				x-text="value"
				@click.self="value = (value === logged_out_percent) ? logged_out_total : logged_out_percent">
			</div>
		</div>

		<?php
	}
}
