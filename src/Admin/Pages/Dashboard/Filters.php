<?php

namespace CommentsWP\Admin\Pages\Dashboard;

/**
 * Class Filters.
 *
 * @since 1.0.0
 */
class Filters {

	/**
	 * Option key from wp_options table.
	 *
	 * @since 1.0.0
	 */
	const OPTION_NAME = 'commentswp_dashboard_filters';

	/**
	 * List of supported periods.
	 *
	 * @since 1.0.0
	 */
	const PERIODS = [
		'last_1_day',
		'last_7_days',
		'last_14_days',
		'last_30_days',
		'last_90_days',
		'all_time',
	];

	/**
	 * Default value of the Periods filter.
	 *
	 * @since 1.0.0
	 */
	const PERIOD_DEFAULT = 'last_7_days';

	/**
	 * How mnay items display "per page" in card tables.
	 *
	 * @since 1.0.0
	 */
	const ITEMS_PER_PAGE = [
		5,
		10,
		15,
	];

	/**
	 * Default value of the Periods filter.
	 *
	 * @since 1.0.0
	 */
	const ITEMS_PER_PAGE_DEFAULT = 10;

	/**
	 * Filters data.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $data = [];

	/**
	 * Filters constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->populate_data();
	}

	/**
	 * Retrieve and process filters.
	 *
	 * @since 1.0.0
	 */
	private function populate_data() {

		// Set default values.
		$this->data['period']         = self::PERIOD_DEFAULT;
		$this->data['items_per_page'] = self::ITEMS_PER_PAGE_DEFAULT;

		$this->validate_data( get_option( self::OPTION_NAME ) );
	}

	/**
	 * Rewrite with data with correct values.
	 *
	 * @since 1.0.0
	 *
	 * @param array $options Values to set.
	 */
	private function validate_data( $options ) {

		if ( isset( $options['period'] ) ) {
			$this->data['period'] = $this->validate_period( $options['period'] );
		}

		if ( isset( $options['items_per_page'] ) ) {
			$this->data['items_per_page'] = $this->validate_per_page( $options['items_per_page'] );
		}
	}

	/**
	 * Validate and return the value of a period filter.
	 *
	 * @since 1.0.0
	 *
	 * @param string $period Value to validate. Fallback to the default value on error.
	 *
	 * @return string
	 */
	private function validate_period( $period ) {

		if ( ! in_array( $period, self::PERIODS, true ) ) {
			$period = self::PERIOD_DEFAULT;
		}

		return $period;
	}

	/**
	 * Validate and return the value of an items_per_page filter.
	 *
	 * @since 1.0.0
	 *
	 * @param string|int $per_page Value to validate. Fallback to the default value on error.
	 *
	 * @return string
	 */
	private function validate_per_page( $per_page ) {

		$per_page = absint( $per_page );

		if ( empty( $per_page ) ) {
			$per_page = self::ITEMS_PER_PAGE_DEFAULT;
		}

		return $per_page;
	}

	/**
	 * Get the single filter value.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name Filter name.
	 *
	 * @return mixed
	 */
	public function get( $name ) {

		$name = sanitize_key( $name );

		if ( array_key_exists( $name, $this->data ) ) {
			return $this->data[ $name ];
		}

		return null;
	}

	/**
	 * Retrieve all filter values.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function all() {

		return $this->data;
	}

	/**
	 * Set the filter values in bulk.
	 *
	 * @since 1.0.0
	 *
	 * @param array $values List of new value of selected filters.
	 */
	public function set_bulk( $values ) {

		$options = wp_parse_args( $values, $this->data );

		$this->validate_data( $options );

		update_option( self::OPTION_NAME, $this->data, false );
	}

	/**
	 * Build a WHERE part of the SQL query used later in cards
	 * to retrieve comments' data for a proper period of time.
	 *
	 * @since 1.0.0
	 *
	 * @param string $table Which table to check the date for.
	 *
	 * @return string
	 */
	public function build_sql_where_period( $table = 'comments' ) { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh

		global $wpdb;

		switch ( $table ) {
			case 'posts':
				$date_col = "$wpdb->posts.post_date";
				break;

			case 'comments':
			default:
				$date_col = "$wpdb->comments.comment_date";
				break;
		}

		switch ( $this->get( 'period' ) ) {
			case 'all_time':
				$where_period = '';
				break;

			case 'last_1_day':
				$where_period = "AND ( $date_col BETWEEN SUBDATE(NOW(), 1) AND SUBDATE(NOW(), 0) )";
				break;

			case 'last_14_days':
				$where_period = "AND ( $date_col BETWEEN SUBDATE(NOW(), 14) AND SUBDATE(NOW(), 0) )";
				break;

			case 'last_30_days':
				$where_period = "AND ( $date_col BETWEEN SUBDATE(NOW(), 30) AND SUBDATE(NOW(), 0) )";
				break;

			case 'last_90_days':
				$where_period = "AND ( $date_col BETWEEN SUBDATE(NOW(), 90) AND SUBDATE(NOW(), 0) )";
				break;

			case 'last_7_days':
			default:
				$where_period = "AND ( $date_col BETWEEN SUBDATE(NOW(), 7) AND SUBDATE(NOW(), 0) )";
				break;
		}

		return $where_period;
	}

	/**
	 * Build a LIMIT part of the SQL query used later in cards
	 * to retrieve a defined number of DB rows.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function build_sql_limit() {

		return 'LIMIT ' . $this->get( 'items_per_page' );
	}

	/**
	 * Map slugs to names for Periods filter.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_periods() {

		return (array) apply_filters(
			'commentswp_admin_dashboard_filters_periods',
			array_combine(
				self::PERIODS,
				[
					__( 'Last 24 hours', 'commentswp' ),
					__( 'Last 7 Days', 'commentswp' ),
					__( 'Last 14 Days', 'commentswp' ),
					__( 'Last 30 Days', 'commentswp' ),
					__( 'Last 90 Days', 'commentswp' ),
					__( 'All Time', 'commentswp' ),
				]
			)
		);
	}

	/**
	 * Map slugs to names for Items Per Page filter.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_items_per_page() {

		return (array) apply_filters(
			'commentswp_admin_dashboard_filters_items_per_page',
			array_combine(
				self::ITEMS_PER_PAGE,
				[
					__( '5 items', 'commentswp' ),
					__( '10 items', 'commentswp' ),
					__( '15 items', 'commentswp' ),
				]
			)
		);
	}
}
