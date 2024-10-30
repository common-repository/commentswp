<?php

namespace CommentsWP\Helpers;

/**
 * Class HumanDate.
 *
 * @since 1.0.0
 */
class HumanDate {

	/**
	 * All possible parts.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $parts = [
		'years'   => null,
		'months'  => null,
		'weeks'   => null,
		'days'    => null,
		'hours'   => null,
		'minutes' => null,
		'seconds' => null,
	];

	/**
	 * Whether special rules for joining the values should be applied.
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	private $is_joined = false;

	/**
	 * Whether to return a shortened version of the part label.
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	private $is_short = false;

	/**
	 * How parts should be joined.
	 *
	 * @since 1.0.0
	 *
	 * @var string[]
	 */
	private $join = [
		'glue'  => ',',
		'final' => '',
	];

	/**
	 * HumanDate constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param int $from Timestamp from.
	 * @param int $to   Timestamp to.
	 */
	public function __construct( $from, $to = 0 ) {

		$diff = abs( (int) $from - (int) $to );

		$this->parts = array_filter( $this->generate_parts( $diff, [] ) );
	}

	/**
	 * Genearate parts based on the date.
	 *
	 * @since 1.0.0
	 *
	 * @param int   $diff  Seconds between dates, unprocessed.
	 * @param array $parts Parts.
	 *
	 * @return array
	 */
	private function generate_parts( $diff, $parts ) { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.MaxExceeded

		// phpcs:disable PSR2.ControlStructures.SwitchDeclaration.TerminatingComment,WPForms.Formatting.Switch.AddEmptyLineBefore
		switch ( true ) {
			case $diff >= YEAR_IN_SECONDS:
				$years = (int) floor( $diff / YEAR_IN_SECONDS );

				if ( $years === 0 ) {
					break;
				}

				$parts['years'] = $years;

				$diff -= $years * YEAR_IN_SECONDS;

			case $diff >= MONTH_IN_SECONDS:
				$months = (int) floor( $diff / MONTH_IN_SECONDS );

				if ( $months === 0 ) {
					break;
				}

				$parts['months'] = $months;

				$diff -= $months * MONTH_IN_SECONDS;

			case $diff >= WEEK_IN_SECONDS:
				$weeks = (int) floor( $diff / WEEK_IN_SECONDS );

				if ( $weeks === 0 ) {
					break;
				}

				$parts['weeks'] = $weeks;

				$diff -= $weeks * WEEK_IN_SECONDS;

			case $diff >= DAY_IN_SECONDS:
				$days = (int) floor( $diff / DAY_IN_SECONDS );

				if ( $days === 0 ) {
					break;
				}

				$parts['days'] = $days;

				$diff -= $days * DAY_IN_SECONDS;

			case $diff >= HOUR_IN_SECONDS:
				$hours = (int) floor( $diff / HOUR_IN_SECONDS );

				if ( $hours === 0 ) {
					break;
				}

				$parts['hours'] = $hours;

				$diff -= $hours * HOUR_IN_SECONDS;

			case $diff >= MINUTE_IN_SECONDS:
				$minutes = (int) floor( $diff / MINUTE_IN_SECONDS );

				if ( $minutes === 0 ) {
					break;
				}

				$parts['minutes'] = $minutes;

				$diff -= $minutes * MINUTE_IN_SECONDS;

			case $diff < MINUTE_IN_SECONDS:
				$seconds = $diff;

				if ( $seconds <= 1 ) {
					$seconds = 1;
				}

				$parts['seconds'] = $seconds;

				$diff = 0;
		}
		// phpcs:enable

		if ( $diff > 0 ) {
			$parts = $this->generate_parts( $diff, $parts );
		}

		return $parts;
	}

	/**
	 * Get the proper label for a specific part.
	 *
	 * @since 1.0.0
	 *
	 * @param string $part   Part key.
	 * @param int    $number Used to output a correct number and determine a singular or plural form.
	 *
	 * @return string
	 */
	private function get_label( $part, $number ) { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh

		if ( $this->is_short ) {
			return $this->get_label_short( $part, $number );
		}

		$label = '';

		switch ( $part ) {
			case 'years':
				/* translators: Time difference between two dates, in years. %d - number of years. */
				$label = sprintf( _n( '%d year', '%d years', $number, 'commentswp' ), $number );
				break;

			case 'months':
				/* translators: Time difference between two dates, in months. %d - number of months. */
				$label = sprintf( _n( '%d month', '%d months', $number, 'commentswp' ), $number );
				break;

			case 'weeks':
				/* translators: Time difference between two dates, in weeks. %d - number of weeks. */
				$label = sprintf( _n( '%d week', '%d weeks', $number, 'commentswp' ), $number );
				break;

			case 'days':
				/* translators: Time difference between two dates, in days. %d - number of days. */
				$label = sprintf( _n( '%d day', '%d days', $number, 'commentswp' ), $number );
				break;

			case 'hours':
				/* translators: Time difference between two dates, in hours. %d - number of hours. */
				$label = sprintf( _n( '%d hour', '%d hours', $number, 'commentswp' ), $number );
				break;

			case 'minutes':
				/* translators: Time difference between two dates, in minutes. %d - number of minutes. */
				$label = sprintf( _n( '%d minute', '%d minutes', $number, 'commentswp' ), $number );
				break;

			case 'seconds':
				/* translators: Time difference between two dates, in seconds. %d - number of seconds. */
				$label = sprintf( _n( '%d second', '%d seconds', $number, 'commentswp' ), $number );
				break;
		}

		return $label;
	}

	/**
	 * Get a proper shortened label for a specific part.
	 *
	 * @since 1.0.0
	 *
	 * @param string $part   Part key.
	 * @param int    $number Output a correct number.
	 *
	 * @return string
	 */
	private function get_label_short( $part, $number ) { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh

		$label = '';

		switch ( $part ) {
			case 'years':
				$label = sprintf( '%dy', $number );
				break;

			case 'months':
				$label = sprintf( '%dm', $number );
				break;

			case 'weeks':
				$label = sprintf( '%dw', $number );
				break;

			case 'days':
				$label = sprintf( '%dd', $number );
				break;

			case 'hours':
				$label = sprintf( '%dh', $number );
				break;

			case 'minutes':
				$label = sprintf( '%dmin', $number );
				break;

			case 'seconds':
				$label = sprintf( '%ds', $number );
				break;
		}

		return $label;
	}

	/**
	 * Whether and how exactly to join the parts.
	 * Usage:
	 *      ( new HumanDate( $from, $to ) )->joined()->get( 2 );
	 *
	 * @since 1.0.0
	 *
	 * @param string $glue       Main glue.
	 * @param string $final_glue Final glue.
	 *
	 * @return HumanDate
	 */
	public function joined( $glue = ',', $final_glue = '' ) {

		$this->is_joined = true;

		$this->join['glue']  = wp_kses_post( $glue );
		$this->join['final'] = wp_kses_post( $final_glue );

		return $this;
	}

	/**
	 * Implode array items.
	 *
	 * @since 1.0.0
	 *
	 * @param array $sliced Array to implode.
	 *
	 * @return string
	 */
	private function join( $sliced ) {

		$count = count( $sliced );

		if ( $this->join['final'] === '' ) {
			return implode( $this->join['glue'], $sliced );
		}

		if ( $count === 0 ) {
			return '';
		}

		if ( $count === 1 ) {
			return end( $sliced );
		}

		$final_part = array_pop( $sliced );

		return implode( $this->join['glue'], $sliced ) . $this->join['final'] . $final_part;
	}

	/**
	 * Which parts to return, empty means all.
	 * Usage:
	 *      ( new HumanDate( $from, $to ) )->select( [ 'hours', 'minutes' ] )->get();
	 *      ( new HumanDate( $from, $to ) )->select( 'days' )->get();
	 *
	 * @since 1.0.0
	 *
	 * @param array|string $keys List of parts to return.
	 *
	 * @return HumanDate
	 */
	public function select( $keys = [] ) {

		$keys = array_unique(
			array_filter(
				array_map( 'sanitize_key', (array) $keys )
			)
		);

		if ( empty( $keys ) ) {
			return $this;
		}

		$this->parts = array_intersect_key(
			$this->parts,
			array_flip( $keys )
		);

		return $this;
	}

	/**
	 * Exclude certain keys from parts. Technically opposite to the select() method.
	 * Usage:
	 *      ( new HumanDate( $from, $to ) )->exclude( [ 'hours', 'minutes' ] )->get();
	 *      ( new HumanDate( $from, $to ) )->exclude( 'days' )->get();
	 *
	 * @since 1.0.0
	 *
	 * @param array|string $keys Which parts to exclude. Empty means nothing to exclude.
	 *
	 * @return HumanDate
	 */
	public function exclude( $keys = [] ) {

		$keys = array_unique(
			array_filter(
				array_map( 'sanitize_key', (array) $keys )
			)
		);

		if ( empty( $keys ) ) {
			return $this;
		}

		foreach ( $keys as $part ) {
			unset( $this->parts[ $part ] );
		}

		return $this;
	}

	/**
	 * Whether to return full period names, or their shortened version.
	 * So it's either days or d, months or m, minutes or min, etc.
	 *
	 * @since 1.0.0
	 *
	 * @return HumanDate
	 */
	public function short() {

		$this->is_short = true;

		return $this;
	}

	/**
	 * Retrieve a specified number of parts.
	 * Usage:
	 *      ( new HumanDate( $from, $to ) )->get( 2 );
	 *
	 * @since 1.0.0
	 *
	 * @param int $count Number of parts to retrieve.
	 *
	 * @return string|array String if joined(), otherwise - array.
	 */
	public function get( $count = 0 ) {

		$count = absint( $count );

		if ( $count === 0 ) {
			$count = count( $this->parts );
		} else {
			$count = min( count( $this->parts ), $count );
		}

		$sliced = array_slice( $this->parts, 0, $count );

		foreach ( $sliced as $part => &$value ) {
			$sliced[ $part ] = $this->get_label( $part, $value );
		}
		unset( $value );

		if ( $this->is_joined ) {
			return $this->join( $sliced );
		}

		return $sliced;
	}

	/**
	 * Get first X number of parts. Alias to get().
	 * Usage:
	 *      ( new HumanDate( $from, $to ) )->first( 2 );
	 *
	 * @since 1.0.0
	 *
	 * @param int $count Number of parts to retrieve.
	 *
	 * @return string|array String if joined(), otherwise - array.
	 */
	public function first( $count = 0 ) {

		$count = absint( $count );

		if ( $count === 0 ) {
			$count = 1;
		}

		return $this->get( $count );
	}

	/**
	 * Get last X number of parts.
	 * Usage:
	 *      ( new HumanDate( $from, $to ) )->last( 2 );
	 *
	 * @since 1.0.0
	 *
	 * @param int $count Number of parts to retrieve.
	 *
	 * @return string|array String if joined(), otherwise - array.
	 */
	public function last( $count = 0 ) {

		$count = absint( $count );

		if ( $count === 0 ) {
			$count = 1;
		} elseif ( $count > 0 ) {
			$count = min( count( $this->parts ), $count );
		}

		$sliced = array_slice( $this->parts, count( $this->parts ) - $count );

		foreach ( $sliced as $part => &$value ) {
			$sliced[ $part ] = $this->get_label( $part, $value );
		}
		unset( $value );

		if ( $this->is_joined ) {
			return $this->join( $sliced );
		}

		return $sliced;
	}
}
