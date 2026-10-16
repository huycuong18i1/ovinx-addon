<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor_Widget_Addon class.
 *
 * The main class that initiates and runs the addon.
 *
 * @since 1.0.0
 */
final class TFWC_Elementor_Widget_Addon {

	/**
	 * Addon Version
	 *
	 * @since 1.0.0
	 * @var string The addon version.
	 */
	const VERSION = '1.0.0';

	/**
	 * Minimum Elementor Version
	 *
	 * @since 1.0.0
	 * @var string Minimum Elementor version required to run the addon.
	 */
	const MINIMUM_ELEMENTOR_VERSION = '3.7.0';

	/**
	 * Minimum PHP Version
	 *
	 * @since 1.0.0
	 * @var string Minimum PHP version required to run the addon.
	 */
	const MINIMUM_PHP_VERSION = '5.2';

	/**
	 * Instance
	 *
	 * @since 1.0.0
	 * @access private
	 * @static
	 * @var \Elementor_Test_Addon\Elementor_Widget_Addon The single instance of the class.
	 */
	private static $_instance = null;

	private $widget_settings = [];

	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 * @return \Elementor_Test_Addon\Elementor_Widget_Addon An instance of the class.
	 */
	public static function instance() {

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;

	}

	/**
	 * Constructor
	 *
	 * Perform some compatibility checks to make sure basic requirements are meet.
	 * If all compatibility checks pass, initialize the functionality.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {

		if ( $this->is_compatible() ) {
			add_action( 'elementor/frontend/after_register_styles', [ $this, 'widget_styles' ], 100 );
			add_action( 'elementor/frontend/after_register_scripts', [ $this, 'widget_scripts' ], 100 );
			add_action( 'elementor/init', [ $this, 'init' ] );
			$this->init_ajax();
		}
			
	}

	/**
	 * Compatibility Checks
	 *
	 * Checks whether the site meets the addon requirement.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function is_compatible() {

		// Check for required PHP version
		if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_php_version' ] );
			return false;
		}

		// Check if Elementor installed and activated
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_missing_main_plugin' ] );
			return false;
		}

		// Check for required Elementor version
		if ( ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_elementor_version' ] );
			return false;
		}

		// Check if Woocommerce installed and activated
		if ( ! did_action( 'woocommerce_loaded' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_missing_woocommerce_plugin' ] );
			return;
        }

		$current_theme = wp_get_theme();
		$required_theme_slug = 'ovinx';

		if ( $current_theme->get_stylesheet() !== $required_theme_slug && $current_theme->get_template() !== $required_theme_slug ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_wrong_theme' ] );
			return false;
		}

		return true;

	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have Elementor installed or activated.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function admin_notice_missing_main_plugin() {

		if ( isset( $_GET['activate'] ) )
			unset( $_GET['activate'] );

		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor */
			esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'ovinx-addon' ),
			'<strong>' . esc_html__( 'ovinx Addon', 'ovinx-addon' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'ovinx-addon' ) . '</strong>'
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have a minimum required Elementor version.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function admin_notice_minimum_elementor_version() {

		if ( isset( $_GET['activate'] ) )
			unset( $_GET['activate'] );

		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'ovinx-addon' ),
			'<strong>' . esc_html__( 'ovinx Addon', 'ovinx-addon' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'ovinx-addon' ) . '</strong>',
			self::MINIMUM_ELEMENTOR_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

	}

	  /**
     * Admin notice
     *
     * Warning when the site doesn't have Woocommerce installed or activated.
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function admin_notice_missing_woocommerce_plugin() {

        if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

        $message = sprintf(
            /* translators: 1: Plugin name 2: Woocommerce */
            esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'ovinx-addon' ),
            '<strong>' . esc_html__( 'ovinx Addon', 'ovinx-addon' ) . '</strong>',
            '<strong>' . esc_html__( 'Woocommerce', 'ovinx-addon' ) . '</strong>'
        );

        printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

    }

	public function admin_notice_wrong_theme() {
		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(
            /* translators: 1: Plugin name 2: Woocommerce */
            esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'ovinx-addon' ),
            '<strong>' . esc_html__( 'ovinx Addon', 'ovinx-addon' ) . '</strong>',
            '<strong>' . esc_html__( 'ovinx Theme', 'ovinx-addon' ) . '</strong>'
        );

        printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
		
	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have a minimum required PHP version.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function admin_notice_minimum_php_version() {

		if ( isset( $_GET['activate'] ) )
			unset( $_GET['activate'] );

		$message = sprintf(
			/* translators: 1: Plugin name 2: PHP 3: Required PHP version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'ovinx-addon' ),
			'<strong>' . esc_html__( 'ovinx Addon', 'ovinx-addon' ) . '</strong>',
			'<strong>' . esc_html__( 'PHP', 'ovinx-addon' ) . '</strong>',
			self::MINIMUM_PHP_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

	}

	/**
	 * Initialize
	 *
	 * Load the addons functionality only after Elementor is initialized.
	 *
	 * Fired by `elementor/init` action hook.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function init() {
		$elementsManager = \Elementor\Plugin::instance()->elements_manager;
		$elementsManager->add_category(
			'ovinx_addons',
			array(
				'title' => esc_html__( 'ovinx Addon', 'ovinx-addon' ),
				'icon'  => 'fonts',
			) );
		$elementsManager->add_category(
			'ovinx_addons_core',
			array(
				'title' => esc_html__( 'ovinx Addon Core', 'ovinx-addon' ),
				'icon'  => 'fonts',
			) );
		
		add_filter( 'elementor/icons_manager/additional_tabs', [ $this, 'register_icons_picker'] );
		add_action('elementor/widgets/register', [ $this, 'register_widgets' ] );
		add_action('elementor/controls/register', [$this, 'register_controls' ]);
		add_action( 'pre_get_posts', [ $this, 'filter_shop_query_from_search_widget' ] );
	}

	/**
	 * "Ovinx Slideshow" widget's Product Search Filter panel submits real
	 * category/tag/attribute slugs as GET params (`product_cat`, `product_tag`,
	 * `filter_{attribute}`) to the Shop page. WooCommerce core does not apply
	 * these on the plain Shop archive by itself (that only happens when the
	 * matching Layered Nav / Product Categories widget is actually rendered on
	 * the page) — so we filter the main Shop query ourselves here to make the
	 * widget's search button actually work regardless of which widgets are active.
	 */
	public function filter_shop_query_from_search_widget( $query ) {
		if ( is_admin() || ! $query->is_main_query() || ! function_exists( 'is_shop' ) || ! is_shop() ) {
			return;
		}

		$tax_query = (array) $query->get( 'tax_query' );

		if ( ! empty( $_GET['product_cat'] ) ) {
			$tax_query[] = [
				'taxonomy' => 'product_cat',
				'field'    => 'slug',
				'terms'    => sanitize_title( wp_unslash( $_GET['product_cat'] ) ),
			];
		}

		if ( ! empty( $_GET['product_tag'] ) ) {
			$tax_query[] = [
				'taxonomy' => 'product_tag',
				'field'    => 'slug',
				'terms'    => sanitize_title( wp_unslash( $_GET['product_tag'] ) ),
			];
		}

		if ( ! empty( $_GET['product_brand'] ) && taxonomy_exists( 'product_brand' ) ) {
			$tax_query[] = [
				'taxonomy' => 'product_brand',
				'field'    => 'slug',
				'terms'    => sanitize_title( wp_unslash( $_GET['product_brand'] ) ),
			];
		}

		// Attribute checkboxes in the real shop sidebar (class-widget-attribute-filter.php)
		// use the term ID as their value, not the slug — match that exactly so the
		// widget's dropdown value and this query filter agree with the sidebar's own
		// checkboxes without needing a slug<->id lookup anywhere.
		foreach ( $_GET as $key => $value ) {
			if ( 0 !== strpos( $key, 'filter_' ) || '' === $value ) {
				continue;
			}

			$taxonomy = sanitize_key( str_replace( 'filter_', '', $key ) );
			$term_id  = absint( $value );

			if ( $term_id && taxonomy_exists( $taxonomy ) ) {
				$tax_query[] = [
					'taxonomy' => $taxonomy,
					'field'    => 'term_id',
					'terms'    => $term_id,
				];
			}
		}

		if ( count( $tax_query ) < 2 ) {
			if ( ! empty( $tax_query ) ) {
				$query->set( 'tax_query', $tax_query );
			}
			return;
		}

		$tax_query['relation'] = 'AND';
		$query->set( 'tax_query', $tax_query );
	}

	public function init_ajax(){
		add_action('wp_ajax_tfwc_select2_options', [ $this, 'fetch_tfwc_select2_product_options']);
		// add_action('wp_ajax_nopriv_tfwc_select2_options', [ $this, 'fetch_tfwc_select2_product_options']);

		// "Ovinx Product Tabs" widget lazy-loads every tab past the first one —
		// see the perf note atop widget-product-tabs.php. Registered here
		// (constructor-time, unconditional) rather than inside register_widgets()
		// because that only fires on `elementor/widgets/register`, which a bare
		// admin-ajax.php request isn't guaranteed to trigger.
		add_action('wp_ajax_ovinx_load_product_tab', [ $this, 'ajax_load_product_tab']);
		add_action('wp_ajax_nopriv_ovinx_load_product_tab', [ $this, 'ajax_load_product_tab']);

		// "Ovinx Product Tab Grid" widget — same lazy-load-past-first-tab
		// perf pattern, own action (grid card markup, not swiper slides), see
		// widget-product-tab-grid.php's class doc comment.
		add_action('wp_ajax_ovinx_load_product_tab_grid', [ $this, 'ajax_load_product_tab_grid']);
		add_action('wp_ajax_nopriv_ovinx_load_product_tab_grid', [ $this, 'ajax_load_product_tab_grid']);
	}

	public function ajax_load_product_tab() {
		// The widget class `use`s this trait; in an AJAX request the normal widget bootstrap has not run, so load it first
		// (without it: fatal 'Trait Ovinx_Swiper_Group_Controls not found' -> HTTP 500 and the tab skeleton never goes away).
		require_once( __DIR__ . '/templates/swiper-group-controls.php' );
		require_once( __DIR__ . '/widgets/widget-product-tabs.php' );
		\ovinx_Product_Tabs::ajax_render_tab();
	}

	public function ajax_load_product_tab_grid() {
		require_once( __DIR__ . '/widgets/widget-product-tab-grid.php' );
		\ovinx_Product_Tab_Grid::ajax_render_tab();
	}

	public function widget_scripts() {
		// 3rd
		if ( ! wp_script_is( 'themesflat-bootstrap', 'registered' ) ) {
			wp_register_script( 'themesflat-bootstrap', plugins_url( 'assets/js/bootstrap.min.js', __FILE__ ), [], null, true );
		}

		if ( ! wp_script_is( 'themesflat-swiper', 'registered' ) ) {
			wp_register_script( 'themesflat-swiper', plugins_url( 'assets/js/swiper-bundle.min.js', __FILE__ ), [], null, true );
		}

		wp_register_script( 'gsap', plugins_url( 'assets/js/gsap.min.js', __FILE__ ), [], null, true );
		wp_register_script( 'splittext', plugins_url( 'assets/js/splittext.js', __FILE__ ), [], null, true );
		wp_register_script( 'scrolltrigger', plugins_url( 'assets/js/ScrollTrigger.min.js', __FILE__ ), [], null, true );
		wp_register_script( 'js-countdown', plugins_url( 'assets/js/count-down.js', __FILE__ ), [], null, true );
		wp_register_script( 'js-countdown-core', plugins_url( 'assets/js/count-down-core.js', __FILE__ ), [], null, true );
		wp_register_script( 'parallaxie', plugins_url( 'assets/js/parallaxie.js', __FILE__ ), [], null, true );
		wp_register_script( 'parallaxie-core', plugins_url( 'assets/js/parallaxie-core.js', __FILE__ ), [ 'jquery', 'parallaxie' ], null, true );

		wp_register_script( 'tf-magnific-popup', plugins_url( 'assets/js/magnific-popup.min.js', __FILE__ ), [], null, true );

		if ( ! wp_script_is( 'themesflat-lazysize', 'registered' ) ) {
			wp_register_script(
				'themesflat-lazysize',
				THEMESFLAT_LINK . 'js/3rd/lazysize.min.js',
				array(),
				'',
				true
			);
		}

		// Core Js
		// Version = file mtime: with `null` the URL never changed, so browsers kept serving an old slider-core.js after every update.
		wp_register_script( 'slider-core', plugins_url( 'assets/js/slider-core.js', __FILE__ ), [ 'jquery', 'themesflat-swiper' ], (string) @filemtime( __DIR__ . '/assets/js/slider-core.js' ), true );
		wp_register_script( 'privacy-policy-core', plugins_url( 'assets/js/privacy-policy.js', __FILE__ ), [ 'jquery' ], null, true );

	}

	public function widget_styles() {
		// 3rd
		// if ( ! wp_style_is( 'themesflat-bootstrap', 'registered' ) ) {
		// 	wp_register_style( 'themesflat-bootstrap', plugins_url( 'assets/css/bootstrap.min.css', __FILE__ ) );
		// }
		if ( ! wp_style_is( 'themesflat-swiper', 'registered' ) ) {
			wp_register_style( 'themesflat-swiper', plugins_url( 'assets/css/swiper-bundle.min.css', __FILE__ ) );
		}
		wp_register_style( 'tf-magnific-popup', plugins_url( 'assets/css/magnific-popup.min.css', __FILE__ ) );

	}

	public function widget_styles_editor() {
		if ( did_action( 'elementor/loaded' ) ) {
			
		}
	}

	/**
	 * Register Widgets
	 *
	 * Load widgets files and register new Elementor widgets.
	 *
	 * Fired by `elementor/widgets/register` action hook.
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager.
	 */
	public function register_widgets( $widgets_manager ) {

		// NOTE (2026-09-26): TOÀN BỘ widget Elementor (layout tĩnh + WooCommerce product) đã bị xoá
		// theo yêu cầu để làm lại từ đầu — bản cũ kế thừa nguyên gốc từ theme amerce, nhiều option
		// thừa/khó kiểm soát. Widget mới sẽ được học hỏi convention từ plugin amerce (tham khảo, không
		// copy máy móc) và build lại bằng skill /widget-convert, đăng ký thêm vào đây từng widget một.

		require_once( __DIR__ . '/templates/vehicle-filter-fields.php' );
		require_once( __DIR__ . '/templates/swiper-group-controls.php' );

		require_once( __DIR__ . '/widgets/widget-slideshow.php' );
		$widgets_manager->register( new \ovinx_Slideshow() );

		require_once( __DIR__ . '/widgets/widget-check-fit.php' );
		$widgets_manager->register( new \ovinx_Check_Fit() );

		require_once( __DIR__ . '/widgets/widget-category.php' );
		$widgets_manager->register( new \ovinx_Category_Product() );

		require_once( __DIR__ . '/widgets/widget-collection-banner.php' );
		$widgets_manager->register( new \ovinx_Collection_Banner() );

		require_once( __DIR__ . '/widgets/widget-banner-feature.php' );
		$widgets_manager->register( new \ovinx_Banner_Feature() );

		require_once( __DIR__ . '/widgets/widget-banner.php' );
		$widgets_manager->register( new \ovinx_Banner() );

		require_once( __DIR__ . '/widgets/widget-grid-banner.php' );
		$widgets_manager->register( new \ovinx_Grid_Banner() );

		require_once( __DIR__ . '/widgets/widget-two-image-banner.php' );
		$widgets_manager->register( new \ovinx_Two_Image_Banner() );

		require_once( __DIR__ . '/widgets/widget-contact-support.php' );
		$widgets_manager->register( new \ovinx_Contact_Support() );

		require_once( __DIR__ . '/widgets/widget-contact.php' );
		$widgets_manager->register( new \ovinx_Contact() );

		require_once( __DIR__ . '/widgets/widget-store-info.php' );
		$widgets_manager->register( new \ovinx_Store_Info() );

		require_once( __DIR__ . '/widgets/widget-faqs.php' );
		$widgets_manager->register( new \ovinx_Faqs() );

		require_once( __DIR__ . '/widgets/widget-privacy-policy.php' );
		$widgets_manager->register( new \ovinx_Privacy_Policy() );

		require_once( __DIR__ . '/widgets/widget-blog.php' );
		$widgets_manager->register( new \ovinx_Blog() );

		require_once( __DIR__ . '/widgets/widget-testimonial.php' );
		$widgets_manager->register( new \ovinx_Testimonial() );

		require_once( __DIR__ . '/widgets/widget-countdown.php' );
		$widgets_manager->register( new \ovinx_Countdown() );

		require_once( __DIR__ . '/widgets/widget-product-tabs.php' );
		$widgets_manager->register( new \ovinx_Product_Tabs() );

		require_once( __DIR__ . '/widgets/widget-product-tab-grid.php' );
		$widgets_manager->register( new \ovinx_Product_Tab_Grid() );

		require_once( __DIR__ . '/widgets/widget-product-banner-grid.php' );
		$widgets_manager->register( new \ovinx_Product_Banner_Grid() );

		require_once( __DIR__ . '/widgets/widget-product-carousel.php' );
		$widgets_manager->register( new \ovinx_Product_Carousel() );

		require_once( __DIR__ . '/widgets/widget-product-list-carousel.php' );
		$widgets_manager->register( new \ovinx_Product_List_Carousel() );

		require_once( __DIR__ . '/widgets/widget-banner-sale-coupon.php' );
		$widgets_manager->register( new \ovinx_Banner_Sale_Coupon() );

		require_once( __DIR__ . '/widgets/widget-product-showcase-slider.php' );
		$widgets_manager->register( new \ovinx_Product_Showcase_Slider() );

		require_once( __DIR__ . '/widgets/widget-icon-list.php' );
		$widgets_manager->register( new \ovinx_Icon_List() );

		require_once( __DIR__ . '/widgets/widget-footer.php' );
		$widgets_manager->register( new \ovinx_Footer() );

		require_once( __DIR__ . '/widgets/widget-mega-menu.php' );
		$widgets_manager->register( new \ovinx_Mega_Menu() );
	}

	public function register_controls( $controls_manager ) {
		require_once( __DIR__ . '/controls/tfwc-select2.php' );
    	$controls_manager->register_control('tfwc_select2', new TFWC_Select2_Control());
	}

	public function register_icons_picker( $icons = array() ) {			
		$icons['ovinx_icon'] = array(
			'name'          => 'ovinx_icon',
			'label'         => esc_html__( 'ovinx Icons', 'ovinx-addon' ),
			'labelIcon'     => 'icon-Package',
			'prefix'        => '',
			'displayPrefix' => '',
			'url' 			=> get_template_directory_uri() . '/css/icon-ovinx.css',
			'fetchJson'     => plugins_url( 'assets/icon/ovinx-json.json', __FILE__ ),
			'ver'           => '1.0.0',
		);
	
		return $icons;
	}

	public function fetch_tfwc_select2_product_options() {
        check_ajax_referer('tfwc_select2_nonce', 'security');

        $search = isset($_GET['q']) ? sanitize_text_field($_GET['q']) : '';
		$selected = isset($_GET['selected']) ? (array) $_GET['selected'] : [];

        $args = [
            'post_type'      => 'product',
            'posts_per_page' => 10,
            's'              => $search
        ];

		if (!empty($selected)) {
			$args['post__in'] = $selected;
			$args['orderby'] = 'post__in';
		}

        $query = new WP_Query($args);

        $results = [];
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $results[] = [
                    'id'   => get_the_ID(),
                    'text' => get_the_title(),
                ];
            }
            wp_reset_postdata();
        }

        wp_send_json($results);
    }

}
