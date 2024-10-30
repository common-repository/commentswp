<?php

namespace CommentsWP\Admin\Pages\Dashboard\Cards\Templates;

/**
 * Class CardSmall.
 *
 * @since 1.0.0
 */
abstract class CardSmall extends Card {

	const TYPE = 'small';

	/**
	 * Card width.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $width = '1/4';

	/**
	 * Render the card on a page.
	 *
	 * @since 1.0.0
	 */
	public function render() {
		?>

		<div class="<?php echo esc_attr( $this->width() ); ?> commentswp-card <?php echo esc_attr( $this->type_css() ); ?>"
			x-data="<?php echo esc_attr( $this->class ); ?>"
			@period-changed.window="refresh()">

			<?php $this->render_menu(); ?>

			<div class="commentswp-card-content">

				<div class="commentswp-card-content-icon">
					<?php $this->render_icon(); ?>
				</div>

				<div class="commentswp-card-content-data commentswp-refreshable">
					<?php $this->render_data(); ?>
				</div>
			</div>
		</div>

		<?php
		$this->render_inline_bind_events();
	}

	/**
	 * Render the internal part of the icon div.
	 *
	 * @since 1.0.0
	 */
	protected function render_icon() {
		?>

		<div class="icon">
			<a href="<?php echo esc_url( $this->get_card_url() ); ?>"
				title="<?php esc_attr_e( 'See more', 'commentswp' ); ?>">
				<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
					<path d="M2 5a2 2 0 012-2h7a2 2 0 012 2v4a2 2 0 01-2 2H9l-3 3v-3H4a2 2 0 01-2-2V5z" />
					<path d="M15 7v2a4 4 0 01-4 4H9.828l-1.766 1.767c.28.149.599.233.938.233h2l3 3v-3h2a2 2 0 002-2V9a2 2 0 00-2-2h-1z" />
				</svg>
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

		return admin_url( 'edit-comments.php' );
	}
}
