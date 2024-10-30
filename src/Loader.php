<?php

namespace CommentsWP;

/**
 * Class Loader.
 *
 * @since 1.0.0
 */
class Loader {

	/**
	 * Classes to register.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $classes = [];

	/**
	 * Loader init.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->populate_classes();

		commentswp()->register_bulk( $this->classes );
	}

	/**
	 * Populate the classes to register.
	 *
	 * @since 1.0.0
	 */
	private function populate_classes() {

		$this->populate_tasks();
		$this->populate_admin();
	}

	/**
	 * Populate Admin related classes.
	 *
	 * @since 1.0.0
	 */
	private function populate_admin() {

		$this->classes[] = [
			'name' => 'Admin\Admin',
			'id'   => 'admin',
		];
	}

	/**
	 * Populate migration classes.
	 *
	 * @since 1.0.0
	 */
	private function populate_migrations() {

		$this->classes[] = [
			'name'     => 'Migrations',
			'priority' => 0,
		];
	}

	/**
	 * Populate tasks-related classes.
	 *
	 * @since 1.0.0
	 */
	private function populate_tasks() {

		array_push(
			$this->classes,
			[
				'name' => 'Tasks\Tasks',
				'id'   => 'tasks',
				'hook' => 'init',
			],
			[
				'name' => 'Tasks\Meta',
				'id'   => 'tasks_meta',
				'hook' => false,
				'run'  => false,
			]
		);
	}
}
