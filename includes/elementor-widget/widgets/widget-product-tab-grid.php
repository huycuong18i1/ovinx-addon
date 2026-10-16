<?php

/**
 * "Ovinx Product Tab Grid" — same multi-tab-of-real-WooCommerce-products
 * shape as `widget-product-tabs.php` ("Ovinx Product Tabs"), but ground
 * truth for THIS widget (`home-03.html`'s "Product Tab" section — the user
 * explicitly asked for a non-carousel variant of it) renders every tab as a
 * plain CSS grid (`.tf-grid-layout.tf-col-1.md-col-2.xl-col-3.gap-10` — all
 * 4 classes confirmed already compiled in `themes/ovinx/css/styles.css`,
 * matching the original swiper's `data-view-xl="3" data-view-md="2"
 * data-preview="1"` breakpoints 1:1) of compact horizontal
 * `.card-product.product-style_list-2` cards — no Swiper, no nav arrows,
 * no pagination dots, no `.br-line` separator (ground truth's heading is
 * just `<h4>` + `.list-tab-btn-v2`, nothing else — verified against
 * `themes/ovinx-html/home-03.html` line ~2464, do not copy
 * `widget-product-tabs.php`'s heading shape here).
 *
 * The card itself has a smaller real footprint than the theme's usual full
 * product card: image + category + name + price + a single square
 * add-to-cart icon button — no wishlist/compare/quickview/rating/
 * color-swatch/stock-badge (verified against ground truth: this card style
 * genuinely doesn't have them, not an oversight). Because of that it is NOT
 * built via `wc_get_template_part('content','product')` (that always prints
 * the full standard card) — each card's markup is hand-built in
 * `render_product_card()` from real product data instead (same "never fake
 * a price, always real product data" rule as every other WooCommerce widget
 * in this plugin), which also means `force_style1_hooks()` (used by
 * `widget-product-tabs.php`/`widget-product-carousel.php` to steer that
 * template-part pipeline) is irrelevant here and intentionally not called.
 *
 * Perf + UX: identical lazy-load pattern to `widget-product-tabs.php`
 * (2026-09-28 perf/UX fixes there — see that file's doc comment for the
 * full rationale). Only the first tab's products are queried/rendered on
 * page load; every other tab defers its `WP_Query` to AJAX
 * (`wp_ajax_ovinx_load_product_tab_grid`, wired in
 * `elementor-widget-addon.php::init_ajax()`) fired on click AND on
 * hover/focus prefetch (`initProductTabGridLazyLoadInScope()` in
 * `slider-core.js`), with a skeleton placeholder shaped for THIS card style
 * (horizontal image-left/2-lines-right, not the square grid skeleton
 * `widget-product-tabs.php` uses) and a crossfade swap instead of a hard
 * replace.
 */
class ovinx_Product_Tab_Grid extends \Elementor\Widget_Base {

	public function get_name() {
		return 'ovinx_product_tab_grid';
	}

	public function get_title() {
		return esc_html__( 'Ovinx Product Tab Grid', 'ovinx-addon' );
	}

	public function get_icon() {
		return 'eicon-post-list';
	}

	public function get_categories() {
		return [ 'ovinx_addons_core' ];
	}

	public function get_keywords() {
		return [ 'product', 'tabs', 'grid', 'woocommerce' ];
	}

	public function get_script_depends() {
		return [ 'slider-core' ];
	}

	/**
	 * Same private-per-widget convention already used by
	 * `widget-product-tabs.php`/`widget-product-carousel.php` (no shared
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
				'default'     => esc_html__( 'Featured Products', 'ovinx-addon' ),
				'label_block' => true,
			]
		);

		$this->end_controls_section();

		/**
		 * Tabs
		 */
		$this->start_controls_section(
			'section_tabs',
			[
				'label' => esc_html__( 'Tabs', 'ovinx-addon' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'tab_label',
			[
				'label'       => esc_html__( 'Tab Label', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'All Product', 'ovinx-addon' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
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

		$repeater->add_control(
			'posts_count',
			[
				'label'   => esc_html__( 'Number of Products', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 20,
				'default' => 8,
			]
		);

		$repeater->add_control(
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

		$repeater->add_control(
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
			'tab_items',
			[
				'label'       => esc_html__( 'Tab Items', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ tab_label }}}',
				'default'     => [
					[ 'tab_label' => esc_html__( 'All Product', 'ovinx-addon' ) ],
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
					'{{WRAPPER}} .sect-heading h4' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_card',
			[
				'label' => esc_html__( 'Product Card', 'ovinx-addon' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'category_color',
			[
				'label'     => esc_html__( 'Category Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .product-cate' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'name_color',
			[
				'label'     => esc_html__( 'Product Name Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .name-product' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'price_color',
			[
				'label'     => esc_html__( 'Price Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .price-wrap' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label'     => esc_html__( 'Add to Cart Icon Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tf-btn-square .icon' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
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
	 * Hand-built compact card — NOT `wc_get_template_part('content','product')`,
	 * see class doc comment for why. Image src/permalink/name/price/cart URL
	 * are all pulled from the real product; only the markup shape is fixed
	 * to match `product-style_list-2` (`themes/ovinx-html/home-03.html`).
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
			<?php
			/**
			 * `product_type_<type>` class + `data-product_id` are what
			 * `shop.js`'s Quick Add handler (`.tf_add_to_cart.product_type_variable`)
			 * matches on to open the real variation-picker popup instead of
			 * following `href` — `woocommerce_template_loop_add_to_cart()`
			 * adds both automatically, but this card is hand-built, so they
			 * have to be added explicitly or a variable product's button
			 * silently falls back to a plain link.
			 */
			?>
			<a href="<?php echo esc_url( $product->add_to_cart_url() ); ?>" data-quantity="1" data-product_id="<?php echo esc_attr( $product->get_id() ); ?>" class="<?php echo esc_attr( self::cart_button_classes( $product ) ); ?>" aria-label="<?php echo esc_attr( $product->add_to_cart_description() ); ?>">
				<i class="icon icon-ShoppingBagOpen" aria-hidden="true"></i>
			</a>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Same query-building convention as `widget-product-tabs.php`'s
	 * `render_tab_products()`, but returns concatenated
	 * `render_product_card()` markup instead of `swiper-slide`-wrapped
	 * `wc_get_template_part()` output.
	 */
	private static function render_tab_products( array $tab ): string {
		$args = [
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'posts_per_page' => ! empty( $tab['posts_count'] ) ? absint( $tab['posts_count'] ) : 8,
			'orderby'        => ! empty( $tab['order_by'] ) ? $tab['order_by'] : 'date',
			'order'          => ! empty( $tab['order'] ) ? $tab['order'] : 'DESC',
		];

		if ( 'price' === $args['orderby'] ) {
			$args['orderby']  = 'meta_value_num';
			$args['meta_key'] = '_price'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
		}

		if ( ! empty( $tab['category_ids'] ) ) {
			$args['tax_query'] = [ // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				[
					'taxonomy' => 'product_cat',
					'field'    => 'term_id',
					'terms'    => array_map( 'absint', (array) $tab['category_ids'] ),
				],
			];
		}

		$query = new WP_Query( $args );

		if ( ! $query->have_posts() ) {
			return '';
		}

		$cards = '';

		while ( $query->have_posts() ) {
			$query->the_post();

			$product = wc_get_product( get_the_ID() );

			if ( $product ) {
				$cards .= self::render_product_card( $product );
			}
		}

		wp_reset_postdata();

		return $cards;
	}

	/**
	 * Printed once per request regardless of widget instance count — same
	 * ordering rationale as `widget-product-tabs.php::render_skeleton_css_once()`
	 * (Elementor resolves `get_style_depends()` before this widget's own
	 * `render()` runs, so `wp_add_inline_style()` doesn't work here; a real
	 * `<style>` tag in the widget's own output does). Distinct class names
	 * from that widget's skeleton on purpose — this is a horizontal
	 * "image left, 2 lines right" shape for `.product-style_list-2`, not
	 * that widget's square product-grid shape.
	 */
	private static function render_skeleton_css_once(): string {
		static $printed = false;

		if ( $printed ) {
			return '';
		}
		$printed = true;

		return '<style>'
			. '.tf-skeleton-grid-card .card-product_wrapper{flex:0 0 100px;width:100px}'
			. '.tf-skeleton-grid-card .tf-skeleton-grid-img{width:100%;height:100%}'
			. '.tf-skeleton-grid-card .card-product_info{flex:1;min-width:0}'
			. '.tf-skeleton-grid-line{height:14px;border-radius:4px;margin-bottom:10px}'
			. '.tf-skeleton-grid-line.mid{width:75%}'
			. '.tf-skeleton-grid-line.short{width:40%}'
			. '.tf-skeleton-grid-line:last-child{margin-bottom:0}'
			. '.tf-skeleton-grid-btn{flex-shrink:0}'
			. '.tf-skeleton-grid-img,.tf-skeleton-grid-line,.tf-skeleton-grid-btn{background:linear-gradient(90deg,rgba(0,0,0,.06) 25%,rgba(0,0,0,.12) 37%,rgba(0,0,0,.06) 63%);background-size:400% 100%;animation:tfSkeletonGridShimmer 1.4s ease infinite}'
			. '@keyframes tfSkeletonGridShimmer{0%{background-position:100% 50%}100%{background-position:0 50%}}'
			. '.tf-tab-pane-fade{transition:opacity .2s ease}'
			. '.tf-tab-pane-fade.tf-tab-pane-fade-out{opacity:0}'
			. '</style>';
	}

	/**
	 * Skeleton rows placed inside the same `.tf-grid-layout` wrapper/column
	 * classes the real content uses, so the swap-in height/column shift is
	 * minimal (same technique as `widget-product-tabs.php`'s skeleton, just
	 * targeting a CSS grid instead of a swiper).
	 */
	private static function render_skeleton_markup( int $posts_count ): string {
		$count = min( 8, max( 3, $posts_count ) );

		ob_start();
		?>
		<div class="tf-grid-layout tf-col-1 md-col-2 xl-col-3 gap-10 tf-tab-pane-fade">
			<?php for ( $i = 0; $i < $count; $i++ ) : ?>
				<?php // Same classes as the real card so padding / border / gap / sizing come from the theme CSS. ?>
				<div class="card-product product-style_list-2 tf-skeleton-grid-card">
					<div class="card-product_wrapper">
						<div class="tf-skeleton-grid-img"></div>
					</div>
					<div class="card-product_info">
						<div class="tf-skeleton-grid-line short"></div>
						<div class="tf-skeleton-grid-line"></div>
						<div class="tf-skeleton-grid-line mid"></div>
					</div>
					<div class="tf-btn-square tf-skeleton-grid-btn"></div>
				</div>
			<?php endfor; ?>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * The grid markup shared by the eager first tab (`render()`) and every
	 * lazily-loaded tab (`ajax_render_tab()`), so both stay pixel-identical.
	 * `.tf-grid-layout` items are its own direct children (confirmed in
	 * `themes/ovinx/css/styles.css`) — no `swiper-slide` wrapper needed.
	 */
	private static function render_tab_pane_markup( string $cards_html ): string {
		return '<div class="tf-grid-layout tf-col-1 md-col-2 xl-col-3 gap-10">' . $cards_html . '</div>';
	}

	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$tabs     = ! empty( $settings['tab_items'] ) && is_array( $settings['tab_items'] ) ? $settings['tab_items'] : [];

		if ( empty( $tabs ) ) {
			return;
		}

		$has_title = ! empty( $settings['title'] );
		$has_tabs  = count( $tabs ) > 1;

		$skeleton_css = $has_tabs ? self::render_skeleton_css_once() : '';

		$widget_id = $this->get_id();
		?>
		<?php echo $skeleton_css; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static CSS string literal, see render_skeleton_css_once() ?>
		<div class="tf-product-tab-grid flat-animate-tab pd-tab-grid">
			<div class="container">
				<?php if ( $has_title || $has_tabs ) : ?>
					<div class="sect-heading">
						<?php if ( $has_title ) : ?>
							<h4><?php echo esc_html( $settings['title'] ); ?></h4>
						<?php endif; ?>
						<?php if ( $has_tabs ) : ?>
							<ul class="list-tab-btn-v2" role="tablist">
								<?php foreach ( $tabs as $index => $tab ) : ?>
									<li class="nav-tab-item" role="presentation">
										<a href="#tf-tab-grid-<?php echo esc_attr( $widget_id . '-' . $index ); ?>" data-bs-toggle="tab" class="tf-btn-tab<?php echo ( 0 === $index ) ? ' active' : ''; ?>" role="tab">
											<span class="fw-semibold text-uppercase"><?php echo esc_html( $tab['tab_label'] ); ?></span>
										</a>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</div>
				<?php endif; ?>
				<div class="tab-content">
					<?php foreach ( $tabs as $index => $tab ) :

						// Tab 0 only: query + render now (this is what's actually
						// visible on first paint). Every other tab defers its
						// WP_Query entirely to ajax_render_tab() — see the class
						// doc comment.
						if ( 0 === $index ) {
							$cards_html = self::render_tab_products( $tab );

							if ( empty( $cards_html ) ) {
								continue;
							}
							?>
							<div class="tab-pane active show" id="tf-tab-grid-<?php echo esc_attr( $widget_id . '-' . $index ); ?>" role="tabpanel">
								<?php echo self::render_tab_pane_markup( $cards_html ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- own escaped markup, see render_product_card()/render_tab_pane_markup() ?>
							</div>
							<?php
							continue;
						}

						$tab_query = wp_json_encode(
							[
								'category_ids' => ! empty( $tab['category_ids'] ) ? array_map( 'absint', (array) $tab['category_ids'] ) : [],
								'posts_count'  => ! empty( $tab['posts_count'] ) ? absint( $tab['posts_count'] ) : 8,
								'order_by'     => ! empty( $tab['order_by'] ) ? $tab['order_by'] : 'date',
								'order'        => ! empty( $tab['order'] ) ? $tab['order'] : 'DESC',
							]
						);
						?>
						<div class="tab-pane" id="tf-tab-grid-<?php echo esc_attr( $widget_id . '-' . $index ); ?>" role="tabpanel" data-tf-tab-query="<?php echo esc_attr( $tab_query ); ?>">
							<?php echo self::render_skeleton_markup( ! empty( $tab['posts_count'] ) ? absint( $tab['posts_count'] ) : 8 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- own escaped markup, see render_skeleton_markup() ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * AJAX target for `wp_ajax_ovinx_load_product_tab_grid` / its `nopriv`
	 * twin, both wired in `elementor-widget-addon.php::init_ajax()`
	 * (constructor-time, unconditional — NOT from `elementor/widgets/register`,
	 * same reasoning as `widget-product-tabs.php::ajax_render_tab()`). Reuses
	 * the same `tfwc_woo_params.ajax_url`/`.nonce` (action `tfwc_nonce`)
	 * convention as every other WooCommerce AJAX call in this theme.
	 */
	public static function ajax_render_tab(): void {
		check_ajax_referer( 'tfwc_nonce', 'nonce' );

		$order_by_whitelist = [ 'date', 'title', 'price', 'rand' ];
		$order_whitelist    = [ 'ASC', 'DESC' ];

		$order_by = isset( $_POST['order_by'] ) && in_array( wp_unslash( $_POST['order_by'] ), $order_by_whitelist, true ) ? wp_unslash( $_POST['order_by'] ) : 'date';
		$order    = isset( $_POST['order'] ) && in_array( wp_unslash( $_POST['order'] ), $order_whitelist, true ) ? wp_unslash( $_POST['order'] ) : 'DESC';

		$tab = [
			'category_ids' => isset( $_POST['category_ids'] ) ? array_map( 'absint', (array) $_POST['category_ids'] ) : [],
			'posts_count'  => isset( $_POST['posts_count'] ) ? min( 20, max( 1, absint( $_POST['posts_count'] ) ) ) : 8,
			'order_by'     => $order_by,
			'order'        => $order,
		];

		$cards_html = self::render_tab_products( $tab );

		if ( empty( $cards_html ) ) {
			wp_send_json_success( [ 'html' => '' ] );
		}

		wp_send_json_success( [ 'html' => self::render_tab_pane_markup( $cards_html ) ] );
	}
}
