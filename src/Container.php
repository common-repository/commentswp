<?php

namespace CommentsWP;

use stdClass;
use CommentsWP\Vendor\Auryn\Injector;
use CommentsWP\Vendor\Auryn\InjectionException;
use CommentsWP\Vendor\Auryn\ConfigException;

/**
 * Trait Container.
 *
 * @since 1.0.0
 */
trait Container {

	/**
	 * Classes registry.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $registry = [];

	/**
	 * Injector for the dependency injection container.
	 *
	 * @since 1.0.0
	 *
	 * @var Injector
	 */
	private $injector;

	/**
	 * Register a class.
	 *
	 * @since 1.0.0
	 *
	 * @param array $class Class registration info.
	 *
	 * @throws InjectionException|ConfigException If a cyclic gets detected when provisioning.
	 */
	public function register( array $class ) { // phpcs:ignore WPForms.PHP.HooksMethod.InvalidPlaceForAddingHooks,Generic.Metrics.CyclomaticComplexity.MaxExceeded

		if ( empty( $class['name'] ) || ! is_string( $class['name'] ) ) {
			return;
		}

		if ( isset( $class['condition'] ) && empty( $class['condition'] ) ) {
			return;
		}

		$full_name = '\CommentsWP\\' . $class['name'];

		if ( ! class_exists( $full_name ) ) {
			return;
		}

		$this->injector = new Injector();

		$pattern  = '/[^a-zA-Z0-9_\\\-]/';
		$id       = isset( $class['id'] ) ? $class['id'] : '';
		$id       = $id ? preg_replace( $pattern, '', (string) $id ) : $id;
		$hook     = isset( $class['hook'] ) ? $class['hook'] : 'commentswp_loaded';
		$hook     = $hook ? preg_replace( $pattern, '', (string) $hook ) : $hook;
		$run      = isset( $class['run'] ) ? $class['run'] : 'init';
		$priority = isset( $class['priority'] ) && is_int( $class['priority'] ) ? $class['priority'] : 10;

		$callback = function () use ( $full_name, $id, $run ) {

			$instance = $this->injector->make( $full_name );

			if ( $id && ! array_key_exists( $id, $this->registry ) ) {
				$this->registry[ $id ] = $instance;
			}

			if ( $run && method_exists( $instance, $run ) ) {
				$instance->{$run}();
			}
		};

		if ( $hook ) {
			add_action( $hook, $callback, $priority );
		} else {
			$callback();
		}
	}

	/**
	 * Register classes in bulk.
	 *
	 * @since 1.0.0
	 *
	 * @param array $classes Classes to register.
	 */
	public function register_bulk( array $classes ) {

		foreach ( $classes as $class ) {
			$this->register( $class );
		}
	}

	/**
	 * Get a class instance from a registry.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name Class name or an alias.
	 *
	 * @return mixed|stdClass|null
	 */
	public function get( $name ) {

		if ( ! empty( $this->registry[ $name ] ) ) {
			return $this->registry[ $name ];
		}

		return new stdClass();
	}
}
