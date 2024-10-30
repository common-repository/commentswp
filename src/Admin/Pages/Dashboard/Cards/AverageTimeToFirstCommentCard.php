<?php

namespace CommentsWP\Admin\Pages\Dashboard\Cards;

use CommentsWP\Helpers\HumanDate;
use CommentsWP\Admin\Pages\Dashboard\Cards\Feature\HasMenu;
use CommentsWP\Admin\Pages\Dashboard\Cards\Templates\CardSmall;
use CommentsWP\Admin\Pages\Dashboard\Cards\Feature\CardMenuItem;

/**
 * Class AverageTimeToFirstCommentCard.
 *
 * @since 1.0.0
 */
class AverageTimeToFirstCommentCard extends CardSmall {

	use HasMenu;

	/**
	 * URL to card documentation article.
	 *
	 * @since 1.1.0
	 */
	const DOC_URL = 'https://commentswp.com/docs/average-time-to-first-comment-card/';

	/**
	 * Card width.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $width = '1/2';

	/**
	 * Set some card-specific options.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$data = [
			'title' => esc_html__( 'Average Time To First Comment', 'commentswp' ),
		];

		parent::__construct( $data );

		$this->register_menu(
			( new CardMenuItem( $this->get_class_name() ) )
				->url( self::DOC_URL, true )
				->label( esc_html__( 'Learn More', 'commentswp' ) )
		);
	}

	/**
	 * Retrieve the data needed for the card.
	 *
	 * @since 1.0.0
	 */
	protected function generate_data() {

		global $wpdb;

		$build_where_period = commentswp()->get( 'admin' )->dashboard->filters->build_sql_where_period();

		$average = (int) $wpdb->get_var(
			"SELECT 
		    ROUND( 
				AVG(
					TIMESTAMPDIFF(SECOND, $wpdb->posts.post_date, $wpdb->comments.comment_date)
			   )
			)
			FROM $wpdb->posts 
			INNER JOIN $wpdb->comments on $wpdb->comments.comment_post_ID = $wpdb->posts.ID
			WHERE $wpdb->comments.comment_type = 'comment'
			  AND $wpdb->comments.comment_approved = 1
			  AND TIMESTAMPDIFF(SECOND, $wpdb->posts.post_date, $wpdb->comments.comment_date) > 0
			  $build_where_period"
		);

		if ( $average === 0 ) {
			$this->data = esc_html__( 'N/A', 'commentswp' );
		} else {
			$this->data = ( new HumanDate( $average ) )
				->joined( ' ' )
				->short()
				->exclude( 'weeks' )
				->get();
		}
	}

	/**
	 * Render the internal part of the data div.
	 *
	 * @since 1.0.0
	 */
	protected function render_data() {

		if ( empty( $this->data ) ) {
			$this->populate_data();
		}
		?>

		<div class="label">
			<?php echo wp_kses_post( $this->title() ); ?>
		</div>

		<div class="value">
			<?php echo esc_html( $this->data ); ?>
		</div>

		<?php
	}

	/**
	 * Render the internal part of the icon div.
	 *
	 * @since 1.0.0
	 */
	protected function render_icon() {

		?>

		<div class="icon">
			<a href="<?php echo esc_url( $this->get_card_url() ); ?>">
				<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
					<path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V4a2 2 0 00-2-2H6zm1 2a1 1 0 000 2h6a1 1 0 100-2H7zm6 7a1 1 0 011 1v3a1 1 0 11-2 0v-3a1 1 0 011-1zm-3 3a1 1 0 100 2h.01a1 1 0 100-2H10zm-4 1a1 1 0 011-1h.01a1 1 0 110 2H7a1 1 0 01-1-1zm1-4a1 1 0 100 2h.01a1 1 0 100-2H7zm2 1a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1zm4-4a1 1 0 100 2h.01a1 1 0 100-2H13zM9 9a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1zM7 8a1 1 0 000 2h.01a1 1 0 000-2H7z"
						clip-rule="evenodd" />
				</svg>
			</a>
		</div>

		<?php
	}

	/**
	 * Get the URL this card should link to.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function get_card_url() {

		return add_query_arg(
			'comment_status',
			'approved',
			admin_url( 'edit-comments.php' )
		);
	}
}
