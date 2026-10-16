<?php

/**
 * Ground truth (`home-03.html`'s "Grid Banner" section) is a FIXED 3-slot
 * asymmetric grid (`.grid-cls-layout.grid-cls-v2`'s CSS `grid-template-areas:
 * "aa cc" "bb cc"` — item1 top-left, item2 bottom-left, item3 spans the
 * right column at 2x height) — not a flexible repeater, so this widget uses
 * 3 fixed per-item control sets instead of a `Repeater` (per this project's
 * own "fixed slot count → fixed controls, not a repeater" convention).
 */
class ovinx_Grid_Banner extends \Elementor\Widget_Base {

	public function get_name() {
		return 'ovinx_grid_banner';
	}

	public function get_title() {
		return esc_html__( 'Ovinx Grid Banner', 'ovinx-addon' );
	}

	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	public function get_categories() {
		return [ 'ovinx_addons_core' ];
	}

	public function get_keywords() {
		return [ 'banner', 'grid', 'collection' ];
	}

	private function add_item_controls( $item_key, $label, $defaults ) {
		$this->start_controls_section(
			"section_{$item_key}",
			[
				'label' => $label,
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			"{$item_key}_image",
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
				'name'      => "{$item_key}_image_size",
				'default'   => 'full',
				'separator' => 'none',
			]
		);

		$this->add_control(
			"{$item_key}_tag",
			[
				'label'       => esc_html__( 'Tag', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => $defaults['tag'],
				'label_block' => true,
			]
		);

		$this->add_control(
			"{$item_key}_title",
			[
				'label'       => esc_html__( 'Title', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'default'     => $defaults['title'],
				'label_block' => true,
			]
		);

		$this->add_control(
			"{$item_key}_button_text",
			[
				'label'       => esc_html__( 'Button Text', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'SHOP NOW', 'ovinx-addon' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			"{$item_key}_link",
			[
				'label'       => esc_html__( 'Link', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::URL,
				'label_block' => true,
				'default'     => [
					'url' => '',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function register_controls() {

		$this->add_item_controls(
			'item1',
			esc_html__( 'Item 1', 'ovinx-addon' ),
			[
				'tag'   => esc_html__( 'FOR ENHANCED TRACTION', 'ovinx-addon' ),
				'title' => esc_html__( 'Premium Tires You Can Rely On', 'ovinx-addon' ),
			]
		);

		$this->add_item_controls(
			'item2',
			esc_html__( 'Item 2', 'ovinx-addon' ),
			[
				'tag'   => esc_html__( 'FOR ENHANCED TRACTION', 'ovinx-addon' ),
				'title' => esc_html__( 'Premium Tires You Can Rely On', 'ovinx-addon' ),
			]
		);

		$this->add_item_controls(
			'item3',
			esc_html__( 'Item 3 (Large)', 'ovinx-addon' ),
			[
				'tag'   => esc_html__( 'FOR ENHANCED TRACTION', 'ovinx-addon' ),
				'title' => esc_html__( 'Synthetic Motor Oil You Can Rely On', 'ovinx-addon' ),
			]
		);

		/**
		 * Card Style
		 */
		$this->start_controls_section(
			'section_style_card',
			[
				'label' => esc_html__( 'Card', 'ovinx-addon' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'tag_color',
			[
				'label'     => esc_html__( 'Tag Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .collection-v04 .cls_tag' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__( 'Title Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .collection-v04 .cls_name' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_color',
			[
				'label'     => esc_html__( 'Button Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .collection-v04 .tf-btn-line' => 'color: {{VALUE}};',
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
	 * `item3` ("large" slot spanning the grid's right column) is the only
	 * one that gets `item3`/`style-2` on `.collection-v04` and a bigger
	 * title tag (`h3` vs `h5`) — matches ground truth exactly, not a style
	 * choice this widget invents.
	 */
	private function render_item( $settings, $item_key, $title_tag ) {
		$image_url = $settings[ "{$item_key}_image" ]['url'] ?? '';
		$title     = $settings[ "{$item_key}_title" ] ?? '';

		if ( empty( $image_url ) && empty( $title ) ) {
			return '';
		}

		if ( empty( $settings[ "{$item_key}_link" ]['url'] ) ) {
			$settings[ "{$item_key}_link" ]['url'] = '#';
		}
		$link_key = "{$item_key}_link";
		$this->add_link_attributes( $link_key, $settings[ $link_key ] );

		$extra_class = 'item3' === $item_key ? ' style-2' : '';

		ob_start();
		?>
		<div class="collection-v04 has-text-overlay <?php echo esc_attr( $item_key . $extra_class ); ?> hover-img6">
			<a <?php $this->print_render_attribute_string( $link_key ); ?> class="cls-image img-style6" aria-label="<?php echo esc_attr( ! empty( $title ) ? wp_strip_all_tags( $title ) : __( 'View collection', 'ovinx-addon' ) ); ?>">
				<?php echo $this->get_image_html( $settings, "{$item_key}_image", "{$item_key}_image_size" ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from esc_url()/esc_attr() above or Elementor's own escaped image markup ?>
			</a>
			<div class="cls-content">
				<?php if ( ! empty( $settings[ "{$item_key}_tag" ] ) ) : ?>
					<p class="cls_tag text-label fw-semibold text-white mb-4"><?php echo esc_html( $settings[ "{$item_key}_tag" ] ); ?></p>
				<?php endif; ?>
				<?php if ( ! empty( $title ) ) : ?>
					<a <?php $this->print_render_attribute_string( $link_key ); ?> class="cls_name <?php echo esc_attr( $title_tag ); ?> text-white link-underline mb-12"><?php echo wp_kses_post( $title ); ?></a>
				<?php endif; ?>
				<?php if ( ! empty( $settings[ "{$item_key}_button_text" ] ) ) : ?>
					<a <?php $this->print_render_attribute_string( $link_key ); ?> class="tf-btn-line style-white">
						<span class="text-button-small"><?php echo esc_html( $settings[ "{$item_key}_button_text" ] ); ?></span>
					</a>
				<?php endif; ?>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	protected function render(): void {
		$settings = $this->get_settings_for_display();

		$items_html  = $this->render_item( $settings, 'item1', 'h5' );
		$items_html .= $this->render_item( $settings, 'item2', 'h5' );
		$items_html .= $this->render_item( $settings, 'item3', 'h3' );

		if ( empty( $items_html ) ) {
			return;
		}
		?>
		<div class="themesFlat">
			<div class="container">
				<div class="grid-cls-layout grid-cls-v2">
					<?php echo $items_html; ?>
				</div>
			</div>
		</div>
		<?php
	}
}
