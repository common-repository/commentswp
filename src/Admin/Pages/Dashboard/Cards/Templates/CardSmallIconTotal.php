<?php

namespace CommentsWP\Admin\Pages\Dashboard\Cards\Templates;

use CommentsWP\Helpers\Calculations;
use CommentsWP\Admin\Pages\Dashboard\Cards\Feature\HasMenu;
use CommentsWP\Admin\Pages\Dashboard\Cards\Feature\CardMenuItem;

/**
 * Class CardSmallIconTotal.
 *
 * @since 1.0.0
 */
abstract class CardSmallIconTotal extends CardSmall {

	use HasMenu;

	/**
	 * What type of comments this card counts.
	 *
	 * @since 1.0.0
	 */
	const SOURCE = '';

	/**
	 * URL to card documentation article.
	 *
	 * @since 1.1.0
	 */
	const DOC_URL = 'https://commentswp.com/docs/';

	/**
	 * Set some card-specific options.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Common card data.
	 */
	public function __construct( $data ) {

		parent::__construct( $data );

		$this->register_menu(
			( new CardMenuItem( $this->get_class_name() ) )
				->url( static::DOC_URL, true )
				->label( __( 'Learn More', 'commentswp' ) )
		);
	}

	/**
	 * Do all calculations to generate card's data.
	 *
	 * @since 1.0.0
	 */
	protected function generate_data() {

		$this->data = Calculations::get_comments_count();
	}

	/**
	 * Render the internal part of the data div.
	 *
	 * @since 1.0.0
	 */
	protected function render_data() {

		if ( empty( $this->data ) ) {
			$this->populate_data();
		}
		?>

		<div class="label">
			<?php echo wp_kses_post( $this->title() ); ?>
		</div>

		<div class="value">
			<a href="<?php echo esc_url( $this->get_card_url() ); ?>" title="<?php esc_attr_e( 'See more', 'commentswp' ); ?>">
				<?php echo (int) $this->data[ static::SOURCE ]; ?>
			</a>
		</div>

		<?php
	}

	/**
	 * Get the URL this card should link to.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function get_card_url() {

		return add_query_arg(
			'comment_status',
			static::SOURCE,
			admin_url( 'edit-comments.php' )
		);
	}
}
