<?php

namespace CommentsWP\Admin\Pages\Dashboard\Cards;

use CommentsWP\Admin\Pages\Dashboard\Cards\Templates\CardSmallIconTotal;

/**
 * Class SpamNumberCard.
 *
 * @since 1.0.0
 */
class SpamNumberCard extends CardSmallIconTotal {

	/**
	 * What type of comments this card counts.
	 *
	 * @since 1.0.0
	 */
	const SOURCE = 'spam';

	/**
	 * URL to card documentation article.
	 *
	 * @since 1.1.0
	 */
	const DOC_URL = 'https://commentswp.com/docs/default-number-comments-cards/#spam';

	/**
	 * Set some card-specific options.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$data = [
			'title' => esc_html__( 'Spam', 'commentswp' ),
		];

		parent::__construct( $data );
	}
}
