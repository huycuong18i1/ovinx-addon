<?php

/**
 * Ground truth's `.form-support` (`index.html`/`home-04.html`) is a
 * hand-built contact form (name/email/subject/message fields), but the user
 * explicitly wants to paste their own Contact Form 7 shortcode into that
 * slot instead — this widget renders a `[contact-form-7 ...]`-style
 * shortcode via `do_shortcode()` rather than building form fields, since
 * Contact Form 7 (confirmed installed — `plugins/contact-form-7`) already
 * owns the real field markup/validation/submission handling.
 */
class ovinx_Contact_Support extends \Elementor\Widget_Base {

	public function get_name() {
		return 'ovinx_contact_support';
	}

	public function get_title() {
		return esc_html__( 'Ovinx Contact Support', 'ovinx-addon' );
	}

	public function get_icon() {
		return 'eicon-form-horizontal';
	}

	public function get_categories() {
		return [ 'ovinx_addons_core' ];
	}

	public function get_keywords() {
		return [ 'contact', 'support', 'form' ];
	}

	protected function register_controls() {

		/**
		 * Media
		 */
		$this->start_controls_section(
			'section_media',
			[
				'label' => esc_html__( 'Media', 'ovinx-addon' ),
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
					'style_2' => esc_html__( 'Style 2 — Boxed', 'ovinx-addon' ),
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
			'media_title',
			[
				'label'       => esc_html__( 'Media Title', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Need Help With Auto Parts?', 'ovinx-addon' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'media_desc',
			[
				'label'   => esc_html__( 'Media Description', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => esc_html__( 'Fast, reliable support from real experts.', 'ovinx-addon' ),
			]
		);

		$this->add_control(
			'media_button_text',
			[
				'label'       => esc_html__( 'Button Text', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'CONTACT US', 'ovinx-addon' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'media_button_link',
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
			'mini_title',
			[
				'label'       => esc_html__( 'Mini Title', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'CONTACT SUPPORT', 'ovinx-addon' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'title',
			[
				'label'       => esc_html__( 'Title', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Need The Right Part?', 'ovinx-addon' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'desc',
			[
				'label'   => esc_html__( 'Description', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => esc_html__( 'We help you choose quickly and accurately.', 'ovinx-addon' ),
			]
		);

		$this->add_control(
			'shortcode',
			[
				'label'       => esc_html__( 'Form Shortcode', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'placeholder' => '[contact-form-7 id="..." title="..."]',
				'description' => esc_html__( 'Paste your Contact Form 7 shortcode here.', 'ovinx-addon' ),
			]
		);

		$this->end_controls_section();

		/**
		 * Media Style
		 */
		$this->start_controls_section(
			'section_style_media',
			[
				'label' => esc_html__( 'Media', 'ovinx-addon' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'media_title_color',
			[
				'label'     => esc_html__( 'Title Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .section-support .media-contact h3, {{WRAPPER}} .section-support .media-contact h5' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'media_desc_color',
			[
				'label'     => esc_html__( 'Description Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .section-support .ct-desc' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .section-support .sect-heading .mini-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__( 'Title Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .section-support .sect-heading .s-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'desc_color',
			[
				'label'     => esc_html__( 'Description Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .section-support .sect-heading .text-body-02' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .section-support .media-contact .tf-btn' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .section-support .media-contact .tf-btn' => 'background-color: {{VALUE}};',
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

	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$style    = ! empty( $settings['style'] ) ? $settings['style'] : 'style_1';
		$is_style_2 = 'style_2' === $style;

		if ( empty( $settings['image']['url'] ) && empty( $settings['title'] ) && empty( $settings['shortcode'] ) ) {
			return;
		}

		if ( empty( $settings['media_button_link']['url'] ) ) {
			$settings['media_button_link']['url'] = '#';
		}
		$this->add_link_attributes( 'media_button_link', $settings['media_button_link'] );

		ob_start();
		?>
		<div class="section-support<?php echo $is_style_2 ? ' style-2' : ''; ?> section-split">
			<div class="col-media">
				<div class="media-image overflow-hidden">
					<?php echo $this->get_image_html( $settings, 'image', 'image_size' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from esc_url()/esc_attr() above or Elementor's own escaped image markup ?>
				</div>
				<div class="media-contact">
					<?php if ( $is_style_2 ) : ?>
						<div class="flex-sm-1">
							<?php if ( ! empty( $settings['media_title'] ) ) : ?>
								<h5 class="text-white mb-4"><?php echo esc_html( $settings['media_title'] ); ?></h5>
							<?php endif; ?>
							<?php if ( ! empty( $settings['media_desc'] ) ) : ?>
								<p class="ct-desc text-body-02 cl-text-mute"><?php echo esc_html( $settings['media_desc'] ); ?></p>
							<?php endif; ?>
						</div>
						<?php if ( ! empty( $settings['media_button_text'] ) ) : ?>
							<a <?php $this->print_render_attribute_string( 'media_button_link' ); ?> class="tf-btn btn-white-3 px-v1"><?php echo esc_html( $settings['media_button_text'] ); ?></a>
						<?php endif; ?>
					<?php else : ?>
						<?php if ( ! empty( $settings['media_title'] ) ) : ?>
							<h3 class="text-white mb-12"><?php echo esc_html( $settings['media_title'] ); ?></h3>
						<?php endif; ?>
						<?php if ( ! empty( $settings['media_desc'] ) ) : ?>
							<p class="text-body-02 cl-text-mute mb-20 mb-xl-32"><?php echo esc_html( $settings['media_desc'] ); ?></p>
						<?php endif; ?>
						<?php if ( ! empty( $settings['media_button_text'] ) ) : ?>
							<a <?php $this->print_render_attribute_string( 'media_button_link' ); ?> class="tf-btn btn-white-3 px-v1"><?php echo esc_html( $settings['media_button_text'] ); ?></a>
						<?php endif; ?>
					<?php endif; ?>
				</div>
			</div>
			<div class="col-content">
				<?php if ( ! empty( $settings['mini_title'] ) || ! empty( $settings['title'] ) || ! empty( $settings['desc'] ) ) : ?>
					<div class="sect-heading text-center mb-xl-32">
						<?php if ( ! empty( $settings['mini_title'] ) ) : ?>
							<p class="mini-title text-primary fw-semibold"><?php echo esc_html( $settings['mini_title'] ); ?></p>
						<?php endif; ?>
						<?php if ( ! empty( $settings['title'] ) ) :
							$title_tag = $is_style_2 ? 'h3' : 'h2';
							?>
							<<?php echo $title_tag; ?> class="s-title"><?php echo esc_html( $settings['title'] ); ?></<?php echo $title_tag; ?>>
						<?php endif; ?>
						<?php if ( ! empty( $settings['desc'] ) ) : ?>
							<p class="text-body-02"><?php echo esc_html( $settings['desc'] ); ?></p>
						<?php endif; ?>
					</div>
				<?php endif; ?>
				<?php if ( ! empty( $settings['shortcode'] ) ) : ?>
					<div class="form-support">
						<?php echo do_shortcode( $settings['shortcode'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- shortcode output (Contact Form 7) is trusted, admin-configured markup ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php
		$html = ob_get_clean();

		if ( $is_style_2 ) {
			?>
			<div class="container">
				<?php echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built above, already escaped per field ?>
			</div>
			<?php
		} else {
			echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built above, already escaped per field
		}
	}
}
