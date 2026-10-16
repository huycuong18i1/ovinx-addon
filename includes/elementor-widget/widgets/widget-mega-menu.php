<?php

/**
 * Ground truth: `themes/ovinx-html/index.html` header nav — the two
 * `.sub-menu.mega-menu` panels (~line 660 "Shop" and ~line 855 "Products").
 * This widget is the *content* of one panel: it is meant to be dropped into a
 * "Mega Menu" post (CPT `megamenu`, `woocommerce/megamenu/megamenu.php`) which
 * `TF_MegaMenu_Walker` already wraps in `.sub-menu.mega-menu`, so the widget
 * itself renders only the inner `.container > .row`.
 *
 * Style 1 — link columns (`.col-lg-2`) + one `.collection-v01` banner (`.col-lg-6`).
 * Style 2 — link columns (`.col-lg-2`) + `.product-style_list-2` cards (`.col-lg-4`).
 *
 * Link columns are fixed per style (Style 1 = 3, Style 2 = 4): each column is its own
 * Content section with a Heading control + a links repeater (text + URL), since
 * Elementor doesn't support nested repeaters.
 * Style 2 cards use real WooCommerce data (name, category, price, cart URL) —
 * markup copied from `widget-product-showcase-slider.php::render_product_card()`.
 * No CSS added: `.menu-heading`, `.sub-menu_link`, `.collection-v01`,
 * `.product-style_list-2` and the `.box-nav-menu` overrides already exist in
 * `themes/ovinx/css/styles.css`.
 */
class ovinx_Mega_Menu extends \Elementor\Widget_Base {

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

	public function get_name() {
		return 'ovinx_mega_menu';
	}

	public function get_title() {
		return esc_html__( 'Ovinx Mega Menu', 'ovinx-addon' );
	}

	public function get_icon() {
		return 'eicon-nav-menu';
	}

	public function get_categories() {
		return [ 'ovinx_addons_core' ];
	}

	public function get_keywords() {
		return [ 'mega', 'menu', 'navigation', 'dropdown', 'header' ];
	}

	/**
	 * Fixed columns per style (Style 1 = 3, Style 2 = 4). Defaults = ground truth content.
	 */
	private function get_columns_data( $style ) {
		if ( 'style_2' === $style ) {
			return [
				'PRODUCT LAYOUT'  => [ 'Product Detail', 'Right Thumbnail', 'Left Thumbnail', 'Product Grid', 'Product Grid 2', 'Description Tab', 'Description List' ],
				'PRODUCT DETAIL'  => [ 'Inner Zoom', 'Inner Circle Zoom', 'No Zoom', 'External Zoom', 'Open Lightbox', 'Product Variant', 'Product Video' ],
				'PRODUCT FEATURE' => [ 'Buy Together', 'Product Group', 'Product Affiliate', 'Countdown Timer', 'Stock Bar', 'Out Stock', 'Vendor Logo' ],
				'PRODUCT SWATCH'  => [ 'Swatch Color', 'Swatch Image', 'Swatch Radio', 'Rounded', 'Rounded Color', 'Dropdown', 'Dropdown Color' ],
			];
		}

		return [
			'SHOP LAYOUT'  => [ 'Shop Default', 'Left Sidebar', 'Right Sidebar', 'Shop List', 'Sub Collection' ],
			'SHOP FEATURE' => [ 'Pagination Link', 'Pagination Loadmore', 'Infinite Scroll', 'Filter Sidebar', 'Filter Hidden', 'Filter Drawer' ],
			'MY PAGES'     => [ 'Wish List', 'View Cart', 'Checkout', 'Search Result', 'Compare', 'Order Tracking' ],
		];
	}

	/**
	 * One section per column: heading + links repeater (Elementor can't nest repeaters).
	 * Control keys: `{style}_col_{n}_heading` / `{style}_col_{n}_links`.
	 */
	private function register_column_sections( $style ) {
		$n = 0;

		foreach ( $this->get_columns_data( $style ) as $heading => $labels ) {
			++$n;

			$this->start_controls_section(
				"section_{$style}_col_{$n}",
				[
					/* translators: %d: column number */
					'label'     => sprintf( esc_html__( 'Column %d', 'ovinx-addon' ), $n ),
					'tab'       => \Elementor\Controls_Manager::TAB_CONTENT,
					'condition' => [ 'style' => $style ],
				]
			);

			$this->add_control(
				"{$style}_col_{$n}_heading",
				[
					'label'       => esc_html__( 'Heading', 'ovinx-addon' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'default'     => $heading,
					'label_block' => true,
				]
			);

			$repeater = new \Elementor\Repeater();

			$repeater->add_control(
				'text',
				[
					'label'       => esc_html__( 'Text', 'ovinx-addon' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'label_block' => true,
				]
			);

			$repeater->add_control(
				'link',
				[
					'label'       => esc_html__( 'Link', 'ovinx-addon' ),
					'type'        => \Elementor\Controls_Manager::URL,
					'label_block' => true,
					'default'     => [ 'url' => '' ],
				]
			);

			$default = [];
			foreach ( $labels as $label ) {
				$default[] = [ 'text' => $label ];
			}

			$this->add_control(
				"{$style}_col_{$n}_links",
				[
					'label'       => esc_html__( 'Links', 'ovinx-addon' ),
					'type'        => \Elementor\Controls_Manager::REPEATER,
					'fields'      => $repeater->get_controls(),
					'title_field' => '{{{ text }}}',
					'default'     => $default,
				]
			);

			$this->end_controls_section();
		}
	}

	protected function register_controls() {

		/**
		 * Layout
		 */
		$this->start_controls_section(
			'section_layout',
			[
				'label' => esc_html__( 'Layout', 'ovinx-addon' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'style',
			[
				'label'   => esc_html__( 'Style', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'style_1',
				'options' => [
					'style_1' => esc_html__( 'Style 1 — Links + Banner', 'ovinx-addon' ),
					'style_2' => esc_html__( 'Style 2 — Links + Products', 'ovinx-addon' ),
				],
			]
		);

		$this->end_controls_section();

		$this->register_column_sections( 'style_1' );
		$this->register_column_sections( 'style_2' );

		/**
		 * Banner — Style 1
		 */
		$this->start_controls_section(
			'section_banner',
			[
				'label'     => esc_html__( 'Banner', 'ovinx-addon' ),
				'tab'       => \Elementor\Controls_Manager::TAB_CONTENT,
				'condition' => [ 'style' => 'style_1' ],
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
				'default'     => esc_html__( 'FOR PROTECTION', 'ovinx-addon' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'banner_title',
			[
				'label'       => esc_html__( 'Title', 'ovinx-addon' ),
				'description' => esc_html__( 'Each new line becomes a line break on screens ≥ 576px.', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'default'     => "Advanced Full\nSynthetic Motor\nOil Formula",
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
				'default'     => [ 'url' => '' ],
			]
		);

		$this->end_controls_section();

		/**
		 * Products — Style 2
		 */
		$this->start_controls_section(
			'section_products',
			[
				'label'     => esc_html__( 'Products', 'ovinx-addon' ),
				'tab'       => \Elementor\Controls_Manager::TAB_CONTENT,
				'condition' => [ 'style' => 'style_2' ],
			]
		);

		$this->add_control(
			'product_ids',
			[
				'label'       => esc_html__( 'Products', 'ovinx-addon' ),
				'description' => esc_html__( 'Leave empty to show the 2 latest products.', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple'    => true,
				'options'     => $this->get_products_options(),
				'default'     => [],
			]
		);

		$this->end_controls_section();

		/**
		 * Heading Style
		 */
		$this->start_controls_section(
			'section_style_heading',
			[
				'label' => esc_html__( 'Column Heading', 'ovinx-addon' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'heading_color',
			[
				'label'     => esc_html__( 'Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tf-mega-menu .menu-heading' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'heading_typography',
				'selector' => '{{WRAPPER}} .tf-mega-menu .menu-heading',
			]
		);

		$this->end_controls_section();

		/**
		 * Link Style
		 */
		$this->start_controls_section(
			'section_style_link',
			[
				'label' => esc_html__( 'Links', 'ovinx-addon' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'link_typography',
				'selector' => '{{WRAPPER}} .tf-mega-menu .sub-menu_link',
			]
		);

		$this->start_controls_tabs( 'link_color_tabs' );

		$this->start_controls_tab(
			'link_color_normal',
			[ 'label' => esc_html__( 'Normal', 'ovinx-addon' ) ]
		);

		$this->add_control(
			'link_color',
			[
				'label'     => esc_html__( 'Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tf-mega-menu .sub-menu_link' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'link_color_hover',
			[ 'label' => esc_html__( 'Hover', 'ovinx-addon' ) ]
		);

		$this->add_control(
			'link_hover_color',
			[
				'label'     => esc_html__( 'Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tf-mega-menu .sub-menu_link:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		/**
		 * Banner Style (Style 1 only)
		 * Markup carries `.text-white` (Bootstrap, `!important`), so color rules need `!important` too.
		 */
		$this->start_controls_section(
			'section_style_banner',
			[
				'label'     => esc_html__( 'Banner', 'ovinx-addon' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [ 'style' => 'style_1' ],
			]
		);

		$this->add_control(
			'banner_tag_color',
			[
				'label'     => esc_html__( 'Tag Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tf-mega-menu .collection-v01 .cls_tag' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'banner_title_color',
			[
				'label'     => esc_html__( 'Title Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tf-mega-menu .collection-v01 .cls_name' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'banner_title_typography',
				'selector' => '{{WRAPPER}} .tf-mega-menu .collection-v01 .cls_name',
			]
		);

		$this->add_control(
			'banner_button_color',
			[
				'label'     => esc_html__( 'Button Text Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tf-mega-menu .collection-v01 .btn-action' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'banner_button_bg_color',
			[
				'label'     => esc_html__( 'Button Background', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tf-mega-menu .collection-v01 .btn-action' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	private function render_column( $heading, $links, $key ) {
		$links_html = '';

		foreach ( (array) $links as $index => $item ) {
			if ( empty( $item['text'] ) ) {
				continue;
			}

			$link_key = $key . '_link_' . $index;
			$link     = ! empty( $item['link'] ) ? $item['link'] : [];
			if ( empty( $link['url'] ) ) {
				$link['url'] = '#';
			}
			$this->add_link_attributes( $link_key, $link );

			$links_html .= sprintf(
				'<li><a %1$s class="sub-menu_link text-caption"><span class="text">%2$s</span></a></li>',
				$this->get_render_attribute_string( $link_key ),
				esc_html( $item['text'] )
			);
		}

		if ( empty( $heading ) && '' === $links_html ) {
			return '';
		}

		ob_start();
		?>
		<div class="col-lg-2">
			<div class="mega-menu-item menu-lv-2">
				<?php if ( ! empty( $heading ) ) : ?>
					<p class="menu-heading text-caption"><?php echo esc_html( $heading ); ?></p>
				<?php endif; ?>
				<?php if ( '' !== $links_html ) : ?>
					<ul class="sub-menu_list">
						<?php echo $links_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attributes from Elementor's add_link_attributes(), text via esc_html() ?>
					</ul>
				<?php endif; ?>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	private function get_banner_image_html( $settings ) {
		if ( empty( $settings['banner_image']['url'] ) ) {
			return '';
		}

		if ( ! empty( $settings['banner_image']['id'] ) ) {
			return \Elementor\Group_Control_Image_Size::get_attachment_image_html( $settings, 'banner_image_size', 'banner_image' );
		}

		return sprintf(
			'<img loading="lazy" width="635" height="250" src="%s" alt="%s">',
			esc_url( $settings['banner_image']['url'] ),
			esc_attr__( 'Image', 'ovinx-addon' )
		);
	}

	private function render_banner( $settings ) {
		if ( empty( $settings['banner_image']['url'] ) && empty( $settings['banner_title'] ) ) {
			return '';
		}

		$link = ! empty( $settings['banner_link'] ) ? $settings['banner_link'] : [];
		if ( empty( $link['url'] ) ) {
			$link['url'] = '#';
		}
		$this->add_link_attributes( 'banner_link', $link );

		$title_html = '';
		if ( ! empty( $settings['banner_title'] ) ) {
			$lines      = array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', $settings['banner_title'] ) ), 'strlen' );
			$title_html = implode( ' <br class="d-none d-sm-block">', array_map( 'esc_html', $lines ) );
		}

		ob_start();
		?>
		<div class="col-lg-6">
			<div class="collection-v01 hover-img6">
				<a <?php $this->print_render_attribute_string( 'banner_link' ); ?> class="cls-image img-style6" aria-label="<?php echo esc_attr( ! empty( $settings['banner_title'] ) ? trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( $settings['banner_title'] ) ) ) : __( 'View collection', 'ovinx-addon' ) ); ?>">
					<?php echo $this->get_banner_image_html( $settings ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from esc_url()/esc_attr() above or Elementor's own escaped image markup ?>
				</a>
				<div class="cls-content">
					<?php if ( ! empty( $settings['banner_tag'] ) ) : ?>
						<p class="cls_tag text-white fw-semibold"><?php echo esc_html( $settings['banner_tag'] ); ?></p>
					<?php endif; ?>
					<?php if ( '' !== $title_html ) : ?>
						<h4 class="cls_name text-white"><?php echo $title_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- each line passed through esc_html() above ?></h4>
					<?php endif; ?>
					<?php if ( ! empty( $settings['banner_button_text'] ) ) : ?>
						<a <?php $this->print_render_attribute_string( 'banner_link' ); ?> class="btn-action tf-btn btn-white-2 px-v1"><?php echo esc_html( $settings['banner_button_text'] ); ?></a>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
		return ob_get_clean();
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
	 * Same hand-built compact card as `widget-product-showcase-slider.php::render_product_card()`
	 * (`product_type_<type>` + `data-product_id` are what shop.js's Quick Add handler matches on).
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

	private function render_products( $settings ) {
		$ids = ! empty( $settings['product_ids'] ) ? array_map( 'absint', (array) $settings['product_ids'] ) : [];

		$args = [
			'status' => 'publish',
			'limit'  => 2,
			'return' => 'objects',
		];

		if ( ! empty( $ids ) ) {
			$args['include'] = $ids;
			$args['limit']   = count( $ids );
			$args['orderby'] = 'post__in';
		} else {
			$args['orderby'] = 'date';
			$args['order']   = 'DESC';
		}

		$cards_html = '';
		foreach ( wc_get_products( $args ) as $product ) {
			$cards_html .= self::render_product_card( $product );
		}

		if ( '' === $cards_html ) {
			return '';
		}

		ob_start();
		?>
		<div class="col-lg-4">
			<div class="tf-grid-layout gap-15">
				<?php echo $cards_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside render_product_card() ?>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$style    = ! empty( $settings['style'] ) ? $settings['style'] : 'style_1';
		$is_two   = ( 'style_2' === $style );

		$columns_html = '';
		$count        = count( $this->get_columns_data( $style ) );
		for ( $n = 1; $n <= $count; $n++ ) {
			$columns_html .= $this->render_column(
				isset( $settings[ "{$style}_col_{$n}_heading" ] ) ? $settings[ "{$style}_col_{$n}_heading" ] : '',
				isset( $settings[ "{$style}_col_{$n}_links" ] ) ? $settings[ "{$style}_col_{$n}_links" ] : [],
				"{$style}_col_{$n}"
			);
		}

		$side_html = $is_two ? $this->render_products( $settings ) : $this->render_banner( $settings );

		if ( '' === $columns_html && '' === $side_html ) {
			return;
		}
		?>
		<div class="tf-mega-menu themesFlat">
			<div class="container">
				<div class="row">
					<?php echo $columns_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside render_column() ?>
					<?php echo $side_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside render_banner()/render_products() ?>
				</div>
			</div>
		</div>
		<?php
	}
}
