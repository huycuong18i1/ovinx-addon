<?php

class ovinx_Two_Image_Banner extends \Elementor\Widget_Base {

	public function get_name() {
		return 'ovinx_two_image_banner';
	}

	public function get_title() {
		return esc_html__( 'Ovinx Two Image Banner', 'ovinx-addon' );
	}

	public function get_icon() {
		return 'eicon-image-before-after';
	}

	public function get_categories() {
		return [ 'ovinx_addons_core' ];
	}

	public function get_keywords() {
		return [ 'banner', 'image', 'split' ];
	}

	protected function register_controls() {

		/**
		 * Images
		 */
		$this->start_controls_section(
			'section_images',
			[
				'label' => esc_html__( 'Images', 'ovinx-addon' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'image_left',
			[
				'label'   => esc_html__( 'Left Image', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Image_Size::get_type(),
			[
				'name'      => 'image_left_size',
				'default'   => 'full',
				'separator' => 'none',
			]
		);

		$this->add_control(
			'image_right',
			[
				'label'   => esc_html__( 'Right Image', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Image_Size::get_type(),
			[
				'name'      => 'image_right_size',
				'default'   => 'full',
				'separator' => 'none',
			]
		);

		$this->end_controls_section();

		/**
		 * Content
		 */
		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__( 'Content', 'ovinx-addon' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'title',
			[
				'label'       => esc_html__( 'Title', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'Trusted Car Parts You Can Rely On Today', 'ovinx-addon' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'desc',
			[
				'label'   => esc_html__( 'Description', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => esc_html__( 'Upgrade your driving experience with our top-quality auto parts selection.', 'ovinx-addon' ),
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
			'panel_bg_color',
			[
				'label'     => esc_html__( 'Panel Background Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .banner-v04 .bn-content' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__( 'Title Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .banner-v04 .title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .banner-v04 .title',
			]
		);

		$this->add_control(
			'desc_color',
			[
				'label'     => esc_html__( 'Description Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .banner-v04 .desc' => 'color: {{VALUE}};',
				],
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
					'{{WRAPPER}} .banner-v04 .tf-btn' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .banner-v04 .tf-btn' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

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

	/**
	 * Ground truth (`home-04.html`) places `.banner-v04` directly on the
	 * page with no `flat-spacing-4`/`themesFlat`/`.container` wrapper at
	 * all — it's a bare, full-bleed 3-column row (image/content/image), so
	 * this widget doesn't add one either.
	 */
	protected function render(): void {
		$settings = $this->get_settings_for_display();

		if ( empty( $settings['image_left']['url'] ) && empty( $settings['image_right']['url'] ) && empty( $settings['title'] ) ) {
			return;
		}

		if ( empty( $settings['button_link']['url'] ) ) {
			$settings['button_link']['url'] = '#';
		}
		$this->add_link_attributes( 'button_link', $settings['button_link'] );
		?>
		<div class="banner-v04">
			<div class="bn-image">
				<?php echo $this->get_image_html( $settings, 'image_left', 'image_left_size' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from esc_url()/esc_attr() above or Elementor's own escaped image markup ?>
			</div>
			<div class="bn-content">
				<?php if ( ! empty( $settings['title'] ) ) : ?>
					<h3 class="title text-white"><?php echo wp_kses_post( $settings['title'] ); ?></h3>
				<?php endif; ?>
				<?php if ( ! empty( $settings['desc'] ) ) : ?>
					<p class="desc text-body-02 text-white"><?php echo esc_html( $settings['desc'] ); ?></p>
				<?php endif; ?>
				<?php if ( ! empty( $settings['button_text'] ) ) : ?>
					<a <?php $this->print_render_attribute_string( 'button_link' ); ?> class="tf-btn btn-white-3 px-v1"><?php echo esc_html( $settings['button_text'] ); ?></a>
				<?php endif; ?>
			</div>
			<div class="bn-image">
				<?php echo $this->get_image_html( $settings, 'image_right', 'image_right_size' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from esc_url()/esc_attr() above or Elementor's own escaped image markup ?>
			</div>
		</div>
		<?php
	}
}
