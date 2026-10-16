<?php

class ovinx_Slideshow extends \Elementor\Widget_Base {

	use Ovinx_Vehicle_Filter_Fields;

	public function get_name() {
		return 'ovinx_slideshow';
	}

	public function get_title() {
		return esc_html__( 'Ovinx Slideshow', 'ovinx-addon' );
	}

	public function get_icon() {
		return 'eicon-slides';
	}

	public function get_categories() {
		return [ 'ovinx_addons_core' ];
	}

	public function get_keywords() {
		return [ 'banner', 'hero', 'slider', 'slideshow' ];
	}

	public function get_style_depends() {
		return [ 'themesflat-swiper' ];
	}

	public function get_script_depends() {
		return [ 'themesflat-swiper', 'slider-core' ];
	}

	protected function register_controls() {

		/**
		 * Slider Settings
		 */
		$this->start_controls_section(
			'section_slider_settings',
			[
				'label' => esc_html__( 'Slider Settings', 'ovinx-addon' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'style',
			[
				'label'   => esc_html__( 'Style', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'style-1' => esc_html__( 'Style 1 — Full Width', 'ovinx-addon' ),
					'style-2' => esc_html__( 'Style 2 — Compact with Sidebar', 'ovinx-addon' ),
					'style-3' => esc_html__( 'Style 3 — Full Width Carousel Peek', 'ovinx-addon' ),
				],
				'default' => 'style-1',
			]
		);

		$this->add_control(
			'sidebar_type',
			[
				'label'     => esc_html__( 'Sidebar', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'options'   => [
					'none'           => esc_html__( 'None', 'ovinx-addon' ),
					'category'       => esc_html__( 'Category List', 'ovinx-addon' ),
					'vehicle_filter' => esc_html__( 'Product Search Filter', 'ovinx-addon' ),
				],
				'default'   => 'none',
				'condition' => [
					'style' => 'style-2',
				],
			]
		);

		$this->add_control(
			'category_count',
			[
				'label'     => esc_html__( 'Number of Categories', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 1,
				'max'       => 20,
				'default'   => 9,
				'condition' => [
					'style'        => 'style-2',
					'sidebar_type' => 'category',
				],
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
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'delay',
			[
				'label'     => esc_html__( 'Autoplay Delay (ms)', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 1000,
				'max'       => 10000,
				'step'      => 500,
				'default'   => 4000,
				'condition' => [
					'autoplay' => 'yes',
				],
			]
		);

		$this->add_control(
			'pause_on_hover',
			[
				'label'        => esc_html__( 'Pause on Hover', 'ovinx-addon' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'ovinx-addon' ),
				'label_off'    => esc_html__( 'No', 'ovinx-addon' ),
				'return_value' => 'yes',
				// QA UX-027: pausing on hover is the baseline for auto-moving content (WCAG 2.2.2).
				'default'      => 'yes',
				'condition'    => [
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
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'show_navigation',
			[
				'label'        => esc_html__( 'Navigation Arrows', 'ovinx-addon' ),
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
				'label'        => esc_html__( 'Pagination Dots', 'ovinx-addon' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'ovinx-addon' ),
				'label_off'    => esc_html__( 'No', 'ovinx-addon' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->end_controls_section();

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

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'image',
			[
				'label'   => esc_html__( 'Image', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
			]
		);

		$repeater->add_group_control(
			\Elementor\Group_Control_Image_Size::get_type(),
			[
				'name'      => 'image_size',
				'default'   => 'full',
				'separator' => 'none',
			]
		);

		$repeater->add_control(
			'tag',
			[
				'label'       => esc_html__( 'Tag', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'SALE! UP TO 50% OFF!', 'ovinx-addon' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'title',
			[
				'label'       => esc_html__( 'Title', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'Explore High-Performance', 'ovinx-addon' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'desc',
			[
				'label'       => esc_html__( 'Description', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'Shop new arrivals of high-performance brake system and alloy wheels', 'ovinx-addon' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'button_text',
			[
				'label'   => esc_html__( 'Button Text', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Shop Now', 'ovinx-addon' ),
			]
		);

		$repeater->add_control(
			'link',
			[
				'label'       => esc_html__( 'Button Link', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::URL,
				'placeholder' => esc_html__( 'https://your-link.com', 'ovinx-addon' ),
				'default'     => [
					'url' => '#',
				],
			]
		);

		$this->add_control(
			'slides',
			[
				'label'       => esc_html__( 'Slides', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ title }}}',
				'default'     => [
					[
						'tag'         => esc_html__( 'SALE! UP TO 50% OFF!', 'ovinx-addon' ),
						'title'       => esc_html__( 'Explore High-Performance', 'ovinx-addon' ),
						'desc'        => esc_html__( 'Shop new arrivals of high-performance brake system and alloy wheels', 'ovinx-addon' ),
						'button_text' => esc_html__( 'Shop Now', 'ovinx-addon' ),
						'link'        => [
							'url' => '#',
						],
					],
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Vehicle / Product Search Filter (left panel shown when Sidebar = Product Search Filter)
		 */
		$this->start_controls_section(
			'section_vehicle_filter',
			[
				'label'     => esc_html__( 'Product Search Filter', 'ovinx-addon' ),
				'tab'       => \Elementor\Controls_Manager::TAB_CONTENT,
				'condition' => [
					'style'        => 'style-2',
					'sidebar_type' => 'vehicle_filter',
				],
			]
		);

		$this->add_control(
			'filter_title',
			[
				'label'   => esc_html__( 'Title', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Find The Right Parts', 'ovinx-addon' ),
			]
		);

		$this->add_control(
			'filter_desc',
			[
				'label'   => esc_html__( 'Description', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => esc_html__( 'Find the right parts quickly by searching for your vehicle model and specifications.', 'ovinx-addon' ),
			]
		);

		$this->register_vehicle_cascade_controls();

		$this->add_control(
			'filter_button_text',
			[
				'label'   => esc_html__( 'Button Text', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Search Parts', 'ovinx-addon' ),
			]
		);

		$this->add_control(
			'filter_bottom_desc',
			[
				'label'   => esc_html__( 'Bottom Note', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => esc_html__( 'You can find the product you are looking for by entering the search criteria correctly.', 'ovinx-addon' ),
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
					'{{WRAPPER}} .sld__tag' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'tag_typography',
				'selector' => '{{WRAPPER}} .sld__tag',
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
					'{{WRAPPER}} .sld__title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .sld__title',
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
					'{{WRAPPER}} .sld__desc' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'desc_typography',
				'selector' => '{{WRAPPER}} .sld__desc',
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

		$this->start_controls_tabs( 'button_styles_tab' );

		$this->start_controls_tab(
			'button_default',
			[
				'label' => esc_html__( 'Default', 'ovinx-addon' ),
			]
		);

		$this->add_control(
			'button_color',
			[
				'label'     => esc_html__( 'Text Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .sld-content_inner .tf-btn' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_background',
			[
				'label'     => esc_html__( 'Background Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .sld-content_inner .tf-btn' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'button_hover',
			[
				'label' => esc_html__( 'Hover', 'ovinx-addon' ),
			]
		);

		$this->add_control(
			'button_color_hover',
			[
				'label'     => esc_html__( 'Text Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .sld-content_inner .tf-btn:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_background_hover',
			[
				'label'     => esc_html__( 'Background Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .sld-content_inner .tf-btn:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		/**
		 * Category Sidebar Style
		 */
		$this->start_controls_section(
			'section_style_category_sidebar',
			[
				'label'     => esc_html__( 'Category Sidebar', 'ovinx-addon' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'style'        => 'style-2',
					'sidebar_type' => 'category',
				],
			]
		);

		$this->add_control(
			'category_link_color',
			[
				'label'     => esc_html__( 'Link Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .box-nav-category .nav-category_link' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'category_link_color_hover',
			[
				'label'     => esc_html__( 'Link Color Hover', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .box-nav-category .nav-category_link:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Product Search Filter Style
		 */
		$this->start_controls_section(
			'section_style_vehicle_filter',
			[
				'label'     => esc_html__( 'Product Search Filter', 'ovinx-addon' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'style'        => 'style-2',
					'sidebar_type' => 'vehicle_filter',
				],
			]
		);

		$this->add_control(
			'filter_title_color',
			[
				'label'     => esc_html__( 'Title Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .banner-check-fit .s-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'filter_button_color',
			[
				'label'     => esc_html__( 'Button Text Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .btn-action_submit' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'filter_button_background',
			[
				'label'     => esc_html__( 'Button Background', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .btn-action_submit' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Navigation Style
		 */
		$this->start_controls_section(
			'section_style_navigation',
			[
				'label'     => esc_html__( 'Navigation', 'ovinx-addon' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_navigation' => 'yes',
				],
			]
		);

		$this->add_control(
			'navigation_color',
			[
				'label'     => esc_html__( 'Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tf-sw-nav'   => 'color: {{VALUE}};',
					'{{WRAPPER}} .tf-sw-nav i' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'navigation_color_hover',
			[
				'label'     => esc_html__( 'Color Hover', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tf-sw-nav:hover'   => 'color: {{VALUE}};',
					'{{WRAPPER}} .tf-sw-nav:hover i' => 'color: {{VALUE}};',
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
					'show_pagination' => 'yes',
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

		$this->end_controls_section();
	}

	/**
	 * $is_first: the first slide is the page's Largest Contentful Paint element on mobile — it must NOT be lazy-loaded and
	 * gets fetchpriority="high" so the browser requests it immediately; every other slide is lazy. Any loading/fetchpriority
	 * Elementor or WordPress put on the tag is stripped first so the attributes are never duplicated.
	 */
	private function get_repeater_image_html( $item, $image_key, $size_key, $class = '', $is_first = false ) {
		if ( empty( $item[ $image_key ]['url'] ) ) {
			return '';
		}

		if ( ! empty( $item[ $image_key ]['id'] ) ) {
			$image_html = \Elementor\Group_Control_Image_Size::get_attachment_image_html( $item, $size_key, $image_key );

			if ( ! empty( $class ) ) {
				$image_html = str_replace( '<img ', '<img class="' . esc_attr( $class ) . '" ', $image_html );
			}

			$image_html = preg_replace( '/\s(?:loading|fetchpriority)=(["\'])[a-z]+\1/i', '', $image_html );
			$image_html = preg_replace(
				'/<img\b/i',
				$is_first ? '<img fetchpriority="high" loading="eager"' : '<img loading="lazy"',
				$image_html,
				1
			);

			return $image_html;
		}

		return sprintf(
			'<img class="%s" loading="lazy" src="%s" alt="%s">',
			esc_attr( $class ),
			esc_url( $item[ $image_key ]['url'] ),
			esc_attr__( 'Image', 'ovinx-addon' )
		);
	}
	private function render_slide_content( $item, $tag_color_class, $text_white_class, $link_key, $title_tag = 'h1', $tag_secondary_class = 'text-button-small', $desc_extra_class = '' ) {
		$title_tag = in_array( $title_tag, [ 'h1', 'h2', 'h3' ], true ) ? $title_tag : 'h1';
		ob_start();
		?>
		<?php if ( ! empty( $item['tag'] ) ) : ?>
			<div class="sld__tag <?php echo esc_attr( $tag_color_class ); ?> <?php echo esc_attr( $tag_secondary_class ); ?> fade-item fade-item-1">
				<?php echo esc_html( $item['tag'] ); ?>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $item['title'] ) ) : ?>
			<<?php echo $title_tag; ?> class="sld__title<?php echo esc_attr( $text_white_class ); ?> fade-item fade-item-2">
 				<?php echo wp_kses_post( $item['title'] ); ?>
			</<?php echo $title_tag; ?>>
		<?php endif; ?>

		<?php if ( ! empty( $item['desc'] ) ) : ?>
			<p class="sld__desc<?php echo esc_attr( $text_white_class ); ?> text-body-02 fade-item fade-item-3<?php echo esc_attr( $desc_extra_class ); ?>">
				<?php echo wp_kses_post( $item['desc'] ); ?>
			</p>
		<?php endif; ?>

		<?php if ( ! empty( $item['button_text'] ) ) : ?>
			<div class="fade-item fade-item-4">
				<a <?php $this->print_render_attribute_string( $link_key ); ?> class="tf-btn px-v1">
					<span class="text-uppercase"><?php echo esc_html( $item['button_text'] ); ?></span>
				</a>
			</div>
		<?php endif; ?>
		<?php
		return ob_get_clean();
	}

	private function render_category_sidebar( $settings ) {
		$count = ! empty( $settings['category_count'] ) ? absint( $settings['category_count'] ) : 9;

		$terms = get_terms(
			[
				'taxonomy'   => 'product_cat',
				'hide_empty' => true,
				'number'     => $count,
				'orderby'    => 'count',
				'order'      => 'DESC',
				'exclude'    => [ absint( get_option( 'default_product_cat', 0 ) ) ],
			]
		);

		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			return '';
		}

		ob_start();
		?>
		<ul class="box-nav-category">
			<?php foreach ( $terms as $term ) : ?>
				<li>
					<a href="<?php echo esc_url( get_term_link( $term ) ); ?>" class="nav-category_link">
						<span class="text"><?php echo esc_html( $term->name ); ?></span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php
		return ob_get_clean();
	}

	private function render_vehicle_filter( $settings ) {
		$levels = ! empty( $settings['levels'] ) && is_array( $settings['levels'] ) ? $settings['levels'] : [];
		$field  = $this->get_cascade_field( $settings );

		ob_start();
		?>
		<div class="banner-check-fit style-3">
			<div class="content-wrap">
				<?php if ( ! empty( $settings['filter_title'] ) || ! empty( $settings['filter_desc'] ) ) : ?>
					<div class="sect-heading text-center">
						<?php if ( ! empty( $settings['filter_title'] ) ) : ?>
							<h4 class="s-title"><?php echo esc_html( $settings['filter_title'] ); ?></h4>
						<?php endif; ?>
						<?php if ( ! empty( $settings['filter_desc'] ) ) : ?>
							<p><?php echo esc_html( $settings['filter_desc'] ); ?></p>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<div class="content filter-vehicle is-cascade">
					<?php echo $this->render_vehicle_cascade_fields( $field, $levels ); ?>
					<?php echo $this->render_vehicle_filter_button( $settings['filter_button_text'], true ); ?>
				</div>

				<?php if ( ! empty( $settings['filter_bottom_desc'] ) ) : ?>
					<p class="desc text-caption-01 text-center"><?php echo esc_html( $settings['filter_bottom_desc'] ); ?></p>
				<?php endif; ?>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	protected function render(): void {
		$settings = $this->get_settings_for_display();

		if ( empty( $settings['slides'] ) || ! is_array( $settings['slides'] ) ) {
			return;
		}

		$style        = ! empty( $settings['style'] ) ? $settings['style'] : 'style-1';
		$sidebar_type = ( 'style-2' === $style && ! empty( $settings['sidebar_type'] ) ) ? $settings['sidebar_type'] : 'none';
		$show_sidebar = ( 'none' !== $sidebar_type );

		$auto_play   = ! empty( $settings['autoplay'] ) ? 'true' : 'false';
		$loop        = ! empty( $settings['loop'] ) ? 'true' : 'false';
		$delay       = ! empty( $settings['delay'] ) ? absint( $settings['delay'] ) : 4000;
		$pause_hover = ! empty( $settings['pause_on_hover'] ) ? 'true' : 'false';

		$is_peek_carousel = ( 'style-3' === $style );

		$slide_extra_class = ( in_array( $style, [ 'style-2', 'style-3' ], true ) ) ? ' style-2' : '';
		if ( 'vehicle_filter' === $sidebar_type ) {
			$slide_extra_class .= ' px-s2-v2';
		}
		$tag_color_class     = ( 'style-2' === $style ) ? 'text-primary' : 'text-white';
		$tag_secondary_class = $is_peek_carousel ? 'fw-semibold' : 'text-button-small';
		$text_white_class    = ( 'style-2' === $style ) ? '' : ' text-white';
		$desc_extra_class    = $is_peek_carousel ? ' mb-xl-32' : '';
		$title_tag           = ( 'vehicle_filter' === $sidebar_type ) ? 'h2' : 'h1';
		$use_flat_wrapper    = $show_sidebar || $is_peek_carousel;
		$nav_pst_class       = $show_sidebar ? 'pst-3' : 'pst-1';
		$nav_style_class     = $show_sidebar ? ' style-3' : '';
		$pagination_extra    = ( 'style-2' === $style ) ? '' : ' style-white';
		// Text is white on every style except style-2 (dark text) — only those slides get the mobile contrast gradient.
		$slide_extra_class  .= ( 'style-2' === $style ) ? '' : ' has-text-overlay';

		ob_start();
		?>
		<?php if ( $is_peek_carousel ) : ?><div class="overflow-hidden"><?php endif; ?>
		<div class="tf-slideshow tf-btn-swiper-main">
			<div dir="ltr" class="swiper tf-swiper tfc-swiper sw-slide-show slider_effect_fade"
				data-loop="<?php echo esc_attr( $loop ); ?>"
				data-auto="<?php echo esc_attr( $auto_play ); ?>"
				data-delay="<?php echo esc_attr( $delay ); ?>"
				data-pause-hover="<?php echo esc_attr( $pause_hover ); ?>"
				data-effect=""
				<?php if ( $is_peek_carousel ) : ?>
					data-preview="1.3"
					data-view-xl="1.48091"
					data-laptop="1.48091"
					data-space="10"
					data-space-xl="20"
					data-center="true"
					data-init="1"
				<?php endif; ?>
				>
				<div class="swiper-wrapper">
					<?php foreach ( $settings['slides'] as $index => $item ) :
						$link_key = 'slide_link_' . $index;
						if ( empty( $item['link']['url'] ) ) {
							$item['link']['url'] = '#';
						}
						$this->add_link_attributes( $link_key, $item['link'] );
						?>
						<div class="swiper-slide">
							<div class="slideshow-v01<?php echo esc_attr( $slide_extra_class ); ?>">
								<div class="sld-image overflow-hidden">
									<?php echo $this->get_repeater_image_html( $item, 'image', 'image_size', 'scale-item scale-item-1', 0 === $index ); ?>
								</div>
								<div class="sld-content">
									<?php if ( $use_flat_wrapper ) : ?>
										<div class="sld-content_inner">
											<?php echo $this->render_slide_content( $item, $tag_color_class, $text_white_class, $link_key, $title_tag, $tag_secondary_class, $desc_extra_class ); ?>
										</div>
									<?php else : ?>
										<div class="container">
											<div class="row">
												<div class="col-11 col-sm-9">
													<div class="sld-content_inner">
														<?php echo $this->render_slide_content( $item, $tag_color_class, $text_white_class, $link_key, $title_tag, $tag_secondary_class, $desc_extra_class ); ?>
													</div>
												</div>
											</div>
										</div>
									<?php endif; ?>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>

			<?php if ( 'yes' === $settings['show_navigation'] ) : ?>
				<?php if ( $show_sidebar ) : ?>
					<div class="group-nav-action <?php echo esc_attr( $nav_pst_class ); ?>">
						<div class="d-flex align-items-center justify-content-between">
							<div class="tf-sw-nav<?php echo esc_attr( $nav_style_class ); ?> nav-prev-swiper">
								<i class="icon icon-CaretLeft"></i>
							</div>
							<div class="tf-sw-nav<?php echo esc_attr( $nav_style_class ); ?> nav-next-swiper">
								<i class="icon icon-CaretRight"></i>
							</div>
						</div>
					</div>
				<?php else : ?>
					<div class="group-nav-action <?php echo esc_attr( $nav_pst_class ); ?>">
						<div class="container-full">
							<div class="d-flex align-items-center justify-content-between">
								<div class="tf-sw-nav<?php echo esc_attr( $nav_style_class ); ?> nav-prev-swiper">
									<i class="icon icon-CaretLeft"></i>
								</div>
								<div class="tf-sw-nav<?php echo esc_attr( $nav_style_class ); ?> nav-next-swiper">
									<i class="icon icon-CaretRight"></i>
								</div>
							</div>
						</div>
					</div>
				<?php endif; ?>
			<?php endif; ?>

			<?php if ( 'yes' === $settings['show_pagination'] ) : ?>
				<div class="box-pag">
					<div class="sw-dot-default<?php echo esc_attr( $pagination_extra ); ?> tf-sw-pagination"></div>
				</div>
			<?php endif; ?>
		</div>
		<?php if ( $is_peek_carousel ) : ?></div><?php endif; ?>
		<?php
		$slideshow_html = ob_get_clean();

		if ( 'category' === $sidebar_type ) :
			?>
			<div class="section-banner-slideshow">
				<div class="container">
					<div class="row">
						<div class="col-lg-3 lg-d-none">
							<?php echo $this->render_category_sidebar( $settings ); ?>
						</div>
						<div class="col-lg-9">
							<?php echo $slideshow_html; ?>
						</div>
					</div>
				</div>
			</div>
			<?php
		elseif ( 'vehicle_filter' === $sidebar_type ) :
			?>
			<div class="sc-slider">
				<div class="container">
					<div class="row flex-wrap-reverse gy-30">
						<div class="col-lg-4">
							<?php echo $this->render_vehicle_filter( $settings ); ?>
						</div>
						<div class="col-lg-8">
							<?php echo $slideshow_html; ?>
						</div>
					</div>
				</div>
			</div>
			<?php
		else :
			echo $slideshow_html;
		endif;
	}
}


