<?php

/**
 * Ground truth: `themes/ovinx-html/` "Our Store" list — a
 * `.tf-grid-layout.gap-12.sm-col-2.lg-col-1.h-100` of `.wg-our-store` cards
 * (store name `h6.store-name`, Phone + Email side by side, Address below).
 *
 * Defaults are the 4 offices from the HTML export (New York / Houston / San
 * Francisco / Miami). A field left empty is simply not rendered; the address
 * link falls back to a Google Maps search of the address.
 */
class ovinx_Store_Info extends \Elementor\Widget_Base {

	public function get_name() {
		return 'ovinx_store_info';
	}

	public function get_title() {
		return esc_html__( 'Ovinx Store Info', 'ovinx-addon' );
	}

	public function get_icon() {
		return 'eicon-google-maps';
	}

	public function get_categories() {
		return [ 'ovinx_addons_core' ];
	}

	public function get_keywords() {
		return [ 'store', 'office', 'location', 'address', 'contact' ];
	}

	protected function register_controls() {

		/**
		 * Labels
		 */
		$this->start_controls_section(
			'section_labels',
			[
				'label' => esc_html__( 'Labels', 'ovinx-addon' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'phone_label',
			[
				'label'   => esc_html__( 'Phone Label', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'PHONE:', 'ovinx-addon' ),
			]
		);

		$this->add_control(
			'email_label',
			[
				'label'   => esc_html__( 'Email Label', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'EMAIL:', 'ovinx-addon' ),
			]
		);

		$this->add_control(
			'address_label',
			[
				'label'   => esc_html__( 'Address Label', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'ADDRESS:', 'ovinx-addon' ),
			]
		);

		$this->end_controls_section();

		/**
		 * Stores
		 */
		$this->start_controls_section(
			'section_stores',
			[
				'label' => esc_html__( 'Stores', 'ovinx-addon' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'name',
			[
				'label'       => esc_html__( 'Store Name', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'New York Office', 'ovinx-addon' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'phone',
			[
				'label'       => esc_html__( 'Phone', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => '+1 666 234 8888',
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'email',
			[
				'label'       => esc_html__( 'Email', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => 'hi.ovinx@gmail.com',
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'address',
			[
				'label'       => esc_html__( 'Address', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => '432 Park Avenue, New York, New York',
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'map_link',
			[
				'label'       => esc_html__( 'Map Link (empty = Google Maps search)', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::URL,
				'label_block' => true,
				'default'     => [ 'url' => '' ],
			]
		);

		$this->add_control(
			'stores',
			[
				'label'       => esc_html__( 'Stores', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ name }}}',
				'default'     => [
					[
						'name'    => esc_html__( 'New York Office', 'ovinx-addon' ),
						'phone'   => '+1 666 234 8888',
						'email'   => 'hi.ovinx@gmail.com',
						'address' => '432 Park Avenue, New York, New York',
					],
					[
						'name'    => esc_html__( 'Houston Office', 'ovinx-addon' ),
						'phone'   => '+1 713 432 8765',
						'email'   => 'hi.ovinx@gmail.com',
						'address' => '789 Main Street, Houston, Texas',
					],
					[
						'name'    => esc_html__( 'San Francisco Office', 'ovinx-addon' ),
						'phone'   => '+1 415 987 6543',
						'email'   => 'hi.ovinx@gmail.com',
						'address' => '321 Market Street, San Francisco, California',
					],
					[
						'name'    => esc_html__( 'Miami Office', 'ovinx-addon' ),
						'phone'   => '+1 305 543 2109',
						'email'   => 'hi.ovinx@gmail.com',
						'address' => '654 Ocean Drive, Miami, Florida',
					],
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Style
		 */
		$this->start_controls_section(
			'section_style_store',
			[
				'label' => esc_html__( 'Store Card', 'ovinx-addon' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'name_color',
			[
				'label'     => esc_html__( 'Name Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wg-our-store .store-name' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'name_typography',
				'selector' => '{{WRAPPER}} .wg-our-store .store-name',
			]
		);

		$this->add_control(
			'label_color',
			[
				'label'     => esc_html__( 'Label Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wg-our-store .infor_name' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'value_color',
			[
				'label'     => esc_html__( 'Value Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wg-our-store .infor_link' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	private function render_info( $label, $text, $href, $external = false ) {
		?>
		<div class="store-infor">
			<?php if ( '' !== $label ) : ?>
				<p class="infor_name text-button-small cl-text-main"><?php echo esc_html( $label ); ?></p>
			<?php endif; ?>
			<a href="<?php echo esc_url( $href ); ?>"<?php echo $external ? ' target="_blank" rel="noopener"' : ''; ?> class="infor_link link link-underline">
				<?php echo esc_html( $text ); ?>
			</a>
		</div>
		<?php
	}

	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$stores   = ! empty( $settings['stores'] ) && is_array( $settings['stores'] ) ? $settings['stores'] : [];

		if ( empty( $stores ) ) {
			return;
		}
		?>
		<div class="tf-store-info tf-grid-layout gap-12 sm-col-2 lg-col-1 h-100">
			<?php foreach ( $stores as $store ) :
				$phone   = isset( $store['phone'] ) ? trim( $store['phone'] ) : '';
				$email   = isset( $store['email'] ) ? trim( $store['email'] ) : '';
				$address = isset( $store['address'] ) ? trim( $store['address'] ) : '';
				$map_url = ! empty( $store['map_link']['url'] )
					? $store['map_link']['url']
					: 'https://www.google.com/maps?q=' . str_replace( [ '%20', '%2C' ], [ '+', ',' ], rawurlencode( $address ) );
				?>
				<div class="wg-our-store">
					<?php if ( ! empty( $store['name'] ) ) : ?>
						<h6 class="store-name"><?php echo esc_html( $store['name'] ); ?></h6>
					<?php endif; ?>

					<?php if ( $phone || $email ) : ?>
						<div class="tf-grid-layout md-col-2 gap-16">
							<?php
							if ( $phone ) {
								$this->render_info( $settings['phone_label'], $phone, 'tel:' . preg_replace( '/[^0-9+]/', '', $phone ) );
							}
							if ( $email ) {
								$this->render_info( $settings['email_label'], $email, 'mailto:' . $email );
							}
							?>
						</div>
					<?php endif; ?>

					<?php if ( $address ) {
						$this->render_info( $settings['address_label'], $address, $map_url, true );
					} ?>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}
}
