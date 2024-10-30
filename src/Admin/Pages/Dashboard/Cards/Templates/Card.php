<?php

namespace CommentsWP\Admin\Pages\Dashboard\Cards\Templates;

use CommentsWP\Helpers\Transient;
use CommentsWP\Admin\Pages\Dashboard\Filters;

/**
 * Class Card.
 *
 * @since 1.0.0
 */
abstract class Card {

	const TYPE = '';

	/**
	 * Card title.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $title = '';

	/**
	 * Card width.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $width = '1/4';

	/**
	 * The raw data needed for the card to be rendered.
	 *
	 * @since 1.0.0
	 *
	 * @var mixed
	 */
	protected $data;

	/**
	 * For how long in the past all the calculations should be made for.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $period;

	/**
	 * Number of items to display per table.
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	protected $items_per_page;

	/**
	 * Class name.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $class;

	/**
	 * Class name in lowercase.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $class_low;

	/**
	 * Current Dashboard filters.
	 *
	 * @since 1.0.0
	 *
	 * @var Filters
	 */
	protected $filters;

	/**
	 * Card constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Common card data.
	 */
	public function __construct( array $data ) {

		$this->class     = $this->get_class_name();
		$this->class_low = strtolower( $this->get_class_name() );

		$this->set_properties( $data );

		$this->set_filters();
	}

	/**
	 * Set card properties, like title and description.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Common card data.
	 */
	protected function set_properties( array $data ) {

		// These 2 class properties can be translatable.
		if ( isset( $data['title'] ) ) {
			$this->title = $data['title'];
		}
	}

	/**
	 * Set default card filter values.
	 *
	 * @since 1.0.0
	 */
	protected function set_filters() {

		$admin         = commentswp()->get( 'admin' );
		$this->filters = $admin && ! empty( $admin->dashboard->filters ) ? $admin->dashboard->filters : null;

		if ( $this->filters === null ) {
			$this->filters = new Filters();
		}
	}

	/**
	 * Process card-specific hooks.
	 *
	 * @since 1.0.0
	 */
	public function hooks() {

		$class_low = strtolower( $this->get_class_name() );

		add_action( "wp_ajax_commentswp_{$class_low}_refresh", [ $this, 'ajax_changed_filters' ] );
	}

	/**
	 * Process the "changed per table value" event via AJAX.
	 *
	 * @since 1.0.0
	 */
	public function ajax_changed_filters() {

		check_ajax_referer( 'commentswp_dashboard_filters' );

		$this->process_dashboard_filters();
		$this->process_card_filters();

		// Remove the data that might have been cached.
		unset( $this->data );

		// After saving the values - they may have changed, so get them again.
		$this->set_filters();

		// Finally, regenerate the HTML with new data according to new filters.
		wp_send_json_success(
			$this->get_refreshed_card()
		);
	}

	/**
	 * Process dashboard-specific filters.
	 *
	 * @since 1.0.0
	 */
	protected function process_dashboard_filters() {

		// phpcs:disable WordPress.Security.NonceVerification.Missing
		if ( empty( $_POST['itemsPerPage'] ) || empty( $_POST['period'] ) ) {
			wp_send_json_error();
		}

		commentswp()->get( 'admin' )->dashboard->filters->set_bulk(
			[
				'period'         => sanitize_key( $_POST['period'] ),
				'items_per_page' => absint( $_POST['itemsPerPage'] ),
			]
		);
		// phpcs:enable WordPress.Security.NonceVerification.Missing
	}

	/**
	 * Process card-specific filters, if any.
	 *
	 * @since 1.0.0
	 */
	protected function process_card_filters() {
	}

	/**
	 * Regenerate all the HTML needed to render the table.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function get_refreshed_card() {

		ob_start();

		$this->render_data();

		return ob_get_clean();
	}

	/**
	 * Get the card title.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function title() {

		return $this->title;
	}

	/**
	 * Get HTML classes that will properly render the card.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function width() { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh,Generic.Metrics.CyclomaticComplexity.MaxExceeded

		// phpcs:disable WPForms.Formatting.EmptyLineAfterAssigmentVariables.AddEmptyLine,WPForms.Formatting.Switch.AddEmptyLineBefore
		switch ( $this->width ) {
			case '1':
				$classes = 'pure-u-1';
				break;
			case '1/2':
			case '2/4':
				$classes = 'pure-u-1 pure-u-md-1-2';
				break;
			case '1/3':
				$classes = 'pure-u-1 pure-u-md-1-3';
				break;
			case '2/3':
				$classes = 'pure-u-1 pure-u-md-2-3';
				break;
			case '3/4':
				$classes = 'pure-u-1 pure-u-md-3-4';
				break;
			case '1/4':
			default:
				$classes = 'pure-u-1 pure-u-sm-1-2 pure-u-lg-1-4';
				break;
		}
		// phpcs:enable

		return $classes;
	}

	/**
	 * Prepare/retrieve cached card's data.
	 *
	 * @since 1.0.0
	 */
	protected function populate_data() {

		$data = Transient::get( $this->cache_key() );

		if ( $data ) {
			$this->data = $data;

			return;
		}

		$this->generate_data();

		$this->cache_data();
	}

	/**
	 * Do all calculations to generate card's data.
	 *
	 * @since 1.0.0
	 */
	abstract protected function generate_data();

	/**
	 * Get the key used to cache data.
	 *
	 * @since 1.0.0
	 */
	protected function cache_key() {

		return $this->class_low . '_' . md5( wp_json_encode( $this->filters->all() ) );
	}

	/**
	 * Save the data in cache.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True when data is cached, false otherwise.
	 */
	protected function cache_data() {

		//$expire_in = strtotime( wp_date( 'Y-m-d 23:59:59' ) ) - time();
		$expire_in = MINUTE_IN_SECONDS;

		return Transient::set( $this->cache_key(), $this->data, $expire_in );
	}

	/**
	 * Render the card menu.
	 * Usable only if specific cards utilize HasMenu trait.
	 *
	 * @since 1.0.0
	 */
	protected function render_menu() {
	}

	/**
	 * Render the card on a page.
	 *
	 * @since 1.0.0
	 */
	abstract public function render();

	/**
	 * Render table-specific part of the card.
	 *
	 * @since 1.0.0
	 */
	abstract protected function render_data();

	/**
	 * Return a CSS class for the type of the current card.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function type_css() {

		if ( empty( static::TYPE ) ) {
			return '';
		}

		return implode(
			' ',
			[
				'commentswp-card-' . sanitize_key( static::TYPE ),
				'commentswp-card-' . $this->class_low,
			]
		);
	}

	/**
	 * Retrieve the class name of the current card.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function get_class_name() {

		return substr( strrchr( get_class( $this ), '\\' ), 1 );
	}

	/**
	 * Generate alpine-specific bind events.
	 *
	 * @since 1.0.0
	 */
	protected function render_inline_bind_events() {

		$class     = $this->get_class_name();
		$class_low = strtolower( $class );
		?>

		<script>//@formatter:off
		document.addEventListener( 'alpine:init', () => {
			Alpine.data( '<?php echo esc_attr( $class ); ?>', () => ( {
				tableFilters: {},
				refresh() {
					jQuery
						.ajax({
							type: "POST",
							url: ajaxurl,
							data: {
								action: 'commentswp_<?php echo esc_attr( $class_low ); ?>_refresh',
								itemsPerPage: this.itemsPerPage,
								period: this.period,
								tableFilters: this.tableFilters,
								_ajax_nonce: this._ajax_nonce,
							},
							beforeSend: function() {
								jQuery( '.commentswp-card-<?php echo esc_attr( $class_low ); ?>' ).addClass( 'loading' );
							},
							dataType: 'json'
						})
						.done( function( response ) {
							// Do nothing if not successful or empty data.
							if ( ! response.success || ! response.data.length ) {
								return;
							}

							jQuery( '.commentswp-card-<?php echo esc_attr( $class_low ); ?> .commentswp-refreshable' )
								.html( response.data );
						} )
						.always(function() {
							jQuery( '.commentswp-card-<?php echo esc_attr( $class_low ); ?>' ).removeClass( 'loading' );
						});
				},
			} ) );
		} );
		//@formatter:on
		</script>

		<?php
	}
}
