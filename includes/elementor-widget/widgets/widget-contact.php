<?php

/**
 * Ground truth: `themes/ovinx-html/contact.html` "Contact" section
 * (`<section class="section-contact flat-spacing-4">`, ~line 1350).
 *
 * Left column: heading + `.grid-info` (Phone / Email / Address / Open Time /
 * Social). Right column: "Get In Touch" heading + the form. Ground truth's
 * `.form-get` is a hand-built static form; here the form is the user's real
 * Contact Form 7 shortcode (same convention as `widget-contact-support.php`) —
 * CF7 owns the field markup/validation/submission, so the shortcode output is
 * wrapped in `.form-get` and its template should reuse ground truth's
 * `.tf-field` fieldsets.
 *
 * Phone / Email / Address / Map link left empty fall back to the Customizer's
 * Footer "Contact" values (`footer_contact_*`), so one place keeps the site's
 * contact details in sync. A block with no value is simply not rendered.
 */
class ovinx_Contact extends \Elementor\Widget_Base {

	public function get_name() {
		return 'ovinx_contact';
	}

	public function get_title() {
		return esc_html__( 'Ovinx Contact', 'ovinx-addon' );
	}

	public function get_icon() {
		return 'eicon-form-horizontal';
	}

	public function get_categories() {
		return [ 'ovinx_addons_core' ];
	}

	public function get_keywords() {
		return [ 'contact', 'form', 'info', 'address', 'phone' ];
	}

	/**
	 * Social network => icon class (all present in `css/icon-ovinx.css`).
	 */
	private function get_social_options() {
		return [
			'icon-FacebookLogoStroke'  => esc_html__( 'Facebook', 'ovinx-addon' ),
			'icon-InstagramLogoStroke' => esc_html__( 'Instagram', 'ovinx-addon' ),
			'icon-YoutubeLogo'         => esc_html__( 'YouTube', 'ovinx-addon' ),
			'icon-XLogoFill'           => esc_html__( 'X (Twitter)', 'ovinx-addon' ),
			'icon-TiktokLogo'          => esc_html__( 'TikTok', 'ovinx-addon' ),
			'icon-PinterestLogo'       => esc_html__( 'Pinterest', 'ovinx-addon' ),
		];
	}

	protected function register_controls() {

		/**
		 * Heading (left column)
		 */
		$this->start_controls_section(
			'section_heading',
			[
				'label' => esc_html__( 'Heading', 'ovinx-addon' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'heading_title',
			[
				'label'       => esc_html__( 'Title', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'We’re here to help you grow', 'ovinx-addon' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'heading_desc',
			[
				'label'   => esc_html__( 'Description', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => esc_html__( 'Feel free to contact us anytime. We’re ready to help and support your success.', 'ovinx-addon' ),
			]
		);

		$this->end_controls_section();

		/**
		 * Contact info (left column)
		 */
		$this->start_controls_section(
			'section_info',
			[
				'label' => esc_html__( 'Contact Info', 'ovinx-addon' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'phone_label',
			[
				'label'   => esc_html__( 'Phone Label', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Phone:', 'ovinx-addon' ),
			]
		);

		$this->add_control(
			'phone',
			[
				'label'       => esc_html__( 'Phone', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Empty = Customizer › Footer contact phone', 'ovinx-addon' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'email_label',
			[
				'label'     => esc_html__( 'Email Label', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => esc_html__( 'Email:', 'ovinx-addon' ),
				'separator' => 'before',
			]
		);

		$this->add_control(
			'email',
			[
				'label'       => esc_html__( 'Email', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Empty = Customizer › Footer contact email', 'ovinx-addon' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'address_label',
			[
				'label'     => esc_html__( 'Address Label', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => esc_html__( 'Address:', 'ovinx-addon' ),
				'separator' => 'before',
			]
		);

		$this->add_control(
			'address',
			[
				'label'       => esc_html__( 'Address', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Empty = Customizer › Footer contact address', 'ovinx-addon' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'address_link',
			[
				'label'       => esc_html__( 'Map Link', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::URL,
				'label_block' => true,
				'placeholder' => esc_html__( 'Empty = Customizer map URL, else Google Maps search', 'ovinx-addon' ),
				'default'     => [ 'url' => '' ],
			]
		);

		$this->add_control(
			'open_label',
			[
				'label'     => esc_html__( 'Open Time Label', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => esc_html__( 'Open Time:', 'ovinx-addon' ),
				'separator' => 'before',
			]
		);

		$open_repeater = new \Elementor\Repeater();

		$open_repeater->add_control(
			'day',
			[
				'label'   => esc_html__( 'Day', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Mon - Sat:', 'ovinx-addon' ),
			]
		);

		$open_repeater->add_control(
			'hours',
			[
				'label'   => esc_html__( 'Hours', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( '7:30am - 8:00pm PST', 'ovinx-addon' ),
			]
		);

		$this->add_control(
			'open_times',
			[
				'label'       => esc_html__( 'Open Time Rows (empty = hide block)', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $open_repeater->get_controls(),
				'title_field' => '{{{ day }}} {{{ hours }}}',
				'default'     => [
					[
						'day'   => esc_html__( 'Mon - Sat:', 'ovinx-addon' ),
						'hours' => esc_html__( '7:30am - 8:00pm PST', 'ovinx-addon' ),
					],
					[
						'day'   => esc_html__( 'Sunday:', 'ovinx-addon' ),
						'hours' => esc_html__( '9:00am - 5:00pm PST', 'ovinx-addon' ),
					],
				],
			]
		);

		$this->add_control(
			'social_label',
			[
				'label'     => esc_html__( 'Social Label', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => esc_html__( 'Follow Us On social media:', 'ovinx-addon' ),
				'separator' => 'before',
			]
		);

		$social_repeater = new \Elementor\Repeater();

		$social_repeater->add_control(
			'icon',
			[
				'label'   => esc_html__( 'Network', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => $this->get_social_options(),
				'default' => 'icon-FacebookLogoStroke',
			]
		);

		$social_repeater->add_control(
			'link',
			[
				'label'       => esc_html__( 'Link', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::URL,
				'label_block' => true,
				'default'     => [
					'url'         => '#',
					'is_external' => true,
				],
			]
		);

		$this->add_control(
			'socials',
			[
				'label'       => esc_html__( 'Social Links (empty = hide block)', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $social_repeater->get_controls(),
				'title_field' => '{{{ icon.replace("icon-", "").replace("LogoStroke", "").replace("LogoFill", "").replace("Logo", "") }}}',
				'default'     => [
					[ 'icon' => 'icon-FacebookLogoStroke' ],
					[ 'icon' => 'icon-InstagramLogoStroke' ],
					[ 'icon' => 'icon-YoutubeLogo' ],
					[ 'icon' => 'icon-XLogoFill' ],
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Form (right column)
		 */
		$this->start_controls_section(
			'section_form',
			[
				'label' => esc_html__( 'Form', 'ovinx-addon' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'form_title',
			[
				'label'       => esc_html__( 'Title', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Get In Touch', 'ovinx-addon' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'form_desc',
			[
				'label'   => esc_html__( 'Description', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => esc_html__( 'We\'d love to hear from you! If you have any questions', 'ovinx-addon' ),
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
		 * Heading Style
		 */
		$this->start_controls_section(
			'section_style_heading',
			[
				'label' => esc_html__( 'Heading', 'ovinx-addon' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'heading_title_color',
			[
				'label'     => esc_html__( 'Title Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .section-contact .col-left .heading h4' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'heading_title_typography',
				'selector' => '{{WRAPPER}} .section-contact .col-left .heading h4',
			]
		);

		$this->add_control(
			'heading_desc_color',
			[
				'label'     => esc_html__( 'Description Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .section-contact .col-left .heading p' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Info Style
		 */
		$this->start_controls_section(
			'section_style_info',
			[
				'label' => esc_html__( 'Contact Info', 'ovinx-addon' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'info_label_color',
			[
				'label'     => esc_html__( 'Label Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .section-contact .grid-info h6' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'info_label_typography',
				'selector' => '{{WRAPPER}} .section-contact .grid-info h6',
			]
		);

		$this->add_control(
			'info_value_color',
			[
				'label'     => esc_html__( 'Value Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .section-contact .grid-info p a, {{WRAPPER}} .section-contact .grid-info .open-text li' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'social_color',
			[
				'label'     => esc_html__( 'Social Icon Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .section-contact .social-list a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Form Style
		 */
		$this->start_controls_section(
			'section_style_form',
			[
				'label' => esc_html__( 'Form', 'ovinx-addon' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'form_title_color',
			[
				'label'     => esc_html__( 'Title Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .section-contact .col-form h4' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'form_title_typography',
				'selector' => '{{WRAPPER}} .section-contact .col-form h4',
			]
		);

		$this->add_control(
			'form_desc_color',
			[
				'label'     => esc_html__( 'Description Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .section-contact .col-form > p' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Info blocks share one shape in ground truth: `.d-grid.gap-8` > `h6.lh-28`
	 * label + value (`.wd-full` on the full-width ones).
	 */
	private function render_link_block( $label, $text, $href, $wide = false, $external = false ) {
		?>
		<div class="<?php echo $wide ? 'wd-full ' : ''; ?>d-grid gap-8">
			<?php if ( '' !== $label ) : ?>
				<h6 class="lh-28"><?php echo esc_html( $label ); ?></h6>
			<?php endif; ?>
			<p>
				<a href="<?php echo esc_url( $href ); ?>"<?php echo $external ? ' target="_blank" rel="noopener"' : ''; ?> class="link link-underline">
					<?php echo esc_html( $text ); ?>
				</a>
			</p>
		</div>
		<?php
	}

	protected function render(): void {
		$settings = $this->get_settings_for_display();

		$customizer = static function ( $key ) {
			return function_exists( 'themesflat_get_opt' ) ? (string) themesflat_get_opt( $key ) : '';
		};

		$phone   = ! empty( $settings['phone'] ) ? $settings['phone'] : $customizer( 'footer_contact_phone' );
		$email   = ! empty( $settings['email'] ) ? $settings['email'] : $customizer( 'footer_contact_email' );
		$address = ! empty( $settings['address'] ) ? $settings['address'] : $customizer( 'footer_contact_address' );

		$map_url = '';
		if ( ! empty( $settings['address_link']['url'] ) ) {
			$map_url = $settings['address_link']['url'];
		} elseif ( $address && empty( $settings['address'] ) && $customizer( 'footer_contact_map_url' ) ) {
			$map_url = $customizer( 'footer_contact_map_url' );
		} elseif ( $address ) {
			$map_url = 'https://www.google.com/maps?q=' . rawurlencode( $address );
		}

		$open_times = ! empty( $settings['open_times'] ) && is_array( $settings['open_times'] ) ? $settings['open_times'] : [];
		$socials    = ! empty( $settings['socials'] ) && is_array( $settings['socials'] ) ? $settings['socials'] : [];
		$allowed    = $this->get_social_options();

		$has_heading = ! empty( $settings['heading_title'] ) || ! empty( $settings['heading_desc'] );
		$has_form    = ! empty( $settings['form_title'] ) || ! empty( $settings['form_desc'] ) || ! empty( $settings['shortcode'] );
		?>
		<section class="section-contact flat-spacing-4 tf-contact">
			<div class="container">
				<div class="row gy-5 flex-wrap-reverse">
					<div class="col-md-6 col-xl-5">
						<div class="col-left">
							<?php if ( $has_heading ) : ?>
								<div class="heading d-grid gap-12">
									<?php if ( ! empty( $settings['heading_title'] ) ) : ?>
										<h4><?php echo esc_html( $settings['heading_title'] ); ?></h4>
									<?php endif; ?>
									<?php if ( ! empty( $settings['heading_desc'] ) ) : ?>
										<p class="text-body-02"><?php echo esc_html( $settings['heading_desc'] ); ?></p>
									<?php endif; ?>
								</div>
							<?php endif; ?>

							<div class="grid-info tf-grid-layout sm-col-2">
								<?php
								if ( $phone ) {
									$this->render_link_block( $settings['phone_label'], $phone, 'tel:' . preg_replace( '/[^0-9+]/', '', $phone ) );
								}
								if ( $email ) {
									$this->render_link_block( $settings['email_label'], $email, 'mailto:' . $email );
								}
								if ( $address ) {
									$this->render_link_block( $settings['address_label'], $address, $map_url, true, true );
								}
								?>

								<?php if ( $open_times ) : ?>
									<div class="wd-full d-grid gap-8">
										<?php if ( ! empty( $settings['open_label'] ) ) : ?>
											<h6 class="lh-28"><?php echo esc_html( $settings['open_label'] ); ?></h6>
										<?php endif; ?>
										<ul class="open-text">
											<?php foreach ( $open_times as $index => $row ) : ?>
												<li class="d-flex gap-4<?php echo $index < count( $open_times ) - 1 ? ' mb-4' : ''; ?>">
													<span class="cl-text-2"><?php echo esc_html( $row['day'] ); ?></span><?php echo esc_html( $row['hours'] ); ?>
												</li>
											<?php endforeach; ?>
										</ul>
									</div>
								<?php endif; ?>

								<?php if ( $socials ) : ?>
									<div class="wd-full d-grid gap-16">
										<?php if ( ! empty( $settings['social_label'] ) ) : ?>
											<h6 class="lh-28"><?php echo esc_html( $settings['social_label'] ); ?></h6>
										<?php endif; ?>
										<div class="tf-list social-list style-2">
											<?php foreach ( $socials as $index => $social ) :
												if ( empty( $social['icon'] ) || ! isset( $allowed[ $social['icon'] ] ) ) {
													continue;
												}
												$link_key = 'social_link_' . $index;
												if ( empty( $social['link']['url'] ) ) {
													$social['link']['url'] = '#';
												}
												$this->add_link_attributes( $link_key, $social['link'] );
												?>
												<a <?php $this->print_render_attribute_string( $link_key ); ?> aria-label="<?php echo esc_attr( $allowed[ $social['icon'] ] ); ?>">
													<i class="icon <?php echo esc_attr( $social['icon'] ); ?>"></i>
												</a>
											<?php endforeach; ?>
										</div>
									</div>
								<?php endif; ?>
							</div>
						</div>
					</div>

					<?php if ( $has_form ) : ?>
						<div class="col-md-6 col-xl-7 col-form">
							<?php if ( ! empty( $settings['form_title'] ) ) : ?>
								<h4 class="mb-12"><?php echo esc_html( $settings['form_title'] ); ?></h4>
							<?php endif; ?>
							<?php if ( ! empty( $settings['form_desc'] ) ) : ?>
								<p class="text-body-02 mb-28"><?php echo esc_html( $settings['form_desc'] ); ?></p>
							<?php endif; ?>
							<?php if ( ! empty( $settings['shortcode'] ) ) : ?>
								<div class="form-get">
									<?php echo do_shortcode( $settings['shortcode'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- shortcode output (Contact Form 7) is trusted, admin-configured markup ?>
								</div>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</section>
		<?php
	}
}
