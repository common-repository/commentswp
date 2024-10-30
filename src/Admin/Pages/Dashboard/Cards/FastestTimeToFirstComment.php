<?php

namespace CommentsWP\Admin\Pages\Dashboard\Cards;

use CommentsWP\Helpers\HumanDate;
use CommentsWP\Admin\Pages\Dashboard\Cards\Feature\HasMenu;
use CommentsWP\Admin\Pages\Dashboard\Cards\Templates\CardSmall;
use CommentsWP\Admin\Pages\Dashboard\Cards\Feature\CardMenuItem;

/**
 * Class FastestTimeToFirstComment.
 *
 * @since 1.0.0
 */
class FastestTimeToFirstComment extends CardSmall {

	use HasMenu;

	/**
	 * URL to card documentation article.
	 *
	 * @since 1.1.0
	 */
	const DOC_URL = 'https://commentswp.com/docs/fastest-time-to-first-comment-card/';

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
			'title' => esc_html__( 'Fastest Time To First Comment', 'commentswp' ),
		];

		parent::__construct( $data );

		$this->register_menu(
			( new CardMenuItem( $this->get_class_name() ) )
				->url( self::DOC_URL, true )
				->label( esc_html__( 'Learn More', 'commentswp' ) )
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

		$average = (int) $wpdb->get_var(
			"SELECT 
			    ROUND( 
					MIN(
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
					<path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z" clip-rule="evenodd" />
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

