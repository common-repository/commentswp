<?php

namespace CommentsWP\Admin\Pages\Dashboard\Cards\Feature;

/**
 * Trait HasMenu.
 *
 * @since 1.0.0
 */
trait HasMenu {

	/**
	 * List of menu links.
	 *
	 * @since 1.0.0
	 *
	 * @var CardMenuItem[]
	 */
	protected $menu = [];

	/**
	 * Register menu items.
	 *
	 * @since 1.0.0
	 *
	 * @param CardMenuItem|CardMenuItem[] $items List of menu items.
	 */
	protected function register_menu( $items ) {

		if ( ! is_array( $items ) ) {
			$items = [ $items ];
		}

		// Make sure all the classes are actually of a proper type.
		$this->menu = array_filter(
			$items,
			static function ( $item ) {

				return is_a( $item, CardMenuItem::class );
			}
		);
	}

	/**
	 * Get the potentially filtered list of menu links.
	 *
	 * @since 1.0.0
	 *
	 * @return CardMenuItem[]
	 */
	protected function get_menu_items() {

		return $this->menu;
	}

	/**
	 * Render the card context menu.
	 *
	 * @since 1.0.0
	 */
	protected function render_menu() {

		$menu = $this->get_menu_items();

		if ( empty( $menu ) ) {
			return;
		}
		?>

		<div class="commentswp-card-menu"
			x-data="{
	            open: false,
	            toggle() {
	                if (this.open) {
	                    return this.close()
	                }

	                this.$refs.button.focus()

	                this.open = true
	            },
	            close(focusAfter) {
	                if (! this.open) return

	                this.open = false

	                focusAfter && focusAfter.focus()
	            }
	        }"
			x-on:keydown.escape.prevent.stop="close($refs.button)"
			x-on:focusin.window="! $refs.panel.contains($event.target) && close()"
			x-id="['dropdown-button']"
		>
			<!-- Button -->
			<button title="<?php esc_attr_e( 'Menu', 'commentswp' ); ?>" type="button"
				x-ref="button"
				x-on:click="toggle()"
				:aria-expanded="open"
				:aria-controls="$id('dropdown-button')"
			>
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
					<path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
				</svg>
			</button>

			<!-- Panel -->
			<div class="commentswp-card-menu-panel" style="display: none"
				x-ref="panel"
				x-show="open"
				x-transition.origin.top.left
				x-on:click.outside="close($refs.button)"
				:id="$id('dropdown-button')"
			>

				<?php /** @var CardMenuItem $item */ ?>
				<?php foreach ( $menu as $item ) : ?>

					<div class="commentswp-card-menu-item">
						<a href="<?php echo esc_url( $item->get_url() ); ?>" target="<?php echo esc_attr( $item->get_target() ); ?>">
							<?php echo esc_html( $item->get_label() ); ?>
						</a>
					</div>

				<?php endforeach; ?>
			</div>
		</div>

		<?php
	}
}
