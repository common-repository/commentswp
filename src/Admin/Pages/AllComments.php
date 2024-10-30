<?php

namespace CommentsWP\Admin\Pages;

/**
 * Class AllComments.
 *
 * @since 1.0.0
 */
class AllComments {

	/**
	 * Extend WordPress default All Comments page.
	 *
	 * @since 1.0.0
	 */
	public function hooks() {

		add_filter( 'comments_list_table_query_args', [ $this, 'register_parent_param' ] );
		add_filter( 'comments_list_table_query_args', [ $this, 'register_comment_type_param' ] );
		add_filter( 'comments_list_table_query_args', [ $this, 'register_comment_date_param' ] );
	}

	/**
	 * Add support for "parent" in URL.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Default filtering arguments.
	 *
	 * @return array
	 */
	public function register_parent_param( $args ) {

		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if ( isset( $_REQUEST['parent'] ) ) {
			$args['parent'] = (int) $_REQUEST['parent'];
		}

		// phpcs:enable WordPress.Security.NonceVerification.Recommended

		return $args;
	}

	/**
	 * Add support for "comment_type" in URL.
	 * Expected formats:
	 * 1. ?comment_type=comment
	 * 2. ?comment_type=pings.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Default filtering arguments.
	 *
	 * @return array
	 */
	public function register_comment_type_param( $args ) {

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( empty( $_REQUEST['comment_type'] ) ) {
			return $args;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$type = sanitize_key( $_REQUEST['comment_type'] );

		if ( empty( $type ) ) {
			return $args;
		}

		$args['type'] = $type;

		return $args;
	}

	/**
	 * Add support for "comment_date" in URL.
	 * Expected formats:
	 * 1. ?comment_date=2022
	 * 2. ?comment_date=2022-02
	 * 3. ?comment_date=2022-02-22.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Default filtering arguments.
	 *
	 * @return array
	 */
	public function register_comment_date_param( $args ) {

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( empty( $_REQUEST['comment_date'] ) ) {
			return $args;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$comment_date = preg_replace( '/[^0-9-.]/', '', wp_unslash( $_REQUEST['comment_date'] ) );

		$date_query = [];

		// Is it a range?
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( strpos( $comment_date, '..' ) !== false ) {
			$range = array_slice(
				explode( '..', $comment_date ),
				0,
				2
			);

			$after  = $this->prepare_comment_date_string( $range[0] );
			$before = $this->prepare_comment_date_string( $range[1] );

			$before_date = '';
			$after_date  = '';

			if ( $after[0] ) {
				$after_date = $after[0];

				if ( $after[1] ) {
					$after_date .= '-' . $after[1];

					if ( $after[2] ) {
						$after_date .= '-' . $after[2];
					}
				}
			}

			if ( $before[0] ) {
				$before_date = $before[0];

				if ( $before[1] ) {
					$before_date .= '-' . $before[1];

					if ( $before[2] ) {
						$before_date .= '-' . $before[2];
					}
				}
			}

			if ( empty( $after_date ) || empty( $before_date ) ) {
				return $args;
			}

			$args['date_query'] = [
				'compare' => 'BETWEEN',
				[
					'before'    => $before_date,
					'after'     => $after_date,
					'inclusive' => true,
				],
			];

			return $args;
		}

		$dates = $this->prepare_comment_date_string( $comment_date );

		if ( $dates[0] ) {
			$date_query['year'] = $dates[0];

			if ( $dates[1] ) {
				$date_query['month'] = $dates[1];

				if ( $dates[2] ) {
					$date_query['day'] = $dates[2];
				}
			}
		}

		$args['date_query'] = [ $date_query ];

		return $args;
	}

	/**
	 * Steps done to prepare the data:
	 * 1. sanitize using default WP function;
	 * 2. use preg_replace() to leave only numbers and dashes;
	 * 3. transform the string to array;
	 * 4. the initial strings may have more than 2 dashes, resulting in big array - return first 3 array items;
	 * 5. remove all potentially empty values;
	 * 6. if there are less than 3 items in array, fill with 0 to have exactly 3 values (year, month, day - in this order).
	 *
	 * @since 1.1.0
	 *
	 * @param string $comment_date Potentially a date.
	 *
	 * @return array
	 */
	private function prepare_comment_date_string( $comment_date ) {

		return array_pad(
			array_filter(
				array_slice(
					explode( '-', $comment_date ),
					0,
					3
				)
			),
			3,
			0
		);
	}
}
