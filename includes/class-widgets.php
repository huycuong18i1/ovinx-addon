<?php
if (!defined('ABSPATH')) {
	exit;
}
if (!class_exists('Register_Widgets')) {
	class Register_Widgets
	{
		/**
		 * Construct
		 */
		public function __construct() {
			require_once TF_PLUGIN_PATH . 'includes/wordpress-widgets/class-widget-recent-post.php';
			require_once TF_PLUGIN_PATH . 'includes/wordpress-widgets/class-widget-blog-categories.php';
			require_once TF_PLUGIN_PATH . 'includes/wordpress-widgets/class-widget-popular-tags.php';
			require_once TF_PLUGIN_PATH . 'includes/wordpress-widgets/class-widget-category-filter.php';
			require_once TF_PLUGIN_PATH . 'includes/wordpress-widgets/class-widget-available-filter.php';
			require_once TF_PLUGIN_PATH . 'includes/wordpress-widgets/class-widget-attribute-filter.php';
			require_once TF_PLUGIN_PATH . 'includes/wordpress-widgets/class-widget-rating-filter.php';
			// require_once TF_PLUGIN_PATH . 'includes/wordpress-widgets/class-widget-price-filter.php';
			require_once TF_PLUGIN_PATH . 'includes/wordpress-widgets/class-widget-price-slider-filter.php';
			require_once TF_PLUGIN_PATH . 'includes/wordpress-widgets/class-widget-brand-filter.php';
			require_once TF_PLUGIN_PATH . 'includes/wordpress-widgets/class-widget-socials.php';
		}

	}
}