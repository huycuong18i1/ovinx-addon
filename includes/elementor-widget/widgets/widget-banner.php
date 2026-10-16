<?php

class ovinx_Banner extends \Elementor\Widget_Base {

	/**
	 * Same "bounded, non-AJAX SELECT2" convention already used by
	 * `widget-banner-feature.php`/`widget-category.php` — see the comment
	 * there on why `tfwc_select2`'s AJAX handler isn't used (never built).
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

	public function get_name() {
		return 'ovinx_banner';
	}

	public function get_title() {
		return esc_html__( 'Ovinx Banner', 'ovinx-addon' );
	}

	public function get_icon() {
		return 'eicon-banner';
	}

	public function get_categories() {
		return [ 'ovinx_addons_core' ];
	}

	public function get_keywords() {
		return [ 'banner', 'cta', 'image' ];
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
			'style',
			[
				'label'   => esc_html__( 'Style', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'style_1' => esc_html__( 'Style 1 — Default', 'ovinx-addon' ),
					'style_2' => esc_html__( 'Style 2 — With Price', 'ovinx-addon' ),
				],
				'default' => 'style_1',
			]
		);

		$this->add_control(
			'image',
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
				'name'      => 'image_size',
				'default'   => 'full',
				'separator' => 'none',
			]
		);

		$this->add_control(
			'mini_title',
			[
				'label'       => esc_html__( 'Mini Title', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'POWER, AND CONTROL', 'ovinx-addon' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'title',
			[
				'label'       => esc_html__( 'Title', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'Trusted Parts You Can Rely On', 'ovinx-addon' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'button_text',
			[
				'label'       => esc_html__( 'Button Text', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'SHOP NOW', 'ovinx-addon' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'button_link',
			[
				'label'       => esc_html__( 'Button Link', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::URL,
				'label_block' => true,
				'default'     => [
					'url' => '',
				],
			]
		);

		/**
		 * Ground truth's Style 2 (`index.html`, second `.banner-v01` instance)
		 * shows a price pair ("$15.99" / "$21.99") — always real WooCommerce
		 * sale/regular price via a linked product (same "never type a price
		 * by hand" rule this whole plugin follows), not admin-typed text.
		 */
		$this->add_control(
			'featured_product_id',
			[
				'label'       => esc_html__( 'Product (for Price)', 'ovinx-addon' ),
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
		 * Content Style
		 */
		$this->start_controls_section(
			'section_style_content',
			[
				'label' => esc_html__( 'Content', 'ovinx-addon' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'mini_title_color',
			[
				'label'     => esc_html__( 'Mini Title Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .banner-v01 .mini-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__( 'Title Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .banner-v01 .title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .banner-v01 .title',
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
					'{{WRAPPER}} .banner-v01 .tf-btn' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .banner-v01 .tf-btn' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Same convention as `widget-slideshow.php`'s `get_repeater_image_html()`
	 * (adapted for a single, non-repeater control) — real width/height from
	 * the actual uploaded attachment instead of ground truth's static
	 * `width="1290" height="320"`.
	 */
	private function get_image_html( $settings, $image_key, $size_key ) {
		if ( empty( $settings[ $image_key ]['url'] ) ) {
			return '';
		}

		if ( ! empty( $settings[ $image_key ]['id'] ) ) {
			return \Elementor\Group_Control_Image_Size::get_attachment_image_html( $settings, $size_key, $image_key );
		}

		return sprintf(
			'<img loading="lazy" src="%s" alt="%s">',
			esc_url( $settings[ $image_key ]['url'] ),
			esc_attr__( 'Image', 'ovinx-addon' )
		);
	}

	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$style    = ! empty( $settings['style'] ) ? $settings['style'] : 'style_1';
		$is_style_2 = 'style_2' === $style;

		if ( empty( $settings['image']['url'] ) && empty( $settings['title'] ) ) {
			return;
		}

		if ( empty( $settings['button_link']['url'] ) ) {
			$settings['button_link']['url'] = '#';
		}
		$this->add_link_attributes( 'button_link', $settings['button_link'] );

		$wrapper_class     = $is_style_2 ? 'themesFlat' : '';
		$mini_title_class  = 'mini-title text-button-small mb-8 ' . ( $is_style_2 ? 'text-primary' : 'text-white' );
		$title_class       = 'title ' . ( $is_style_2 ? 'mb-16' : 'text-white mb-36' );
		$button_class      = 'tf-btn px-v1' . ( $is_style_2 ? '' : ' btn-white-2' );
		?>
			<div class="<?php echo esc_attr( $wrapper_class ); ?>">
			<div class="container">
				<div class="banner-v01 hover-img">
					<div class="bn-image img-style">
						<?php echo $this->get_image_html( $settings, 'image', 'image_size' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from esc_url()/esc_attr() above or Elementor's own escaped image markup ?>
					</div>
					<div class="bn-content">
						<?php if ( ! empty( $settings['mini_title'] ) ) : ?>
							<p class="<?php echo esc_attr( $mini_title_class ); ?>"><?php echo esc_html( $settings['mini_title'] ); ?></p>
						<?php endif; ?>
						<?php if ( ! empty( $settings['title'] ) ) : ?>
							<h3 class="<?php echo esc_attr( $title_class ); ?>"><?php echo wp_kses_post( $settings['title'] ); ?></h3>
						<?php endif; ?>
						<?php
						if ( $is_style_2 && ! empty( $settings['featured_product_id'] ) ) {
							global $product;
							$product = wc_get_product( absint( $settings['featured_product_id'] ) );

							if ( $product ) {
								/**
								 * `tfwc_price()` already prints its own
								 * `<div class="price-wrap">` (the theme's
								 * shared, real-price `.price-new`/`.price-old`
								 * template) — ground truth's Style 2 just adds
								 * `mb-36 gap-12` spacing modifiers on top of
								 * it, so those are added onto the SAME div
								 * here instead of nesting a duplicate wrapper.
								 */
								ob_start();
								tfwc_price();
								$price_html = ob_get_clean();
								echo str_replace( 'class="price-wrap"', 'class="price-wrap mb-36 gap-12"', $price_html ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tfwc_price() output is already-escaped theme template markup
							}
						}
						?>
						<?php if ( ! empty( $settings['button_text'] ) ) : ?>
							<a <?php $this->print_render_attribute_string( 'button_link' ); ?> class="<?php echo esc_attr( $button_class ); ?>"><?php echo esc_html( $settings['button_text'] ); ?></a>
						<?php endif; ?>
					</div>
				</div>
			</div>
			</div>
		<?php
	}
}
