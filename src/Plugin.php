<?php

namespace CommentsWP;

/**
 * Class Plugin.
 *
 * @since 1.0.0
 */
final class Plugin {
	use Container;

	/**
	 * I'm single.
	 *
	 * @since 1.0.0
	 *
	 * @var Plugin
	 */
	private static $instance;

	/**
	 * You shall not pass.
	 *
	 * @since 1.0.0
	 */
	private function __construct(){}

	/**
	 * Main instance.
	 *
	 * Only one instance of the plugin exists in memory at any one time.
	 * Also prevent the need to define globals all over the place.
	 *
	 * @since 1.0.0
	 *
	 * @return Plugin
	 */
	public static function instance() {

		if (
			self::$instance === null ||
			! self::$instance instanceof self
		) {
			self::$instance = new self();

			self::$instance->init();
		}

		return self::$instance;
	}

	/**
	 * Let's start the ball rolling.
	 *
	 * @since 1.0.0
	 */
	private function init() {

		// Load the class loader.
		$this->register(
			[
				'name' => 'Loader',
				'hook' => false,
			]
		);
	}
}
