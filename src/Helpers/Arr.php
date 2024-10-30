<?php

namespace CommentsWP\Helpers;

/**
 * Class Arr, methods are copied from the \Illuminate\Support\Arr class.
 *
 * @since 1.0.0
 */
class Arr {

	/**
	 * Flatten a multi-dimensional array into a single level.
	 *
	 * @since 1.0.0
	 *
	 * @param array $array Array to flatten.
	 * @param int   $depth How deep should it go.
	 *
	 * @return array
	 */
	public static function flatten( $array, $depth = INF ) {

		$result = [];

		foreach ( $array as $item ) {
			if ( ! is_array( $item ) ) {
				$result[] = $item;
			} elseif ( $depth === 1 ) {
				$result = array_merge( $result, array_values( $item ) );
			} else {
				$result = array_merge( $result, static::flatten( $item, $depth - 1 ) );
			}
		}

		return $result;
	}
}
