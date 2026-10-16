<?php

class ovinx_Banner_Sale_Coupon extends \Elementor\Widget_Base {

	/**
	 * Same "bounded, non-AJAX SELECT2" convention already used by
	 * `widget-banner.php`/`widget-category.php` (`get_products_options()`)
	 * — single-select, options built once from `get_posts()`, no
	 * `tfwc_select2` AJAX control needed for a bounded list.
	 */
	private function get_coupon_options() {
		$options = [];

		$coupons = get_posts(
			[
				'post_type'      => 'shop_coupon',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'orderby'        => 'title',
				'order'          => 'ASC',
			]
		);

		foreach ( $coupons as $post ) {
			$options[ $post->ID ] = $post->post_title;
		}

		return $options;
	}

	public function get_name() {
		return 'ovinx_banner_sale_coupon';
	}

	public function get_title() {
		return esc_html__( 'Ovinx Banner Sale Coupon', 'ovinx-addon' );
	}

	public function get_icon() {
		return 'eicon-product-price';
	}

	public function get_categories() {
		return [ 'ovinx_addons_core' ];
	}

	public function get_keywords() {
		return [ 'banner', 'sale', 'coupon', 'discount' ];
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
				'label' => esc_html__( 'Banner Sale Coupon', 'ovinx-addon' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		/**
		 * Real WooCommerce coupon required — no manual "% off" / "coupon
		 * code" text fields. The discount amount and the code copied to
		 * clipboard are always derived from this coupon (see render()),
		 * never admin-typed, so they can never drift from the real coupon.
		 */
		$this->add_control(
			'coupon_id',
			[
				'label'       => esc_html__( 'Coupon', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'label_block' => true,
				'options'     => $this->get_coupon_options(),
				'default'     => '',
			]
		);

		$this->add_control(
			'title',
			[
				'label'       => esc_html__( 'Title', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Super Discount For Your First Purchase', 'ovinx-addon' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'description',
			[
				'label'       => esc_html__( 'Description', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'Sign In For Discount. Min spend $60. Max reward $20', 'ovinx-addon' ),
				'label_block' => true,
			]
		);

		$this->end_controls_section();

		/**
		 * Style — Banner
		 */
		$this->start_controls_section(
			'section_style_banner',
			[
				'label' => esc_html__( 'Banner', 'ovinx-addon' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'banner_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .banner-sale' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'sale_text_color',
			[
				'label'     => esc_html__( 'Discount Text Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .banner-sale .sale-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__( 'Title Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .banner-sale .infor_title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'description_color',
			[
				'label'     => esc_html__( 'Description Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .banner-sale .infor_desc' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Style — Copy Button
		 */
		$this->start_controls_section(
			'section_style_button',
			[
				'label' => esc_html__( 'Copy Button', 'ovinx-addon' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'button_text_color',
			[
				'label'     => esc_html__( 'Text Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .banner-sale .coupon-copy-wrap' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .banner-sale .coupon-copy-wrap' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Ground truth's `-25%` is static text; the real coupon only ever
	 * exposes `get_amount()` as a string ("25.00", "25.5", ...). This
	 * trims a trailing ".00"/".50" → "25%" / "25.5%" instead of printing
	 * the raw decimal string.
	 */
	private function format_percent_amount( $amount ) {
		$amount = (float) $amount;

		if ( floor( $amount ) === $amount ) {
			return (string) (int) $amount;
		}

		return rtrim( rtrim( number_format( $amount, 2, '.', '' ), '0' ), '.' );
	}

	protected function render(): void {
		$settings = $this->get_settings_for_display();

		if ( empty( $settings['coupon_id'] ) ) {
			return;
		}

		$coupon = new WC_Coupon( absint( $settings['coupon_id'] ) );

		if ( ! $coupon->get_id() ) {
			return;
		}

		$discount_type = $coupon->get_discount_type();

		if ( 'percent' === $discount_type ) {
			$sale_text = '-' . $this->format_percent_amount( $coupon->get_amount() ) . '%';
		} else {
			$sale_text = '-' . wp_kses_post( wc_price( $coupon->get_amount() ) );
		}

		$coupon_code = $coupon->get_code();
		?>
		<div class="themesFlat">
			<div class="container">
				<div class="banner-sale">
					<p class="sale-text h1 text-white"><?php echo wp_kses_post( $sale_text ); ?></p>
					<div class="sale-infor">
						<?php if ( ! empty( $settings['title'] ) ) : ?>
							<p class="infor_title h5 text-white"><?php echo esc_html( $settings['title'] ); ?></p>
						<?php endif; ?>
						<?php if ( ! empty( $settings['description'] ) ) : ?>
							<p class="infor_desc cl-text-mute"><?php echo esc_html( $settings['description'] ); ?></p>
						<?php endif; ?>
					</div>
					<button type="button" class="tf-btn btn-white_10 px-v2 coupon-copy-wrap" data-coupon-code="<?php echo esc_attr( $coupon_code ); ?>">
						<span class="coppyText text-uppercase"><?php echo esc_html( $coupon_code ); ?></span>
					</button>
				</div>
			</div>
		</div>
		<?php
	}
}
