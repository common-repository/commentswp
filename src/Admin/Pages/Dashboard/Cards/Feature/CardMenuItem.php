<?php

namespace CommentsWP\Admin\Pages\Dashboard\Cards\Feature;

use CommentsWP\Helpers\URL;

/**
 * Class CardMenuItem.
 *
 * @since 1.0.0
 */
class CardMenuItem {

	/**
	 * The card name.
	 *
	 * @since 1.1.0
	 *
	 * @var string
	 */
	protected $card_name = '';

	/**
	 * Menu item URL.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $url = '';

	/**
	 * Whether the URL should open in a new tab.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $is_blank = false;

	/**
	 * Whether to add UTM params to the URL.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $is_utm = true;

	/**
	 * Menu item label.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $label = '';

	/**
	 * CardMenuItem constructor.
	 *
	 * @since 1.1.0
	 *
	 * @param string $card_name Passing the card name to track it.
	 */
	public function __construct( $card_name ) {

		$this->card_name = sanitize_text_field( $card_name );
	}

	/**
	 * Set the URL of the menu item.
	 *
	 * @since 1.0.0
	 *
	 * @param string $url      URL.
	 * @param bool   $is_blank Whether this URL should be opened in a new tab or not.
	 * @param bool   $is_utm   Whether this URL should have UTM params.
	 *
	 * @return CardMenuItem
	 */
	public function url( $url, $is_blank = false, $is_utm = true ) {

		$this->url      = sanitize_url( $url );
		$this->is_blank = (bool) $is_blank;
		$this->is_utm   = (bool) $is_utm;

		return $this;
	}

	/**
	 * Set the label of the menu item.
	 *
	 * @since 1.0.0
	 *
	 * @param string $label Menu item label.
	 *
	 * @return CardMenuItem
	 */
	public function label( $label ) {

		$this->label = sanitize_text_field( $label );

		return $this;
	}

	/**
	 * Get the URL of the menu item.
	 *
	 * @since 1.0.0
	 * @since 1.1.0 Add UTM params to the URL.
	 *
	 * @return string
	 */
	public function get_url() {

		if ( $this->is_utm ) {
			return URL::add_utm( $this->url, 'Dashboard Card', $this->card_name, $this->get_label() );
		}

		return $this->url;
	}

	/**
	 * Get the URL target value of the menu item.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_target() {

		return $this->is_blank ? '_blank' : '_self';
	}

	/**
	 * Get the label of the menu item.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_label() {

		return $this->label;
	}
}
