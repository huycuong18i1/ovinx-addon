<?php

/**
 * Basic, fully-configurable WooCommerce product carousel — not a port of one
 * specific `ovinx-html` section (user asked for a generic reusable widget:
 * one row of real products, no tabs, no banner). Heading follows the same
 * `.sect-heading.type-2` (title + optional "View All" button) shape already
 * used by `widget-category.php`'s Style 4 (`home-04.html` ~line 2301), and
 * the product query / real WooCommerce card rendering follows the same
 * "force Style 1 hooks + real `WP_Query` + `wc_get_template_part('content',
 * 'product')`" convention as `widget-product-tabs.php` — no hand-built card
 * markup, no typed-in price fields.
 *
 * `layout_style` "Grid" option (2026-09-28) ports ground truth's other
 * product-swiper variant (`index.html`'s "Get Best Prices" section,
 * `data-grid="2"` + `.layout-prd_2row` — a fixed multi-row grid on
 * tablet/desktop showing every product with no scrolling needed) while still
 * degrading to a plain single-row carousel on phones, per an explicit user
 * request. That degrade needed a real `slider-core.js` change
 * (`data-grid-mobile`, see that function's comment) since `initSwiperInScope()`
 * previously only supported a grid applied uniformly at every breakpoint —
 * kept backward compatible for `widget-blog.php`'s existing Style 3 grid,
 * which never sets the new attribute and keeps its exact old behavior.
 */
class ovinx_Product_Carousel extends \Elementor\Widget_Base {

	use Ovinx_Swiper_Group_Controls;

	public function get_name() {
		return 'ovinx_product_carousel';
	}

	public function get_title() {
		return esc_html__( 'Ovinx Product Carousel', 'ovinx-addon' );
	}

	public function get_icon() {
		return 'eicon-slider-push';
	}

	public function get_categories() {
		return [ 'ovinx_addons_core' ];
	}

	public function get_keywords() {
		return [ 'product', 'carousel', 'slider', 'swiper', 'woocommerce' ];
	}

	public function get_script_depends() {
		return [ 'slider-core', 'js-countdown-core' ];
	}

	public function get_style_depends() {
		return [ 'themesflat-swiper' ];
	}

	/**
	 * Same private-per-widget convention already used by
	 * `widget-product-tabs.php`/`widget-category.php` (no shared static
	 * helper class exists in this plugin) — keyed by `term_id`.
	 */
	private function get_product_category_options() {
		$options = [];

		$terms = get_terms(
			[
				'taxonomy'   => 'product_cat',
				'hide_empty' => false,
			]
		);

		if ( ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$options[ $term->term_id ] = $term->name;
			}
		}

		return $options;
	}

	protected function register_controls() {

		/**
		 * Heading
		 */
		$this->start_controls_section(
			'section_heading',
			[
				'label' => esc_html__( 'Heading', 'ovinx-addon' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'title',
			[
				'label'       => esc_html__( 'Title', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
			]
		);

		$this->add_control(
			'view_all_text',
			[
				'label'   => esc_html__( 'View All Text', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'VIEW ALL PRODUCTS', 'ovinx-addon' ),
			]
		);

		$this->add_control(
			'view_all_link',
			[
				'label'       => esc_html__( 'View All Link', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::URL,
				'label_block' => true,
				'default'     => [
					'url' => '',
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Products
		 */
		$this->start_controls_section(
			'section_products',
			[
				'label' => esc_html__( 'Products', 'ovinx-addon' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'category_ids',
			[
				'label'       => esc_html__( 'Categories (empty = all products)', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple'    => true,
				'options'     => $this->get_product_category_options(),
				'default'     => [],
			]
		);

		$this->add_control(
			'posts_count',
			[
				'label'   => esc_html__( 'Number of Products', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 20,
				'default' => 8,
			]
		);

		$this->add_control(
			'order_by',
			[
				'label'   => esc_html__( 'Order By', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'date'  => esc_html__( 'Date', 'ovinx-addon' ),
					'title' => esc_html__( 'Title', 'ovinx-addon' ),
					'price' => esc_html__( 'Price', 'ovinx-addon' ),
					'rand'  => esc_html__( 'Random', 'ovinx-addon' ),
				],
				'default' => 'date',
			]
		);

		$this->add_control(
			'order',
			[
				'label'   => esc_html__( 'Order', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'DESC' => esc_html__( 'Descending', 'ovinx-addon' ),
					'ASC'  => esc_html__( 'Ascending', 'ovinx-addon' ),
				],
				'default' => 'DESC',
			]
		);

		$this->end_controls_section();

		/**
		 * Carousel Layout
		 */
		$this->start_controls_section(
			'section_carousel',
			[
				'label' => esc_html__( 'Carousel Layout', 'ovinx-addon' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		/**
		 * Ground truth (`themes/ovinx-html/index.html`'s "Get Best Prices"
		 * section, `data-grid="2"` + `.layout-prd_2row`) shows all products as
		 * a fixed 2-row grid on tablet/desktop, but a plain single-row
		 * carousel on phones — `slider-core.js`'s `initSwiperInScope()` only
		 * supports a uniform grid across every breakpoint by default (see
		 * `widget-blog.php` Style 3), so this style wires the new
		 * `data-grid-mobile="1"` override added there specifically to make
		 * this possible without changing any other widget's grid behavior.
		 */
		$this->add_control(
			'layout_style',
			[
				'label'   => esc_html__( 'Layout', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'default' => esc_html__( 'Carousel — Single Row', 'ovinx-addon' ),
					'grid'    => esc_html__( 'Grid — Multi-row on Tablet/Desktop, Carousel on Mobile', 'ovinx-addon' ),
					'promo'   => esc_html__( 'Carousel + Promo Banner (Countdown) as first slide', 'ovinx-addon' ),
				],
				'default' => 'default',
			]
		);

		$this->add_control(
			'grid_rows',
			[
				'label'     => esc_html__( 'Grid Rows (Tablet/Desktop)', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 2,
				'max'       => 3,
				'default'   => 2,
				'condition' => [
					'layout_style' => 'grid',
				],
			]
		);

		$this->add_control(
			'items_desktop',
			[
				'label'   => esc_html__( 'Items — Desktop (≥1440px)', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 8,
				'default' => 5,
			]
		);

		$this->add_control(
			'items_laptop',
			[
				'label'   => esc_html__( 'Items — Laptop (≥1200px)', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 8,
				'default' => 4,
			]
		);

		$this->add_control(
			'items_tablet',
			[
				'label'   => esc_html__( 'Items — Tablet (≥768px)', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 8,
				'default' => 3,
			]
		);

		$this->add_control(
			'items_mobile',
			[
				'label'   => esc_html__( 'Items — Mobile (≥575px)', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 8,
				'default' => 2,
			]
		);

		$this->add_control(
			'items_mobile_sm',
			[
				'label'   => esc_html__( 'Items — Small Mobile (<575px)', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 8,
				'default' => 1,
			]
		);


		$this->add_control(
			'show_nav',
			[
				'label'        => esc_html__( 'Show Navigation Arrows', 'ovinx-addon' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'ovinx-addon' ),
				'label_off'    => esc_html__( 'No', 'ovinx-addon' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'show_pagination',
			[
				'label'        => esc_html__( 'Show Pagination Dots', 'ovinx-addon' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'ovinx-addon' ),
				'label_off'    => esc_html__( 'No', 'ovinx-addon' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label'        => esc_html__( 'Autoplay', 'ovinx-addon' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'ovinx-addon' ),
				'label_off'    => esc_html__( 'No', 'ovinx-addon' ),
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);

		$this->add_control(
			'autoplay_delay',
			[
				'label'     => esc_html__( 'Autoplay Delay (ms)', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 500,
				'step'      => 100,
				'default'   => 3000,
				'condition' => [
					'autoplay' => 'yes',
				],
			]
		);

		$this->add_control(
			'loop',
			[
				'label'        => esc_html__( 'Loop', 'ovinx-addon' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'ovinx-addon' ),
				'label_off'    => esc_html__( 'No', 'ovinx-addon' ),
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);

		$this->end_controls_section();

		$this->register_swiper_group_controls( [ 20, 20, 20, 20, 20 ], [ 'layout_style!' => 'grid' ] );

		/**
		 * Promo banner slide — ground truth `home-0x.html` "Deals Of The Week":
		 * a `.banner-cd-v01` countdown card is the FIRST swiper slide
		 * (`.md-d-none`, hidden below 768px) followed by the product cards.
		 * Same markup/fields as `widget-countdown.php`; the banner counts as one
		 * of the "Items" set above.
		 */
		$this->start_controls_section(
			'section_promo',
			[
				'label'     => esc_html__( 'Promo Banner Slide', 'ovinx-addon' ),
				'tab'       => \Elementor\Controls_Manager::TAB_CONTENT,
				'condition' => [
					'layout_style' => 'promo',
				],
			]
		);

		/**
		 * `countdown` = `.banner-cd-v01` (home-01/02); `image` = `.banner-v03`
		 * (home-03 "Deals Of The Week": image card with white text overlay,
		 * no countdown, visible on mobile too).
		 */
		$this->add_control(
			'promo_style',
			[
				'label'   => esc_html__( 'Promo Banner Style', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'countdown' => esc_html__( 'Countdown Card', 'ovinx-addon' ),
					'image'     => esc_html__( 'Image Card (white text overlay)', 'ovinx-addon' ),
				],
				'default' => 'countdown',
			]
		);

		$this->add_control(
			'promo_mini_title',
			[
				'label'       => esc_html__( 'Mini Title', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'FOR HIGH-PERFORMANCE', 'ovinx-addon' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'promo_title',
			[
				'label'       => esc_html__( 'Title', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Trusted Car Parts', 'ovinx-addon' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'promo_button_text',
			[
				'label'       => esc_html__( 'Button Text', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Shop Now', 'ovinx-addon' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'promo_button_link',
			[
				'label'       => esc_html__( 'Button Link', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::URL,
				'label_block' => true,
			]
		);

		$this->add_control(
			'promo_image',
			[
				'label'   => esc_html__( 'Image', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
			]
		);

				$this->add_control(
			'promo_countdown_mode',
			[
				'label'   => esc_html__( 'Countdown Mode', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'fixed' => esc_html__( 'Fixed end date/time', 'ovinx-addon' ),
					'daily' => esc_html__( 'Repeat daily (auto-reset every day)', 'ovinx-addon' ),
				],
				'default'   => 'fixed',
				'condition' => [ 'promo_style' => 'countdown' ],
			]
		);

		$this->add_control(
			'promo_daily_start',
			[
				'label'          => esc_html__( 'Daily Start Time', 'ovinx-addon' ),
				'type'           => \Elementor\Controls_Manager::DATE_TIME,
				'picker_options' => [
					'enableTime' => true,
					'noCalendar' => true,
					'dateFormat' => 'H:i',
					'time_24hr'  => true,
				],
				'default'        => '00:00',
				'description'    => esc_html__( 'Site timezone. Every day at this time the countdown restarts from the full duration.', 'ovinx-addon' ),
				'condition'      => [
					'promo_style'          => 'countdown',
					'promo_countdown_mode' => 'daily',
				],
			]
		);

		$this->add_control(
			'promo_daily_hours',
			[
				'label'       => esc_html__( 'Duration (hours)', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'min'         => 0.5,
				'max'         => 24,
				'step'        => 0.5,
				'default'     => 12,
				'description' => esc_html__( 'After the duration ends the countdown shows zeros until the next daily start.', 'ovinx-addon' ),
				'condition'   => [
					'promo_style'          => 'countdown',
					'promo_countdown_mode' => 'daily',
				],
			]
		);

		$this->add_control(
			'promo_end_date',
			[
				'label'       => esc_html__( 'Countdown End Date/Time', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::DATE_TIME,
				'label_block' => true,
				'description' => esc_html__( 'Leave empty to hide the countdown.', 'ovinx-addon' ),
				'condition'   => [
					'promo_style'          => 'countdown',
					'promo_countdown_mode' => 'fixed',
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Style tab
		 */
		$this->start_controls_section(
			'section_style_heading',
			[
				'label' => esc_html__( 'Heading', 'ovinx-addon' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__( 'Title Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .sect-heading .s-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .sect-heading .s-title',
			]
		);

		$this->add_control(
			'view_all_color',
			[
				'label'     => esc_html__( 'View All Button Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .sect-heading .tf-btn-line' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_nav',
			[
				'label' => esc_html__( 'Navigation', 'ovinx-addon' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'nav_color',
			[
				'label'     => esc_html__( 'Arrow Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tf-sw-nav.style-2 .icon' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_pagination',
			[
				'label' => esc_html__( 'Pagination', 'ovinx-addon' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'pagination_color',
			[
				'label'     => esc_html__( 'Bullet Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .sw-dot-default .swiper-pagination-bullet' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'pagination_color_active',
			[
				'label'     => esc_html__( 'Active Bullet Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .sw-dot-default .swiper-pagination-bullet-active' => 'background: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Real ecommerce card (wishlist/compare/quickview state, sale badge,
	 * color swatches, rating + stock badge, add-to-cart) — reuses the
	 * theme's own production card pipeline, same "force Style 1 regardless
	 * of the site's Customizer default" convention as
	 * `widget-product-tabs.php::force_style1_hooks()`.
	 */
	private static function force_style1_hooks(): void {
		tfwc_get_template_part( '/product-style-remove/style-1.php' );
		tfwc_get_template_part( '/product-style-remove/style-2.php' );
		tfwc_get_template_part( '/product-style-remove/style-3.php' );
		tfwc_get_template_part( '/product-style-remove/style-4.php' );
		tfwc_get_template_part( '/product-style-remove/style-list.php' );
		tfwc_get_template_part( '/product-style-remove/style-wishlist.php' );
		tfwc_get_template_part( '/product-style/style-1.php' );

		$_POST['product_style'] = 'style-1';
	}

	/**
	 * `.sect-heading.type-2` (title + optional "View All" button) shape,
	 * same as `widget-category.php`'s Style 4 `render_heading()` — plus the
	 * shared prev/next nav buttons living inside the heading, same
	 * convention as `widget-product-tabs.php`. Hidden entirely if there is
	 * nothing to show (no title, no View All link, no nav arrows).
	 */
	private function render_heading( array $settings ): void {
		$has_title = ! empty( $settings['title'] );
		$has_link  = ! empty( $settings['view_all_link']['url'] );
		$show_nav  = 'yes' === $settings['show_nav'];

		// home-03's "Deals Of The Week" heading button carries `.line-primary`.
		$is_image_promo = 'promo' === ( $settings['layout_style'] ?? 'default' ) && 'image' === ( $settings['promo_style'] ?? 'countdown' );

		if ( ! $has_title && ! $has_link && ! $show_nav ) {
			return;
		}
		?>
		<div class="sect-heading type-2">
			<?php if ( $has_title ) : ?>
				<h4 class="s-title"><?php echo esc_html( $settings['title'] ); ?></h4>
			<?php endif; ?>
			<?php if ( $has_link ) :
				$this->add_link_attributes( 'view_all_link', $settings['view_all_link'] );
				?>
				<a <?php $this->print_render_attribute_string( 'view_all_link' ); ?> class="tf-btn-line<?php echo $is_image_promo ? ' line-primary' : ''; ?>">
					<span class="text-uppercase fw-semibold"><?php echo esc_html( $settings['view_all_text'] ); ?></span>
				</a>
			<?php endif; ?>
			<?php if ( $show_nav ) : ?>
				<div class="tf-list gap-8">
					<div class="tf-sw-nav style-2 nav-prev-swiper">
						<i class="icon icon-CaretLeft"></i>
					</div>
					<div class="tf-sw-nav style-2 nav-next-swiper">
						<i class="icon icon-CaretRight"></i>
					</div>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * "Repeat daily" mode: the countdown window opens every day at the
	 * configured site-timezone time and lasts N hours. If the current time is
	 * before today's start, the window that started yesterday is still the
	 * one that counts (relevant for durations that cross midnight). Once the
	 * window is over it stays at zero until the next daily start.
	 */
	private function get_daily_seconds_left( array $settings ): int {
		$start = ! empty( $settings['promo_daily_start'] ) ? $settings['promo_daily_start'] : '00:00';
		$hours = ! empty( $settings['promo_daily_hours'] ) ? (float) $settings['promo_daily_hours'] : 12;

		if ( ! preg_match( '/^(\d{1,2}):(\d{2})/', $start, $parts ) ) {
			return 0;
		}

		$now    = new DateTimeImmutable( 'now', wp_timezone() );
		$window = $now->setTime( (int) $parts[1], (int) $parts[2], 0 );

		if ( $window > $now ) {
			$window = $window->modify( '-1 day' );
		}

		return max( 0, (int) ( $window->getTimestamp() + $hours * HOUR_IN_SECONDS - $now->getTimestamp() ) );
	}

	/**
	 * "Image Card" promo slide — ground truth `home-03.html` `.banner-v03`.
	 * Not `.md-d-none`, so it stays visible on phones.
	 */
	private function render_promo_image_slide( array $settings, string $link_key ): void {
		?>
		<div class="swiper-slide">
			<div class="banner-v03 hover-img">
				<?php if ( ! empty( $settings['promo_image']['url'] ) ) : ?>
					<div class="bn-image img-style">
						<img loading="lazy" width="250" height="238" src="<?php echo esc_url( $settings['promo_image']['url'] ); ?>" alt="<?php echo esc_attr( $settings['promo_title'] ); ?>">
					</div>
				<?php endif; ?>
				<div class="bn-content">
					<?php if ( ! empty( $settings['promo_mini_title'] ) ) : ?>
						<p class="mini-title text-label fw-semibold text-white"><?php echo esc_html( $settings['promo_mini_title'] ); ?></p>
					<?php endif; ?>
					<?php if ( ! empty( $settings['promo_title'] ) ) : ?>
						<h5 class="title text-white"><?php echo esc_html( $settings['promo_title'] ); ?></h5>
					<?php endif; ?>
					<?php if ( ! empty( $settings['promo_button_text'] ) ) : ?>
						<a <?php $this->print_render_attribute_string( $link_key ); ?> class="tf-btn-line style-white">
							<span class="text-button-small"><?php echo esc_html( $settings['promo_button_text'] ); ?></span>
						</a>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * First slide of the "Promo" layout — same `.banner-cd-v01` markup as
	 * `widget-countdown.php` (whose countdown JS hook `js-countdown-core` is
	 * reused). `.md-d-none` hides it below 768px, as ground truth does.
	 */
	private function render_promo_slide( array $settings ): void {
		if ( empty( $settings['promo_title'] ) && empty( $settings['promo_image']['url'] ) ) {
			return;
		}

		$link_key = 'promo_button_link';
		$link     = ! empty( $settings[ $link_key ]['url'] ) ? $settings[ $link_key ] : [ 'url' => '#' ];
		$this->add_link_attributes( $link_key, $link );

		if ( 'image' === ( $settings['promo_style'] ?? 'countdown' ) ) {
			$this->render_promo_image_slide( $settings, $link_key );
			return;
		}

		$is_daily      = 'daily' === ( $settings['promo_countdown_mode'] ?? 'fixed' );
		$seconds_left  = 0;
		$show_countdown = $is_daily || ! empty( $settings['promo_end_date'] );

		if ( $is_daily ) {
			$seconds_left = $this->get_daily_seconds_left( $settings );
		} elseif ( ! empty( $settings['promo_end_date'] ) ) {
			$end_dt = date_create( $settings['promo_end_date'], wp_timezone() );

			if ( $end_dt ) {
				$seconds_left = max( 0, $end_dt->getTimestamp() - time() );
			}
		}
		?>
		<div class="swiper-slide md-d-none">
			<div class="banner-cd-v01 hover-img6">
				<?php if ( ! empty( $settings['promo_mini_title'] ) ) : ?>
					<p class="mini-title text-label fw-semibold text-primary mb-4"><?php echo esc_html( $settings['promo_mini_title'] ); ?></p>
				<?php endif; ?>
				<?php if ( ! empty( $settings['promo_title'] ) ) : ?>
					<h5 class="title mb-12"><?php echo esc_html( $settings['promo_title'] ); ?></h5>
				<?php endif; ?>
				<?php if ( ! empty( $settings['promo_button_text'] ) ) : ?>
					<a <?php $this->print_render_attribute_string( $link_key ); ?> class="tf-btn-line py-4 mb-16">
						<span class="text-button-small text-uppercase"><?php echo esc_html( $settings['promo_button_text'] ); ?></span>
					</a>
				<?php endif; ?>
				<?php if ( ! empty( $settings['promo_image']['url'] ) ) : ?>
					<div class="bn-image mb-16 img-style6 overflow-visible">
						<img loading="lazy" src="<?php echo esc_url( $settings['promo_image']['url'] ); ?>" alt="<?php echo esc_attr( $settings['promo_title'] ); ?>">
					</div>
				<?php endif; ?>
				<?php if ( $show_countdown ) : ?>
					<div class="countdown-v02">
						<div class="js-countdown-core cd-has-zero cd-no" data-labels="Days,Hours,Mins,Secs" data-timer="<?php echo esc_attr( $seconds_left ); ?>"></div>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	protected function render(): void {
		$settings = $this->get_settings_for_display();

		$args = [
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'posts_per_page' => ! empty( $settings['posts_count'] ) ? absint( $settings['posts_count'] ) : 8,
			'orderby'        => ! empty( $settings['order_by'] ) ? $settings['order_by'] : 'date',
			'order'          => ! empty( $settings['order'] ) ? $settings['order'] : 'DESC',
		];

		if ( 'price' === $args['orderby'] ) {
			$args['orderby']  = 'meta_value_num';
			$args['meta_key'] = '_price'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
		}

		if ( ! empty( $settings['category_ids'] ) ) {
			$args['tax_query'] = [ // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				[
					'taxonomy' => 'product_cat',
					'field'    => 'term_id',
					'terms'    => array_map( 'absint', (array) $settings['category_ids'] ),
				],
			];
		}

		$query = new WP_Query( $args );

		// Nothing matched the filters — bail before printing anything at
		// all, including the heading (see class doc comment).
		if ( ! $query->have_posts() ) {
			return;
		}

		self::force_style1_hooks();

		$show_pagination = 'yes' === $settings['show_pagination'];
		$autoplay        = 'yes' === $settings['autoplay'];
		$loop            = 'yes' === $settings['loop'];
		$is_grid         = 'grid' === ( $settings['layout_style'] ?? 'default' );
		$is_promo        = 'promo' === ( $settings['layout_style'] ?? 'default' );
		$space           = $is_grid ? 0 : ( isset( $settings['space_between']['size'] ) && '' !== $settings['space_between']['size'] ? $settings['space_between']['size'] : 20 );
		?>
		<div class=" tf-btn-swiper-main">
			<div class="container">
				<?php $this->render_heading( $settings ); ?>
				<div dir="ltr" class="swiper tf-swiper tfc-swiper wrap-sw-over-2"
					data-laptop="<?php echo esc_attr( $settings['items_desktop'] ); ?>"
					data-preview="<?php echo esc_attr( $settings['items_laptop'] ); ?>"
					data-tablet="<?php echo esc_attr( $settings['items_tablet'] ); ?>"
					data-mobile-sm="<?php echo esc_attr( $settings['items_mobile'] ); ?>"
					data-mobile="<?php echo esc_attr( $settings['items_mobile_sm'] ); ?>"
					<?php echo $this->get_swiper_group_attrs( $settings ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built with esc_attr() in the trait ?>
					<?php echo $this->get_swiper_space_attrs( $is_grid ? [ 'space_mode' => 'default' ] : $settings, array_fill( 0, 5, $space ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built with esc_attr() in the trait ?>
					<?php if ( $autoplay ) : ?>
						data-auto="true" data-delay="<?php echo esc_attr( ! empty( $settings['autoplay_delay'] ) ? absint( $settings['autoplay_delay'] ) : 3000 ); ?>"
					<?php endif; ?>
					<?php if ( $loop ) : ?>
						data-loop="true"
					<?php endif; ?>
					<?php if ( $is_grid ) : ?>
						<?php /**
						 * `data-grid-mobile="1"` (added to `slider-core.js` for
						 * this widget) keeps grid rows OFF for the two phone-width
						 * tiers (root/<575 and 575px), so those fall back to a
						 * plain single-row carousel — only the 768/1200/1440
						 * tiers actually render the multi-row grid, matching
						 * ground truth's tablet/desktop-only grid.
						 */ ?>
						data-grid="<?php echo esc_attr( ! empty( $settings['grid_rows'] ) ? $settings['grid_rows'] : 2 ); ?>" data-grid-mobile="1"
					<?php endif; ?>>
					<div class="swiper-wrapper<?php echo $is_grid ? ' layout-prd_2row' : ''; ?>">
						<?php
						if ( $is_promo ) {
							$this->render_promo_slide( $settings );
						}
						?>
						<?php
						while ( $query->have_posts() ) :
							$query->the_post();
							?>
							<div class="swiper-slide">
								<?php wc_get_template_part( 'content', 'product' ); ?>
							</div>
							<?php
						endwhile;
						wp_reset_postdata();
						?>
					</div>
				</div>
				<?php if ( $show_pagination ) : ?>
					<div class="sw-dot-default tf-sw-pagination d-xl-none"></div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}
