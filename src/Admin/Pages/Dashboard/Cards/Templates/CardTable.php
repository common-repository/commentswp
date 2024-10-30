<?php

namespace CommentsWP\Admin\Pages\Dashboard\Cards\Templates;

/**
 * Class CardTable.
 *
 * @since 1.0.0
 */
abstract class CardTable extends Card {

	const TYPE = 'table';

	/**
	 * Card width.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $width = '1/2';

	/**
	 * Whether this card is filterable.
	 *
	 * @since 1.0.0
	 */
	const FILTERABLE = true;

	/**
	 * Render the card on a page.
	 *
	 * @since 1.0.0
	 */
	public function render() {
		?>

		<div class="<?php echo esc_attr( $this->width() ); ?> commentswp-card <?php echo esc_attr( $this->type_css() ); ?>"
			<?php if ( static::FILTERABLE ) : ?>
				x-data="<?php echo esc_attr( $this->class ); ?>"
				@period-changed.window="refresh()"
				@items-per-page-changed.window="refresh()"
				@<?php echo esc_attr( $this->class_low ); ?>-filters-changed.window="refresh()"
			<?php endif; ?>
		>

			<?php $this->render_menu(); ?>

			<div class="commentswp-card-content">
				<div class="commentswp-card-content-header">
					<h3><?php echo esc_html( $this->title() ); ?></h3>

					<?php $this->display_filters(); ?>
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
	 * Display table-specific filters if any.
	 *
	 * @since 1.0.0
	 */
	protected function display_filters() {
	}
}
