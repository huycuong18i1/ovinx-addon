<?php

/**
 * Ground truth: `themes/ovinx-html/home-04.html` "Product List" section
 * (~line 3646, heading "Featured Products" / `sect-heading type-2`, swiper
 * `data-view-xl="2" data-view-lg="2" data-view-md="1" data-view-sm="1"
 * data-preview="1" data-pagination="1" data-pagination-sm="1"
 * data-pagination-md="2" data-pagination-lg="2"`, dots only, no nav arrows —
 * pagination markup there has no `d-xl-none`, i.e. dots stay visible at every
 * breakpoint since there is no separate desktop arrow nav to hand off to).
 * Each slide is one `.card-product.product-style_list-3` — a wide horizontal
 * row card (image left, info right) with the FULL real card: sale/new badge,
 * `.product-action_list` (wishlist/compare/quickview), `.product-color_list`
 * swatches, category -> title -> rating+stock badge -> price ->
 * `.product-statusbar` -> a static (always-visible, not hover-overlay)
 * full-width "ADD TO CART" button.
 *
 * Real card pipeline: same "force Style 1 hooks + real `WP_Query` +
 * `wc_get_template_part('content','product')`" convention as
 * `widget-product-carousel.php`/`widget-product-tabs.php` — no hand-built
 * card markup, no typed-in price/rating/stock fields. `style-1.php`'s hook
 * order (category -> title -> rating -> price -> ... -> progressbar(45) ->
 * ... -> action_bot/add-to-cart(99)) already matches ground truth's info
 * block order, so no hook re-ordering is needed here. Three real gaps
 * between that stock pipeline and this ground truth still had to be
 * patched, scoped to just this widget's own render() call so no other
 * widget/page sharing the same real hooks is affected:
 *
 * 1. `.product-style_list-3` — no real hook adds it on its own. Added via a
 *    `post_class` filter, applied to every product in this widget's loop
 *    (not scoped to one product id like `widget-product-showcase-slider.php`
 *    does for its single `card-s2` card, since here EVERY card in the
 *    widget needs it) — added right before the loop, removed right after.
 *
 * 2. `.product-statusbar` (real stock progress bar) — `tfwc_progressbar`
 *    already hooks the right spot (`woocommerce_after_shop_loop_item_title`,
 *    priority 45) but outputs the WRONG markup for this ground truth
 *    (`.product-progress-sale`/`.progress-cart`/`.value`/`.text-sold`/
 *    `.text-avaiable` instead of `.product-statusbar`/`.progress-sold
 *    .progress`/`.progress-bar`/`.box-quantity`), and its "Sold:" line never
 *    renders at all (`$show_sold_count` is never set anywhere in
 *    `themes/ovinx/woocommerce/tpl/product-base/progressbar.php`) while
 *    ground truth always shows both Available and Sold. Rather than editing
 *    that shared template (still hooked by `product-style/style-1.php` and
 *    `style-3.php` for other widgets/pages), this widget swaps the callback
 *    for the duration of its own render: `remove_action(...,
 *    'tfwc_progressbar', 45)` + `add_action(..., [self,
 *    'render_stock_progress'], 45)`, then restores the original right after
 *    the loop — same add-before/remove-after convention as #1. Real data via
 *    `WC_Product::get_stock_quantity()` (not raw `get_post_meta('_stock')`)
 *    and `get_total_sales()`; nothing is printed for a product that doesn't
 *    manage stock (`! $product->managing_stock()` — no real "Available"
 *    number to show, so none is invented). Box order: ground truth's actual
 *    HTML (`home-04.html` ~line 3861) prints "Available" before "Sold" —
 *    that real file, not any paraphrase of it, is the order followed here.
 *
 * 3. Static "ADD TO CART" instead of hover-overlay — `tfwc_action_bot()`
 *    (priority 99, real `.tf_add_to_cart` button with full AJAX add-to-cart
 *    / Quick Add wiring) is kept completely as-is; only its wrapper
 *    `.box-action_add`'s hover-reveal CSS (`themes/ovinx/css/styles.css`
 *    ~line 14059, inside `@media (min-width:1200px)`) is overridden back to
 *    static/visible, scoped to this widget's own `.tf-product-list-carousel`
 *    wrapper class so no other card grid on the site is affected. Printed as
 *    a real inline `<style>` tag once per request (`render_card_css_once()`)
 *    rather than via `wp_add_inline_style()` — same reasoning as
 *    `widget-product-tabs.php::render_skeleton_css_once()`: Elementor
 *    resolves/prints a widget's `get_style_depends()` handles during
 *    `wp_head`, before this widget's own `render()` runs, so inline CSS
 *    attached at render time would miss that already-printed handle.
 */
class ovinx_Product_List_Carousel extends \Elementor\Widget_Base {

	use Ovinx_Swiper_Group_Controls;

	public function get_name() {
		return 'ovinx_product_list_carousel';
	}

	public function get_title() {
		return esc_html__( 'Ovinx Product List Carousel', 'ovinx-addon' );
	}

	public function get_icon() {
		return 'eicon-product-related';
	}

	public function get_categories() {
		return [ 'ovinx_addons_core' ];
	}

	public function get_keywords() {
		return [ 'product', 'list', 'carousel', 'swiper', 'woocommerce' ];
	}

	public function get_script_depends() {
		return [ 'slider-core' ];
	}

	public function get_style_depends() {
		return [ 'themesflat-swiper' ];
	}

	/**
	 * Same private-per-widget convention already used by
	 * `widget-product-carousel.php`/`widget-product-tabs.php` (no shared
	 * static helper class exists in this plugin) — keyed by `term_id`.
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
			'description',
			[
				'label'       => esc_html__( 'Description', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'label_block' => true,
			]
		);

		$this->add_control(
			'show_view_all',
			[
				'label'        => esc_html__( 'View All Button', 'ovinx-addon' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'ovinx-addon' ),
				'label_off'    => esc_html__( 'Hide', 'ovinx-addon' ),
				'return_value' => 'yes',
				'default'      => '',
				'separator'    => 'before',
			]
		);

		$this->add_control(
			'view_all_text',
			[
				'label'     => esc_html__( 'View All Text', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => esc_html__( 'VIEW ALL PRODUCTS', 'ovinx-addon' ),
				'condition' => [ 'show_view_all' => 'yes' ],
			]
		);

		$this->add_control(
			'view_all_link',
			[
				'label'       => esc_html__( 'View All Link (empty = Shop page)', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::URL,
				'label_block' => true,
				'default'     => [ 'url' => '' ],
				'condition'   => [ 'show_view_all' => 'yes' ],
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
				'default' => 6,
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
		 * Ground truth's own tier names (`data-view-xl/-lg/-md/-sm`) don't
		 * map to this shared plugin's real 5-tier swiper init
		 * (`slider-core.js::initSwiperInScope()`, tiers keyed
		 * laptop=1440/preview=1200/tablet=768/mobile-sm=575/mobile=root) —
		 * same translation `widget-product-carousel.php` already uses.
		 * xl=2/lg=2 -> items_desktop/items_laptop, md=1/sm=1 ->
		 * items_tablet/items_mobile/items_mobile_sm. This card is wide
		 * (row layout, image + info side by side), so max is kept at 4
		 * rather than the 8 a plain grid-card carousel allows.
		 */
		$this->add_control(
			'items_desktop',
			[
				'label'   => esc_html__( 'Items — Desktop (≥1440px)', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 4,
				'default' => 2,
			]
		);

		$this->add_control(
			'items_laptop',
			[
				'label'   => esc_html__( 'Items — Laptop (≥1200px)', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 4,
				'default' => 2,
			]
		);

		$this->add_control(
			'items_tablet',
			[
				'label'   => esc_html__( 'Items — Tablet (≥768px)', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 4,
				'default' => 1,
			]
		);

		$this->add_control(
			'items_mobile',
			[
				'label'   => esc_html__( 'Items — Mobile (≥575px)', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 4,
				'default' => 1,
			]
		);

		$this->add_control(
			'items_mobile_sm',
			[
				'label'   => esc_html__( 'Items — Small Mobile (<575px)', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 4,
				'default' => 1,
			]
		);

		$this->end_controls_section();

		$this->register_swiper_group_controls( [ 20, 20, 20, 20, 20 ] );

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

		$this->add_control(
			'description_color',
			[
				'label'     => esc_html__( 'Description Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .sect-heading .s-desc' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'view_all_color',
			[
				'label'     => esc_html__( 'View All Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .sect-heading .tf-btn-line' => 'color: {{VALUE}};',
				],
				'condition' => [ 'show_view_all' => 'yes' ],
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
	 * `widget-product-carousel.php`/`widget-product-tabs.php`.
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
	 * Replaces `tfwc_progressbar` (see class doc comment, gap #2) at the
	 * exact same hook/priority for the duration of this widget's own loop.
	 * Runs while WooCommerce's own `the_post()`/global `$product` setup from
	 * `render()`'s `WP_Query` loop is still active, same as every other real
	 * hook callback in `product-style/style-1.php`.
	 */
	public static function render_stock_progress(): void {
		global $product;

		if ( ! $product instanceof WC_Product || ! $product->managing_stock() ) {
			return;
		}

		$available = (int) $product->get_stock_quantity();
		$sold      = (int) $product->get_total_sales();
		$total     = $available + $sold;
		$percentage = $total > 0 ? round( ( $sold / $total ) * 100 ) : 0;
		?>
		<div class="product-statusbar">
			<div class="progress-sold progress" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?php echo esc_attr( $percentage ); ?>" aria-label="<?php esc_attr_e( 'Sold', 'ovinx-addon' ); ?>">
				<div class="progress-bar" style="<?php echo esc_attr( 'width: ' . $percentage . '%' ); ?>"></div>
			</div>
			<div class="box-quantity">
				<p class="text-avaiable text-caption-01"><?php esc_html_e( 'Available:', 'ovinx-addon' ); ?> <span class="fw-semibold cl-text-main"><?php echo esc_html( number_format_i18n( $available ) ); ?></span></p>
				<p class="text-avaiable text-caption-01"><?php esc_html_e( 'Sold:', 'ovinx-addon' ); ?> <span class="fw-semibold cl-text-main"><?php echo esc_html( number_format_i18n( $sold ) ); ?></span></p>
			</div>
		</div>
		<?php
	}

	/**
	 * See class doc comment, gap #3. Printed once per request regardless of
	 * how many instances of this widget are on the page — same
	 * static-flag-guarded real `<style>` tag technique as
	 * `widget-product-tabs.php::render_skeleton_css_once()`.
	 */
	private static function render_card_css_once(): string {
		static $printed = false;

		if ( $printed ) {
			return '';
		}
		$printed = true;

		return '<style>.tf-product-list-carousel .box-action_add{position:static!important;opacity:1!important;visibility:visible!important;height:auto!important;padding:0!important;transition:none!important}</style>';
	}

	/**
	 * `.sect-heading.text-center` (`.s-title` h4 + `.s-desc` p) — same
	 * convention as `widget-product-showcase-slider.php`/
	 * `widget-banner-feature.php`. With "View All Button" on it switches to
	 * ground truth's `.sect-heading.type-2` (title left, `.tf-btn-line` right).
	 * Hidden entirely if title, description and button are all absent.
	 */
	private function render_heading( array $settings ): void {
		$has_title = ! empty( $settings['title'] );
		$has_desc  = ! empty( $settings['description'] );
		$has_btn   = 'yes' === ( $settings['show_view_all'] ?? '' );

		if ( ! $has_title && ! $has_desc && ! $has_btn ) {
			return;
		}

		if ( $has_btn ) {
			$view_all_url = ! empty( $settings['view_all_link']['url'] )
				? $settings['view_all_link']['url']
				: ( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : '#' );
			$link         = array_merge( (array) $settings['view_all_link'], [ 'url' => $view_all_url ] );
			$this->add_link_attributes( 'view_all_link', $link );
			?>
			<div class="sect-heading type-2">
				<?php if ( $has_title || $has_desc ) : ?>
					<div>
						<?php if ( $has_title ) : ?>
							<h4 class="s-title"><?php echo esc_html( $settings['title'] ); ?></h4>
						<?php endif; ?>
						<?php if ( $has_desc ) : ?>
							<p class="s-desc text-body-02"><?php echo esc_html( $settings['description'] ); ?></p>
						<?php endif; ?>
					</div>
				<?php endif; ?>
				<a <?php $this->print_render_attribute_string( 'view_all_link' ); ?> class="tf-btn-line">
					<span class="text-uppercase fw-semibold"><?php echo esc_html( $settings['view_all_text'] ); ?></span>
				</a>
			</div>
			<?php
			return;
		}
		?>
		<div class="sect-heading text-center">
			<?php if ( $has_title ) : ?>
				<h4 class="s-title"><?php echo esc_html( $settings['title'] ); ?></h4>
			<?php endif; ?>
			<?php if ( $has_desc ) : ?>
				<p class="s-desc text-body-02"><?php echo esc_html( $settings['description'] ); ?></p>
			<?php endif; ?>
		</div>
		<?php
	}

	protected function render(): void {
		$settings = $this->get_settings_for_display();

		$args = [
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'posts_per_page' => ! empty( $settings['posts_count'] ) ? absint( $settings['posts_count'] ) : 6,
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
		// all, including the heading (same convention as
		// widget-product-carousel.php).
		if ( ! $query->have_posts() ) {
			return;
		}

		self::force_style1_hooks();

		// Gap #1 (see class doc comment): every product in this widget's
		// loop needs `.product-style_list-3` — added right before the loop,
		// removed right after.
		$list3_class_filter = static function ( $classes ) {
			$classes[] = 'product-style_list-3';
			return $classes;
		};
		add_filter( 'post_class', $list3_class_filter, 10, 1 );

		// Gap #2 (see class doc comment): swap the real stock-progress
		// callback for this widget's own loop only.
		remove_action( 'woocommerce_after_shop_loop_item_title', 'tfwc_progressbar', 45 );
		add_action( 'woocommerce_after_shop_loop_item_title', [ __CLASS__, 'render_stock_progress' ], 45 );

		$space = isset( $settings['space_between']['size'] ) && '' !== $settings['space_between']['size'] ? $settings['space_between']['size'] : 20;
		?>
		<?php echo self::render_card_css_once(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static CSS string literal, see render_card_css_once() ?>
		<?php /* `tf-btn-swiper-main` lets slider-core.js find `.tf-sw-pagination` (it sits outside `.swiper`, same as the other carousel widgets). */ ?>
		<div class="tf-btn-swiper-main tf-product-list-carousel">
			<div class="container">
				<?php $this->render_heading( $settings ); ?>
				<div dir="ltr" class="swiper tf-swiper tfc-swiper"
					data-laptop="<?php echo esc_attr( $settings['items_desktop'] ); ?>"
					data-preview="<?php echo esc_attr( $settings['items_laptop'] ); ?>"
					data-tablet="<?php echo esc_attr( $settings['items_tablet'] ); ?>"
					data-mobile-sm="<?php echo esc_attr( $settings['items_mobile'] ); ?>"
					data-mobile="<?php echo esc_attr( $settings['items_mobile_sm'] ); ?>"
					<?php echo $this->get_swiper_group_attrs( $settings ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built with esc_attr() in the trait ?>
					<?php echo $this->get_swiper_space_attrs( $settings, array_fill( 0, 5, $space ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built with esc_attr() in the trait ?>>
					<div class="swiper-wrapper">
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
				<div class="sw-dot-default tf-sw-pagination"></div>
			</div>
		</div>
		<?php
		remove_filter( 'post_class', $list3_class_filter, 10 );
		remove_action( 'woocommerce_after_shop_loop_item_title', [ __CLASS__, 'render_stock_progress' ], 45 );
		add_action( 'woocommerce_after_shop_loop_item_title', 'tfwc_progressbar', 45 );
	}
}
