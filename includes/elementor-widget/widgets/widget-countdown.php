<?php

/**
 * Ground truth (`home-02.html`'s "Deals Of The Week" carousel, first slide)
 * uses `class="js-countdown cd-has-zero cd-no" data-timer="..."` — but
 * neither script this plugin already registers for countdowns
 * (`js-countdown` → `count-down.js`, `js-countdown-core` →
 * `count-down-core.js`) actually selects `.js-countdown` at all; both read
 * `.js-countdown-core` (checked their source directly). `count-down-core.js`
 * is also the one with Elementor-scoped init/destroy AND an
 * `frontend/element_ready/ovinx_countdown.default` hook **already wired up**
 * (pre-existing, unused until now) — so this widget is named `ovinx_countdown`
 * to match that hook, and renders `.js-countdown-core` (not the ground
 * truth's dead `.js-countdown` class) so the timer actually runs.
 */
class ovinx_Countdown extends \Elementor\Widget_Base {

	public function get_name() {
		return 'ovinx_countdown';
	}

	public function get_title() {
		return esc_html__( 'Ovinx Countdown', 'ovinx-addon' );
	}

	public function get_icon() {
		return 'eicon-countdown';
	}

	public function get_categories() {
		return [ 'ovinx_addons_core' ];
	}

	public function get_keywords() {
		return [ 'countdown', 'banner', 'timer', 'deal' ];
	}

	public function get_script_depends() {
		return [ 'js-countdown-core' ];
	}

	protected function register_controls() {

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
				'default'     => esc_html__( 'FOR HIGH-PERFORMANCE', 'ovinx-addon' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'title',
			[
				'label'       => esc_html__( 'Title', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Trusted Car Parts', 'ovinx-addon' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'button_text',
			[
				'label'       => esc_html__( 'Button Text', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Shop Now', 'ovinx-addon' ),
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

		$this->end_controls_section();

		/**
		 * Countdown
		 */
		$this->start_controls_section(
			'section_countdown',
			[
				'label' => esc_html__( 'Countdown', 'ovinx-addon' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		/**
		 * A real target date/time (recalculated correctly on every page
		 * load) rather than ground truth's fixed `data-timer="42141"`
		 * (a duration counted from whenever the page happens to load,
		 * resetting to the same length for every visitor) — more useful for
		 * an actual promotion deadline, and `count-down-core.js` already
		 * supports both `data-timer` and `data-countdown`; this widget just
		 * always computes the former server-side from a real end date so
		 * only one control is needed.
		 */
		$this->add_control(
			'end_date',
			[
				'label'       => esc_html__( 'End Date/Time', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::DATE_TIME,
				'label_block' => true,
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
					'{{WRAPPER}} .banner-cd-v01 .mini-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__( 'Title Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .banner-cd-v01 .title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_color',
			[
				'label'     => esc_html__( 'Button Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .banner-cd-v01 .tf-btn-line' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Countdown Style
		 */
		$this->start_controls_section(
			'section_style_countdown',
			[
				'label' => esc_html__( 'Countdown', 'ovinx-addon' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'countdown_value_color',
			[
				'label'     => esc_html__( 'Value Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .countdown-v02 .countdown__value' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'countdown_label_color',
			[
				'label'     => esc_html__( 'Label Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .countdown-v02 .countdown__label' => 'color: {{VALUE}};',
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

		if ( empty( $settings['title'] ) && empty( $settings['image']['url'] ) ) {
			return;
		}

		if ( empty( $settings['button_link']['url'] ) ) {
			$settings['button_link']['url'] = '#';
		}
		$this->add_link_attributes( 'button_link', $settings['button_link'] );

		$seconds_left = 0;

		if ( ! empty( $settings['end_date'] ) ) {
			$end_dt = date_create( $settings['end_date'], wp_timezone() );

			if ( $end_dt ) {
				$seconds_left = max( 0, $end_dt->getTimestamp() - time() );
			}
		}
		?>
		<div class="banner-cd-v01 hover-img6">
			<?php if ( ! empty( $settings['mini_title'] ) ) : ?>
				<p class="mini-title text-label fw-semibold text-primary mb-4"><?php echo esc_html( $settings['mini_title'] ); ?></p>
			<?php endif; ?>
			<?php if ( ! empty( $settings['title'] ) ) : ?>
				<h5 class="title mb-12"><?php echo esc_html( $settings['title'] ); ?></h5>
			<?php endif; ?>
			<?php if ( ! empty( $settings['button_text'] ) ) : ?>
				<a <?php $this->print_render_attribute_string( 'button_link' ); ?> class="tf-btn-line py-4 mb-16">
					<span class="text-button-small text-uppercase"><?php echo esc_html( $settings['button_text'] ); ?></span>
				</a>
			<?php endif; ?>
			<?php if ( ! empty( $settings['image']['url'] ) ) : ?>
				<div class="bn-image mb-16 img-style6 overflow-visible">
					<?php echo $this->get_image_html( $settings, 'image', 'image_size' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from esc_url()/esc_attr() above or Elementor's own escaped image markup ?>
				</div>
			<?php endif; ?>
			<?php if ( ! empty( $settings['end_date'] ) ) : ?>
				<div class="countdown-v02">
					<div class="js-countdown-core cd-has-zero cd-no" data-labels="Days,Hours,Mins,Secs" data-timer="<?php echo esc_attr( $seconds_left ); ?>"></div>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}
}
