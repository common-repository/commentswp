<?php

namespace CommentsWP\Admin\Pages\Dashboard\Cards;

/**
 * Class SuggestFeaturePlaceholder.
 *
 * @since 1.1.0
 */
class SuggestFeaturePlaceholder extends Templates\CardTable {

	/**
	 * Whether this card is filterable.
	 *
	 * @since 1.1.0
	 */
	const FILTERABLE = false;

	/**
	 * Provide card's default data.
	 *
	 * @since 1.1.0
	 */
	public function __construct() {

		parent::__construct(
			[
				'title' => esc_html__( 'Suggest a Feature', 'commentswp' ),
			]
		);
	}

	/**
	 * Do all calculations to generate card's data.
	 *
	 * @since 1.1.0
	 */
	protected function generate_data() {

		$this->data = [];
	}

	/**
	 * Render the card on a page.
	 *
	 * @since 1.1.0
	 */
	public function render() {
		?>

		<div class="<?php echo esc_attr( $this->width() ); ?> commentswp-card <?php echo esc_attr( $this->type_css() ); ?>">

			<div class="commentswp-card-content">
				<div class="commentswp-card-content-header" style="margin-bottom: 20px; height: 30px">
					<h3><?php echo esc_html( $this->title() ); ?></h3>
				</div>

				<div class="commentswp-card-content-data" style="margin-top: 0.5em;">
					<p style="padding-top: 7px"><?php esc_html_e( 'Do you have an idea or suggestion for CommentsWP? If you have thoughts on features or improvements - please reach out!', 'commentswp' ); ?></p>
					<p><?php esc_html_e( 'Any feedback and insights from actual users is appreciated.', 'commentswp' ); ?></p>
					<p><a href="https://commentswp.com/submit-feedback/" target="_blank" class="button button-primary"><?php esc_html_e( 'Suggest a Feature', 'commentswp' ); ?></a></p>
				</div>
			</div>

		</div>

		<?php
	}

	/**
	 * Render table-specific part of the card.
	 *
	 * @since 1.1.0
	 */
	protected function render_data() {
		// This method does nothing.
	}
}
