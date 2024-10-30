<?php

namespace CommentsWP;

use CommentsWP\Vendor\Auryn\ConfigException;
use CommentsWP\Vendor\Auryn\InjectionException;

/**
 * Class Injector.
 *
 * @since 1.0.0
 */
class Injector extends \CommentsWP\Vendor\Auryn\Injector {
	/**
	 * Instantiate/provision a class instance.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name Name of class.
	 * @param array  $args Construct arguments for this class.
	 *
	 * @return mixed
	 * @throws InjectionException|ConfigException If a cyclic gets detected when provisioning.
	 */
	public function make( $name, array $args = [] ) {

		$this->share( $name );

		return parent::make( $name, $args );
	}
}
