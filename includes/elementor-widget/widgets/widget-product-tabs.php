<?php

/**
 * Ground truth (`home-02.html`'s "Product" section) is a Bootstrap-tab
 * switcher (`data-bs-toggle="tab"`, already loaded site-wide —
 * `themes/ovinx/functions.php` enqueues bootstrap.min.js unconditionally,
 * confirmed working elsewhere e.g. `woocommerce/single-product/tabs/tabs.php`
 * — no extra JS needed just for tab switching itself) where EVERY tab is its
 * own independent Swiper carousel of REAL WooCommerce products
 * (`.card-product`, rendered via the same "force Style 1 hooks + real
 * WP_Query + wc_get_template_part('content','product')" convention already
 * used by `widget-banner-feature.php`'s Style 2 and `widget-testimonial.php`
 * Style 2), but only ONE shared pair of prev/next arrows exists (in the
 * heading, outside `.tab-content`) — see `initProductTabsNavInScope()` in
 * `slider-core.js` for how that's coordinated (a real, confirmed-missing
 * gap: `slider-core.js`'s existing nav-button lookup already falls back to
 * `.closest('.tf-btn-swiper-main')`, so every tab's swiper binds to the
 * *same* physical nav buttons unless something disables all but the active
 * one's navigation — Swiper itself has no such tab-awareness).
 *
 * Perf (2026-09-28): only the FIRST tab's products are queried/rendered on
 * page load (`render()`). Every other tab renders an empty placeholder with
 * its query args serialized into `data-tf-tab-query` — the actual
 * `WP_Query` + full `.card-product` markup for that tab is deferred to
 * `ajax_render_tab()` (`wp_ajax_ovinx_load_product_tab`, wired in
 * `elementor-widget-addon.php::init_ajax()`), fired only when the user
 * actually clicks that tab (`initProductTabsLazyLoadInScope()` in
 * `slider-core.js`). Without this, a widget with e.g. 10 tabs ran 10 full
 * `WP_Query`s + rendered 10 tabs' worth of full product-card markup on every
 * single page load, regardless of whether the visitor ever looked past tab 1.
 *
 * UX (2026-09-28): a plain spinner made the AJAX swap look like a layout
 * jump (spinner box's height has nothing to do with the real content's
 * height) and only started loading on click (visible delay). Fixed two ways:
 * (1) the placeholder is now a skeleton grid (`render_skeleton_markup()`)
 * shaped like the real card grid so the swap-in height change is small; (2)
 * `initProductTabsLazyLoadInScope()` also prefetches on `mouseenter`/`focus`
 * of the tab trigger, so by the time a mouse user actually clicks, the tab
 * has often already finished loading in the background. No local build
 * tooling exists for this plugin, and the usual
 * `wp_add_inline_style()` route doesn't work for a widget-render-time
 * addition (see `render_skeleton_css_once()`), so the skeleton's
 * shimmer/fade CSS ships as a small `<style>` tag printed once, inline in
 * this widget's own markup.
 */
class ovinx_Product_Tabs extends \Elementor\Widget_Base {

	use Ovinx_Swiper_Group_Controls;

	public function get_name() {
		return 'ovinx_product_tabs';
	}

	public function get_title() {
		return esc_html__( 'Ovinx Product Tabs', 'ovinx-addon' );
	}

	public function get_icon() {
		return 'eicon-tabs';
	}

	public function get_categories() {
		return [ 'ovinx_addons_core' ];
	}

	public function get_keywords() {
		return [ 'product', 'tabs', 'carousel', 'woocommerce' ];
	}

	public function get_script_depends() {
		return [ 'slider-core' ];
	}

	public function get_style_depends() {
		return [ 'themesflat-swiper' ];
	}

	/**
	 * Same private-per-widget convention already used by
	 * `widget-category.php`/`widget-banner-feature.php` (no shared static
	 * helper class exists in this plugin) — keyed by `term_id`, matching
	 * that same convention (the old, unused reference widget in
	 * `plugins/New folder/ovinx-addon` keyed by `slug` instead; not
	 * followed here for consistency with the plugin's real widgets).
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
				'default'     => esc_html__( 'Get Best Prices', 'ovinx-addon' ),
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

		$this->end_controls_section();

		$this->register_swiper_group_controls( [ 15, 15, 15, 20, 20 ] );

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
					'{{WRAPPER}} .sect-heading > .h4' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_tabs',
			[
				'label' => esc_html__( 'Tab Buttons', 'ovinx-addon' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'tab_color',
			[
				'label'     => esc_html__( 'Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tf-btn-tab' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tab_color_active',
			[
				'label'     => esc_html__( 'Active Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tf-btn-tab.active' => 'color: {{VALUE}};',
				],
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
	 * `widget-banner-feature.php::render_full_product_card()` /
	 * `wishlist-recently.php`. Called once per request (hooks persist for
	 * the rest of the request once added) — from `render()` for the eager
	 * first tab, and again from `ajax_render_tab()` since that runs as its
	 * own separate request.
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

		ob_start();

		while ( $query->have_posts() ) {
			$query->the_post();
			?>
			<div class="swiper-slide">
				<?php wc_get_template_part( 'content', 'product' ); ?>
			</div>
			<?php
		}

		wp_reset_postdata();

		return ob_get_clean();
	}

	/**
	 * Printed once per request (guarded by the static flag) regardless of how
	 * many widget instances are on the page. `wp_add_inline_style()` was
	 * tried first but doesn't work here: Elementor resolves+prints a
	 * widget's `get_style_depends()` handles during `wp_head` (it pre-walks
	 * the page's elements before the body renders), which is *before* this
	 * widget's own `render()` runs — so inline CSS attached at render time
	 * would be added to the handle after WP already printed it. A real
	 * `<style>` tag right in the widget's own output has no such ordering
	 * problem.
	 */
	private static function render_skeleton_css_once(): string {
		static $printed = false;

		if ( $printed ) {
			return '';
		}
		$printed = true;

		return '<style>'
			. '.tf-tab-pane-skeleton{display:flex;flex-wrap:nowrap;overflow:hidden;gap:var(--sk-gap,15px);--sk-n:var(--sk-xs)}'
			. '.tf-tab-pane-skeleton .tf-skeleton-card{flex:0 0 calc((100% - (var(--sk-n) - 1) * var(--sk-gap,15px)) / var(--sk-n));min-width:0}'
			. '@media (min-width:575px){.tf-tab-pane-skeleton{--sk-n:var(--sk-sm)}}'
			. '@media (min-width:768px){.tf-tab-pane-skeleton{--sk-n:var(--sk-md)}}'
			. '@media (min-width:1200px){.tf-tab-pane-skeleton{--sk-n:var(--sk-lg);--sk-gap:20px}}'
			. '@media (min-width:1440px){.tf-tab-pane-skeleton{--sk-n:var(--sk-xl)}}'
			. '.tf-tab-pane-skeleton .tf-skeleton-img{width:100%;aspect-ratio:1/1;margin-bottom:16px}'
			. '.tf-tab-pane-skeleton .tf-skeleton-line{height:14px;border-radius:4px;margin-bottom:10px}'
			. '.tf-tab-pane-skeleton .tf-skeleton-line.short{width:40%}'
			. '.tf-tab-pane-skeleton .tf-skeleton-line.mid{width:75%}'
			. '.tf-tab-pane-skeleton .tf-skeleton-img,.tf-tab-pane-skeleton .tf-skeleton-line{background:linear-gradient(90deg,rgba(0,0,0,.06) 25%,rgba(0,0,0,.12) 37%,rgba(0,0,0,.06) 63%);background-size:400% 100%;animation:tfSkeletonShimmer 1.4s ease infinite}'
			. '@keyframes tfSkeletonShimmer{0%{background-position:100% 50%}100%{background-position:0 50%}}'
			. '.tf-tab-pane-fade{transition:opacity .2s ease}'
			. '.tf-tab-pane-fade.tf-tab-pane-fade-out{opacity:0}'
			. '</style>';
	}

	/**
	 * Grid of fake `.card-product`-shaped boxes standing in for a lazy tab's
	 * real content while it loads — sized close enough to the real grid
	 * (columns auto-fill by width, same as the eventual product cards) that
	 * swapping it for real content doesn't read as a layout jump.
	 */
	private static function render_skeleton_markup( array $swiper_attrs ): string {
		// One row of cards sized exactly like the swiper slides at each breakpoint
		// (see initSwiperInScope: <575 = items_mobile_sm, 575 = items_mobile,
		// 768 = items_tablet, 1200 = items_laptop, 1440 = items_desktop) — extra
		// cards are clipped by the container's overflow:hidden.
		$cols = [
			'xs' => max( 1, absint( $swiper_attrs['items_mobile_sm'] ) ),
			'sm' => max( 1, absint( $swiper_attrs['items_mobile'] ) ),
			'md' => max( 1, absint( $swiper_attrs['items_tablet'] ) ),
			'lg' => max( 1, absint( $swiper_attrs['items_laptop'] ) ),
			'xl' => max( 1, absint( $swiper_attrs['items_desktop'] ) ),
		];
		$count = min( 8, max( $cols ) );
		$style = '';
		foreach ( $cols as $key => $value ) {
			$style .= '--sk-' . $key . ':' . $value . ';';
		}

		ob_start();
		?>
		<div class="tf-tab-pane-skeleton tf-tab-pane-fade" style="<?php echo esc_attr( $style ); ?>">
			<?php for ( $i = 0; $i < $count; $i++ ) : ?>
				<div class="tf-skeleton-card">
					<div class="tf-skeleton-img"></div>
					<div class="tf-skeleton-line short"></div>
					<div class="tf-skeleton-line"></div>
					<div class="tf-skeleton-line mid"></div>
					<div class="tf-skeleton-line short"></div>
				</div>
			<?php endfor; ?>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * The swiper markup shared by the eager first tab (`render()`) and every
	 * lazily-loaded tab (`ajax_render_tab()`), so both stay pixel-identical.
	 */
	private static function render_tab_pane_markup( string $slides_html, array $swiper_attrs ): string {
		ob_start();
		?>
		<div dir="ltr" class="swiper tf-swiper tfc-swiper wrap-sw-over-2"
			data-laptop="<?php echo esc_attr( $swiper_attrs['items_desktop'] ); ?>"
			data-preview="<?php echo esc_attr( $swiper_attrs['items_laptop'] ); ?>"
			data-tablet="<?php echo esc_attr( $swiper_attrs['items_tablet'] ); ?>"
			data-mobile-sm="<?php echo esc_attr( $swiper_attrs['items_mobile'] ); ?>"
			data-mobile="<?php echo esc_attr( $swiper_attrs['items_mobile_sm'] ); ?>"
			data-pagination="<?php echo esc_attr( $swiper_attrs['group_xs'] ); ?>" data-pagination-sm="<?php echo esc_attr( $swiper_attrs['group_sm'] ); ?>"
			data-pagination-md="<?php echo esc_attr( $swiper_attrs['group_md'] ); ?>" data-pagination-lg="<?php echo esc_attr( $swiper_attrs['group_lg'] ); ?>" data-pagination-xl="<?php echo esc_attr( $swiper_attrs['group_xl'] ); ?>"
			data-space="<?php echo esc_attr( $swiper_attrs['space_xs'] ); ?>" data-space-sm="<?php echo esc_attr( $swiper_attrs['space_sm'] ); ?>"
			data-space-md="<?php echo esc_attr( $swiper_attrs['space_md'] ); ?>" data-space-lg="<?php echo esc_attr( $swiper_attrs['space_lg'] ); ?>" data-space-xl="<?php echo esc_attr( $swiper_attrs['space_xl'] ); ?>">
			<div class="swiper-wrapper">
				<?php echo $slides_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from wc_get_template_part('content','product'), the theme's own already-escaped card markup ?>
			</div>
		</div>
		<div class="sw-dot-default tf-sw-pagination d-xl-none"></div>
		<?php
		return ob_get_clean();
	}

	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$tabs     = ! empty( $settings['tab_items'] ) && is_array( $settings['tab_items'] ) ? $settings['tab_items'] : [];

		if ( empty( $tabs ) ) {
			return;
		}

		self::force_style1_hooks();

		$skeleton_css = count( $tabs ) > 1 ? self::render_skeleton_css_once() : '';

		$widget_id = $this->get_id();

		$swiper_attrs = [
			'items_desktop'   => $settings['items_desktop'],
			'items_laptop'    => $settings['items_laptop'],
			'items_tablet'    => $settings['items_tablet'],
			'items_mobile'    => $settings['items_mobile'],
			'items_mobile_sm' => $settings['items_mobile_sm'],
			// Slides per group per breakpoint (xs/sm/md/lg/xl), see Ovinx_Swiper_Group_Controls.
		] + array_combine(
			[ 'group_xs', 'group_sm', 'group_md', 'group_lg', 'group_xl' ],
			array_values( $this->get_swiper_group_values( $settings ) )
		) + array_combine(
			[ 'space_xs', 'space_sm', 'space_md', 'space_lg', 'space_xl' ],
			array_values( $this->get_swiper_space_values( $settings, [ 15, 15, 15, 20, 20 ] ) )
		);
		?>
		<?php echo $skeleton_css; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static CSS string literal, see render_skeleton_css_once() ?>
		<div class="section-product-tab tf-btn-swiper-main flat-animate-tab">
			<div class="container">
				<div class="sect-heading">
					<?php if ( ! empty( $settings['title'] ) ) : ?>
						<p class="h4"><?php echo esc_html( $settings['title'] ); ?></p>
					<?php endif; ?>
					<?php if ( count( $tabs ) > 1 ) : ?>
						<span class="br-line type-vertical xl-d-none"></span>
						<ul class="list-tab-btn-v2" role="tablist">
							<?php foreach ( $tabs as $index => $tab ) : ?>
								<li class="nav-tab-item" role="presentation">
									<a href="#tf-tab-<?php echo esc_attr( $widget_id . '-' . $index ); ?>" data-bs-toggle="tab" class="tf-btn-tab<?php echo ( 0 === $index ) ? ' active' : ''; ?>" role="tab">
										<span class="fw-semibold text-uppercase"><?php echo esc_html( $tab['tab_label'] ); ?></span>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
					<div class="tf-list gap-8">
						<div class="tf-sw-nav style-2 nav-prev-swiper">
							<i class="icon icon-CaretLeft"></i>
						</div>
						<div class="tf-sw-nav style-2 nav-next-swiper">
							<i class="icon icon-CaretRight"></i>
						</div>
					</div>
				</div>
				<div class="tab-content">
					<?php foreach ( $tabs as $index => $tab ) :

						// Tab 0 only: query + render now (this is what's actually visible on
						// first paint). Every other tab defers its WP_Query entirely to
						// ajax_render_tab() — see the perf note at the top of this file.
						if ( 0 === $index ) {
							$slides_html = self::render_tab_products( $tab );

							if ( empty( $slides_html ) ) {
								continue;
							}
							?>
							<div class="tab-pane active show" id="tf-tab-<?php echo esc_attr( $widget_id . '-' . $index ); ?>" role="tabpanel">
								<?php echo self::render_tab_pane_markup( $slides_html, $swiper_attrs ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- own escaped markup, see render_tab_pane_markup() ?>
							</div>
							<?php
							continue;
						}

						$tab_query = wp_json_encode(
							array_merge(
								[
									'category_ids' => ! empty( $tab['category_ids'] ) ? array_map( 'absint', (array) $tab['category_ids'] ) : [],
									'posts_count'  => ! empty( $tab['posts_count'] ) ? absint( $tab['posts_count'] ) : 8,
									'order_by'     => ! empty( $tab['order_by'] ) ? $tab['order_by'] : 'date',
									'order'        => ! empty( $tab['order'] ) ? $tab['order'] : 'DESC',
								],
								$swiper_attrs
							)
						);
						?>
						<div class="tab-pane" id="tf-tab-<?php echo esc_attr( $widget_id . '-' . $index ); ?>" role="tabpanel" data-tf-tab-query="<?php echo esc_attr( $tab_query ); ?>">
							<?php echo self::render_skeleton_markup( $swiper_attrs ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- own escaped markup, see render_skeleton_markup() ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * AJAX target for `wp_ajax_ovinx_load_product_tab` / its `nopriv` twin,
	 * both wired in `elementor-widget-addon.php::init_ajax()` (called
	 * unconditionally from the addon's constructor — NOT from
	 * `elementor/widgets/register`, which isn't guaranteed to fire on a bare
	 * `admin-ajax.php` request). Reuses the same nonce/localized globals the
	 * theme already sets up for every other WooCommerce AJAX call
	 * (`tfwc_woo_params.ajax_url` / `.nonce`, action `tfwc_nonce`) instead of
	 * localizing a widget-specific one.
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

		self::force_style1_hooks();

		$slides_html = self::render_tab_products( $tab );

		if ( empty( $slides_html ) ) {
			wp_send_json_success( [ 'html' => '' ] );
		}

		$swiper_attrs = [
			'items_desktop'   => isset( $_POST['items_desktop'] ) ? min( 8, max( 1, absint( $_POST['items_desktop'] ) ) ) : 5,
			'items_laptop'    => isset( $_POST['items_laptop'] ) ? min( 8, max( 1, absint( $_POST['items_laptop'] ) ) ) : 4,
			'items_tablet'    => isset( $_POST['items_tablet'] ) ? min( 8, max( 1, absint( $_POST['items_tablet'] ) ) ) : 3,
			'items_mobile'    => isset( $_POST['items_mobile'] ) ? min( 8, max( 1, absint( $_POST['items_mobile'] ) ) ) : 2,
			'items_mobile_sm' => isset( $_POST['items_mobile_sm'] ) ? min( 8, max( 1, absint( $_POST['items_mobile_sm'] ) ) ) : 1,
		];

		// Slides per group per breakpoint; falls back to the matching items count when a request predates this option.
		foreach ( [ 'xs' => 'items_mobile_sm', 'sm' => 'items_mobile', 'md' => 'items_tablet', 'lg' => 'items_laptop', 'xl' => 'items_desktop' ] as $key => $items_key ) {
			$swiper_attrs[ 'group_' . $key ] = isset( $_POST[ 'group_' . $key ] ) ? min( 8, max( 1, absint( $_POST[ 'group_' . $key ] ) ) ) : $swiper_attrs[ $items_key ];
		}

		// Spacing (px) per breakpoint; same design default as render() when the request predates this option.
		foreach ( [ 'xs' => 15, 'sm' => 15, 'md' => 15, 'lg' => 20, 'xl' => 20 ] as $key => $default_space ) {
			$swiper_attrs[ 'space_' . $key ] = isset( $_POST[ 'space_' . $key ] ) ? min( 100, max( 0, (float) $_POST[ 'space_' . $key ] ) ) : $default_space;
		}

		wp_send_json_success( [ 'html' => self::render_tab_pane_markup( $slides_html, $swiper_attrs ) ] );
	}
}
