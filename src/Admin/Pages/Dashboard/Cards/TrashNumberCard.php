<?php

namespace CommentsWP\Admin\Pages\Dashboard\Cards;

use CommentsWP\Admin\Pages\Dashboard\Cards\Templates\CardSmallIconTotal;

/**
 * Class TrashNumberCard.
 *
 * @since 1.0.0
 */
class TrashNumberCard extends CardSmallIconTotal {

	/**
	 * What type of comments this card counts.
	 *
	 * @since 1.0.0
	 */
	const SOURCE = 'trash';

	/**
	 * URL to card documentation article.
	 *
	 * @since 1.1.0
	 */
	const DOC_URL = 'https://commentswp.com/docs/default-number-comments-cards/#trash';

	/**
	 * Set some card-specific options.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$data = [
			'title' => esc_html__( 'Trash', 'commentswp' ),
		];

		parent::__construct( $data );
	}
}
