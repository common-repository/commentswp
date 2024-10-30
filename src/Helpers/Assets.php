<?php

namespace CommentsWP\Helpers;

/**
 * Class Assets to help manage assets.
 *
 * @since 1.0.0
 */
class Assets {

	/**
	 * Based on the SCRIPT_DEBUG const adds or not the `.min` to the file name.
	 * Usage: `Assets::min( 'alpine.js' );`.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file Filename: alpine.js or tailwind.css, or jquery.plugin.js.
	 *
	 * @return string Will output either `alpine.js` or `alpine.min.js`
	 */
	public static function min( $file ) {

		$chunks = explode( '.', (string) $file );
		$ext    = (array) array_pop( $chunks );
		$min    = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? [] : [ 'min' ];

		return implode( '.', array_merge( $chunks, $min, $ext ) );
	}

	/**
	 * Define the version of an asset.
	 *
	 * @since 1.0.0
	 *
	 * @param string $default Default value.
	 *
	 * @return string Either the defined version, COMMENTSWP_VERSION if not provided, or time() when in SCRIPT_DEBUG mode.
	 */
	public static function ver( $default = '' ) {

		if ( empty( $default ) ) {
			$default = COMMENTSWP_VERSION;
		}

		return defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? time() : $default;
	}

	/**
	 * Get the URL to a file by its name.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file   File name relative to /assets/ directory in the plugin.
	 * @param bool   $minify Whether the file URL should lead to a minified file.
	 *
	 * @return string URL to the file.
	 */
	public static function url( $file, $minify = false ) {

		$file = untrailingslashit( (string) $file );

		if ( $minify ) {
			$file = self::min( $file );
		}

		return plugins_url( '/assets/' . $file, COMMENTSWP_PLUGIN_FILE );
	}

	/**
	 * Get the content of the SVG file.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file SVG file content to retrieve.
	 *
	 * @return string
	 */
	public static function svg( $file ) {

		$file = untrailingslashit( (string) $file );

		$path = plugin_dir_path( COMMENTSWP_PLUGIN_FILE ) . 'assets/' . $file;

		if ( is_readable( $path ) ) {
			return (string) file_get_contents( $path );
		}

		return '';
	}
}
