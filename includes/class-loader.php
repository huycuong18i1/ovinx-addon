<?php

/**
 * Register all actions and filters hooks for the plugin
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'Loader' ) ) {
	/**
	 * Register all actions and filters hooks for the plugin
	 * Class Loader
	 */
	class Loader {

		/**
		 * The array of actions hooks.
		 */
		protected $actions;

		/**
		 * The array of filters hooks.
		 */
		protected $filters;

		/**
		 * The array of shortcodes.
		 */
		protected $shortcodes;

		/**
		 * Init the collections used to maintain the actions and filters hooks.
		 */
		public function __construct() {
			$this->actions    = array();
			$this->filters    = array();
			$this->shortcodes = array();
		}
		/**
		 * A function that is used to register the actions and filters hooks into a single collection
		 */
		private function tfwc_add_hooks( $hooks, $hook_name, $component, $callback, $priority, $accepted_args ) {

			$hooks[] = array(
				'hook'          => $hook_name,
				'component'     => $component,
				'callback'      => $callback,
				'priority'      => $priority,
				'accepted_args' => $accepted_args
			);

			return $hooks;

		}
		/**
		 * A function that is used to register the shortcodes into a single collection
		 */
		private function tfwc_add_shortcodes( $shortcodes, $tag, $component, $callback ) {
			$shortcodes[] = array(
				'tag'       => $tag,
				'component' => $component,
				'callback'  => $callback,
			);
			return $shortcodes;
		}
		/**
		 * Add a new action to the collection to be registered hook in WordPress
		 */
		public function tfwc_add_action( $hook_name, $component, $callback, $priority = 10, $accepted_args = 1 ) {
			$this->actions = $this->tfwc_add_hooks( $this->actions, $hook_name, $component, $callback, $priority, $accepted_args );
		}
		/**
		 * Add a new filter to the collection to be registered hook in WordPress
		 */
		public function tfwc_add_filter( $hook_name, $component, $callback, $priority = 10, $accepted_args = 1 ) {
			$this->filters = $this->tfwc_add_hooks( $this->filters, $hook_name, $component, $callback, $priority, $accepted_args );
		}
		/**
		 * Add a new short code to the collection
		 */
		public function tfwc_add_shortcode( $tag, $component, $callback ) {
			$this->shortcodes = $this->tfwc_add_shortcodes( $this->shortcodes, $tag, $component, $callback );
		}

		/**
		 * Run the filters hook and actions hooks.
		 */
		public function tfwc_run() {
			foreach ( $this->filters as $hook ) {
				add_filter( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
			}
			foreach ( $this->actions as $hook ) {
				add_action( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
			}
			foreach ( $this->shortcodes as $shortcode ) {
				add_shortcode( $shortcode['tag'], array( $shortcode['component'], $shortcode['callback'] ) );
			}
		}
	}
}