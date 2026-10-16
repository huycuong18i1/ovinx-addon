<?php

class ovinx_Category_Product extends \Elementor\Widget_Base {

	use Ovinx_Swiper_Group_Controls;

	public function get_name() {
		return 'ovinx_category_product';
	}

	public function get_title() {
		return esc_html__( 'Ovinx Category Product', 'ovinx-addon' );
	}

	public function get_icon() {
		return 'eicon-product-categories';
	}

	public function get_categories() {
		return [ 'ovinx_addons_core' ];
	}

	public function get_keywords() {
		return [ 'category', 'product', 'carousel', 'brand' ];
	}

	public function get_script_depends() {
		return [ 'themesflat-swiper', 'slider-core' ];
	}

	public function get_style_depends() {
		return [ 'themesflat-swiper' ];
	}

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
				'label'     => esc_html__( 'Heading', 'ovinx-addon' ),
				'tab'       => \Elementor\Controls_Manager::TAB_CONTENT,
				'condition' => [
					'style!' => 'style_5',
				],
			]
		);

		$this->add_control(
			'title',
			[
				'label'       => esc_html__( 'Title', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Find Parts For Your Vehicle', 'ovinx-addon' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'description',
			[
				'label'     => esc_html__( 'Description', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::TEXTAREA,
				'default'   => esc_html__( 'Unleash automotive excellence with our precision crafted parts and tools.', 'ovinx-addon' ),
				'condition' => [
					'style!' => 'style_4',
				],
			]
		);

		/**
		 * Style 4's ground truth (`home-04.html`) uses a completely different
		 * heading layout — `.sect-heading.type-2` (title + "View All" button,
		 * flex space-between, no description) instead of the centered
		 * title+description block the other styles use.
		 */
		$this->add_control(
			'view_all_text',
			[
				'label'     => esc_html__( 'View All Button Text', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => esc_html__( 'VIEW ALL PRODUCTS', 'ovinx-addon' ),
				'condition' => [
					'style' => 'style_4',
				],
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
				'condition'   => [
					'style' => 'style_4',
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Categories
		 */
		$this->start_controls_section(
			'section_categories',
			[
				'label' => esc_html__( 'Categories', 'ovinx-addon' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'style',
			[
				'label'   => esc_html__( 'Style', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'style_1' => esc_html__( 'Style 1 — Icon', 'ovinx-addon' ),
					'style_2' => esc_html__( 'Style 2 — Image', 'ovinx-addon' ),
					'style_3' => esc_html__( 'Style 3 — Brand Grid', 'ovinx-addon' ),
					'style_4' => esc_html__( 'Style 4 — Image + View All', 'ovinx-addon' ),
					'style_5' => esc_html__( 'Style 5 — Image Card (Overlay Content)', 'ovinx-addon' ),
				],
				'default' => 'style_1',
			]
		);

		$this->add_control(
			'show_count',
			[
				'label'        => esc_html__( 'Show Product Count', 'ovinx-addon' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'ovinx-addon' ),
				'label_off'    => esc_html__( 'No', 'ovinx-addon' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => [
					'style!' => [ 'style_3', 'style_5' ],
				],
			]
		);

		$this->add_control(
			'card_button_text',
			[
				'label'     => esc_html__( 'Button Text', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => esc_html__( 'VIEW ALL', 'ovinx-addon' ),
				'condition' => [
					'style' => 'style_5',
				],
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'icon',
			[
				'label'       => esc_html__( 'Icon', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::ICONS,
				'description' => esc_html__( 'Used by Style 1 only — Style 2 shows the category\'s real WooCommerce thumbnail image instead.', 'ovinx-addon' ),
				'default'     => [
					'value'   => 'icon-Caliper',
					'library' => 'ovinx_icon',
				],
			]
		);

		$repeater->add_control(
			'product_category',
			[
				'label'       => esc_html__( 'Product Category', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'label_block' => true,
				'options'     => $this->get_product_category_options(),
				'default'     => '',
			]
		);

		/**
		 * Ground truth's `home-04.html` mixes one promo "Best Sellers" slide
		 * (`.cate-sale` — admin-typed percent/label, no image) in among the
		 * normal image slides. Only rendered for Style 2/4 (see
		 * `render_category_item()`) — Style 1 has no image to replace with a
		 * badge, so this is ignored there.
		 */
		$repeater->add_control(
			'card_type',
			[
				'label'       => esc_html__( 'Card Type', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'options'     => [
					'normal' => esc_html__( 'Normal (Image/Icon)', 'ovinx-addon' ),
					'sale'   => esc_html__( 'Sale Badge', 'ovinx-addon' ),
				],
				'default'     => 'normal',
				'description' => esc_html__( 'Sale Badge only renders on Style 2/4 — Style 1 always shows its icon regardless.', 'ovinx-addon' ),
			]
		);

		$repeater->add_control(
			'custom_image',
			[
				'label'       => esc_html__( 'Custom Image', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::MEDIA,
				'description' => esc_html__( 'Style 2/4/5 — optional. Leave empty to use the category\'s own WooCommerce thumbnail. Style 5 shows it portrait (242×340).', 'ovinx-addon' ),
				'condition'   => [
					'card_type!' => 'sale',
				],
			]
		);

		$repeater->add_control(
			'card_tag',
			[
				'label'       => esc_html__( 'Tag Text', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => esc_html__( 'Style 5 only — small label above the name (e.g. BRAKE SYSTEM).', 'ovinx-addon' ),
				'condition'   => [
					'card_type!' => 'sale',
				],
			]
		);

		$repeater->add_control(
			'sale_percent',
			[
				'label'     => esc_html__( 'Sale Percent Text', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => '15%',
				'condition' => [
					'card_type' => 'sale',
				],
			]
		);

		$repeater->add_control(
			'sale_label',
			[
				'label'     => esc_html__( 'Sale Label', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => 'OFF',
				'condition' => [
					'card_type' => 'sale',
				],
			]
		);

		$this->add_control(
			'category_items',
			[
				'label'       => esc_html__( 'Category Items', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ product_category ? product_category : "— choose a category —" }}}',
				'default'     => [
					[ 'icon' => [ 'value' => 'icon-Caliper', 'library' => 'ovinx_icon' ] ],
					[ 'icon' => [ 'value' => 'icon-CarBattery', 'library' => 'ovinx_icon' ] ],
					[ 'icon' => [ 'value' => 'icon-CarRim', 'library' => 'ovinx_icon' ] ],
					[ 'icon' => [ 'value' => 'icon-Turbo', 'library' => 'ovinx_icon' ] ],
					[ 'icon' => [ 'value' => 'icon-Oil', 'library' => 'ovinx_icon' ] ],
					[ 'icon' => [ 'value' => 'icon-HighBeam', 'library' => 'ovinx_icon' ] ],
				],
				'condition'   => [
					'style!' => 'style_3',
				],
			]
		);

		$brand_repeater = new \Elementor\Repeater();

		$brand_repeater->add_control(
			'brand_name',
			[
				'label'       => esc_html__( 'Name', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
			]
		);

		$brand_repeater->add_control(
			'brand_link',
			[
				'label'       => esc_html__( 'Link', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::URL,
				'label_block' => true,
				'placeholder' => 'https://',
			]
		);

		$brand_repeater->add_control(
			'custom_image',
			[
				'label'   => esc_html__( 'Image', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
			]
		);

		$this->add_control(
			'brand_items',
			[
				'label'       => esc_html__( 'Brand Items', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $brand_repeater->get_controls(),
				'title_field' => '{{{ brand_name || "— brand —" }}}',
				'default'     => [ [], [], [], [], [], [] ],
				'condition'   => [
					'style' => 'style_3',
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Carousel Layout — irrelevant to Style 3, which is a plain
		 * `.tf-grid-layout` grid, not a swiper carousel.
		 */
		$this->start_controls_section(
			'section_carousel',
			[
				'label'     => esc_html__( 'Carousel Layout', 'ovinx-addon' ),
				'tab'       => \Elementor\Controls_Manager::TAB_CONTENT,
				'condition' => [
					'style!' => 'style_3',
				],
			]
		);

		$this->add_control(
			'items_desktop',
			[
				'label'       => esc_html__( 'Items — Desktop (≥1440px)', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'min'         => 1,
				'max'         => 10,
				'default'     => 6,
			]
		);

		$this->add_control(
			'items_laptop',
			[
				'label'   => esc_html__( 'Items — Laptop (≥1200px)', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 10,
				'default' => 5,
			]
		);

		$this->add_control(
			'items_tablet',
			[
				'label'   => esc_html__( 'Items — Tablet (≥768px)', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 10,
				'default' => 4,
			]
		);

		$this->add_control(
			'items_mobile',
			[
				'label'   => esc_html__( 'Items — Mobile (≥575px)', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 10,
				'default' => 3,
			]
		);

		$this->add_control(
			'items_mobile_sm',
			[
				'label'   => esc_html__( 'Items — Small Mobile (<575px)', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 10,
				'default' => 1,
			]
		);

		$this->end_controls_section();

		$this->register_swiper_group_controls( [ 15, 15, 15, 15, 20 ] );

		/**
		 * Title Style
		 */
		$this->start_controls_section(
			'section_style_title',
			[
				'label'     => esc_html__( 'Title', 'ovinx-addon' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'style!' => 'style_5',
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__( 'Color', 'ovinx-addon' ),
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

		$this->end_controls_section();

		/**
		 * Description Style
		 */
		$this->start_controls_section(
			'section_style_desc',
			[
				'label'     => esc_html__( 'Description', 'ovinx-addon' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'style!' => [ 'style_4', 'style_5' ],
				],
			]
		);

		$this->add_control(
			'desc_color',
			[
				'label'     => esc_html__( 'Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .sect-heading .text-body-02' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'desc_typography',
				'selector' => '{{WRAPPER}} .sect-heading .text-body-02',
			]
		);

		$this->end_controls_section();

		/**
		 * "View All" Button Style — Style 4 only.
		 */
		$this->start_controls_section(
			'section_style_view_all',
			[
				'label'     => esc_html__( 'View All Button', 'ovinx-addon' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'style' => 'style_4',
				],
			]
		);

		$this->add_control(
			'view_all_color',
			[
				'label'     => esc_html__( 'Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .sect-heading.type-2 .tf-btn-line' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Category Card Style
		 */
		$this->start_controls_section(
			'section_style_category',
			[
				'label' => esc_html__( 'Category', 'ovinx-addon' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label'     => esc_html__( 'Icon Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .category-v01 .icon' => 'color: {{VALUE}};',
				],
				'condition' => [
					'style' => 'style_1',
				],
			]
		);

		/**
		 * No `default` on purpose — ground truth already sets its own spacing
		 * (`.category-v01 .icon` margin-bottom 12px/24px responsive,
		 * `.category-v02`'s grid `gap: 4px`, `.wg-brand`'s grid with no gap
		 * at all). Leaving this unset means Elementor emits no override CSS
		 * until the admin actually touches the slider, so all 3 styles keep
		 * their real default look.
		 */
		$this->add_responsive_control(
			'icon_image_spacing',
			[
				'label'      => esc_html__( 'Icon/Image – Title Spacing', 'ovinx-addon' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 80,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .category-v01 .icon'       => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .category-v02 .cate-image' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .wg-brand .brand-image'    => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'cate_name_color',
			[
				'label'     => esc_html__( 'Name Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .category-v01 .cate-name, {{WRAPPER}} .category-v02 .cate-name, {{WRAPPER}} .category-v03 .cate-content .h5, {{WRAPPER}} .wg-brand .brand-name' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'cate_name_typography',
				'selector' => '{{WRAPPER}} .category-v01 .cate-name, {{WRAPPER}} .category-v02 .cate-name, {{WRAPPER}} .category-v03 .cate-content .h5, {{WRAPPER}} .wg-brand .brand-name',
			]
		);

		$this->add_control(
			'cate_count_color',
			[
				'label'     => esc_html__( 'Count Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'condition' => [
					'style!' => [ 'style_3', 'style_5' ],
				],
				'selectors' => [
					'{{WRAPPER}} .category-v01 .cate-count, {{WRAPPER}} .category-v02 .cate-count' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'cate_tag_color',
			[
				'label'     => esc_html__( 'Tag Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'condition' => [
					'style' => 'style_5',
				],
				'selectors' => [
					'{{WRAPPER}} .category-v03 .cate_tag' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'      => 'cate_tag_typography',
				'selector'  => '{{WRAPPER}} .category-v03 .cate_tag',
				'condition' => [
					'style' => 'style_5',
				],
			]
		);

		$this->add_control(
			'cate_button_color',
			[
				'label'     => esc_html__( 'Button Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'condition' => [
					'style' => 'style_5',
				],
				'selectors' => [
					'{{WRAPPER}} .category-v03 .tf-btn-line' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Pagination Style
		 */
		$this->start_controls_section(
			'section_style_pagination',
			[
				'label'     => esc_html__( 'Pagination', 'ovinx-addon' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'style!' => 'style_3',
				],
			]
		);

		$this->add_control(
			'pagination_color',
			[
				'label'     => esc_html__( 'Bullet Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .sw-dot-default .swiper-pagination-bullet, {{WRAPPER}} .sw-line-default .swiper-pagination-bullet' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'pagination_color_active',
			[
				'label'     => esc_html__( 'Active Bullet Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .sw-dot-default .swiper-pagination-bullet-active, {{WRAPPER}} .sw-line-default .swiper-pagination-bullet-active' => 'background: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Ground truth (`index.html`'s `.category-v01`, `home-02.html`/`home-04.html`'s
	 * `.category-v02`) has no real per-term icon field in WooCommerce's data
	 * model — so Style 1's icon stays admin-picked. Style 2/4's image, however,
	 * *does* have a real WooCommerce equivalent (the category's own
	 * "Thumbnail" field, `term_meta['thumbnail_id']`), so it's pulled live
	 * instead of adding a redundant per-item image upload control. Name/count
	 * are always pulled live from the real linked `product_cat` term in all
	 * styles, so they can never go stale against the real catalog — the one
	 * exception is Style 4's optional "Sale Badge" card (`home-04.html`'s
	 * "Best Sellers" slide), whose percent/label text has no WooCommerce
	 * equivalent to pull from, so that part alone stays admin-typed.
	 */
	private function render_category_item( $item, $style, $show_count, $button_text = '' ) {
		if ( empty( $item['product_category'] ) ) {
			return '';
		}

		$term = get_term( absint( $item['product_category'] ), 'product_cat' );

		if ( ! $term || is_wp_error( $term ) ) {
			return '';
		}

		$count_text = sprintf(
			/* translators: %s: number of products */
			_n( '%s Product', '%s Products', $term->count, 'ovinx-addon' ),
			number_format_i18n( $term->count )
		);

		ob_start();

		if ( 'style_5' === $style ) {
			/**
			 * Style 5 — `home-03.html` `.category-v03`: portrait image with the
			 * tag / name / button overlaid on top. Image is admin-uploaded
			 * (falls back to the category thumbnail); name + links are the
			 * real term's.
			 */
			$term_url  = get_term_link( $term );
			$image_url = ! empty( $item['custom_image']['id'] ) ? wp_get_attachment_image_url( $item['custom_image']['id'], 'large' ) : '';

			if ( empty( $image_url ) ) {
				$thumbnail_id = get_term_meta( $term->term_id, 'thumbnail_id', true );
				$image_url    = $thumbnail_id ? wp_get_attachment_image_url( $thumbnail_id, 'large' ) : '';
			}

			if ( empty( $image_url ) ) {
				$image_url = \Elementor\Utils::get_placeholder_image_src();
			}
			?>
			<div class="swiper-slide">
				<div class="category-v03 hover-img">
					<div class="cate-image img-style">
						<img loading="lazy" width="242" height="340" src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $term->name ); ?>">
					</div>
					<div class="cate-content">
						<?php if ( ! empty( $item['card_tag'] ) ) : ?>
							<p class="cate_tag text-label fw-semibold mb-4"><?php echo esc_html( $item['card_tag'] ); ?></p>
						<?php endif; ?>
						<a href="<?php echo esc_url( $term_url ); ?>" class="h5 link-underline mb-12"><?php echo esc_html( $term->name ); ?></a>
						<?php if ( '' !== $button_text ) : ?>
							<a href="<?php echo esc_url( $term_url ); ?>" class="tf-btn-line">
								<span class="text-button-small"><?php echo esc_html( $button_text ); ?></span>
							</a>
						<?php endif; ?>
					</div>
				</div>
			</div>
			<?php
		} elseif ( 'style_2' === $style || 'style_4' === $style ) {
			$is_style_4 = 'style_4' === $style;
			$is_sale    = $is_style_4 && ! empty( $item['card_type'] ) && 'sale' === $item['card_type'];
			$image_size = $is_style_4 ? 160 : 140;
			$name_class = 'cate-name h6 link-underline' . ( $is_sale ? ' text-primary' : '' );
			$count_class = 'cate-count text-caption-01' . ( $is_sale ? ' text-primary' : '' );
			?>
			<div class="swiper-slide">
				<a href="<?php echo esc_url( get_term_link( $term ) ); ?>" class="category-v02<?php echo $is_style_4 ? ' style-2' : ''; ?> hover-img6">
					<?php if ( $is_sale ) : ?>
						<div class="cate-sale img-style6">
							<span class="h2"><?php echo esc_html( ! empty( $item['sale_percent'] ) ? $item['sale_percent'] : '15%' ); ?></span>
							<span class="h5"><?php echo esc_html( ! empty( $item['sale_label'] ) ? $item['sale_label'] : 'OFF' ); ?></span>
						</div>
					<?php else :
						$image_url = ! empty( $item['custom_image']['id'] ) ? wp_get_attachment_image_url( $item['custom_image']['id'], 'medium' ) : '';

						if ( empty( $image_url ) ) {
							$thumbnail_id = get_term_meta( $term->term_id, 'thumbnail_id', true );
							$image_url    = $thumbnail_id ? wp_get_attachment_image_url( $thumbnail_id, 'thumbnail' ) : '';
						}

						if ( empty( $image_url ) ) {
							$image_url = \Elementor\Utils::get_placeholder_image_src();
						}
						?>
						<div class="cate-image img-style6">
							<img loading="lazy" width="<?php echo esc_attr( $image_size ); ?>" height="<?php echo esc_attr( $image_size ); ?>" src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $term->name ); ?>">
						</div>
					<?php endif; ?>
					<p class="<?php echo esc_attr( $name_class ); ?>"><?php echo esc_html( $term->name ); ?></p>
					<?php if ( $show_count ) : ?>
						<p class="<?php echo esc_attr( $count_class ); ?>"><?php echo esc_html( $count_text ); ?></p>
					<?php endif; ?>
				</a>
			</div>
			<?php
		} else {
			?>
			<div class="swiper-slide">
				<a href="<?php echo esc_url( get_term_link( $term ) ); ?>" class="category-v01">
					<?php if ( ! empty( $item['icon']['value'] ) ) : ?>
						<?php \Elementor\Icons_Manager::render_icon( $item['icon'], [ 'class' => 'icon', 'aria-hidden' => 'true' ] ); ?>
					<?php endif; ?>
					<p class="cate-name h6 link-underline"><?php echo esc_html( $term->name ); ?></p>
					<?php if ( $show_count ) : ?>
						<p class="cate-count text-caption-01"><?php echo esc_html( $count_text ); ?></p>
					<?php endif; ?>
				</a>
			</div>
			<?php
		}

		return ob_get_clean();
	}

	/**
	 * Style 3's ground truth (`home-02.html`'s "Brand" section, `.wg-brand`)
	 * is plain typed content — name, link and image are entered per item.
	 * Items saved before that change (legacy `product_brand` term id, no
	 * name/image typed) still resolve to the real WooCommerce brand so
	 * already-built pages don't go blank.
	 */
	private function render_brand_item( $item ) {
		$name      = ! empty( $item['brand_name'] ) ? $item['brand_name'] : '';
		$url       = ! empty( $item['brand_link']['url'] ) ? $item['brand_link']['url'] : '';
		$image_url = ! empty( $item['custom_image']['id'] ) ? wp_get_attachment_image_url( $item['custom_image']['id'], 'medium' ) : '';

		if ( ! empty( $item['product_brand'] ) ) {
			$term = get_term( absint( $item['product_brand'] ), 'product_brand' );

			if ( $term && ! is_wp_error( $term ) ) {
				$name = '' !== $name ? $name : $term->name;
				$url  = '' !== $url ? $url : (string) get_term_link( $term, 'product_brand' );

				if ( empty( $image_url ) ) {
					$thumbnail_id = get_term_meta( $term->term_id, 'thumbnail_id', true );
					$image_url    = $thumbnail_id ? wp_get_attachment_image_url( $thumbnail_id, 'thumbnail' ) : '';
				}
			}
		}

		if ( empty( $image_url ) && ! empty( $item['custom_image']['url'] ) ) {
			$image_url = $item['custom_image']['url'];
		}

		if ( '' === $name && empty( $image_url ) ) {
			return '';
		}

		if ( empty( $image_url ) ) {
			$image_url = \Elementor\Utils::get_placeholder_image_src();
		}

		ob_start();
		?>
		<a href="<?php echo esc_url( '' !== $url ? $url : '#' ); ?>" class="wg-brand"<?php echo ! empty( $item['brand_link']['is_external'] ) ? ' target="_blank" rel="noopener"' : ''; ?>>
			<div class="brand-image">
				<img loading="lazy" width="80" height="80" src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $name ); ?>">
			</div>
			<?php if ( '' !== $name ) : ?>
				<div class="brand-name font-manrope h6 lh-28 link-underline"><?php echo esc_html( $name ); ?></div>
			<?php endif; ?>
		</a>
		<?php
		return ob_get_clean();
	}

	private function render_heading( $settings, $style = 'style_1' ) {
		if ( 'style_4' === $style ) {
			$has_link = ! empty( $settings['view_all_link']['url'] );

			if ( empty( $settings['title'] ) && ! $has_link ) {
				return;
			}
			?>
			<div class="sect-heading type-2">
				<?php if ( ! empty( $settings['title'] ) ) : ?>
					<h4 class="s-title"><?php echo esc_html( $settings['title'] ); ?></h4>
				<?php endif; ?>
				<?php if ( $has_link ) :
					$this->add_link_attributes( 'view_all_link', $settings['view_all_link'] );
					?>
					<a <?php $this->print_render_attribute_string( 'view_all_link' ); ?> class="tf-btn-line">
						<span class="text-uppercase fw-semibold"><?php echo esc_html( $settings['view_all_text'] ); ?></span>
					</a>
				<?php endif; ?>
			</div>
			<?php
			return;
		}

		if ( empty( $settings['title'] ) && empty( $settings['description'] ) ) {
			return;
		}
		?>
		<div class="sect-heading text-center">
			<?php if ( ! empty( $settings['title'] ) ) : ?>
				<p class="s-title h4"><?php echo esc_html( $settings['title'] ); ?></p>
			<?php endif; ?>
			<?php if ( ! empty( $settings['description'] ) ) : ?>
				<p class="text-body-02"><?php echo esc_html( $settings['description'] ); ?></p>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Style 3 (`.wg-brand` grid) has no swiper at all — ground truth uses the
	 * theme's generic `.tf-grid-layout` responsive-column utility instead,
	 * so there's no carousel init and no data-view/pagination to wire up.
	 */
	private function render_brand_grid( $settings ): void {
		$items = ! empty( $settings['brand_items'] ) && is_array( $settings['brand_items'] ) ? $settings['brand_items'] : [];

		$cards_html = '';
		foreach ( $items as $item ) {
			$cards_html .= $this->render_brand_item( $item );
		}

		if ( empty( $cards_html ) ) {
			return;
		}
		?>
			<div class="container">
				<?php $this->render_heading( $settings ); ?>
				<div class="tf-grid-layout gap-20 tf-col-2 sm-col-3 md-col-4 xl-col-6 px-1">
					<?php echo $cards_html; ?>
				</div>
			</div>
		<?php
	}

	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$style    = ! empty( $settings['style'] ) ? $settings['style'] : 'style_1';

		if ( 'style_3' === $style ) {
			$this->render_brand_grid( $settings );
			return;
		}

		$show_count = 'yes' === $settings['show_count'];
		$items      = ! empty( $settings['category_items'] ) && is_array( $settings['category_items'] ) ? $settings['category_items'] : [];

		$slides_html = '';
		foreach ( $items as $item ) {
			$slides_html .= $this->render_category_item( $item, $style, $show_count, isset( $settings['card_button_text'] ) ? (string) $settings['card_button_text'] : '' );
		}

		if ( empty( $slides_html ) ) {
			return;
		}

		/**
		 * Style 2's ground truth (`home-02.html`) uses `data-space-xl="20"
		 * data-space="15"`, Style 4's (`home-04.html`) uses `data-space-xl="28.33333"
		 * data-space="15"` — but `slider-core.js` (see comment below) has no
		 * `data-space-xl` breakpoint at all, so that value would silently be
		 * ignored same as `data-view-xl` was. Translated to the breakpoints
		 * this JS actually supports: 15 up through the tablet tier, then the
		 * xl value from the "laptop"/1200px tier up.
		 */
		if ( 'style_2' === $style || 'style_5' === $style ) {
			// Style 5 (`home-03.html`) uses the same `data-space-xl="20" data-space="15"` as Style 2.
			$space_attrs   = $this->get_swiper_space_attrs( $settings, [ 15, 15, 15, 15, 20 ] );
			$wrapper_class = 'themesFlat';
		} elseif ( 'style_4' === $style ) {
			$space_attrs   = $this->get_swiper_space_attrs( $settings, [ 15, 15, 15, 15, 28.33333 ] );
			$wrapper_class = ' bg-surface';
		} else {
			$space_attrs   = $this->get_swiper_space_attrs( $settings, [ 0, 0, 0, 0, 0 ] );
			$wrapper_class = '';
		}
		?>
			<div class="<?php echo esc_attr( $wrapper_class ); ?>">
				<div class="container">
					<?php
					// Style 5's ground truth has no heading at all.
					if ( 'style_5' !== $style ) {
						$this->render_heading( $settings, $style );
					}
					?>
					<?php
						/**
						 * The `.tfc-swiper` class routes this element to
						 * `slider-core.js`'s `initSwiperInScope()`, NOT the static
						 * export's `carousel.js` — that JS reads `data-mobile` /
						 * `data-mobile-sm` / `data-tablet` / `data-preview` /
						 * `data-laptop` (breakpoints 0/575/768/1200/1440px), never
						 * `data-view-xl/-lg/-md/-sm` (the ground-truth HTML's own
						 * attribute names, which this JS ignores entirely).
						 */
					?>
					<div dir="ltr" class="swiper tf-swiper tfc-swiper"
						data-laptop="<?php echo esc_attr( $settings['items_desktop'] ); ?>"
						data-preview="<?php echo esc_attr( $settings['items_laptop'] ); ?>"
						data-tablet="<?php echo esc_attr( $settings['items_tablet'] ); ?>"
						data-mobile-sm="<?php echo esc_attr( $settings['items_mobile'] ); ?>"
						data-mobile="<?php echo esc_attr( $settings['items_mobile_sm'] ); ?>"
						<?php echo $this->get_swiper_group_attrs( $settings ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built with esc_attr() in the trait ?>
						<?php echo $space_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built with esc_attr() in the trait ?>>
						<div class="swiper-wrapper">
							<?php echo $slides_html; ?>
						</div>
						<?php /* Style 5: ground truth (`home-03.html`) has this bullet pagination commented out; enabled here (same as Style 1) on request. */ ?>
						<?php if ( 'style_4' === $style ) : ?>
							<div class="sw-line-default tf-sw-pagination"></div>
						<?php else : ?>
							<div class="sw-dot-default tf-sw-pagination<?php echo ( 'style_2' === $style ) ? ' d-xl-none' : ''; ?>"></div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		<?php
	}
}
