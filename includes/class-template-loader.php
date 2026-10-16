<?php

/**
 * The template loader functionality of the plugin.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'Template_Loader' ) ) {
	class Template_Loader {
		private static $_instance;

		public static function get_instance() {
			if ( self::$_instance == null ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		public function __construct() {
		}

		public function is_listing_taxonomy() {
			return is_tax( get_object_taxonomies( 'to_book' ) );
		}

		public function template_loader($template) {
            $file     = '';
            $file_arr = array();

            if (is_embed()) {
                return $template;
            }


            if ($file) {
                $template = locate_template(array_unique($file_arr));
                if (!$template) {
                    $template = TF_PLUGIN_PATH . 'woocommerce/templates/' . $file;
                }
            }
            return $template;
        }
	}
}