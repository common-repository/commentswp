<?php

namespace CommentsWP\Admin\Pages\Dashboard\Cards;

use CommentsWP\Admin\Pages\Dashboard\Cards\Templates\CardSmallIconTotal;

/**
 * Class PendingCommentsNumberCard.
 *
 * @since 1.0.0
 */
class PendingNumberCard extends CardSmallIconTotal {

	/**
	 * What type of comments this card counts.
	 *
	 * @since 1.0.0
	 */
	const SOURCE = 'moderated';

	/**
	 * URL to card documentation article.
	 *
	 * @since 1.1.0
	 */
	const DOC_URL = 'https://commentswp.com/docs/default-number-comments-cards/#pending';

	/**
	 * Set some card-specific options.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$data = [
			'title' => esc_html__( 'Pending', 'commentswp' ),
		];

		parent::__construct( $data );
	}
}
