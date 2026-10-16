<?php

class ovinx_Collection_Banner extends \Elementor\Widget_Base {

	public function get_name() {
		return 'ovinx_collection_banner';
	}

	public function get_title() {
		return esc_html__( 'Ovinx Collection Banner', 'ovinx-addon' );
	}

	public function get_icon() {
		return 'eicon-posts-grid';
	}

	public function get_categories() {
		return [ 'ovinx_addons_core' ];
	}

	public function get_keywords() {
		return [ 'collection', 'banner', 'grid', 'promo' ];
	}

	protected function register_controls() {

		/**
		 * Banners
		 */
		$this->start_controls_section(
			'section_banners',
			[
				'label' => esc_html__( 'Banners', 'ovinx-addon' ),
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
				'default'     => esc_html__( 'FOR PROTECTION', 'ovinx-addon' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'tags_item_color',
			[
				'label'     => esc_html__( 'Tags Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .collection-v01{{CURRENT_ITEM}} .cls_tag' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$repeater->add_control(
			'title',
			[
				'label'       => esc_html__( 'Title', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'Advanced Full Synthetic Motor Oil Formula', 'ovinx-addon' ),
				'label_block' => true,
			]
		);

		// Per-slide title color. Markup carries `.text-white` (Bootstrap, `!important`),
		// so every color rule here needs `!important` too; the `.collection-v01{{CURRENT_ITEM}}`
		// selector is more specific than the widget-wide Title color in the Style tab.
		$repeater->add_control(
			'title_item_color',
			[
				'label'     => esc_html__( 'Title Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .collection-v01{{CURRENT_ITEM}} .cls_name' => 'color: {{VALUE}} !important;',
				],
			]
		);

		// Mobile-only gradient behind the text (CSS in the theme's styles.css next to `.collection-v01.has-text-overlay`);
		// this only switches which gradient — rendered as the `cb-overlay-{dark|light}` class on the item.
		$repeater->add_control(
			'overlay_style',
			[
				'label'       => esc_html__( 'Mobile Overlay', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'default'     => 'dark',
				'options'     => [
					'dark'  => esc_html__( 'Dark', 'ovinx-addon' ),
					'light' => esc_html__( 'Light (use with dark Tag/Title color)', 'ovinx-addon' ),
				],
			]
		);

		$repeater->add_control(
			'button_text',
			[
				'label'       => esc_html__( 'Button Text', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'SHOP NOW', 'ovinx-addon' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'link',
			[
				'label'       => esc_html__( 'Link', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::URL,
				'label_block' => true,
				'default'     => [
					'url' => '',
				],
			]
		);

		$this->add_control(
			'banner_items',
			[
				'label'       => esc_html__( 'Banner Items', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ tag ? tag : "— banner —" }}}',
				'default'     => [
					[
						'tag'         => esc_html__( 'FOR PROTECTION', 'ovinx-addon' ),
						'title'       => esc_html__( 'Advanced Full Synthetic Motor Oil Formula', 'ovinx-addon' ),
						'button_text' => esc_html__( 'SHOP NOW', 'ovinx-addon' ),
					],
					[
						'tag'         => esc_html__( 'FOR PROTECTION', 'ovinx-addon' ),
						'title'       => esc_html__( 'Genuine High Replacement Automotive', 'ovinx-addon' ),
						'button_text' => esc_html__( 'SHOP NOW', 'ovinx-addon' ),
					],
				],
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
					'{{WRAPPER}} .collection-v01 .cls_tag' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'tag_typography',
				'selector' => '{{WRAPPER}} .collection-v01 .cls_tag',
			]
		);

		$this->add_responsive_control(
			'tag_spacing',
			[
				'label'      => esc_html__( 'Spacing Bottom', 'ovinx-addon' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [ 'min' => 0, 'max' => 100 ],
				],
				'selectors'  => [
					'{{WRAPPER}} .collection-v01 .cls_tag' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
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
					'{{WRAPPER}} .collection-v01 .cls_name' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .collection-v01 .cls_name',
			]
		);

		$this->add_responsive_control(
			'title_spacing',
			[
				'label'      => esc_html__( 'Spacing Bottom', 'ovinx-addon' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [ 'min' => 0, 'max' => 200 ],
				],
				'selectors'  => [
					'{{WRAPPER}} .collection-v01 .cls_name' => 'margin-bottom: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .collection-v01 .btn-action' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .collection-v01 .btn-action' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Same convention as `widget-slideshow.php`'s `get_repeater_image_html()`
	 * — using Elementor's own `Group_Control_Image_Size` output (real
	 * width/height from the actual uploaded attachment) instead of hardcoding
	 * ground truth's static-export `width="635" height="360"`, since this
	 * image is admin-uploadable here, not a fixed static asset.
	 */
	private function get_repeater_image_html( $item, $image_key, $size_key ) {
		if ( empty( $item[ $image_key ]['url'] ) ) {
			return '';
		}

		if ( ! empty( $item[ $image_key ]['id'] ) ) {
			return \Elementor\Group_Control_Image_Size::get_attachment_image_html( $item, $size_key, $image_key );
		}

		return sprintf(
			'<img loading="lazy" src="%s" alt="%s">',
			esc_url( $item[ $image_key ]['url'] ),
			esc_attr__( 'Image', 'ovinx-addon' )
		);
	}

	private function render_banner_item( $item, $index ) {
		if ( empty( $item['image']['url'] ) && empty( $item['title'] ) ) {
			return '';
		}

		$link_key = 'banner_link_' . $index;

		if ( empty( $item['link']['url'] ) ) {
			$item['link']['url'] = '#';
		}
		$this->add_link_attributes( $link_key, $item['link'] );

		$overlay_class = ( isset( $item['overlay_style'] ) && 'light' === $item['overlay_style'] ) ? 'cb-overlay-light' : 'cb-overlay-dark';

		ob_start();
		?>
		<div class="collection-v01 has-text-overlay <?php echo esc_attr( $overlay_class ); ?> hover-img6 elementor-repeater-item-<?php echo esc_attr( $item['_id'] ); ?>">
			<a <?php $this->print_render_attribute_string( $link_key ); ?> class="cls-image img-style6" aria-label="<?php echo esc_attr( ! empty( $item['title'] ) ? wp_strip_all_tags( $item['title'] ) : __( 'View collection', 'ovinx-addon' ) ); ?>">
				<?php echo $this->get_repeater_image_html( $item, 'image', 'image_size' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from esc_url()/esc_attr() above or Elementor's own escaped image markup ?>
			</a>
			<div class="cls-content">
				<?php if ( ! empty( $item['tag'] ) ) : ?>
					<p class="cls_tag text-white fw-semibold"><?php echo esc_html( $item['tag'] ); ?></p>
				<?php endif; ?>
				<?php if ( ! empty( $item['title'] ) ) : ?>
					<h2 class="cls_name text-white h3"><?php echo wp_kses_post( $item['title'] ); ?></h2>
				<?php endif; ?>
				<?php if ( ! empty( $item['button_text'] ) ) : ?>
					<a <?php $this->print_render_attribute_string( $link_key ); ?> class="btn-action tf-btn btn-white-2 px-v1"><?php echo esc_html( $item['button_text'] ); ?></a>
				<?php endif; ?>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$items    = ! empty( $settings['banner_items'] ) && is_array( $settings['banner_items'] ) ? $settings['banner_items'] : [];

		$cards_html = '';
		foreach ( $items as $index => $item ) {
			$cards_html .= $this->render_banner_item( $item, $index );
		}

		if ( empty( $cards_html ) ) {
			return;
		}
		?>
		<div class="themesFlat">
			<div class="container">
				<div class="tf-grid-layout sm-col-2 gap-20">
					<?php echo $cards_html; ?>
				</div>
			</div>
		</div>
		<?php
	}
}
