<?php

/**
 * "Ovinx Product Showcase Slider" — ground truth is `themes/ovinx-html/index.html`'s
 * "Shop Essential Automotive Gear" section (~line 4618 onward has a *different*,
 * unrelated 5-item carousel reusing the same heading copy — the real match for
 * THIS widget's fixed "list / featured card / list" layout is
 * `themes/ovinx-html/home-03.html` ~line 2121, confirmed identical in
 * `home-04.html`). It is a fixed 3-slide swiper (`data-view-lg="3"
 * data-view-md="2" data-preview="1"`), NOT an N-group repeating carousel:
 *
 * - Slide 1 / Slide 3: `.tf-list.vertical.gap-10` of compact
 *   `.card-product.product-style_list-2` cards (image + category + name +
 *   price + a single square add-to-cart icon button, no wishlist/compare/
 *   quickview/rating) — same card shape and hand-built-markup rationale as
 *   `widget-product-tab-grid.php::render_product_card()`, copied verbatim
 *   from there (including its Quick Add `product_type_<type>` +
 *   `data-product_id` fix — see that method's doc comment).
 * - Slide 2 (`lg-d-none` — hidden below 992px, confirmed
 *   `themes/ovinx/css/styles.css` ~line 3522, kept on purpose): one FULL
 *   product card with sale badge, wishlist/compare/quickview, color
 *   swatches, rating, real price, full-width "ADD TO CART" — i.e. the
 *   theme's real `wc_get_template_part('content','product')` pipeline
 *   forced to Style 1, same `force_style1_hooks()` convention as
 *   `widget-product-carousel.php`/`widget-product-tabs.php`/
 *   `widget-product-banner-grid.php` (each keeps its own copy, no shared
 *   helper class exists in this plugin).
 *
 * Ground truth's middle card also carries a `card-s2` class that no real
 * hook produces (`content-product.php` only ever appends the literal
 * `$product_style` value, e.g. "style-2" — a different string from
 * "card-s2"). The CSS rule `.card-product.card-s2` genuinely exists
 * (`themes/ovinx/css/styles.css` ~line 15517, centers `.card-product_info`/
 * `.product-color_list`/`.product-rating`), so it's added via a `post_class`
 * filter scoped to just that one product — same technique
 * `widget-product-banner-grid.php::render_products()` uses for its first
 * card's `d-md-none d-lg-flex` classes.
 *
 * Single `WP_Query` (`posts_per_page = 2*N+1`, N = "Items per list" control)
 * split into three chunks while looping via `$query->the_post()` (so
 * WordPress's own `setup_postdata()` — not manual global juggling — backs
 * `wc_get_template_part()` for the middle card): first N posts -> left list,
 * post N+1 -> middle card, remaining posts (up to N, gracefully fewer if the
 * query returned less than 2N+1) -> right list. Fewer than N+1 total results
 * -> bail before printing anything (not enough for even the left list + the
 * middle card).
 *
 * No lazy-load/AJAX/skeleton pattern here (unlike `widget-product-tabs.php`/
 * `widget-product-tab-grid.php`) — this widget runs exactly one `WP_Query`
 * per render, same as `widget-product-carousel.php`/
 * `widget-product-banner-grid.php`.
 */
class ovinx_Product_Showcase_Slider extends \Elementor\Widget_Base {

	use Ovinx_Swiper_Group_Controls;

	public function get_name() {
		return 'ovinx_product_showcase_slider';
	}

	public function get_title() {
		return esc_html__( 'Ovinx Product Showcase Slider', 'ovinx-addon' );
	}

	public function get_icon() {
		return 'eicon-carousel';
	}

	public function get_categories() {
		return [ 'ovinx_addons_core' ];
	}

	public function get_keywords() {
		return [ 'product', 'slider', 'showcase', 'swiper', 'woocommerce' ];
	}

	public function get_script_depends() {
		return [ 'slider-core' ];
	}

	public function get_style_depends() {
		return [ 'themesflat-swiper' ];
	}

	/**
	 * Same private-per-widget convention already used by
	 * `widget-product-tabs.php`/`widget-product-carousel.php`/
	 * `widget-product-tab-grid.php` (no shared static helper class exists in
	 * this plugin) — keyed by `term_id`.
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
				'default'     => esc_html__( 'Shop Essential Automotive Gear', 'ovinx-addon' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'description',
			[
				'label'       => esc_html__( 'Description', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'Premium Solutions For Every Driving Need', 'ovinx-addon' ),
				'label_block' => true,
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

		$this->add_control(
			'items_per_list',
			[
				'label'   => esc_html__( 'Items per List (left/right)', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 10,
				'default' => 4,
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

		$this->add_control(
			'items_desktop',
			[
				'label'   => esc_html__( 'Items — Desktop (≥1440px)', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 8,
				'default' => 3,
			]
		);

		$this->add_control(
			'items_laptop',
			[
				'label'   => esc_html__( 'Items — Laptop (≥1200px)', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 8,
				'default' => 3,
			]
		);

		$this->add_control(
			'items_tablet',
			[
				'label'   => esc_html__( 'Items — Tablet (≥768px)', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 8,
				'default' => 2,
			]
		);

		$this->add_control(
			'items_mobile',
			[
				'label'   => esc_html__( 'Items — Mobile (≥575px)', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 8,
				'default' => 1,
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

		$this->end_controls_section();

		$this->register_swiper_group_controls( [ 15, 15, 20, 20, 20 ] );

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
	 * color swatches, rating + stock badge, add-to-cart) for the middle
	 * slide — reuses the theme's own production card pipeline, same "force
	 * Style 1 regardless of the site's Customizer default" convention as
	 * `widget-product-carousel.php::force_style1_hooks()`.
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
	 * Classes for the square add-to-cart icon button. `add_to_cart_button` + `ajax_add_to_cart` are what WooCommerce's own
	 * add-to-cart.js needs to intercept the click and add via AJAX (then fire `added_to_cart`, which the theme's shop.js
	 * answers by opening the mini-cart offcanvas); without them the link just navigates to `?add-to-cart=ID`.
	 * Variable/other types keep only `product_type_*` (variable opens the Quick Add popup via shop.js).
	 */
	private static function cart_button_classes( WC_Product $product ): string {
		$classes = array( 'tf_add_to_cart', 'tf-btn-square' );

		if ( $product->is_type( 'simple' ) ) {
			$classes[] = 'add_to_cart_button';

			if ( $product->supports( 'ajax_add_to_cart' ) && $product->is_purchasable() && ( $product->is_in_stock() || $product->backorders_allowed() ) ) {
				$classes[] = 'ajax_add_to_cart';
			}
		}

		$classes[] = 'product_type_' . $product->get_type();

		return implode( ' ', $classes );
	}
/**
	 * Hand-built compact card for the left/right `.tf-list.vertical` slides
	 * — copied verbatim from
	 * `widget-product-tab-grid.php::render_product_card()` (same
	 * `product-style_list-2` shape: image + category + name + price + a
	 * single square add-to-cart icon button, no wishlist/compare/quickview/
	 * rating — ground truth genuinely doesn't have them here). Image src/
	 * permalink/name/price/cart URL are all pulled from the real product;
	 * only the markup shape is fixed.
	 *
	 * `product_type_<type>` class + `data-product_id` are what `shop.js`'s
	 * Quick Add handler (`.tf_add_to_cart.product_type_variable`) matches on
	 * to open the real variation-picker popup instead of following `href` —
	 * `woocommerce_template_loop_add_to_cart()` adds both automatically, but
	 * this card is hand-built, so they have to be added explicitly or a
	 * variable product's button silently falls back to a plain link.
	 */
	private static function render_product_card( WC_Product $product ): string {
		$image_id  = $product->get_image_id();
		$image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'woocommerce_thumbnail' ) : wc_placeholder_img_src( 'woocommerce_thumbnail' );

		$terms         = wp_get_post_terms( $product->get_id(), 'product_cat' );
		$category_name = ( ! empty( $terms ) && ! is_wp_error( $terms ) ) ? $terms[0]->name : '';

		ob_start();
		?>
		<div class="card-product product-style_list-2">
			<div class="card-product_wrapper">
				<a href="<?php echo esc_url( $product->get_permalink() ); ?>" class="product-img">
					<img class="img-product" loading="lazy" width="268" height="268" src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $product->get_name() ); ?>">
				</a>
			</div>
			<div class="card-product_info">
				<?php if ( ! empty( $category_name ) ) : ?>
					<div class="product-cate text-label fw-semibold"><?php echo esc_html( $category_name ); ?></div>
				<?php endif; ?>
				<a href="<?php echo esc_url( $product->get_permalink() ); ?>" class="name-product lh-24 fw-medium link-underline cl-text-main"><?php echo esc_html( $product->get_name() ); ?></a>
				<div class="price-wrap"><?php echo wp_kses_post( $product->get_price_html() ); ?></div>
			</div>
			<a href="<?php echo esc_url( $product->add_to_cart_url() ); ?>" data-quantity="1" data-product_id="<?php echo esc_attr( $product->get_id() ); ?>" class="<?php echo esc_attr( self::cart_button_classes( $product ) ); ?>" aria-label="<?php echo esc_attr( $product->add_to_cart_description() ); ?>">
				<i class="icon icon-ShoppingBagOpen" aria-hidden="true"></i>
			</a>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Middle slide's full product card. Must be called while `$query`'s own
	 * `the_post()` has already set up global post/product state (see
	 * `render()`) so `wc_get_template_part('content','product')` resolves
	 * the right product on its own, same as every other real-card widget in
	 * this plugin.
	 *
	 * Adds the ground-truth-only `card-s2` class via a `post_class` filter
	 * scoped to just this product id — see class doc comment for why no
	 * real hook produces that class name on its own.
	 */
	private static function render_middle_card(): string {
		$product_id = get_the_ID();

		$card_s2_filter = static function ( $classes, $class, $class_post_id ) use ( $product_id ) {
			if ( $product_id === $class_post_id ) {
				$classes[] = 'card-s2';
			}
			return $classes;
		};
		add_filter( 'post_class', $card_s2_filter, 10, 3 );

		ob_start();
		wc_get_template_part( 'content', 'product' );
		$html = ob_get_clean();

		remove_filter( 'post_class', $card_s2_filter, 10 );

		return $html;
	}

	/**
	 * `.sect-heading.text-center` (`.s-title` h4 + `.s-desc` p) — ground
	 * truth `home-03.html` ~line 2123. Hidden entirely if both title and
	 * description are empty.
	 */
	private function render_heading( array $settings ): void {
		$has_title = ! empty( $settings['title'] );
		$has_desc  = ! empty( $settings['description'] );

		if ( ! $has_title && ! $has_desc ) {
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

		$items_per_list = ! empty( $settings['items_per_list'] ) ? absint( $settings['items_per_list'] ) : 4;
		$items_per_list = max( 1, min( 10, $items_per_list ) );

		$args = [
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'posts_per_page' => ( 2 * $items_per_list ) + 1,
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

		// Not enough products for both the left list AND the middle card —
		// bail before printing anything at all, including the heading (see
		// class doc comment).
		if ( $query->post_count < $items_per_list + 1 ) {
			wp_reset_postdata();
			return;
		}

		self::force_style1_hooks();

		$left_cards  = '';
		$middle_html = '';
		$right_cards = '';
		$index       = 0;

		while ( $query->have_posts() ) {
			$query->the_post();

			if ( $index < $items_per_list ) {
				$product = wc_get_product( get_the_ID() );
				if ( $product ) {
					$left_cards .= self::render_product_card( $product );
				}
			} elseif ( $items_per_list === $index ) {
				$middle_html = self::render_middle_card();
			} else {
				$product = wc_get_product( get_the_ID() );
				if ( $product ) {
					$right_cards .= self::render_product_card( $product );
				}
			}

			++$index;
		}

		wp_reset_postdata();

		// Left list or middle card ended up empty (e.g. every matching
		// product failed `wc_get_product()`) — nothing sensible to render.
		if ( empty( $left_cards ) || empty( $middle_html ) ) {
			return;
		}
		?>
		<div class="tf-btn-swiper-main tf-product-showcase-slider">
			<div class="container">
				<?php $this->render_heading( $settings ); ?>
				<div dir="ltr" class="swiper tf-swiper tfc-swiper wrap-sw-over-2"
					data-laptop="<?php echo esc_attr( $settings['items_desktop'] ); ?>"
					data-preview="<?php echo esc_attr( $settings['items_laptop'] ); ?>"
					data-tablet="<?php echo esc_attr( $settings['items_tablet'] ); ?>"
					data-mobile-sm="<?php echo esc_attr( $settings['items_mobile'] ); ?>"
					data-mobile="<?php echo esc_attr( $settings['items_mobile_sm'] ); ?>"
					<?php echo $this->get_swiper_group_attrs( $settings ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built with esc_attr() in the trait ?>
					<?php echo $this->get_swiper_space_attrs( $settings, [ 15, 15, 20, 20, 20 ] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built with esc_attr() in the trait ?>>
					<div class="swiper-wrapper">
						<div class="swiper-slide">
							<?php echo '<div class="tf-list vertical gap-10">' . $left_cards . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- own escaped markup, see render_product_card() ?>
						</div>
						<div class="swiper-slide lg-d-none">
							<?php echo $middle_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- real WooCommerce template output, see render_middle_card() ?>
						</div>
						<?php if ( ! empty( $right_cards ) ) : ?>
							<div class="swiper-slide">
								<?php echo '<div class="tf-list vertical gap-10">' . $right_cards . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- own escaped markup, see render_product_card() ?>
							</div>
						<?php endif; ?>
					</div>
				</div>
				<div class="sw-dot-default tf-sw-pagination"></div>
			</div>
		</div>
		<?php
	}
}
