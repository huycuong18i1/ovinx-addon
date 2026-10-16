<?php

/**
 * Ground truth: `themes/ovinx-html/privacy-policy.html` / `term-and-condition.html`
 * `<section class="section-privacy flat-spacing-mix-1">`.
 *
 * Left (`col-sm-4`, hidden on phones): sticky `ul.list-name-privacy` whose links
 * (`a.click-scroll`) jump to a section and get `.active` while that section is
 * in view. Right (`col-sm-8`): `.box-privacy` blocks (`h5.privacy-title` +
 * `p.privacy-text`).
 *
 * The nav is generated from the same repeater as the sections, so link and
 * anchor can't drift apart. Click-scroll + active-state live in
 * `assets/js/privacy-policy.js` (a scoped rebuild of ground truth's
 * `main.js` `clickScroll()` + `privacy()`).
 *
 * Defaults are the 5 "Terms of Use" sections of the HTML export.
 */
class ovinx_Privacy_Policy extends \Elementor\Widget_Base {

	public function get_name() {
		return 'ovinx_privacy_policy';
	}

	public function get_title() {
		return esc_html__( 'Ovinx Privacy Policy', 'ovinx-addon' );
	}

	public function get_icon() {
		return 'eicon-text-area';
	}

	public function get_categories() {
		return [ 'ovinx_addons_core' ];
	}

	public function get_keywords() {
		return [ 'privacy', 'policy', 'terms', 'conditions', 'legal' ];
	}

	public function get_script_depends() {
		return [ 'privacy-policy-core' ];
	}

	/**
	 * The 5 default sections from the HTML export. Paragraph text is kept
	 * verbatim; `anchor` / `nav` are the HTML's `id` and nav-link label.
	 */
	private function get_default_sections() {
		$sections = [
			[
				'anchor'     => 'term',
				'nav'        => '1. Terms',
				'title'      => '1. Terms',
				'paragraphs' => [
					"By accessing and using this website, you confirm that you are at least 18 years of age and agree to be bound by these Terms of Use. These terms apply to all visitors, customers, and users who access or make purchases through our online auto parts store.",
					"All products listed on this site are intended for use by qualified individuals with appropriate mechanical knowledge. We reserve the right to refuse service, cancel orders, or restrict access to any user who violates these terms or engages in fraudulent activity. Your continued use of this website constitutes your acceptance of any updates to these terms.",
					"It is your responsibility to ensure that any part purchased is suitable for your specific vehicle. We strongly recommend verifying compatibility using your vehicle's VIN or consulting with our parts specialists before placing an order.",
				],
			],
			[
				'anchor'     => 'limitations',
				'nav'        => '2. Limitations',
				'title'      => '2. Limitations',
				'paragraphs' => [
					"To the fullest extent permitted by law, our liability for any claim arising from the purchase or use of auto parts through this website shall be limited to the original purchase price of the part in question. We shall not be liable for any indirect, incidental, or consequential damages.",
					"We are not responsible for damage caused by incorrect installation, misuse, or use of a part in a manner inconsistent with its intended purpose.",
					"Warranty claims are subject to the terms specified by the individual manufacturer and do not cover damage resulting from negligence or modification.",
					"We do not guarantee uninterrupted availability of the website or that all product listings will be free of errors at all times.",
					"Nothing in these limitations shall exclude liability for death or personal injury caused by negligence, or for any other matter that cannot be excluded or limited under applicable law.",
				],
			],
			[
				'anchor'     => 'revisions',
				'nav'        => '3. Revisions and errata',
				'title'      => '3. Revisions and Errata',
				'paragraphs' => [
					"The information contained on this website, including product descriptions, compatibility data, pricing, and specifications, is provided for general informational purposes. While we strive for accuracy, we do not warrant that all content is complete, current, or free of errors at all times.",
					"Part numbers, fitment data, and technical specifications are updated regularly, but occasional discrepancies may occur due to manufacturer changes or catalogue updates. We reserve the right to correct any errors, inaccuracies, or omissions at any time without prior notice, including after an order has been submitted.",
					"If you believe a product listing contains incorrect information, please contact our support team before completing your purchase. We will investigate and, where necessary, update the listing or provide clarification to ensure you receive the correct part for your vehicle.",
				],
			],
			[
				'anchor'     => 'modifications',
				'nav'        => '4. Site terms of use modifications',
				'title'      => '4. Site Terms of Use Modifications',
				'paragraphs' => [
					"We reserve the right to update, modify, or replace any part of these Terms of Use at any time at our sole discretion. Changes will take effect immediately upon being posted to this page.",
					"It is your responsibility to review these Terms of Use periodically for any changes. Your continued use of our website or services following the posting of any modifications constitutes your acceptance of the revised terms.",
					"We may also update the structure, content, or features of this website at any time without notice, including the addition or removal of product categories, payment options, or delivery services.",
					"If you do not agree with any changes to these terms, you should discontinue use of the website and contact our customer service team if you have any outstanding orders or enquiries.",
					"The most current version of these Terms of Use will always be available on this page, along with the date of the most recent revision for your reference.",
				],
			],
			[
				'anchor'     => 'risks',
				'nav'        => '5. Risks',
				'title'      => '5. Risks',
				'paragraphs' => [
					"The purchase and installation of auto parts carries inherent risks. Improper fitment, use of an incompatible part, or installation by an unqualified individual may result in vehicle damage, mechanical failure, or personal injury. You assume full responsibility for ensuring that any part purchased is appropriate for your vehicle and installed correctly.",
					"We strongly recommend that all parts are installed by a licensed and qualified mechanic, particularly for safety-critical components such as braking systems, suspension parts, steering components, and engine assemblies. Failure to do so may void the manufacturer's warranty and could result in serious harm to yourself or others.",
					"By completing a purchase on this website, you acknowledge that you have read and understood these risks, and that our company bears no responsibility for any outcomes arising from improper use or installation of purchased parts.",
				],
			],
		];

		$defaults = [];
		foreach ( $sections as $section ) {
			$defaults[] = [
				'title'     => $section['title'],
				'nav_label' => $section['nav'],
				'anchor'    => $section['anchor'],
				'content'   => '<p>' . implode( '</p><p>', $section['paragraphs'] ) . '</p>',
			];
		}

		return $defaults;
	}

	protected function register_controls() {

		/**
		 * Sections
		 */
		$this->start_controls_section(
			'section_sections',
			[
				'label' => esc_html__( 'Sections', 'ovinx-addon' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'show_nav',
			[
				'label'        => esc_html__( 'Side Navigation', 'ovinx-addon' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'ovinx-addon' ),
				'label_off'    => esc_html__( 'Hide', 'ovinx-addon' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'title',
			[
				'label'       => esc_html__( 'Title', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Section title', 'ovinx-addon' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'nav_label',
			[
				'label'       => esc_html__( 'Navigation Label (empty = Title)', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'anchor',
			[
				'label'       => esc_html__( 'Anchor ID (empty = from Title)', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => esc_html__( 'Unique per page, letters/numbers/dashes only.', 'ovinx-addon' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'content',
			[
				'label'   => esc_html__( 'Content', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::WYSIWYG,
				'default' => '',
			]
		);

		$this->add_control(
			'sections',
			[
				'label'       => esc_html__( 'Sections', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ title }}}',
				'default'     => $this->get_default_sections(),
			]
		);

		$this->end_controls_section();

		/**
		 * Navigation Style
		 */
		$this->start_controls_section(
			'section_style_nav',
			[
				'label'     => esc_html__( 'Navigation', 'ovinx-addon' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [ 'show_nav' => 'yes' ],
			]
		);

		$this->add_control(
			'nav_color',
			[
				'label'     => esc_html__( 'Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .list-name-privacy a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'nav_line_color',
			[
				'label'     => esc_html__( 'Line Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .list-name-privacy a' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'nav_active_color',
			[
				'label'     => esc_html__( 'Active Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .list-name-privacy a.active' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'nav_active_line_color',
			[
				'label'     => esc_html__( 'Active Line Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .list-name-privacy a.active' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'nav_typography',
				'selector' => '{{WRAPPER}} .list-name-privacy a',
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
					'{{WRAPPER}} .box-privacy .privacy-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .box-privacy .privacy-title',
			]
		);

		$this->end_controls_section();

		/**
		 * Text Style
		 */
		$this->start_controls_section(
			'section_style_text',
			[
				'label' => esc_html__( 'Text', 'ovinx-addon' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'text_color',
			[
				'label'     => esc_html__( 'Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .box-privacy .privacy-text, {{WRAPPER}} .box-privacy li' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'text_typography',
				'selector' => '{{WRAPPER}} .box-privacy .privacy-text, {{WRAPPER}} .box-privacy li',
			]
		);

		$this->end_controls_section();
	}

	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$sections = ! empty( $settings['sections'] ) && is_array( $settings['sections'] ) ? $settings['sections'] : [];

		// Resolve each section's anchor once so the nav link and the block share it.
		$prepared = [];
		$used_ids = [];
		foreach ( $sections as $index => $section ) {
			if ( empty( $section['title'] ) && empty( $section['content'] ) ) {
				continue;
			}

			$anchor = ! empty( $section['anchor'] ) ? sanitize_title( $section['anchor'] ) : sanitize_title( $section['title'] );
			if ( '' === $anchor ) {
				$anchor = 'section-' . ( $index + 1 );
			}
			// Keep ids unique inside this widget.
			$base = $anchor;
			$n    = 2;
			while ( in_array( $anchor, $used_ids, true ) ) {
				$anchor = $base . '-' . $n++;
			}
			$used_ids[] = $anchor;

			$section['_anchor'] = $anchor;
			$section['_nav']    = ! empty( $section['nav_label'] ) ? $section['nav_label'] : $section['title'];
			$prepared[]         = $section;
		}

		if ( empty( $prepared ) ) {
			return;
		}

		$show_nav = 'yes' === $settings['show_nav'];
		?>
		<section class="section-privacy flat-spacing-mix-1 tf-privacy">
			<div class="container">
				<div class="row">
					<?php if ( $show_nav ) : ?>
						<div class="col-sm-4 d-none d-sm-block">
							<div class="col-left sticky-top" style="top: 15px;">
								<ul class="list-name-privacy">
									<?php foreach ( $prepared as $section ) : ?>
										<li class="item">
											<a href="#<?php echo esc_attr( $section['_anchor'] ); ?>" class="click-scroll text-body-01 fw-medium"><?php echo esc_html( $section['_nav'] ); ?></a>
										</li>
									<?php endforeach; ?>
								</ul>
							</div>
						</div>
					<?php endif; ?>
					<div class="<?php echo $show_nav ? 'col-sm-8' : 'col-12'; ?>">
						<div class="col-right">
							<?php foreach ( $prepared as $section ) :
								// Ground truth's paragraphs carry `.privacy-text`; add it to bare <p> from the editor.
								$content = preg_replace( '/<p(?![^>]*\bclass=)/i', '<p class="privacy-text"', (string) $section['content'] );
								?>
								<div class="box-privacy" id="<?php echo esc_attr( $section['_anchor'] ); ?>">
									<?php if ( ! empty( $section['title'] ) ) : ?>
										<h5 class="privacy-title"><?php echo esc_html( $section['title'] ); ?></h5>
									<?php endif; ?>
									<?php echo wp_kses_post( $content ); ?>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
			</div>
		</section>
		<?php
	}
}
