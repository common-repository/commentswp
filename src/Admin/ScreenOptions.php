<?php

namespace CommentsWP\Admin;

use CommentsWP\Helpers\Arr;

/**
 * Class ScreenOptions.
 *
 * @see   https://github.com/jazzsequence/WordPress-Screen-Options-Framework
 *
 * @since 1.0.0
 */
class ScreenOptions {

	/**
	 * The admin page unique hook suffix.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $hook_suffix;

	/**
	 * ScreenOptions constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->hook_suffix = Admin::SLUG;
	}

	/**
	 * Register all the hooks.
	 *
	 * @since 1.0.0
	 */
	public function hooks() {

		add_action( "load-$this->hook_suffix", [ $this, 'get_screen_options' ] );
		add_filter( 'screen_settings', [ $this, 'show_screen_options' ], 10, 2 );
		add_filter( 'set-screen-option', [ $this, 'set_option' ], 11, 3 );
	}

	/**
	 * Options inside the Screen Options panel.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function options() {

		$admin = commentswp()->get( 'admin' );

		return [
			'cards' => Arr::flatten( $admin->dashboard->get_cards() ),
		];
	}

	/**
	 * Array of screen options to display.
	 *
	 * @since 1.0.0
	 *
	 * @return array The screen option function names.
	 */
	private function screen_options() {

		$screen_options = [];

		foreach ( $this->options() as $option => $choices ) {
			switch ( $option ) {
				case 'cards':
					$cards = array_map(
						static function ( $card ) {

							return [
								'option' => get_class( $card ),
								'title'  => $card->title(),
							];
						},
						$choices
					);

					$screen_options = array_merge( $screen_options, $cards );
					break;

				default:
					$screen_options[] = [
						'option' => $option,
						'title'  => ucwords( $option ),
					];
			}
		}

		return $screen_options;
	}

	/**
	 * Register the screen options.
	 *
	 * @since 1.0.0
	 */
	public function get_screen_options() {

		$screen = get_current_screen();

		if ( ! is_object( $screen ) || $this->hook_suffix !== $screen->id ) {
			return;
		}

		// Loop through all the options and add a screen option for each.
		foreach ( $this->options() as $option => $choices ) {

			switch ( $option ) {
				case 'cards':
					foreach ( $choices as $choice ) {
						$class_name = get_class( $choice );

						add_screen_option(
							"commentswp_screen_options_{$class_name}",
							[
								'option' => $class_name,
								'value'  => true,
							]
						);
					}
					break;

				default:
					add_screen_option(
						"commentswp_screen_options_$option",
						[
							'option' => $option,
							'value'  => true,
						]
					);
			}
		}
	}

	/**
	 * The HTML markup to wrap around each option.
	 *
	 * @since 1.0.0
	 */
	public function before() {

		?>
		<fieldset>
		<input type="hidden" name="wp_screen_options_nonce" value="<?php echo esc_textarea( wp_create_nonce( 'wp_screen_options_nonce' ) ); ?>">
		<legend><?php esc_html_e( 'Comments Dashboard', 'commentswp' ); ?></legend>
		<div class="metabox-prefs">
		<div><input type="hidden" name="wp_screen_options[option]" value="commentswp_screen_options" /></div>
		<div><input type="hidden" name="wp_screen_options[value]" value="yes" /></div>
		<div class="commentswp_screen_options_custom_fields">
		<?php
	}

	/**
	 * The HTML markup to close the options.
	 *
	 * @since 1.0.0
	 */
	public function after() {

		?>
		</div><!-- commentswp_screen_options_custom_fields -->
		</div><!-- metabox-prefs -->
		</fieldset>
		<br class="clear">
		<?php
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo get_submit_button( __( 'Apply', 'commentswp' ), 'button', 'screen-options-apply', false );
	}

	/**
	 * Display a screen option.
	 *
	 * @since 1.0.0
	 *
	 * @param string $title  The title to display.
	 * @param string $option The name of the option we're displaying.
	 */
	public function show_option( $title, $option ) {

		$screen    = get_current_screen();
		$id        = "commentswp_screen_options_$option";
		$user_meta = get_user_meta( get_current_user_id(), 'wordpress_screen_options_demo_options', true );

		// Check if the screen options have been saved. If so, use the saved value. Otherwise, use the default values.
		if ( $user_meta ) {
			$checked = array_key_exists( $option, $user_meta );
		} else {
			$checked = $screen->get_option( $id, 'value' ) ? true : false;
		}
		?>

		<label for="<?php echo esc_attr( $id ); ?>">
			<input type="checkbox" name="commentswp_screen_options[<?php echo esc_attr( $option ); ?>]" class="commentswp-screen-options-demo" id="<?php echo esc_attr( $id ); ?>" <?php checked( $checked ); ?>/> <?php echo esc_html( $title ); ?>
		</label>

		<?php
	}

	/**
	 * Render the screen options block.
	 *
	 * @since 1.0.0
	 *
	 * @param string $status The screen options markup.
	 * @param object $args   An object of screen options data.
	 *
	 * @return string The filtered screen options block.
	 */
	public function show_screen_options( $status, $args ) {

		if ( $this->hook_suffix !== $args->base ) {
			return $status;
		}

		ob_start();

		$this->before();
		foreach ( $this->screen_options() as $screen_option ) {
			$this->show_option( $screen_option['title'], $screen_option['option'] );
		}
		$this->after();

		return ob_get_clean();
	}

	/**
	 * Save the screen option setting.
	 *
	 * @since 1.0.0
	 *
	 * @param string $status The default value for the filter. Using anything other than false assumes you are handling saving the option.
	 * @param string $option The option name.
	 * @param array  $value  Whatever option you're setting.
	 */
	public function set_option( $status, $option, $value ) {

		if ( isset( $_POST['wp_screen_options_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wp_screen_options_nonce'] ) ), 'wp_screen_options_nonce' ) ) {
			if ( 'commentswp_screen_options_demo_options' === $option ) {
				$value = isset( $_POST['wordpress_screen_options_demo'] ) && is_array( $_POST['wordpress_screen_options_demo'] ) ? $_POST['wordpress_screen_options_demo'] : []; // WPCS: Sanitization ok.
			}
		}

		return $value;
	}
}
