<?php

/**
 * Ground truth (`themes/ovinx-html/index.html`'s "Product Grid" section,
 * `.layout-prd_2row.tf-grid-layout`) is a plain CSS grid (not a swiper) whose
 * first child is a fixed static banner (`.wd-2-cols.collection-v03`,
 * `grid-column: span 2` via `.wd-2-cols` — already compiled in
 * `themes/ovinx/css/styles.css`) followed by real WooCommerce product cards.
 * Products use the same "force Style 1 hooks + real WP_Query +
 * wc_get_template_part('content','product')" convention as
 * `widget-product-tabs.php` — no tabs/swiper wrapper here, each product card
 * renders straight as a direct grid child.
 */
class ovinx_Product_Banner_Grid extends \Elementor\Widget_Base {

	public function get_name() {
		return 'ovinx_product_banner_grid';
	}

	public function get_title() {
		return esc_html__( 'Ovinx Product Banner Grid', 'ovinx-addon' );
	}

	public function get_icon() {
		return 'eicon-posts-grid';
	}

	public function get_categories() {
		return [ 'ovinx_addons_core' ];
	}

	public function get_keywords() {
		return [ 'product', 'grid', 'banner', 'woocommerce' ];
	}

	/**
	 * Same private-per-widget convention already used by
	 * `widget-product-tabs.php`/`widget-category.php` — keyed by `term_id`.
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
		 * Banner
		 */
		$this->start_controls_section(
			'section_banner',
			[
				'label' => esc_html__( 'Banner', 'ovinx-addon' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'banner_image',
			[
				'label'   => esc_html__( 'Image', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Image_Size::get_type(),
			[
				'name'      => 'banner_image_size',
				'default'   => 'full',
				'separator' => 'none',
			]
		);

		$this->add_control(
			'banner_tag',
			[
				'label'       => esc_html__( 'Tag', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'FEATURED PRODUCT', 'ovinx-addon' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'banner_title',
			[
				'label'       => esc_html__( 'Title', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'default'     => __( 'Top 10 Best <br>Sellers Of Week', 'ovinx-addon' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'banner_desc',
			[
				'label'       => esc_html__( 'Description', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'default'     => __( 'Check out our top 10 bestsellers <br>of this that.', 'ovinx-addon' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'banner_button_text',
			[
				'label'       => esc_html__( 'Button Text', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'SHOP NOW', 'ovinx-addon' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'banner_link',
			[
				'label'       => esc_html__( 'Link', 'ovinx-addon' ),
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
		 * Tag Style
		 */
		$this->start_controls_section(
			'section_style_tag',
			[
				'label' => esc_html__( 'Tag', 'ovinx-addon' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'tag_color',
			[
				'label'     => esc_html__( 'Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .collection-v03 .cls_tag' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Title Style
		 */
		$this->start_controls_section(
			'section_style_title',
			[
				'label' => esc_html__( 'Title', 'ovinx-addon' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__( 'Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .collection-v03 .cls_name' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .collection-v03 .cls_name',
			]
		);

		$this->end_controls_section();

		/**
		 * Description Style
		 */
		$this->start_controls_section(
			'section_style_desc',
			[
				'label' => esc_html__( 'Description', 'ovinx-addon' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'desc_color',
			[
				'label'     => esc_html__( 'Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .collection-v03 .cls_desc' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'desc_typography',
				'selector' => '{{WRAPPER}} .collection-v03 .cls_desc',
			]
		);

		$this->end_controls_section();

		/**
		 * Button Style
		 */
		$this->start_controls_section(
			'section_style_button',
			[
				'label' => esc_html__( 'Button', 'ovinx-addon' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'button_color',
			[
				'label'     => esc_html__( 'Text Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .collection-v03 .btn-action' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .collection-v03 .btn-action' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Same convention as `widget-collection-banner.php`'s
	 * `get_repeater_image_html()` — real width/height from the uploaded
	 * attachment via `Group_Control_Image_Size`, no fixed field/size-key
	 * prefix since this is a single fixed banner, not a repeater item.
	 */
	private function get_banner_image_html( $settings ) {
		if ( empty( $settings['banner_image']['url'] ) ) {
			return '';
		}

		if ( ! empty( $settings['banner_image']['id'] ) ) {
			return \Elementor\Group_Control_Image_Size::get_attachment_image_html( $settings, 'banner_image_size', 'banner_image' );
		}

		return sprintf(
			'<img loading="lazy" src="%s" alt="%s">',
			esc_url( $settings['banner_image']['url'] ),
			esc_attr__( 'Image', 'ovinx-addon' )
		);
	}

	private function render_banner( $settings ) {
		$link_key = 'banner_link';

		$link = $settings['banner_link'];
		if ( empty( $link['url'] ) ) {
			$link['url'] = '#';
		}
		$this->add_link_attributes( $link_key, $link );

		ob_start();
		?>
		<div class="wd-2-cols collection-v03 hover-img6 d-lg-none d-xl-block">
			<a <?php $this->print_render_attribute_string( $link_key ); ?> class="cls-image img-style6" aria-label="<?php echo esc_attr( ! empty( $settings['banner_title'] ) ? trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( $settings['banner_title'] ) ) ) : __( 'View collection', 'ovinx-addon' ) ); ?>">
				<?php echo $this->get_banner_image_html( $settings ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from esc_url()/esc_attr() above or Elementor's own escaped image markup ?>
			</a>
			<div class="cls-content">
				<?php if ( ! empty( $settings['banner_tag'] ) ) : ?>
					<p class="cls_tag text-primary fw-semibold"><?php echo esc_html( $settings['banner_tag'] ); ?></p>
				<?php endif; ?>
				<?php if ( ! empty( $settings['banner_title'] ) ) : ?>
					<h3 class="cls_name cl-text-main"><?php echo wp_kses_post( $settings['banner_title'] ); ?></h3>
				<?php endif; ?>
				<?php if ( ! empty( $settings['banner_desc'] ) ) : ?>
					<p class="cls_desc text-body-02"><?php echo wp_kses_post( $settings['banner_desc'] ); ?></p>
				<?php endif; ?>
				<?php if ( ! empty( $settings['banner_button_text'] ) ) : ?>
					<a <?php $this->print_render_attribute_string( $link_key ); ?> class="btn-action tf-btn px-v1"><?php echo esc_html( $settings['banner_button_text'] ); ?></a>
				<?php endif; ?>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Real ecommerce card (wishlist/compare/quickview state, sale badge,
	 * color swatches, rating + stock badge, add-to-cart) — reuses the
	 * theme's own production card pipeline, same "force Style 1 regardless
	 * of the site's Customizer default" convention as `widget-product-tabs.php`.
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

	private function render_products( $settings ) {
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

		if ( ! $query->have_posts() ) {
			return '';
		}

		self::force_style1_hooks();

		/**
		 * Ground truth's first product card carries extra
		 * `d-md-none d-lg-flex` classes (a deliberate responsive-visibility
		 * quirk to balance the grid at md/lg breakpoints, not a mistake) —
		 * `content-product.php` builds its wrapper classes via `post_class()`,
		 * so the only way to reproduce that exactly for real WooCommerce
		 * output (instead of hand-rolled markup) is a scoped `post_class`
		 * filter applied to just the first product in this loop.
		 */
		$index               = 0;
		$first_card_classes  = static function ( $classes, $class, $post_id ) use ( &$index ) {
			if ( 0 === $index && 'product' === get_post_type( $post_id ) ) {
				$classes[] = 'd-md-none';
				$classes[] = 'd-lg-flex';
			}
			return $classes;
		};
		add_filter( 'post_class', $first_card_classes, 10, 3 );

		ob_start();

		while ( $query->have_posts() ) {
			$query->the_post();
			wc_get_template_part( 'content', 'product' );
			++$index;
		}

		remove_filter( 'post_class', $first_card_classes, 10 );

		wp_reset_postdata();

		return ob_get_clean();
	}

	protected function render(): void {
		$settings = $this->get_settings_for_display();

		$banner_html   = $this->render_banner( $settings );
		$products_html = $this->render_products( $settings );

		if ( empty( $banner_html ) && empty( $products_html ) ) {
			return;
		}
		?>
			<div class="container">
				<div class="layout-prd_2row tf-grid-layout tf-col-2 md-col-3 lg-col-4 xl-col-5 gap-15 gap-lg-20">
					<?php
					echo $banner_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- own escaped markup, see render_banner()
					echo $products_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from wc_get_template_part('content','product'), the theme's own already-escaped card markup
					?>
				</div>
			</div>
		<?php
	}
}
