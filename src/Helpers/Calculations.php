<?php

namespace CommentsWP\Helpers;

/**
 * Class Calculations.
 *
 * @since 1.0.0
 */
class Calculations {

	/**
	 * Get comments count for these statuses: approved, pending, spam, trash.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function get_comments_count() {

		global $wpdb;

		static $comments_count;

		if ( $comments_count !== null ) {
			return $comments_count;
		}

		$build_where_period = commentswp()->get( 'admin' )->dashboard->filters->build_sql_where_period();

		$totals = $wpdb->get_results(
			"SELECT 
       			comment_approved as comment_status, 
       			COUNT( * ) AS total
			FROM $wpdb->comments
			WHERE comment_type = 'comment' 
				$build_where_period
			GROUP BY comment_approved;",
			ARRAY_A
		);

		$comments_count = [
			'approved'  => 0,
			'moderated' => 0,
			'spam'      => 0,
			'trash'     => 0,
			'total'     => 0,
		];

		foreach ( $totals as $row ) {
			switch ( $row['comment_status'] ) {
				case 'trash':
					$comments_count['trash'] = $row['total'];
					break;

				case 'spam':
					$comments_count['spam'] = $row['total'];
					break;

				case '1':
					$comments_count['approved'] = $row['total'];
					break;

				case '0':
					$comments_count['moderated'] = $row['total'];
					break;
			}
		}

		$comments_count = array_map( 'intval', $comments_count );

		return $comments_count;
	}
}
