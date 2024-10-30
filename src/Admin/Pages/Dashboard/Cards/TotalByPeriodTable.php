<?php

namespace CommentsWP\Admin\Pages\Dashboard\Cards;

use DateTime;
use CommentsWP\Admin\Pages\Dashboard\Cards\Feature\HasMenu;
use CommentsWP\Admin\Pages\Dashboard\Cards\Feature\CardMenuItem;

/**
 * Class TotalByPeriodTable.
 *
 * @since 1.0.0
 */
class TotalByPeriodTable extends Templates\CardTable {

	use HasMenu;

	/**
	 * URL to card documentation article.
	 *
	 * @since 1.1.0
	 */
	const DOC_URL = 'https://commentswp.com/docs/total-by-time-period-card/';

	/**
	 * Whether this card is filterable.
	 *
	 * @since 1.0.0
	 */
	const FILTERABLE = true;

	/**
	 * What should be used to calculate the number of unique comments.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $group_by = 'month';

	/**
	 * Provide card's default data.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		parent::__construct(
			[
				'title' => esc_html__( 'Total by Time Period', 'commentswp' ),
			]
		);

		$this->register_menu(
			( new CardMenuItem( $this->get_class_name() ) )
				->url( self::DOC_URL, true )
				->label( esc_html__( 'Learn More', 'commentswp' ) )
		);
	}

	/**
	 * Set default card filter values.
	 *
	 * @since 1.0.0
	 */
	protected function set_filters() {

		parent::set_filters();

		$filters = get_option( "commentswp_card_{$this->class_low}_filters", true );

		$this->group_by = ! empty( $filters['group_by'] ) ? $filters['group_by'] : $this->group_by;
	}

	/**
	 * Process card-specific table filters.
	 *
	 * @since 1.0.0
	 */
	protected function process_card_filters() {

		// phpcs:disable WordPress.Security.NonceVerification.Missing
		if ( empty( $_POST['tableFilters']['groupBy'] ) ) {
			return;
		}

		$group_by = sanitize_key( $_POST['tableFilters']['groupBy'] );
		// phpcs:enable WordPress.Security.NonceVerification.Missing

		if ( in_array( $group_by, [ 'year', 'month', 'week' ], true ) ) {
			update_option( "commentswp_card_{$this->class_low}_filters", [ 'group_by' => $group_by ], false );

			$this->group_by = $group_by;
		}
	}

	/**
	 * Get the key used to cache data.
	 *
	 * @since 1.0.0
	 */
	protected function cache_key() {

		return $this->class_low . '_' . md5( wp_json_encode( $this->filters->all() ) . $this->group_by );
	}

	/**
	 * Do all calculations to generate card's data.
	 *
	 * @since 1.0.0
	 */
	protected function generate_data() { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh

		global $wpdb;

		switch ( $this->group_by ) {
			case 'year':
				$sql_select_month   = '';
				$sql_select_week    = '';
				$sql_group_by_month = '';
				$sql_group_by_week  = '';
				$sql_order_month    = '';
				$sql_order_week     = '';
				break;

			case 'week':
				$sql_select_month   = '';
				$sql_select_week    = 'WEEK(comment_date) AS week,';
				$sql_group_by_month = '';
				$sql_group_by_week  = ', week';
				$sql_order_month    = '';
				$sql_order_week     = ', week DESC';
				break;

			case 'month':
			default:
				$sql_select_month   = 'MONTH(comment_date) AS month,';
				$sql_select_week    = '';
				$sql_group_by_month = ', month';
				$sql_group_by_week  = '';
				$sql_order_month    = ', month DESC';
				$sql_order_week     = '';
				break;
		}

		$sql = "SELECT
					YEAR(comment_date) AS year,
					$sql_select_month
					$sql_select_week
					COUNT(*) AS total
				FROM $wpdb->comments 
				WHERE 
				      comment_type IN ('', 'comment')
				  AND comment_approved = 1
				GROUP BY year $sql_group_by_month $sql_group_by_week
				ORDER BY year DESC $sql_order_month $sql_order_week";

		$data = $wpdb->get_results( $sql, ARRAY_A );

		foreach ( $data as $row ) {
			if ( isset( $row['week'] ) ) {
				$this->data[ $row['year'] ][ $row['week'] ] = $row['total'];
			} elseif ( isset( $row['month'] ) ) {
				$this->data[ $row['year'] ][ $row['month'] ] = $row['total'];
			} else {
				$this->data[ $row['year'] ] = $row['total'];
			}
		}
	}

	/**
	 * Render table-specific part of the card.
	 *
	 * @since 1.0.0
	 */
	protected function render_data() { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh,Generic.Metrics.NestingLevel.MaxExceeded

		if ( empty( $this->data ) ) {
			$this->populate_data();
		}
		?>

		<table class="form-table">
			<thead>
			<tr>
				<th class="value"><?php esc_html_e( 'Year', 'commentswp' ); ?></th>
				<th class="count"><?php esc_html_e( 'Comments', 'commentswp' ); ?></th>
			</tr>
			</thead>
			<tbody
				x-data="{
					<?php foreach ( $this->data as $year => $children ) : ?>
						showChildren<?php echo (int) $year; ?>:false,
					<?php endforeach; ?>
				}">
				<?php if ( empty( $this->data ) ) : ?>
					<tr>
						<td colspan="2">
							<?php esc_html_e( 'No data to display.', 'commentswp' ); ?>
						</td>
					</tr>
				<?php else : ?>
					<?php foreach ( $this->data as $year => $children ) : ?>
						<?php if ( $this->group_by === 'year' ) : ?>

							<tr class="bordered">
								<td class="value"><?php echo (int) $year; ?></td>
								<td class="count">
									<a href="<?php echo esc_url( add_query_arg( 'comment_date', $year, admin_url( 'edit-comments.php' ) ) ); ?>">
										<?php echo (int) $children; ?>
									</a>
								</td>
							</tr>

						<?php else : ?>

							<tr class="bordered has-child-tr">
								<td class="value">
									<details @click="showChildren<?php echo (int) $year; ?>=!showChildren<?php echo (int) $year; ?>">
										<summary>
											<?php echo (int) $year; ?>&nbsp;
											<span class="subvalue">
												<?php
												$string = '';

												switch ( $this->group_by ) {
													case 'week':
														$string = sprintf( /* translators: %d - number of weeks with comments in this year. */
															_n(
																'%d week',
																'%d weeks',
																count( $children ),
																'commentswp'
															),
															count( $children )
														);
														break;

													case 'month':
														$string = sprintf( /* translators: %d - number of months with comments in this year. */
															_n(
																'%d month',
																'%d months',
																count( $children ),
																'commentswp'
															),
															count( $children )
														);
														break;
												}

												echo esc_html( $string );
												?>
											</span>
										</summary>
									</details>
								</td>
								<td class="count">
									<a href="<?php echo esc_url( add_query_arg( 'comment_date', $year, admin_url( 'edit-comments.php' ) ) ); ?>">
										<?php echo (int) array_sum( $children ); ?>
									</a>
								</td>
							</tr>

							<?php foreach ( $children as $child => $total ) : ?>
								<tr x-show="showChildren<?php echo (int) $year; ?>" class="bordered-dashed is-child-tr">
									<td class="value">
										<?php
										$comment_date = $year;

										if ( $this->group_by === 'week' ) {
											$week_number = $child + 1;

											$week_start = new DateTime( 'midnight' );
											$week_end   = new DateTime( 'midnight' );

											$week_start->setISODate( $year, $child, 1 );
											$week_end->setISODate( $year, $child, 7 );

											$comment_date = $week_start->format( 'Y-m-d' ) . '..' . $week_end->format( 'Y-m-d' );

											printf( /* translators: %1$d - week number, %1$s - week date range. */
												esc_html__( 'Week %1$d: %2$s', 'commentswp' ),
												(int) $week_number,
												esc_html( $week_start->format( 'M d' ) . ' - ' . $week_end->format( 'M d' ) )
											);
										} else {
											$comment_date .= '-' . str_pad( $child, 2, '0', STR_PAD_LEFT );

											echo esc_html( DateTime::createFromFormat( '!m', $child )->format( 'F' ) );
										}
										?>
									</td>
									<td class="count">
										<a href="<?php echo esc_url( add_query_arg( 'comment_date', $comment_date, admin_url( 'edit-comments.php' ) ) ); ?>">
											<?php echo (int) $total; ?>
										</a>
									</td>
								</tr>
							<?php endforeach; ?>
						<?php endif; ?>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>

		<?php
	}

	/**
	 * Display various filters that the card supports.
	 *
	 * @since 1.0.0
	 */
	protected function display_filters() {
		?>

		<!--suppress HtmlFormInputWithoutLabel -->
		<select
			x-init="tableFilters.groupBy='<?php echo esc_attr( $this->group_by ); ?>'"
			x-model="tableFilters.groupBy"
			@change="$dispatch('<?php echo esc_attr( $this->class_low ); ?>-filters-changed', {groupBy: $event.target.value})">
			<option value="year" <?php selected( 'year', $this->group_by ); ?>>
				<?php esc_html_e( 'Group by Year', 'commentswp' ); ?>
			</option>
			<option value="month" <?php selected( 'month', $this->group_by ); ?>>
				<?php esc_html_e( 'Group by Month', 'commentswp' ); ?>
			</option>
			<option value="week" <?php selected( 'week', $this->group_by ); ?>>
				<?php esc_html_e( 'Group by Week', 'commentswp' ); ?>
			</option>
		</select>

		<?php
	}
}
