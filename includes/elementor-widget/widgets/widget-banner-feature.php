<?php

class ovinx_Banner_Feature extends \Elementor\Widget_Base {

	public function get_name() {
		return 'ovinx_banner_feature';
	}

	public function get_title() {
		return esc_html__( 'Ovinx Banner Feature', 'ovinx-addon' );
	}

	public function get_icon() {
		return 'eicon-slider-3d';
	}

	public function get_categories() {
		return [ 'ovinx_addons_core' ];
	}

	public function get_keywords() {
		return [ 'banner', 'feature', 'product', 'carousel', 'thumbs' ];
	}

	public function get_script_depends() {
		return [ 'themesflat-swiper', 'slider-core' ];
	}

	public function get_style_depends() {
		return [ 'themesflat-swiper' ];
	}

	/**
	 * Same "bounded, non-AJAX SELECT2" convention already used by this
	 * widget's sibling `get_product_category_options()`/`get_product_brand_options()`
	 * (`widget-category.php`) — the plugin *does* register a `tfwc_select2`
	 * AJAX-searchable control (`controls/tfwc-select2.php`), but its
	 * `fetch_tfwc_select2_options` AJAX handler was never built (grepped —
	 * 0 hits), so it's currently non-functional. Wiring that up is out of
	 * scope here; this stays consistent with the working pattern instead.
	 */
	private function get_products_options() {
		$options = [];

		$products = get_posts(
			[
				'post_type'      => 'product',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'orderby'        => 'title',
				'order'          => 'ASC',
			]
		);

		foreach ( $products as $post ) {
			$options[ $post->ID ] = $post->post_title;
		}

		return $options;
	}

	protected function register_controls() {

		/**
		 * Slides
		 */
		$this->start_controls_section(
			'section_slides',
			[
				'label' => esc_html__( 'Slides', 'ovinx-addon' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'style',
			[
				'label'   => esc_html__( 'Style', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'style_1' => esc_html__( 'Style 1 — Thumbs Carousel', 'ovinx-addon' ),
					'style_2' => esc_html__( 'Style 2 — Single Banner + Product', 'ovinx-addon' ),
					'style_3' => esc_html__( 'Style 3 — Split Image (No Product)', 'ovinx-addon' ),
				],
				'default' => 'style_1',
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'thumb_image',
			[
				'label'   => esc_html__( 'Thumbnail Image', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
			]
		);

		$repeater->add_group_control(
			\Elementor\Group_Control_Image_Size::get_type(),
			[
				'name'      => 'thumb_image_size',
				'default'   => 'full',
				'separator' => 'none',
			]
		);

		$repeater->add_control(
			'mini_title',
			[
				'label'       => esc_html__( 'Mini Title', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'FEATURED WHEELS', 'ovinx-addon' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'title',
			[
				'label'       => esc_html__( 'Title', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'Drive Strong On Every Road Condition. Durable Alloy Wheels', 'ovinx-addon' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'desc',
			[
				'label'   => esc_html__( 'Description', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => esc_html__( 'Experience improved stability and control with durable alloy wheels. Drive strong on every road ahead!', 'ovinx-addon' ),
			]
		);

		$repeater->add_control(
			'product_id',
			[
				'label'       => esc_html__( 'Featured Product', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'label_block' => true,
				'options'     => $this->get_products_options(),
				'default'     => '',
			]
		);

		$this->add_control(
			'banner_slides',
			[
				'label'       => esc_html__( 'Slides', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ mini_title ? mini_title : "— slide —" }}}',
				'default'     => [ [], [] ],
				'condition'   => [
					'style' => 'style_1',
				],
			]
		);

		/**
		 * Style 2/3's ground truth (`index.html`'s plain `.banner-v02`,
		 * `home-03.html`'s `.banner-v02.style-2` — single static banner, no
		 * thumbs/swiper) share the *exact same* col-left text block, so both
		 * styles reuse these same controls instead of duplicating them —
		 * they only diverge in col-right (product card vs. plain image) and
		 * DOM order (see `render_split_banner()`).
		 */
		$this->add_control(
			'banner_title',
			[
				'label'       => esc_html__( 'Title', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Get Rolling With Tire Selections', 'ovinx-addon' ),
				'label_block' => true,
				'condition'   => [
					'style' => [ 'style_2', 'style_3' ],
				],
			]
		);

		$this->add_control(
			'banner_desc',
			[
				'label'     => esc_html__( 'Description', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::TEXTAREA,
				'default'   => esc_html__( 'Upgrade your driving experience with our top-notch tire selections.', 'ovinx-addon' ),
				'condition' => [
					'style' => [ 'style_2', 'style_3' ],
				],
			]
		);

		$this->add_control(
			'banner_button_text',
			[
				'label'       => esc_html__( 'Button Text', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'SHOP NOW', 'ovinx-addon' ),
				'label_block' => true,
				'condition'   => [
					'style' => [ 'style_2', 'style_3' ],
				],
			]
		);

		$this->add_control(
			'banner_button_link',
			[
				'label'       => esc_html__( 'Button Link', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::URL,
				'label_block' => true,
				'default'     => [
					'url' => '',
				],
				'condition'   => [
					'style' => [ 'style_2', 'style_3' ],
				],
			]
		);

		$this->add_control(
			'banner_image',
			[
				'label'     => esc_html__( 'Image', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::MEDIA,
				'default'   => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
				'condition' => [
					'style' => [ 'style_2', 'style_3' ],
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Image_Size::get_type(),
			[
				'name'      => 'banner_image_size',
				'default'   => 'full',
				'separator' => 'none',
				'condition' => [
					'style' => [ 'style_2', 'style_3' ],
				],
			]
		);

		$this->add_control(
			'featured_product_id',
			[
				'label'       => esc_html__( 'Featured Product', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'label_block' => true,
				'options'     => $this->get_products_options(),
				'default'     => '',
				'condition'   => [
					'style' => 'style_2',
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Heading Style — Style 1 only.
		 */
		$this->start_controls_section(
			'section_style_heading',
			[
				'label'     => esc_html__( 'Heading', 'ovinx-addon' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'style' => 'style_1',
				],
			]
		);

		$this->add_control(
			'mini_title_color',
			[
				'label'     => esc_html__( 'Mini Title Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .section-banner-feature .mini-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__( 'Title Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .section-banner-feature .title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .section-banner-feature .title',
			]
		);

		$this->add_control(
			'desc_color',
			[
				'label'     => esc_html__( 'Description Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .section-banner-feature .desc' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Pagination/Nav Style — Style 1 only.
		 */
		$this->start_controls_section(
			'section_style_nav',
			[
				'label'     => esc_html__( 'Navigation', 'ovinx-addon' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'style' => 'style_1',
				],
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

		$this->add_control(
			'nav_color',
			[
				'label'     => esc_html__( 'Arrow Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tf-sw-nav.style-3 .icon' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Banner Text Style — Style 2 only.
		 */
		$this->start_controls_section(
			'section_style_banner_text',
			[
				'label'     => esc_html__( 'Banner Text', 'ovinx-addon' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'style' => [ 'style_2', 'style_3' ],
				],
			]
		);

		$this->add_control(
			'banner_title_color',
			[
				'label'     => esc_html__( 'Title Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .banner-v02 .title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'banner_title_typography',
				'selector' => '{{WRAPPER}} .banner-v02 .title',
			]
		);

		$this->add_control(
			'banner_desc_color',
			[
				'label'     => esc_html__( 'Description Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .banner-v02 .desc' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'banner_button_color',
			[
				'label'     => esc_html__( 'Button Text Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .banner-v02 .btn-action' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'banner_button_bg_color',
			[
				'label'     => esc_html__( 'Button Background Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .banner-v02 .btn-action' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Same convention as `widget-slideshow.php`'s `get_repeater_image_html()`
	 * — real width/height from the actual uploaded attachment instead of
	 * ground truth's static `width="960" height="660"`.
	 */
	private function get_repeater_image_html( $item, $image_key, $size_key ) {
		if ( empty( $item[ $image_key ]['url'] ) ) {
			return '';
		}

		if ( ! empty( $item[ $image_key ]['id'] ) ) {
			return \Elementor\Group_Control_Image_Size::get_attachment_image_html( $item, $size_key, $image_key );
		}

		return sprintf(
			'<img loading="lazy" src="%s" alt="%s">',
			esc_url( $item[ $image_key ]['url'] ),
			esc_attr__( 'Image', 'ovinx-addon' )
		);
	}

	private function render_thumb_slide( $item ) {
		ob_start();
		?>
		<div class="swiper-slide">
			<div class="image">
				<?php echo $this->get_repeater_image_html( $item, 'thumb_image', 'thumb_image_size' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from esc_url()/esc_attr() above or Elementor's own escaped image markup ?>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * `.prd-img`/`.infor_name`/`.price-wrap` are always real WooCommerce
	 * product data (image, name, permalink, price via the theme's own
	 * `tfwc_price()` template part — the same `.price-new`/`.price-old`
	 * partial every other product card in this theme uses, so sale pricing
	 * stays consistent instead of reinventing price markup here). `.infor_tag`
	 * ("WHEELS" in ground truth) is the product's own first category name,
	 * not admin-typed, so it can't go stale either.
	 */
	private function render_main_slide( $item ) {
		ob_start();
		?>
		<div class="swiper-slide">
			<?php if ( ! empty( $item['mini_title'] ) || ! empty( $item['title'] ) || ! empty( $item['desc'] ) ) : ?>
				<div class="heading">
					<?php if ( ! empty( $item['mini_title'] ) ) : ?>
						<p class="mini-title fw-semibold text-primary"><?php echo esc_html( $item['mini_title'] ); ?></p>
					<?php endif; ?>
					<?php if ( ! empty( $item['title'] ) ) : ?>
						<h2 class="title"><?php echo wp_kses_post( $item['title'] ); ?></h2>
					<?php endif; ?>
					<?php if ( ! empty( $item['desc'] ) ) : ?>
						<p class="desc text-body-02"><?php echo esc_html( $item['desc'] ); ?></p>
					<?php endif; ?>
				</div>
			<?php endif; ?>
			<?php
			if ( ! empty( $item['product_id'] ) ) {
				global $product;
				$product = wc_get_product( absint( $item['product_id'] ) );

				if ( $product ) {
					$terms    = get_the_terms( $product->get_id(), 'product_cat' );
					$tag_name = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0]->name : '';
					?>
					<div class="product-feature">
						<div class="prd-img">
							<?php echo wp_get_attachment_image( $product->get_image_id(), 'thumbnail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core function, already escaped ?>
						</div>
						<div class="prd-content">
							<div class="infor">
								<?php if ( ! empty( $tag_name ) ) : ?>
									<div class="infor_tag text-label fw-semibold"><?php echo esc_html( mb_strtoupper( $tag_name ) ); ?></div>
								<?php endif; ?>
								<a href="<?php echo esc_url( $product->get_permalink() ); ?>" class="infor_name fw-medium cl-text-main text-capitalize link-underline text-line-clamp-1"><?php echo esc_html( $product->get_name() ); ?></a>
							</div>
							<?php tfwc_price(); ?>
						</div>
					</div>
					<?php
				}
			}
			?>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Real ecommerce card (wishlist/compare/quickview state, sale badge,
	 * color swatches, add-to-cart) — reuses the theme's own production card
	 * pipeline (`content-product.php` + its `woocommerce_before/after_shop_loop_item*`
	 * hooks) instead of hand-rolling equivalent markup, so it can never drift
	 * from every other real product card on the site. This is the *exact*
	 * "force Style 1 regardless of the site's Customizer default" convention
	 * already used by `woocommerce/wishlist/wishlist-recently.php` (remove
	 * every other style's hooks first so they don't stack with whatever
	 * style already got hooked earlier in this same page load, then add
	 * Style 1's, then loop a single-product WP_Query through
	 * `wc_get_template_part( 'content', 'product' )`).
	 */
	private function render_full_product_card( $product_id ): void {
		if ( empty( $product_id ) ) {
			return;
		}

		$query = new WP_Query(
			[
				'post_type'      => 'product',
				'post__in'       => [ absint( $product_id ) ],
				'posts_per_page' => 1,
				'post_status'    => 'publish',
			]
		);

		if ( ! $query->have_posts() ) {
			return;
		}

		tfwc_get_template_part( '/product-style-remove/style-1.php' );
		tfwc_get_template_part( '/product-style-remove/style-2.php' );
		tfwc_get_template_part( '/product-style-remove/style-3.php' );
		tfwc_get_template_part( '/product-style-remove/style-4.php' );
		tfwc_get_template_part( '/product-style-remove/style-list.php' );
		tfwc_get_template_part( '/product-style-remove/style-wishlist.php' );
		tfwc_get_template_part( '/product-style/style-1.php' );

		$_POST['product_style'] = 'style-1';

		while ( $query->have_posts() ) {
			$query->the_post();
			wc_get_template_part( 'content', 'product' );
		}

		wp_reset_postdata();
	}

	private function render_split_banner_text_col( $settings ) {
		ob_start();
		?>
		<div class="col-left">
			<?php if ( ! empty( $settings['banner_title'] ) ) : ?>
				<h3 class="title text-white mb-12"><?php echo esc_html( $settings['banner_title'] ); ?></h3>
			<?php endif; ?>
			<?php if ( ! empty( $settings['banner_desc'] ) ) : ?>
				<p class="desc text-body-02 text-white mb-16"><?php echo esc_html( $settings['banner_desc'] ); ?></p>
			<?php endif; ?>
			<?php if ( ! empty( $settings['banner_button_text'] ) ) :
				if ( empty( $settings['banner_button_link']['url'] ) ) {
					$settings['banner_button_link']['url'] = '#';
				}
				$this->add_link_attributes( 'banner_button_link', $settings['banner_button_link'] );
				?>
				<a <?php $this->print_render_attribute_string( 'banner_button_link' ); ?> class="btn-action tf-btn btn-white-3 px-v1"><?php echo esc_html( $settings['banner_button_text'] ); ?></a>
			<?php endif; ?>
		</div>
		<?php
		return ob_get_clean();
	}

	private function render_single_banner( $settings ): void {
		if ( empty( $settings['banner_title'] ) && empty( $settings['banner_desc'] ) && empty( $settings['featured_product_id'] ) ) {
			return;
		}
		?>
		<div class="themesFlat">
			<div class="container">
				<div class="banner-v02">
					<?php echo $this->render_split_banner_text_col( $settings ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from esc_html()/esc_url() above ?>
					<div class="col-right">
						<div class="image overflow-hidden">
							<?php echo $this->get_repeater_image_html( $settings, 'banner_image', 'banner_image_size' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from esc_url()/esc_attr() above or Elementor's own escaped image markup ?>
						</div>
						<?php $this->render_full_product_card( $settings['featured_product_id'] ); ?>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Style 3's ground truth (`home-03.html`) is the *same* `.banner-v02`
	 * split banner as Style 2, plus a `.style-2` modifier class — but with
	 * `.col-right` holding a plain image instead of `.card-product`, and
	 * emitted BEFORE `.col-left` in the DOM (ground truth really does list
	 * them in that order; there's no CSS re-ordering `col-right`/`col-left`
	 * back, so this mirrors the layout — image side, then text side).
	 */
	private function render_split_image_banner( $settings ): void {
		if ( empty( $settings['banner_title'] ) && empty( $settings['banner_desc'] ) && empty( $settings['banner_image']['url'] ) ) {
			return;
		}
		?>
		<div class="themesFlat">
			<div class="container">
				<div class="banner-v02 style-2">
					<div class="col-right">
						<div class="image overflow-hidden">
							<?php echo $this->get_repeater_image_html( $settings, 'banner_image', 'banner_image_size' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from esc_url()/esc_attr() above or Elementor's own escaped image markup ?>
						</div>
					</div>
					<?php echo $this->render_split_banner_text_col( $settings ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from esc_html()/esc_url() above ?>
				</div>
			</div>
		</div>
		<?php
	}

	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$style    = ! empty( $settings['style'] ) ? $settings['style'] : 'style_1';

		if ( 'style_2' === $style ) {
			$this->render_single_banner( $settings );
			return;
		}

		if ( 'style_3' === $style ) {
			$this->render_split_image_banner( $settings );
			return;
		}

		$items = ! empty( $settings['banner_slides'] ) && is_array( $settings['banner_slides'] ) ? $settings['banner_slides'] : [];

		if ( empty( $items ) ) {
			return;
		}

		$thumbs_html = '';
		$mains_html  = '';

		foreach ( $items as $item ) {
			$thumbs_html .= $this->render_thumb_slide( $item );
			$mains_html  .= $this->render_main_slide( $item );
		}
		?>
		<div class="section-banner-feature section-split tf-sw-thumbs">
			<div class="col-media">
				<div dir="ltr" class="swiper sw-thumb">
					<div class="swiper-wrapper">
						<?php echo $thumbs_html; ?>
					</div>
				</div>
			</div>
			<div class="col-content position-relative">
				<div>
					<div dir="ltr" class="swiper sw-main-thumb">
						<div class="swiper-wrapper">
							<?php echo $mains_html; ?>
						</div>
					</div>
					<div class="group-nav-action pst-2">
						<div class="d-flex align-items-center justify-content-between">
							<div class="tf-sw-nav style-3 nav-prev-swiper">
								<i class="icon icon-CaretLeft"></i>
							</div>
							<div class="tf-sw-nav style-3 nav-next-swiper">
								<i class="icon icon-CaretRight"></i>
							</div>
						</div>
					</div>
					<div class="sw-dot-default tf-sw-pagination"></div>
				</div>
			</div>
		</div>
		<?php
	}
}
