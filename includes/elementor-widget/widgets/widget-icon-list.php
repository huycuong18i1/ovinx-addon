<?php

/**
 * Ground truth (`themes/ovinx-html/home-04.html` ~line 1479, `<!-- Box Icon -->`)
 * is a plain swiper of admin-typed icon/title/description boxes (`.box-icon_V01`)
 * — no WooCommerce data, so unlike the product widgets in this plugin every
 * field here is a normal Elementor control. Icon picker uses the theme's own
 * `ovinx_icon` library (same convention as `widget-category.php`'s Style 1),
 * items use a `Repeater` (flexible count, not fixed to ground truth's 4) same
 * as `widget-collection-banner.php`/`widget-product-tabs.php`.
 *
 * Ground truth's own `data-view-xl/-lg/-sm` breakpoint names are that unused
 * static-export attribute set again (see `widget-category.php`'s comment on
 * this) — `slider-core.js`'s `initSwiperInScope()` only reads `data-laptop`
 * (1440)/`data-preview` (1200)/`data-tablet` (768)/`data-mobile-sm`
 * (575)/`data-mobile` (0), so item counts below are translated to that real
 * 5-tier set rather than copying the ground truth attribute names.
 */
class ovinx_Icon_List extends \Elementor\Widget_Base {

	use Ovinx_Swiper_Group_Controls;

	public function get_name() {
		return 'ovinx_icon_list';
	}

	public function get_title() {
		return esc_html__( 'Ovinx Icon List', 'ovinx-addon' );
	}

	public function get_icon() {
		return 'eicon-icon-box';
	}

	public function get_categories() {
		return [ 'ovinx_addons_core' ];
	}

	public function get_keywords() {
		return [ 'icon', 'box', 'list', 'carousel', 'feature' ];
	}

	public function get_script_depends() {
		return [ 'slider-core' ];
	}

	public function get_style_depends() {
		return [ 'themesflat-swiper' ];
	}

	protected function register_controls() {

		/**
		 * Items
		 */
		$this->start_controls_section(
			'section_items',
			[
				'label' => esc_html__( 'Items', 'ovinx-addon' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'icon',
			[
				'label'   => esc_html__( 'Icon', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::ICONS,
				'default' => [
					'value'   => 'icon-KeyReturn',
					'library' => 'ovinx_icon',
				],
			]
		);

		$repeater->add_control(
			'title',
			[
				'label'       => esc_html__( 'Title', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Title', 'ovinx-addon' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'description',
			[
				'label'       => esc_html__( 'Description', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'Description', 'ovinx-addon' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'icon_items',
			[
				'label'       => esc_html__( 'Icon Items', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ title }}}',
				'default'     => [
					[
						'icon'        => [ 'value' => 'icon-KeyReturn', 'library' => 'ovinx_icon' ],
						'title'       => esc_html__( '14-Day Returns', 'ovinx-addon' ),
						'description' => esc_html__( 'Easy returns within 14 days.', 'ovinx-addon' ),
					],
					[
						'icon'        => [ 'value' => 'icon-Package2', 'library' => 'ovinx_icon' ],
						'title'       => esc_html__( 'Free Shipping', 'ovinx-addon' ),
						'description' => esc_html__( 'Your order always ships for free.', 'ovinx-addon' ),
					],
					[
						'icon'        => [ 'value' => 'icon-Lifebuoy', 'library' => 'ovinx_icon' ],
						'title'       => esc_html__( '24/7 Support', 'ovinx-addon' ),
						'description' => esc_html__( 'Always here to help you anytime.', 'ovinx-addon' ),
					],
					[
						'icon'        => [ 'value' => 'icon-SealPercent2', 'library' => 'ovinx_icon' ],
						'title'       => esc_html__( 'Member Discounts', 'ovinx-addon' ),
						'description' => esc_html__( 'Exclusive savings for members.', 'ovinx-addon' ),
					],
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
				'default' => 4,
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

		$this->register_swiper_group_controls( [ 15, 15, 20, 30, 30 ] );

		/**
		 * Style — Icon
		 */
		$this->start_controls_section(
			'section_style_icon',
			[
				'label' => esc_html__( 'Icon', 'ovinx-addon' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'icon_bg_color',
			[
				'label'     => esc_html__( 'Circle Background Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .box-icon_V01 .ic-wrap' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label'     => esc_html__( 'Icon Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .box-icon_V01 .ic-wrap' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Style — Content
		 */
		$this->start_controls_section(
			'section_style_content',
			[
				'label' => esc_html__( 'Content', 'ovinx-addon' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__( 'Title Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .box-icon_V01 .title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .box-icon_V01 .title',
			]
		);

		$this->add_control(
			'desc_color',
			[
				'label'     => esc_html__( 'Description Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .box-icon_V01 .desc' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'desc_typography',
				'selector' => '{{WRAPPER}} .box-icon_V01 .desc',
			]
		);

		$this->end_controls_section();

		/**
		 * Style — Pagination
		 */
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
				'label'     => esc_html__( 'Line Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .sw-line-default .swiper-pagination-bullet' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'pagination_color_active',
			[
				'label'     => esc_html__( 'Active Line Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .sw-line-default .swiper-pagination-bullet-active' => 'background: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	private function render_item( array $item ): string {
		if ( empty( $item['icon']['value'] ) && empty( $item['title'] ) ) {
			return '';
		}

		ob_start();
		?>
		<div class="swiper-slide">
			<div class="box-icon_V01">
				<?php if ( ! empty( $item['icon']['value'] ) ) : ?>
					<div class="ic-wrap">
						<?php \Elementor\Icons_Manager::render_icon( $item['icon'], [ 'class' => 'icon', 'aria-hidden' => 'true' ] ); ?>
					</div>
				<?php endif; ?>
				<div class="content">
					<?php if ( ! empty( $item['title'] ) ) : ?>
						<h6 class="title"><?php echo esc_html( $item['title'] ); ?></h6>
					<?php endif; ?>
					<?php if ( ! empty( $item['description'] ) ) : ?>
						<p class="desc"><?php echo esc_html( $item['description'] ); ?></p>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$items    = ! empty( $settings['icon_items'] ) && is_array( $settings['icon_items'] ) ? $settings['icon_items'] : [];

		$slides_html = '';
		foreach ( $items as $item ) {
			$slides_html .= $this->render_item( $item );
		}

		if ( empty( $slides_html ) ) {
			return;
		}
		?>
			<div class="container">
				<div dir="ltr" class="swiper tf-swiper tfc-swiper"
					data-laptop="<?php echo esc_attr( $settings['items_desktop'] ); ?>"
					data-preview="<?php echo esc_attr( $settings['items_laptop'] ); ?>"
					data-tablet="<?php echo esc_attr( $settings['items_tablet'] ); ?>"
					data-mobile-sm="<?php echo esc_attr( $settings['items_mobile'] ); ?>"
					data-mobile="<?php echo esc_attr( $settings['items_mobile_sm'] ); ?>"
					<?php echo $this->get_swiper_group_attrs( $settings ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built with esc_attr() in the trait ?>
					<?php echo $this->get_swiper_space_attrs( $settings, [ 15, 15, 20, 30, 30 ] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built with esc_attr() in the trait ?>>
					<div class="swiper-wrapper">
						<?php echo $slides_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- own escaped markup, see render_item() ?>
					</div>
					<div class="sw-line-default tf-sw-pagination"></div>
				</div>
			</div>
		<?php
	}
}
