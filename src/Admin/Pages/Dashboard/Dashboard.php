<?php

namespace CommentsWP\Admin\Pages\Dashboard;

use CommentsWP\Admin\Pages\Dashboard\Cards\Templates\Card;
use CommentsWP\Admin\Pages\Dashboard\Cards\SpamNumberCard;
use CommentsWP\Admin\Pages\Dashboard\Cards\TrashNumberCard;
use CommentsWP\Admin\Pages\Dashboard\Cards\PendingNumberCard;
use CommentsWP\Admin\Pages\Dashboard\Cards\TotalByPeriodTable;
use CommentsWP\Admin\Pages\Dashboard\Cards\ApprovedNumberCard;
use CommentsWP\Admin\Pages\Dashboard\Cards\TotalByPersonTable;
use CommentsWP\Admin\Pages\Dashboard\Cards\TotalAnomaliesTable;
use CommentsWP\Admin\Pages\Dashboard\Cards\LoggedInOutNumberCard;
use CommentsWP\Admin\Pages\Dashboard\Cards\PostsCommentsNumberCard;
use CommentsWP\Admin\Pages\Dashboard\Cards\PingTrackbacksNumberCard;
use CommentsWP\Admin\Pages\Dashboard\Cards\TopLevelRepliesNumberCard;
use CommentsWP\Admin\Pages\Dashboard\Cards\FastestTimeToFirstComment;
use CommentsWP\Admin\Pages\Dashboard\Cards\SuggestFeaturePlaceholder;
use CommentsWP\Admin\Pages\Dashboard\Cards\AverageTimeToFirstCommentCard;

/**
 * This is the main class responsible for rendering and processing the whole dashboard.
 *
 * @since 1.0.0
 */
class Dashboard {

	const PAGE_SLUG = 'dashboard';

	/**
	 * List of initialized cards.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $cards;

	/**
	 * Dashboard filters.
	 *
	 * @since 1.0.0
	 *
	 * @var Filters
	 */
	public $filters;

	/**
	 * Dashboard constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param Filters $filters Dashboard filters instance.
	 */
	public function __construct( Filters $filters ) {

		$this->filters = $filters;

		$this->populate_cards();
	}

	/**
	 * Generate the list of cards.
	 *
	 * @since 1.0.0
	 */
	private function populate_cards() {

		/**
		 * Filter that gives the ability to add/remove cards from the dashboard.
		 *
		 * @since 1.0.0
		 *
		 * @param array $cards Array of cards. Each sub-array is a row of cards.
		 */
		$list = (array) apply_filters(
			'commentswp_admin_pages_dashboard_cards',
			[
				[
					ApprovedNumberCard::class,
					PendingNumberCard::class,
					SpamNumberCard::class,
					TrashNumberCard::class,
				],
				[
					AverageTimeToFirstCommentCard::class,
					FastestTimeToFirstComment::class,
				],
				[
					PostsCommentsNumberCard::class,
					LoggedInOutNumberCard::class,
					TopLevelRepliesNumberCard::class,
					PingTrackbacksNumberCard::class,
				],
				[
					TotalByPersonTable::class,
					TotalAnomaliesTable::class,
				],
				[
					TotalByPeriodTable::class,
					SuggestFeaturePlaceholder::class,
				],
			]
		);

		foreach ( $list as &$cards ) {
			// Make sure all the cards' classes are actually Cards.
			$cards = array_filter(
				$cards,
				static function ( $card ) {

					return is_subclass_of( $card, Card::class );
				}
			);

			// Init all the cards.
			$cards = array_map(
				static function ( $item ) {

					$card = new $item();

					$card->hooks();

					return $card;
				},
				$cards
			);
		}
		unset( $cards );

		$this->cards = $list;
	}

	/**
	 * Retrieve the list of registered cards.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_cards() {

		if ( empty( $this->cards ) ) {
			$this->populate_cards();
		}

		return $this->cards;
	}

	/**
	 * Render the dashboard.
	 *
	 * @since 1.0.0
	 */
	public function render() {

		$filters = $this->filters->all();

		if ( isset( $filters['period'] ) ) {
			$period = $filters['period'];
		}

		if ( isset( $filters['items_per_page'] ) ) {
			$items_per_page = $filters['items_per_page'];
		}
		?>

		<div id="commentswp-dashboard"
			x-cloak
			x-data="{
				itemsPerPage:<?php echo (int) $items_per_page; ?>,
				period:'<?php echo esc_attr( $period ); ?>',
				_ajax_nonce:'<?php echo wp_create_nonce( 'commentswp_dashboard_filters' ); ?>'
			}">

			<?php $this->render_filters(); ?>

			<?php $this->render_cards(); ?>

		</div>

		<?php
	}

	/**
	 * Render dashboard filters.
	 *
	 * @since 1.0.0
	 */
	protected function render_filters() {

		$periods        = $this->filters->get_periods();
		$items_per_page = $this->filters->get_items_per_page();
		?>

		<div id="commentswp-filters" x-data="{}">
			<h2><?php esc_html_e( 'Dashboard', 'commentswp' ); ?></h2>

			<div class="commentswp-filter commentswp-filter-type hidden">
				<select name="type" disabled title="<?php esc_html_e( 'Comment Type', 'commentswp' ); ?>">
					<option value="comment"><?php esc_html_e( 'Comment', 'commentswp' ); ?></option>
				</select>
			</div>

			<div class="commentswp-filter commentswp-filter-period">
				<!--suppress HtmlFormInputWithoutLabel -->
				<select name="period" title="<?php esc_attr_e( 'Applied to all cards', 'commentswp' ); ?>"
					x-model="period"
					@change="$dispatch('period-changed', { period: $event.target.value })">

					<?php foreach ( $periods as $slug => $label ) : ?>
						<option value="<?php echo esc_attr( $slug ); ?>">
							<?php echo esc_html( $label ); ?>
						</option>
					<?php endforeach; ?>

				</select>
			</div>

			<div class="commentswp-filter commentswp-filter-items_per_table">
				<!--suppress HtmlFormInputWithoutLabel -->
				<select name="item_per_table" title="<?php esc_attr_e( 'Applied to tables only', 'commentswp' ); ?>"
					x-model="itemsPerPage"
					@change="$dispatch('items-per-page-changed', { itemsPerPage: $event.target.value })">

					<?php foreach ( $items_per_page as $number => $label ) : ?>
						<option value="<?php echo (int) $number; ?>">
							<?php echo esc_html( $label ); ?>
						</option>
					<?php endforeach; ?>

				</select>
			</div>

		</div>

		<?php
	}

	/**
	 * Render dashboard cards.
	 *
	 * @since 1.0.0
	 */
	protected function render_cards() {

		?>

		<div id="commentswp-cards" class="pure-g" x-data="{}">
			<?php
			foreach ( $this->get_cards() as $row => $cards ) {
				?>
				<div class="commentswp-cards-row commentswp-cards-row-<?php echo (int) $row; ?>">
					<?php
					foreach ( $cards as $card ) {
						$card->render();
					}
					?>
				</div>
				<?php
			}
			?>
		</div>

		<?php
	}

	/**
	 * Get the Dashboard page URL.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_admin_url() {

		return admin_url( 'edit-comments.php?page=commentswp-' . self::PAGE_SLUG );
	}
}
