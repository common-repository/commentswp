<?php

namespace CommentsWP\Admin\Pages\Dashboard\Cards\Templates;

/**
 * Class CardSmallDuo.
 *
 * @since 1.0.0
 */
abstract class CardSmallDuo extends CardSmall {

	const TYPE = 'small-duo';

	/**
	 * Card width.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $width = '1/4';

	/**
	 * Card title, section one.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $title_one = '';

	/**
	 * Card title, section two.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $title_two = '';

	/**
	 * Card constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Common card data.
	 */
	public function set_properties( array $data ) {

		// These 2 class properties can be translatable.
		if ( isset( $data['title_one'] ) ) {
			$this->title_one = $data['title_one'];
			$this->title     = $data['title_one'];
		}
		if ( isset( $data['title_two'] ) ) {
			$this->title_two = $data['title_two'];
		}
	}

	/**
	 * Get the card title.
	 *
	 * @since 1.0.0
	 *
	 * @param string $which Title of which section to retrieve.
	 *
	 * @return string Return a title.
	 */
	public function title( $which = 'one' ) {

		switch ( $which ) {
			case 'two':
				$this->title = $this->title_two;
				break;

			case 'one':
			default:
				$this->title = $this->title_one;
		}

		return $this->title;
	}

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

			<div class="commentswp-card-content commentswp-refreshable">
				<?php $this->render_data(); ?>
			</div>
		</div>

		<?php
		$this->render_inline_bind_events();
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

		<div class="commentswp-card-content-one">
			<?php $this->render_one(); ?>
		</div>

		<div class="commentswp-card-content-two">
			<?php $this->render_two(); ?>
		</div>

		<?php
	}

	/**
	 * Render the 1st part.
	 *
	 * @since 1.0.0
	 */
	abstract protected function render_one();

	/**
	 * Render the 2nd part.
	 *
	 * @since 1.0.0
	 */
	abstract protected function render_two();

	/**
	 * Not used in this class. Leftover from exteing CardSmall.
	 *
	 * @since 1.0.0
	 */
	protected function render_icon() {}
}
