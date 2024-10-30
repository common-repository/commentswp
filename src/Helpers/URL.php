<?php

namespace CommentsWP\Helpers;

/**
 * Class URL.
 *
 * @since 1.1.0
 */
class URL {

	/**
	 * Add UTM tags to a link that allows detecting traffic sources for our or partners' websites.
	 *
	 * @since 1.1.0
	 *
	 * @param string $url     Link to which you need to add UTM tags.
	 * @param string $medium  The page or location description. Check your current page and try to find
	 *                        and use an already existing medium for links otherwise, use a page name.
	 * @param string $content The feature's name, the button's content, the link's text, or something
	 *                        else that describes the element that contains the link.
	 * @param string $term    Additional information for the content that makes the link more unique.
	 *
	 * @return string
	 */
	public static function add_utm( $url, $medium, $content = '', $term = '' ) {

		return add_query_arg(
			array_filter(
				[
					'utm_campaign' => 'liteplugin',
					'utm_source'   => strpos( $url, 'https://commentswp.com' ) === 0 ? 'WordPress' : 'wpformsplugin',
					'utm_medium'   => rawurlencode( $medium ),
					'utm_content'  => rawurlencode( $content ),
					'utm_term'     => rawurlencode( $term ),
				]
			),
			$url
		);
	}
}
