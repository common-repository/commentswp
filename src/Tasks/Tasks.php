<?php

namespace CommentsWP\Tasks;

use CommentsWP\Helpers\Transient;

/**
 * Class Tasks manages the tasks queue and provides API to work with it.
 * Inspired by WPForms.
 *
 * @since 1.0.0
 */
class Tasks {

	/**
	 * Group that will be assigned to all actions.
	 *
	 * @since 1.0.0
	 */
	const GROUP = 'commentswp';

	/**
	 * Perform certain things on class init.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		// Register tasks.
		foreach ( $this->get_tasks() as $task ) {

			if ( ! is_subclass_of( $task, Task::class ) ) {
				continue;
			}

			new $task();
		}

		$this->hooks();
	}

	/**
	 * Register all hooks.
	 *
	 * @since 1.0.0
	 */
	public function hooks() {

		add_action( 'delete_expired_transients', [ Transient::class, 'delete_all_expired' ], 11 );
	}

	/**
	 * Get the list of default scheduled tasks.
	 * Tasks, that are fired under certain specific circumstances
	 * (like sending form submission email notifications)
	 * are not listed here.
	 *
	 * @since 1.0.0
	 *
	 * @return Task[] List of tasks classes.
	 */
	public function get_tasks() {

		if ( ! $this->is_usable() ) {
			return [];
		}

		$tasks = [
			//Tasks\ExampleTask::class,
		];

		return apply_filters( 'commentswp_tasks_get_tasks', $tasks );
	}

	/**
	 * Create a new task.
	 * Used for "inline" tasks, that require additional information
	 * from the plugin runtime before they can be scheduled.
	 *
	 * Example:
	 *     commentswp()->get( 'tasks' )
	 *              ->create( 'i_am_the_dude' )
	 *              ->async()
	 *              ->params( 'The Big Lebowski', 1998 )
	 *              ->register();
	 *
	 * This `i_am_the_dude` action will be later processed as:
	 *     add_action( 'i_am_the_dude', 'thats_what_you_call_me' );
	 *
	 * Function `thats_what_you_call_me()` will receive `$meta_id` param,
	 * and you will be able to receive all params from the action like this:
	 *     $meta = ( new Meta() )->get( (int) $meta_id );
	 *     list( $name, $year ) = $meta->data;
	 *
	 * @since 1.0.0
	 *
	 * @param string $action Action that will be used as a hook.
	 *
	 * @return Task
	 */
	public function create( $action ) {

		return new Task( $action );
	}

	/**
	 * Cancel all the AS actions for a group.
	 *
	 * @since 1.0.0
	 *
	 * @param string $group Group to cancel all actions for.
	 */
	public function cancel_all( $group = '' ) {

		if ( empty( $group ) ) {
			$group = self::GROUP;
		} else {
			$group = sanitize_key( $group );
		}

		if ( class_exists( 'ActionScheduler_DBStore' ) ) {
			\ActionScheduler_DBStore::instance()->cancel_actions_by_group( $group );
		}
	}

	/**
	 * Whether ActionScheduler thinks that it has migrated or not.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_usable() {

		// No tasks if ActionScheduler wasn't loaded.
		if ( ! class_exists( 'ActionScheduler_DataController' ) ) {
			return false;
		}

		return \ActionScheduler_DataController::is_migration_complete();
	}

	/**
	 * Whether task has been scheduled and is pending.
	 *
	 * @since 1.0.0
	 *
	 * @param string $hook Hook to check for.
	 *
	 * @return bool
	 */
	public function is_scheduled( $hook ) {

		if ( ! function_exists( 'as_next_scheduled_action' ) ) {
			return false;
		}

		return as_next_scheduled_action( $hook );
	}
}
