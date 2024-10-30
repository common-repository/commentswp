<?php

namespace CommentsWP\Admin\Pages\Dashboard\Cards;

use CommentsWP\Admin\Pages\Dashboard\Cards\Feature\HasMenu;
use CommentsWP\Admin\Pages\Dashboard\Cards\Templates\CardTable;
use CommentsWP\Admin\Pages\Dashboard\Cards\Feature\CardMenuItem;

/**
 * Class TotalAnomaliesTable.
 *
 * @since 1.0.0
 */
class TotalAnomaliesTable extends CardTable {

	use HasMenu;

	/**
	 * URL to card documentation article.
	 *
	 * @since 1.1.0
	 */
	const DOC_URL = 'https://commentswp.com/docs/anomalies-card/';

	/**
	 * What should be used to calculate the number of unique comments.
	 * Possible values: `ip_to_emails`, `email_to_ips`.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $group_by = 'ip_to_emails';

	/**
	 * Provide card's default data.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		parent::__construct(
			[
				'title' => esc_html__( 'Anomalies', 'commentswp' ),
			]
		);

		$this->register_menu(
			( new CardMenuItem( $this->get_class_name() ) )
				->url( static::DOC_URL, true )
				->label( __( 'Learn More', 'commentswp' ) )
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

		if ( in_array( $group_by, [ 'email_to_ips', 'ip_to_emails' ], true ) ) {
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
	protected function generate_data() {

		global $wpdb;

		switch ( $this->group_by ) {
			case 'email_to_ips':
				$select_field = 'comment_author_email';
				$values_field = 'comment_author_IP';
				$having       = '';
				break;

			case 'ip_to_emails':
			default:
				$select_field = 'comment_author_IP';
				$values_field = 'comment_author_email';
				$having       = 'HAVING values_count > 1';
		}

		$build_where_period = commentswp()->get( 'admin' )->dashboard->filters->build_sql_where_period();
		$build_limit        = commentswp()->get( 'admin' )->dashboard->filters->build_sql_limit();

		$sql = "SELECT 
					$select_field AS `group`, 
					SUBSTRING_INDEX(
					    GROUP_CONCAT(
					        DISTINCT $values_field 
					        ORDER BY comment_date DESC
					        SEPARATOR ','
				        ), ',', 10) AS `values`,
					COUNT(DISTINCT $values_field) AS `values_count`,
					COUNT(*) AS total
				FROM $wpdb->comments
				WHERE comment_type = 'comment'
				  AND $select_field != '' 
				  AND $values_field != '' 
				  $build_where_period
				GROUP BY $select_field
				$having
				ORDER BY values_count DESC
				$build_limit;";

		$data = $wpdb->get_results( $sql );

		foreach ( $data as &$row ) {
			$row->values = array_filter( explode( ',', $row->values ) );
		}
		unset( $row );

		$this->data = $data;
	}

	/**
	 * Render table-specific part of the card.
	 *
	 * @since 1.0.0
	 */
	protected function render_data() {

		if ( empty( $this->data ) ) {
			$this->populate_data();
		}
		?>

		<table class="form-table">
			<thead>
			<tr>
				<th class="value"><?php esc_html_e( 'Unique Value', 'commentswp' ); ?></th>
				<th class="count"><?php esc_html_e( 'Comments', 'commentswp' ); ?></th>
			</tr>
			</thead>
			<tbody>
			<?php if ( empty( $this->data ) ) : ?>
				<tr class="bordered">
					<td colspan="2">
						<?php esc_html_e( 'No data to display.', 'commentswp' ); ?>
					</td>
				</tr>
			<?php else : ?>
				<?php foreach ( $this->data as $row ) : ?>
					<tr class="bordered">
						<td class="value">
							<details>
								<summary>
									<?php echo esc_html( $row->group ); ?>
									<span class="subvalue"><?php echo (int) $row->values_count; ?></span>
								</summary>
								<p>
									<?php
									printf(
										wp_kses( /* translators: %s - comma-separated list of other names. */
											__( 'Last 10 items: %s', 'commentswp' ),
											[ 'code' => [] ]
										),
										'<code>' . implode( '</code>, <code>', array_map( 'esc_html', $row->values ) ) . '</code>'
									);
									?>
								</p>
							</details>
						</td>
						<td class="count">
							<a class="search" title="<?php esc_attr_e( 'Filter comments', 'commentswp' ); ?>"
								href="<?php echo esc_url( admin_url( add_query_arg( 's', rawurlencode( $row->group ), 'edit-comments.php' ) ) ); ?>">
								<?php echo (int) $row->total; ?>
							</a>
						</td>
					</tr>
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
			<option value="ip_to_emails" <?php selected( 'ip_to_emails', $this->group_by ); ?>>
				<?php esc_html_e( 'IP to Emails', 'commentswp' ); ?>
			</option>
			<option value="email_to_ips" <?php selected( 'email_to_ips', $this->group_by ); ?>>
				<?php esc_html_e( 'Email to IPs', 'commentswp' ); ?>
			</option>
		</select>

		<?php
	}
}
