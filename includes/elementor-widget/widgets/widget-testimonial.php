<?php

class ovinx_Testimonial extends \Elementor\Widget_Base {

	use Ovinx_Swiper_Group_Controls;

	public function get_name() {
		return 'ovinx_testimonial';
	}

	public function get_title() {
		return esc_html__( 'Ovinx Testimonial', 'ovinx-addon' );
	}

	public function get_icon() {
		return 'eicon-testimonial-carousel';
	}

	public function get_categories() {
		return [ 'ovinx_addons_core' ];
	}

	public function get_keywords() {
		return [ 'testimonial', 'review', 'carousel' ];
	}

	public function get_script_depends() {
		return [ 'themesflat-swiper', 'slider-core' ];
	}

	public function get_style_depends() {
		return [ 'themesflat-swiper' ];
	}

	/**
	 * Same "bounded, non-AJAX SELECT2" convention already used by
	 * `widget-banner-feature.php`/`widget-banner.php`/`widget-category.php`.
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

	protected function register_controls() {

		/**
		 * Style
		 */
		$this->start_controls_section(
			'section_style_select',
			[
				'label' => esc_html__( 'Style', 'ovinx-addon' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'style',
			[
				'label'   => esc_html__( 'Style', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'style_1' => esc_html__( 'Style 1 — With Summary Card', 'ovinx-addon' ),
					'style_2' => esc_html__( 'Style 2 — With Product', 'ovinx-addon' ),
				],
				'default' => 'style_1',
			]
		);

		$this->end_controls_section();

		/**
		 * Summary Card — Style 1 only (ground truth's fixed left-hand
		 * "What Drivers Say / 4200 reviews" panel — admin-typed, since
		 * there's no real review-aggregation system behind these numbers in
		 * this codebase, unlike the real product/post data used elsewhere).
		 */
		$this->start_controls_section(
			'section_summary',
			[
				'label'     => esc_html__( 'Summary Card', 'ovinx-addon' ),
				'tab'       => \Elementor\Controls_Manager::TAB_CONTENT,
				'condition' => [
					'style' => 'style_1',
				],
			]
		);

		$this->add_control(
			'summary_title',
			[
				'label'       => esc_html__( 'Title', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'What Drivers Say', 'ovinx-addon' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'summary_rating_label',
			[
				'label'       => esc_html__( 'Rating Label', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Excellent', 'ovinx-addon' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'summary_review_count',
			[
				'label'       => esc_html__( 'Review Count Text', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( '4200 reviews', 'ovinx-addon' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'summary_desc',
			[
				'label'   => esc_html__( 'Description', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => esc_html__( 'All comments are from real users who have made purchases before.', 'ovinx-addon' ),
			]
		);

		$this->end_controls_section();

		/**
		 * Heading — Style 2 only.
		 */
		$this->start_controls_section(
			'section_heading',
			[
				'label'     => esc_html__( 'Heading', 'ovinx-addon' ),
				'tab'       => \Elementor\Controls_Manager::TAB_CONTENT,
				'condition' => [
					'style' => 'style_2',
				],
			]
		);

		$this->add_control(
			'title',
			[
				'label'       => esc_html__( 'Title', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Performance You Can Trust', 'ovinx-addon' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'view_all_text',
			[
				'label'       => esc_html__( 'View All Text', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'VIEW ALL', 'ovinx-addon' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'view_all_link',
			[
				'label'       => esc_html__( 'View All Link', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::URL,
				'label_block' => true,
				'default'     => [
					'url' => '',
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Testimonials
		 */
		$this->start_controls_section(
			'section_testimonials',
			[
				'label' => esc_html__( 'Testimonials', 'ovinx-addon' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'author_avatar',
			[
				'label'       => esc_html__( 'Author Avatar', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::MEDIA,
				'default'     => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
				'description' => esc_html__( 'Used by Style 2 only.', 'ovinx-addon' ),
			]
		);

		$repeater->add_control(
			'author_name',
			[
				'label'       => esc_html__( 'Author Name', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'David Harrison', 'ovinx-addon' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'author_duty',
			[
				'label'       => esc_html__( 'Author Title/Company', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Lead Engineer Volvo', 'ovinx-addon' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'verified',
			[
				'label'        => esc_html__( 'Show "Verified Buyer"', 'ovinx-addon' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'ovinx-addon' ),
				'label_off'    => esc_html__( 'No', 'ovinx-addon' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$repeater->add_control(
			'title',
			[
				'label'       => esc_html__( 'Headline', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Excellent Look And Perfect OEM Fitment', 'ovinx-addon' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'text',
			[
				'label'   => esc_html__( 'Review Text', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => esc_html__( "The new rims transformed my car's appearance. High quality material and very easy to install.", 'ovinx-addon' ),
			]
		);

		/**
		 * Used by Style 2 only — ground truth's `.tes-prd` is a real,
		 * purchasable WooCommerce product (image/name/price), so it's picked
		 * from the catalog rather than typed, same as every other product
		 * reference in this plugin. No `condition` here on purpose: a
		 * repeater field can only condition on a *sibling* field inside the
		 * same repeater row, never on an outer widget-level control like
		 * `style` — Elementor would just always hide it, exactly the bug
		 * this control shipped with the first time (silently missing from
		 * the panel, not merely inert).
		 */
		$repeater->add_control(
			'product_id',
			[
				'label'       => esc_html__( 'Featured Product (Style 2 only)', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'label_block' => true,
				'options'     => $this->get_products_options(),
				'default'     => '',
			]
		);

		$this->add_control(
			'testimonial_items',
			[
				'label'       => esc_html__( 'Testimonial Items', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ author_name }}}',
				'default'     => [ [], [], [] ],
			]
		);

		$this->end_controls_section();

		/**
		 * Carousel Layout
		 */
		$this->start_controls_section(
			'section_carousel',
			[
				'label' => esc_html__( 'Carousel Layout', 'ovinx-addon' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'items_desktop',
			[
				'label'   => esc_html__( 'Items — Desktop (≥1440px)', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 6,
				'default' => 4,
			]
		);

		$this->add_control(
			'items_laptop',
			[
				'label'   => esc_html__( 'Items — Laptop (≥1200px)', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 6,
				'default' => 3,
			]
		);

		$this->add_control(
			'items_tablet',
			[
				'label'   => esc_html__( 'Items — Tablet (≥768px)', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 6,
				'default' => 2,
			]
		);

		$this->add_control(
			'items_mobile',
			[
				'label'   => esc_html__( 'Items — Mobile (≥575px)', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 6,
				'default' => 1,
			]
		);

		$this->add_control(
			'items_mobile_sm',
			[
				'label'   => esc_html__( 'Items — Small Mobile (<575px)', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 6,
				'default' => 1,
			]
		);

		$this->end_controls_section();

		$this->register_swiper_group_controls( [ 15, 15, 15, 20, 30 ] );

		/**
		 * Style tab
		 */
		$this->start_controls_section(
			'section_style_card',
			[
				'label' => esc_html__( 'Card', 'ovinx-addon' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'card_title_color',
			[
				'label'     => esc_html__( 'Headline Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tes-name, {{WRAPPER}} .tes-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'card_text_color',
			[
				'label'     => esc_html__( 'Review Text Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tes-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_pagination',
			[
				'label' => esc_html__( 'Pagination', 'ovinx-addon' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'pagination_color',
			[
				'label'     => esc_html__( 'Bullet Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .sw-dot-default .swiper-pagination-bullet, {{WRAPPER}} .sw-line-default .swiper-pagination-bullet' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'pagination_color_active',
			[
				'label'     => esc_html__( 'Active Bullet Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .sw-dot-default .swiper-pagination-bullet-active, {{WRAPPER}} .sw-line-default .swiper-pagination-bullet-active' => 'background: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Ground truth's 5-star "Trustpilot-style" rating graphic and the green
	 * verified-buyer checkmark are the exact same inline SVG on every card —
	 * decorative brand iconography, not per-item data, and not available as
	 * an `icon-ovinx.css` glyph (checked — no matching compound icon), so
	 * they stay static markup rather than an admin-facing Icon control.
	 */
	private function render_stars_svg( $size = 'lg' ) {
		if ( 'lg' === $size ) {
			return '<svg width="149" height="28" viewBox="0 0 149 28" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0 0H27.9405V28H0V0ZM30.2649 0H58.2054V28H30.2649V0ZM60.5298 0H88.4702V28H60.5298V0ZM90.7946 0H118.735V28H90.7946V0ZM121.06 0H149V28H121.06V0Z" fill="#00B67A"/><path d="M13.9699 18.8759L18.2194 17.7948L19.9955 23.2778L13.9699 18.8759ZM23.7443 11.7863H16.2645L13.964 4.72656L11.6634 11.7863H4.18359L10.239 16.1583L7.93839 23.218L13.9938 18.846L17.7188 16.1643L23.7443 11.7923V11.7863ZM44.2348 18.8759L48.4843 17.7948L50.2604 23.2778L44.2348 18.8759ZM54.0152 11.7863H46.5354L44.2348 4.72656L41.9342 11.7863H34.4544L40.5098 16.1583L38.2092 23.218L44.2646 18.846L47.9896 16.1643L54.0152 11.7923V11.7863ZM74.4997 18.8759L78.7492 17.7948L80.5252 23.2778L74.4997 18.8759ZM84.28 11.7863H76.8002L74.4997 4.72656L72.1991 11.7863H64.7193L70.7747 16.1583L68.4741 23.218L74.5295 18.846L78.2545 16.1643L84.28 11.7923V11.7863ZM104.765 18.8759L109.014 17.7948L110.79 23.2778L104.765 18.8759ZM114.545 11.7863H107.065L104.765 4.72656L102.464 11.7863H94.9842L101.04 16.1583L98.739 23.218L104.794 18.846L108.519 16.1643L114.545 11.7923V11.7863ZM135.029 18.8759L139.279 17.7948L141.055 23.2778L135.029 18.8759ZM144.81 11.7863H137.33L135.029 4.72656L132.729 11.7863H125.249L131.304 16.1583L129.004 23.218L135.059 18.846L138.784 16.1643L144.81 11.7923V11.7863Z" fill="white"/></svg>';
		}

		return '<svg width="128" height="24" viewBox="0 0 128 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0 0H24.0026V24H0V0ZM25.9994 0H50.0019V24H25.9994V0ZM51.9987 0H76.0013V24H51.9987V0ZM77.9981 0H102.001V24H77.9981V0ZM103.997 0H128V24H103.997V0Z" fill="#00B67A"/><path d="M12.0008 16.1788L15.6514 15.2521L17.1771 19.9518L12.0008 16.1788ZM20.3976 10.102H13.972L11.9957 4.05078L10.0194 10.102H3.59375L8.79567 13.8494L6.81935 19.9006L12.0213 16.1532L15.2213 13.8545L20.3976 10.1071V10.102ZM38.0002 16.1788L41.6507 15.2521L43.1765 19.9518L38.0002 16.1788ZM46.4021 10.102H39.9765L38.0002 4.05078L36.0238 10.102H29.5982L34.8002 13.8494L32.8238 19.9006L38.0258 16.1532L41.2257 13.8545L46.4021 10.1071V10.102ZM63.9995 16.1788L67.6501 15.2521L69.1758 19.9518L63.9995 16.1788ZM72.4014 10.102H65.9758L63.9995 4.05078L62.0232 10.102H55.5976L60.7995 13.8494L58.8232 19.9006L64.0251 16.1532L67.2251 13.8545L72.4014 10.1071V10.102ZM89.9989 16.1788L93.6494 15.2521L95.1752 19.9518L89.9989 16.1788ZM98.4008 10.102H91.9752L89.9989 4.05078L88.0226 10.102H81.597L86.7989 13.8494L84.8226 19.9006L90.0245 16.1532L93.2245 13.8545L98.4008 10.1071V10.102ZM115.998 16.1788L119.649 15.2521L121.175 19.9518L115.998 16.1788ZM124.4 10.102H117.975L115.998 4.05078L114.022 10.102H107.596L112.798 13.8494L110.822 19.9006L116.024 16.1532L119.224 13.8545L124.4 10.1071V10.102Z" fill="white"/></svg>';
	}

	private function render_verified_check_svg() {
		return '<svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4.875 0C3.91082 0 2.96829 0.285914 2.1666 0.821586C1.36491 1.35726 0.740067 2.11863 0.37109 3.00942C0.00211226 3.90021 -0.094429 4.88041 0.093674 5.82607C0.281777 6.77172 0.746076 7.64037 1.42786 8.32215C2.10964 9.00393 2.97828 9.46823 3.92394 9.65633C4.8696 9.84443 5.84979 9.74789 6.74058 9.37891C7.63137 9.00994 8.39275 8.3851 8.92842 7.58341C9.46409 6.78172 9.75 5.83918 9.75 4.875C9.74864 3.58249 9.23459 2.34331 8.32064 1.42936C7.4067 0.515418 6.16751 0.00136492 4.875 0ZM7.01532 4.01531L4.39032 6.64031C4.35549 6.67518 4.31413 6.70284 4.26861 6.72171C4.22308 6.74058 4.17428 6.7503 4.125 6.7503C4.07572 6.7503 4.02692 6.74058 3.9814 6.72171C3.93588 6.70284 3.89452 6.67518 3.85969 6.64031L2.73469 5.51531C2.66432 5.44495 2.62479 5.34951 2.62479 5.25C2.62479 5.15049 2.66432 5.05505 2.73469 4.98469C2.80506 4.91432 2.90049 4.87479 3 4.87479C3.09951 4.87479 3.19495 4.91432 3.26532 4.98469L4.125 5.84484L6.48469 3.48469C6.51953 3.44985 6.56089 3.42221 6.60642 3.40335C6.65194 3.3845 6.70073 3.37479 6.75 3.37479C6.79928 3.37479 6.84807 3.3845 6.89359 3.40335C6.93911 3.42221 6.98047 3.44985 7.01532 3.48469C7.05016 3.51953 7.07779 3.56089 7.09665 3.60641C7.11551 3.65194 7.12521 3.70073 7.12521 3.75C7.12521 3.79927 7.11551 3.84806 7.09665 3.89359C7.07779 3.93911 7.05016 3.98047 7.01532 4.01531Z" fill="#16A34A"/></svg>';
	}

	private function render_style1_slide( $item ) {
		if ( empty( $item['author_name'] ) && empty( $item['title'] ) ) {
			return '';
		}
		ob_start();
		?>
		<div class="swiper-slide">
			<div class="testimonial-v01">
				<div class="tes-ic"><?php echo $this->render_stars_svg( 'sm' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static trusted SVG markup ?></div>
				<?php if ( ! empty( $item['title'] ) ) : ?>
					<p class="tes-name h6"><?php echo esc_html( $item['title'] ); ?></p>
				<?php endif; ?>
				<?php if ( ! empty( $item['text'] ) ) : ?>
					<p class="tes-text"><?php echo esc_html( $item['text'] ); ?></p>
				<?php endif; ?>
				<div class="tes-author">
					<div class="author-verified">
						<?php if ( ! empty( $item['author_name'] ) ) : ?>
							<p class="author_name cl-text-main lh-24 fw-medium"><?php echo esc_html( $item['author_name'] ); ?></p>
						<?php endif; ?>
						<?php if ( 'yes' === ( $item['verified'] ?? '' ) ) : ?>
							<span class="br-line"></span>
							<div class="verified-check">
								<span class="icon d-flex"><?php echo $this->render_verified_check_svg(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static trusted SVG markup ?></span>
								<span class="text-caption-02"><?php esc_html_e( 'Verified buyer', 'ovinx-addon' ); ?></span>
							</div>
						<?php endif; ?>
					</div>
					<?php if ( ! empty( $item['author_duty'] ) ) : ?>
						<p class="author_duty text-caption-02"><?php echo esc_html( $item['author_duty'] ); ?></p>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * `.tes-prd` is a real, purchasable WooCommerce product — image/name/
	 * permalink/price all pulled live, same "never fake a price" rule this
	 * plugin follows everywhere else. Ground truth's price markup here
	 * (`.price-new`/plain muted span, no shared `.price-wrap` div) doesn't
	 * match the theme's shared `tfwc_price()` partial's classes closely
	 * enough to reuse directly, so it's rebuilt with the *same* real-price
	 * logic `woocommerce/tpl/product-base/price.php` uses (simple + on-sale
	 * check via `wc_get_price_to_display()`) but this card's own classes.
	 */
	private function render_tes_product( $product_id ) {
		if ( empty( $product_id ) ) {
			return;
		}

		$product = wc_get_product( absint( $product_id ) );

		if ( ! $product ) {
			return;
		}
		?>
		<div class="tes-prd">
			<div class="prd-image">
				<?php echo wp_get_attachment_image( $product->get_image_id(), 'thumbnail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core function, already escaped ?>
			</div>
			<div class="prd-infor">
				<a href="<?php echo esc_url( $product->get_permalink() ); ?>" class="infor_name cl-text-main fw-medium link-underline"><?php echo esc_html( $product->get_name() ); ?></a>
				<div class="price-wrap">
					<?php if ( $product->is_type( 'simple' ) && $product->is_on_sale() ) : ?>
						<span class="price-new text-label fw-semibold cl-text-main"><?php echo wp_kses_post( wc_price( wc_get_price_to_display( $product, [ 'price' => $product->get_sale_price() ] ) ) ); ?></span>
						<span class="text-caption-02 cl-text-mute"><?php echo wp_kses_post( wc_price( wc_get_price_to_display( $product, [ 'price' => $product->get_regular_price() ] ) ) ); ?></span>
					<?php else : ?>
						<span class="price-new text-label fw-semibold cl-text-main"><?php echo wp_kses_post( $product->get_price_html() ); ?></span>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
	}

	private function get_media_url( $item, $image_key ) {
		if ( ! empty( $item[ $image_key ]['url'] ) ) {
			return $item[ $image_key ]['url'];
		}

		return '';
	}

	private function render_style2_slide( $item ) {
		if ( empty( $item['author_name'] ) && empty( $item['title'] ) ) {
			return '';
		}

		$avatar_url = $this->get_media_url( $item, 'author_avatar' );

		ob_start();
		?>
		<div class="swiper-slide">
			<div class="testimonial-v02">
				<div class="tes-author">
					<?php if ( ! empty( $avatar_url ) ) : ?>
						<div class="author-image">
							<img loading="lazy" width="42" height="42" src="<?php echo esc_url( $avatar_url ); ?>" alt="<?php echo esc_attr( $item['author_name'] ?? '' ); ?>">
						</div>
					<?php endif; ?>
					<div class="author-infor">
						<div class="name-verified">
							<?php if ( ! empty( $item['author_name'] ) ) : ?>
								<p class="infor_name text-white fw-medium lh-24"><?php echo esc_html( $item['author_name'] ); ?></p>
							<?php endif; ?>
							<?php if ( 'yes' === ( $item['verified'] ?? '' ) ) : ?>
								<span class="br-line"></span>
								<div class="verified-check">
									<span class="icon d-flex"><?php echo $this->render_verified_check_svg(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static trusted SVG markup ?></span>
									<span class="text-caption-02 cl-text-mute"><?php esc_html_e( 'Verified buyer', 'ovinx-addon' ); ?></span>
								</div>
							<?php endif; ?>
						</div>
						<?php if ( ! empty( $item['author_duty'] ) ) : ?>
							<p class="infor_duty text-caption-02 cl-text-mute"><?php echo esc_html( $item['author_duty'] ); ?></p>
						<?php endif; ?>
					</div>
				</div>
				<div class="content">
					<div class="tes-icon d-flex"><?php echo $this->render_stars_svg( 'sm' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static trusted SVG markup ?></div>
					<?php if ( ! empty( $item['title'] ) ) : ?>
						<p class="tes-title h6"><?php echo esc_html( $item['title'] ); ?></p>
					<?php endif; ?>
					<?php if ( ! empty( $item['text'] ) ) : ?>
						<p class="tes-text"><?php echo esc_html( $item['text'] ); ?></p>
					<?php endif; ?>
					<?php $this->render_tes_product( $item['product_id'] ?? '' ); ?>
				</div>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$style    = ! empty( $settings['style'] ) ? $settings['style'] : 'style_1';
		$is_style_2 = 'style_2' === $style;

		$items = ! empty( $settings['testimonial_items'] ) && is_array( $settings['testimonial_items'] ) ? $settings['testimonial_items'] : [];

		$slides_html = '';
		foreach ( $items as $item ) {
			$slides_html .= $is_style_2 ? $this->render_style2_slide( $item ) : $this->render_style1_slide( $item );
		}

		if ( empty( $slides_html ) ) {
			return;
		}

		/**
		 * Ground truth's Style 1 uses `data-space-xl="30" data-space-lg="20"
		 * data-space="15"`, Style 2 uses `data-space-lg="20" data-space="15"`
		 * (no xl value) — translated onto `slider-core.js`'s real breakpoints
		 * the same way as every other widget this session.
		 */
		$space_attrs = $this->get_swiper_space_attrs( $settings, $is_style_2 ? [ 15, 15, 15, 20, 20 ] : [ 15, 15, 15, 20, 30 ] );
		?>
		<?php if ( $is_style_2 ) : ?>
			<div class="themesFlat">
				<div class="container">
					<?php if ( ! empty( $settings['title'] ) || ! empty( $settings['view_all_link']['url'] ) ) :
						$has_link = ! empty( $settings['view_all_link']['url'] );
						if ( $has_link ) {
							$this->add_link_attributes( 'view_all_link', $settings['view_all_link'] );
						}
						?>
						<div class="sect-heading type-2">
							<?php if ( ! empty( $settings['title'] ) ) : ?>
								<h4 class="s-title"><?php echo esc_html( $settings['title'] ); ?></h4>
							<?php endif; ?>
							<?php if ( $has_link ) : ?>
								<a <?php $this->print_render_attribute_string( 'view_all_link' ); ?> class="tf-btn-line line-primary">
									<span class="text-uppercase fw-semibold"><?php echo esc_html( $settings['view_all_text'] ); ?></span>
								</a>
							<?php endif; ?>
						</div>
					<?php endif; ?>
					<div dir="ltr" class="swiper tf-swiper tfc-swiper"
						data-laptop="<?php echo esc_attr( $settings['items_desktop'] ); ?>"
						data-preview="<?php echo esc_attr( $settings['items_laptop'] ); ?>"
						data-tablet="<?php echo esc_attr( $settings['items_tablet'] ); ?>"
						data-mobile-sm="<?php echo esc_attr( $settings['items_mobile'] ); ?>"
						data-mobile="<?php echo esc_attr( $settings['items_mobile_sm'] ); ?>"
						<?php echo $this->get_swiper_group_attrs( $settings ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built with esc_attr() in the trait ?>
						<?php echo $space_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built with esc_attr() in the trait ?>>
						<div class="swiper-wrapper">
							<?php echo $slides_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from escaped output in render_style2_slide() ?>
						</div>
						<div class="sw-line-default tf-sw-pagination"></div>
					</div>
				</div>
			</div>
		<?php else : ?>
			<div class="section-testimonial">
				<div class="container">
					<div class="row gy-30">
						<div class="col-sm-6 col-md-4 col-lg-3">
							<div class="testimonial-v01 style-2">
								<?php if ( ! empty( $settings['summary_title'] ) ) : ?>
									<p class="tes-name h4"><?php echo esc_html( $settings['summary_title'] ); ?></p>
								<?php endif; ?>
								<div class="tes-ic"><?php echo $this->render_stars_svg( 'lg' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static trusted SVG markup ?></div>
								<div class="tes-base tes-author">
									<div class="author-verified">
										<?php if ( ! empty( $settings['summary_rating_label'] ) ) : ?>
											<p class="author_name cl-text-main lh-24 fw-medium"><?php echo esc_html( $settings['summary_rating_label'] ); ?></p>
										<?php endif; ?>
										<?php if ( ! empty( $settings['summary_review_count'] ) ) : ?>
											<span class="br-line"></span>
											<p>
												<?php esc_html_e( 'Based on', 'ovinx-addon' ); ?>
												<span class="fw-medium cl-text-main text-decoration-underline"><?php echo esc_html( $settings['summary_review_count'] ); ?></span>
											</p>
										<?php endif; ?>
									</div>
								</div>
								<?php if ( ! empty( $settings['summary_desc'] ) ) : ?>
									<p class="tes-text mb-0"><?php echo esc_html( $settings['summary_desc'] ); ?></p>
								<?php endif; ?>
							</div>
						</div>
						<div class="col-sm-6 col-md-8 col-lg-9">
							<div dir="ltr" class="swiper tf-swiper tfc-swiper"
								data-laptop="<?php echo esc_attr( $settings['items_desktop'] ); ?>"
								data-preview="<?php echo esc_attr( $settings['items_laptop'] ); ?>"
								data-tablet="<?php echo esc_attr( $settings['items_tablet'] ); ?>"
								data-mobile-sm="<?php echo esc_attr( $settings['items_mobile'] ); ?>"
								data-mobile="<?php echo esc_attr( $settings['items_mobile_sm'] ); ?>"
								<?php echo $this->get_swiper_group_attrs( $settings ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built with esc_attr() in the trait ?>
								<?php echo $space_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built with esc_attr() in the trait ?>
							>
								<div class="swiper-wrapper">
									<?php echo $slides_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from escaped output in render_style1_slide() ?>
								</div>
								<div class="sw-dot-default tf-sw-pagination d-xl-none"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php endif; ?>
		<?php
	}
}
