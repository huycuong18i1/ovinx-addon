<?php

class ovinx_Check_Fit extends \Elementor\Widget_Base {

	use Ovinx_Vehicle_Filter_Fields;

	public function get_name() {
		return 'ovinx_check_fit';
	}

	public function get_title() {
		return esc_html__( 'Ovinx Check Fit', 'ovinx-addon' );
	}

	public function get_icon() {
		return 'eicon-form-horizontal';
	}

	public function get_categories() {
		return [ 'ovinx_addons_core' ];
	}

	public function get_keywords() {
		return [ 'vehicle', 'filter', 'search', 'check fit', 'parts' ];
	}

	public function get_script_depends() {
		return [ 'slider-core' ];
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
			'layout',
			[
				'label'   => esc_html__( 'Layout', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'surface'              => esc_html__( 'Surface Full Width', 'ovinx-addon' ),
					'boxed-heading-inside' => esc_html__( 'Boxed Card — Heading Inside', 'ovinx-addon' ),
					'boxed-heading-outside' => esc_html__( 'Boxed Card — Heading Outside', 'ovinx-addon' ),
				],
				'default' => 'surface',
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
				'label'   => esc_html__( 'Description', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => esc_html__( 'Find the right parts quickly by searching for your vehicle model and specifications.', 'ovinx-addon' ),
			]
		);

		$this->add_control(
			'button_text',
			[
				'label'   => esc_html__( 'Button Text', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Search Parts', 'ovinx-addon' ),
			]
		);

		$this->end_controls_section();

		/**
		 * Search Fields
		 */
		$this->start_controls_section(
			'section_search_fields',
			[
				'label' => esc_html__( 'Search Fields', 'ovinx-addon' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->register_vehicle_cascade_controls();

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
					'{{WRAPPER}} .banner-check-fit .s-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .banner-check-fit .s-title',
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
					'{{WRAPPER}} .banner-check-fit .sect-heading p' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'desc_typography',
				'selector' => '{{WRAPPER}} .banner-check-fit .sect-heading p',
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
					'{{WRAPPER}} .btn-action_submit' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_background',
			[
				'label'     => esc_html__( 'Background Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .btn-action_submit' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * The 3 real standalone layouts (ground truth: `index.html` / `home-02.html`
	 * / `home-03.html`) — verified each one's actual markup rather than
	 * assuming from its CSS suffix, since `index.html` and `home-02.html` both
	 * carry no `style-*` suffix at all yet are structurally different:
	 *
	 * - `surface`:              `.banner-check-fit.flat-spacing-2.bg-surface`, heading `.sect-heading.type-2` (`<p class="s-title h4">`), no `.content-wrap`.
	 * - `boxed-heading-inside`: `.banner-check-fit.flat-spacing-4` (no suffix), heading (`<h4>`) + fields both inside ONE `.content-wrap`.
	 * - `boxed-heading-outside`: `.banner-check-fit.style-2.flat-spacing-4`, heading (`<h4>`) OUTSIDE its own separate `.content-wrap` that wraps only the fields.
	 */
	protected function render(): void {
		$settings = $this->get_settings_for_display();

		$layout = ! empty( $settings['layout'] ) ? $settings['layout'] : 'surface';
		$levels = ! empty( $settings['levels'] ) && is_array( $settings['levels'] ) ? $settings['levels'] : [];
		$field  = $this->get_cascade_field( $settings );

		$outer_class = 'banner-check-fit';
		if ( 'surface' === $layout ) {
			$outer_class .= ' flat-spacing-2 bg-surface';
		} else {
			if ( 'boxed-heading-outside' === $layout ) {
				$outer_class .= ' style-2';
			}
			$outer_class .= ' ';
		}

		$heading_tag   = ( 'surface' === $layout ) ? 'p' : 'h4';
		$heading_class = ( 'surface' === $layout ) ? 'sect-heading type-2' : 'sect-heading text-center';
		$title_class   = ( 'surface' === $layout ) ? 's-title h4' : 's-title';

		ob_start();
		if ( ! empty( $settings['title'] ) || ! empty( $settings['description'] ) ) :
			?>
			<div class="<?php echo esc_attr( $heading_class ); ?>">
				<?php if ( ! empty( $settings['title'] ) ) : ?>
					<<?php echo esc_html( $heading_tag ); ?> class="<?php echo esc_attr( $title_class ); ?>">
						<?php echo esc_html( $settings['title'] ); ?>
					</<?php echo esc_html( $heading_tag ); ?>>
				<?php endif; ?>
				<?php if ( ! empty( $settings['description'] ) ) : ?>
					<p class="text-body-02"><?php echo esc_html( $settings['description'] ); ?></p>
				<?php endif; ?>
			</div>
			<?php
		endif;
		$heading_html = ob_get_clean();

		ob_start();
		?>
		<div class="content filter-vehicle is-cascade">
			<?php echo $this->render_vehicle_cascade_fields( $field, $levels ); ?>
			<?php echo $this->render_vehicle_filter_button( $settings['button_text'], true ); ?>
		</div>
		<?php
		$fields_html = ob_get_clean();
		?>
		<div class="<?php echo esc_attr( $outer_class ); ?>">
			<div class="container">
				<?php if ( 'boxed-heading-outside' === $layout ) : ?>
					<?php echo $heading_html; ?>
					<div class="content-wrap">
						<?php echo $fields_html; ?>
					</div>
				<?php elseif ( 'boxed-heading-inside' === $layout ) : ?>
					<div class="content-wrap">
						<?php echo $heading_html; ?>
						<?php echo $fields_html; ?>
					</div>
				<?php else : ?>
					<?php echo $heading_html; ?>
					<?php echo $fields_html; ?>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}
