<?php
/*
Plugin Name: ovinx Addon
Description: ovinx Addon Plugin For ovinx Theme
Author: Themesflat
Text Domain: ovinx-addon
Version: 1.0.0
*/

if (!defined('WPINC')) {
	die;
}

if (!defined('TF_PLUGIN_PROTOCOL')) {
	define('TF_PLUGIN_PROTOCOL', (is_ssl()) ? 'https' : 'http');
}

if (!defined('TF_PLUGIN_PATH')) {
	$plugin_dir = plugin_dir_path(__FILE__);
	define('TF_PLUGIN_PATH', $plugin_dir);
}

if (!defined('TF_PLUGIN_URL')) {
	$plugin_url = plugins_url('/', __FILE__);
	define('TF_PLUGIN_URL', $plugin_url);
}

if (!defined('TF_AJAX_URL')) {
	$ajax_url = admin_url('admin-ajax.php');
	define('TF_AJAX_URL', $ajax_url);
}

// Init helper functions
require_once (TF_PLUGIN_PATH . 'includes/helper.php');

// Init Loader hooks
require_once (TF_PLUGIN_PATH . 'includes/class-loader.php');
$loader = new Loader();

// Template Loader
require_once( TF_PLUGIN_PATH . '/includes/class-template-loader.php' );
$template_loader = new Template_Loader();
$loader->tfwc_add_filter( 'template_include', $template_loader, 'template_loader' );


// Template Loader
require_once (TF_PLUGIN_PATH . '/includes/class-template-loader.php');
$template_loader = new Template_Loader();
$loader->tfwc_add_filter('template_include', $template_loader, 'template_loader');

// Add image sizes
add_action( 'after_setup_theme', 'add_image_sizes' );
function add_image_sizes() {

}

// Activation plugin 
function tfwc_image_size_setup()
{
	add_image_size( 'ovinx-testimonial-style1', 72, 72, array( 'center', 'top' ) );
	add_image_size( 'ovinx-video-small', 157, 169, array( 'center', 'top' ) );
	add_image_size( 'ovinx-product-video', 605, 777 );
	add_image_size('tf-live-sale', 164, 202, array('center', 'top'));
}

add_action('after_setup_theme', 'tfwc_image_size_setup');



// Admin enqueue common styles and scripts
function tfwc_add_admin_styles()
{

}

add_action('admin_enqueue_scripts', 'tfwc_add_admin_styles');

function tfwc_add_admin_script()
{
	global $typenow;
	if (!did_action('wp_enqueue_media')) {
		wp_enqueue_media();
	}
	

}

add_action('admin_enqueue_scripts', 'tfwc_add_admin_script');

// Enqueue common public styles and scripts
function tfwc_add_public_styles()
{

}

add_action('wp_enqueue_scripts', 'tfwc_add_public_styles');

function tfwc_add_public_script()
{

}

add_action('wp_enqueue_scripts', 'tfwc_add_public_script');

// Register Widget
require_once (TF_PLUGIN_PATH . '/includes/class-widgets.php');
$widgets = new Register_Widgets();

require_once( TF_PLUGIN_PATH . 'includes/libraries/class-google-client.php' );
$google_client = new Google_Client();

$loader->tfwc_add_action( 'init', $google_client, 'get_instance' );

// Init User
require_once (TF_PLUGIN_PATH . '/woocommerce/partials/user/class-user.php');
$user_public = new User_Public($google_client);
$loader->tfwc_add_action('wp_enqueue_scripts', @$user_public, 'tfwc_enqueue_user_scripts');
$loader->tfwc_add_action('wp_footer', @$user_public, 'tfwc_login_register_modal');

// Register
$loader->tfwc_add_shortcode('register_form', @$user_public, 'tfwc_shortcode_register_form');
$loader->tfwc_add_action('wp_ajax_custom_register', @$user_public, 'tfwc_register_ajax_handler');
$loader->tfwc_add_action('wp_ajax_nopriv_custom_register', @$user_public, 'tfwc_register_ajax_handler');


// Login
$loader->tfwc_add_shortcode('login_form', @$user_public, 'tfwc_shortcode_login_form');
$loader->tfwc_add_action('wp_ajax_custom_login', @$user_public, 'tfwc_login_ajax_handler');
$loader->tfwc_add_action('wp_ajax_nopriv_custom_login', @$user_public, 'tfwc_login_ajax_handler');

// Login with google


$loader->tfwc_add_action( 'wp_ajax_set_access_token_google', @$user_public, 'tfwc_set_access_token_google' );
$loader->tfwc_add_action( 'wp_ajax_nopriv_set_access_token_google', @$user_public, 'tfwc_set_access_token_google' );
$loader->tfwc_add_action( 'wp_ajax_google_login_ajax', @$user_public, 'tfwc_handle_google_login_ajax' );
$loader->tfwc_add_action( 'wp_ajax_nopriv_google_login_ajax', @$user_public, 'tfwc_handle_google_login_ajax' );

// Login with facebook
$loader->tfwc_add_action( 'wp_ajax_handle_facebook_login', $user_public, 'tfre_handle_facebook_login' );
$loader->tfwc_add_action( 'wp_ajax_nopriv_handle_facebook_login', $user_public, 'tfre_handle_facebook_login' );

// Reset password
$loader->tfwc_add_action( 'wp_ajax_reset_password_ajax', $user_public, 'tfwc_reset_password_ajax_handler' );
$loader->tfwc_add_action( 'wp_ajax_nopriv_reset_password_ajax', $user_public, 'tfwc_reset_password_ajax_handler' );


// Elementor Widgets
function tfwc_elementor_addon()
{
	require_once (TF_PLUGIN_PATH . '/includes/elementor-widget/elementor-widget-addon.php');
	$tfwc_elementor_widget_addon = TFWC_Elementor_Widget_Addon::instance();

}


add_action('plugins_loaded', 'tfwc_elementor_addon');

require_once (TF_PLUGIN_PATH . 'woocommerce/megamenu/init.php');
require_once (TF_PLUGIN_PATH . 'woocommerce/popup-builder/init.php');
// header footer
require_once (TF_PLUGIN_PATH . 'includes/elementor-widget/tf-header-footer/index.php');
// Custom post type
require_once (TF_PLUGIN_PATH . '/woocommerce/register-post-type.php');

// Run hooks
$loader->tfwc_run();