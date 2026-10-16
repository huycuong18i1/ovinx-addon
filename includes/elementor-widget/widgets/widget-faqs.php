<?php

/**
 * Ground truth: `themes/ovinx-html/faq.html` `<section class="section-faq flat-spacing-mix-1">`.
 *
 * Left (`col-lg-8`): `ul.faq-list` of groups (`li.faq-item` > `h4.faq_title` +
 * `.faq_wrap` of Bootstrap-collapse `.accordion-faq` rows). Right (`col-lg-4`):
 * sticky "Ask Your Question" box (`.form-get-v2`).
 *
 * Elementor repeaters cannot be nested, so the FAQ list is ONE flat repeater:
 * an item with a "Group Title" starts a new group; items without one continue
 * the current group. Defaults are the 15 questions / 3 groups of the HTML export.
 *
 * The form is the user's Contact Form 7 shortcode (same convention as
 * `widget-contact.php` / `widget-contact-support.php`) placed under the
 * `.form-title` bar inside `.form-get-v2`; its template should output the
 * `.form-content` fieldsets and `.btn-wrap` button of the ground truth.
 */
class ovinx_Faqs extends \Elementor\Widget_Base {

	public function get_name() {
		return 'ovinx_faqs';
	}

	public function get_title() {
		return esc_html__( 'Ovinx FAQs', 'ovinx-addon' );
	}

	public function get_icon() {
		return 'eicon-help-o';
	}

	public function get_categories() {
		return [ 'ovinx_addons_core' ];
	}

	public function get_keywords() {
		return [ 'faq', 'faqs', 'accordion', 'question', 'help' ];
	}

	/**
	 * The 15 default Q&As from `faq.html` (3 groups x 5). `group` only on the
	 * first item of each group; `open` = rendered expanded (ground truth opens
	 * Q2 of group 1).
	 */
	private function get_default_faqs() {
		$items = [
			[
				'group'    => 'Auto Parts & Ordering',
				'question' => "How do I find the correct auto part for my vehicle?",
				'answer'   => [
					"The easiest way to find the right part is to use your vehicle's Year, Make, Model, and Engine size (also known as YMM). You can enter these details in our search bar or use the vehicle selector tool on our website to filter compatible parts automatically.",
					"If you're unsure, you can also search by your vehicle's VIN (Vehicle Identification Number), which provides the most accurate match. Our support team is also available to help you verify compatibility before you place your order.",
				],
			],
			[
				'question' => "Do you offer bulk or wholesale pricing for large orders?",
				'open'     => 'yes',
				'answer'   => [
					"Yes, we offer competitive bulk and wholesale pricing for garages, repair shops, fleet operators, and dealerships. Whether you need regular stock replenishment or a one-time large purchase, our team can tailor a pricing plan to suit your volume and budget.",
					"To get started, contact our wholesale department with your business details, the parts you need, and your estimated monthly or annual quantities. Wholesale clients also enjoy priority order processing and a dedicated account manager.",
				],
			],
			[
				'question' => "What payment methods do you accept?",
				'answer'   => [
					"We accept a wide range of payment methods including major credit and debit cards (Visa, Mastercard, American Express), PayPal, and bank transfers for wholesale orders. All online transactions are secured with SSL encryption to protect your payment information.",
					"For approved business accounts, we also offer net payment terms. Please contact our accounts team to apply for a credit account and discuss suitable payment arrangements for your business.",
				],
			],
			[
				'question' => "Are your parts OEM, aftermarket, or both?",
				'answer'   => [
					"We stock both OEM (Original Equipment Manufacturer) and high-quality aftermarket parts. OEM parts are made by the vehicle's manufacturer and are an exact match to your original components. Aftermarket parts are produced by third-party manufacturers and can offer cost savings while still meeting performance standards.",
					"Each product listing clearly indicates whether the part is OEM or aftermarket, along with brand information and compatibility notes, so you can make an informed decision based on your needs and budget.",
				],
			],
			[
				'question' => "How far in advance should I place my order?",
				'answer'   => [
					"For in-stock items, orders placed before our daily cut-off time are typically dispatched the same day. We recommend ordering as early as possible if you have an urgent repair, especially for less common or specialty parts that may require additional sourcing time.",
					"For bulk or wholesale orders, we advise placing your order at least 5–7 business days in advance to ensure availability and allow time for any custom sourcing. You can check real-time stock availability on each product page.",
				],
			],
			[
				'group'    => 'Shipping & Delivery',
				'question' => "Do you offer nationwide delivery for auto parts?",
				'answer'   => [
					"Yes, we offer nationwide delivery across the country. Most standard orders are delivered within 2–5 business days depending on your location. Express and next-day delivery options are also available at checkout for urgent requirements.",
					"For remote or rural areas, delivery times may vary slightly. You can enter your postcode at checkout to see the available delivery options and estimated timeframes for your specific location.",
				],
			],
			[
				'question' => "What days of the week do you dispatch orders?",
				'answer'   => [
					"We dispatch orders Monday through Friday. Orders placed before 3:00 PM on a business day are usually processed and shipped the same day. Orders received after this cut-off, or on weekends and public holidays, will be dispatched on the next available business day.",
					"You will receive an email confirmation with a tracking number as soon as your order has been dispatched, so you can monitor your delivery in real time.",
				],
			],
			[
				'question' => "How are auto parts packaged to prevent damage during shipping?",
				'answer'   => [
					"All parts are carefully packaged using industry-appropriate materials. Fragile or sensitive components such as sensors, filters, and glass parts are wrapped in protective foam and secured in reinforced boxes to minimise the risk of damage in transit.",
					"Larger or heavier parts like exhaust systems or body panels are palletised where necessary and shipped with specialist freight carriers experienced in handling automotive components. If you receive a damaged item, please contact us within 48 hours with photos and we will arrange a replacement promptly.",
				],
			],
			[
				'question' => "Can I change my delivery address after placing an order?",
				'answer'   => [
					"Address changes can be made if the order has not yet been dispatched. Please contact our customer service team as soon as possible with your order number and the updated delivery address. We will do our best to make the change before the parcel leaves our warehouse.",
					"Once an order has been dispatched and is in transit, we are unable to redirect it. In this case, you may need to arrange redelivery directly with the courier using the tracking information provided in your dispatch email.",
				],
			],
			[
				'question' => "What should I do if my delivery is late or incomplete?",
				'answer'   => [
					"If your order has not arrived within the estimated delivery window, please first check the tracking link sent to your email for the latest status update. Occasionally, delays can occur due to courier backlogs or logistics issues outside our control.",
					"If your tracking shows no movement for more than 48 hours, or if items are missing from your delivery, please contact our support team with your order number. We will investigate immediately and either expedite the remaining items or arrange a replacement shipment at no additional cost.",
				],
			],
			[
				'group'    => 'Support & Product Info',
				'question' => "How can I contact your customer support team?",
				'answer'   => [
					"Our customer support team is available Monday to Friday, 8:00 AM – 6:00 PM. You can reach us by phone, email, or through the live chat feature on our website. We aim to respond to all enquiries within one business day.",
					"For technical questions about fitment or installation, our specialist parts advisors are on hand to help. Simply describe your vehicle and the issue you're experiencing and we'll guide you to the right solution.",
				],
			],
			[
				'question' => "What is your return and warranty policy?",
				'answer'   => [
					"We offer a 30-day return policy on most parts, provided they are in their original, unused condition and in the original packaging. If a part doesn't fit your vehicle due to an incorrect listing on our site, we will cover the return shipping cost and process a full refund or exchange.",
					"Many of our parts also come with a manufacturer's warranty ranging from 12 months to 3 years depending on the brand and part type. Warranty details are listed on each product page. For warranty claims, please contact our support team with your order details and a description of the issue.",
				],
			],
			[
				'question' => "Are your auto parts compatible with all vehicle makes and models?",
				'answer'   => [
					"Our catalogue covers a wide range of domestic and imported vehicles including passenger cars, light commercial vehicles, and 4WDs. Each product listing includes a detailed compatibility list, and our vehicle selector tool allows you to filter parts specifically for your make, model, and year.",
					"If you cannot find your vehicle in the search, it's possible we stock the part but the fitment hasn't been listed yet. Contact our parts team with your vehicle details and we'll check availability and compatibility for you directly.",
				],
			],
			[
				'question' => "Do you provide installation guides or technical support?",
				'answer'   => [
					"Yes, many of our products come with installation instructions either in the box or available as a downloadable PDF on the product page. We also link to relevant video tutorials where available to help guide both professionals and DIY mechanics through the fitting process.",
					"If you need further technical guidance, our parts advisors can assist over the phone or via email. We recommend consulting a qualified mechanic for complex repairs or if you are unsure about any aspect of the installation.",
				],
			],
			[
				'question' => "How can I stay updated on new stock and promotions?",
				'answer'   => [
					"Subscribe to our newsletter to receive the latest updates on new parts arrivals, seasonal promotions, and exclusive discounts straight to your inbox. You can sign up using the form at the bottom of our website — it only takes a few seconds.",
					"You can also follow us on social media for daily tips, product highlights, and flash sales. Registered account holders additionally receive early access to clearance deals and loyalty rewards on repeat purchases.",
				],
			],
		];

		$defaults = [];
		foreach ( $items as $item ) {
			$defaults[] = [
				'group_title' => isset( $item['group'] ) ? $item['group'] : '',
				'question'    => $item['question'],
				'answer'      => '<p>' . implode( '</p><p>', $item['answer'] ) . '</p>',
				'open'        => isset( $item['open'] ) ? $item['open'] : '',
			];
		}

		return $defaults;
	}

	protected function register_controls() {

		/**
		 * FAQs
		 */
		$this->start_controls_section(
			'section_faqs',
			[
				'label' => esc_html__( 'FAQs', 'ovinx-addon' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'group_title',
			[
				'label'       => esc_html__( 'Group Title', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => esc_html__( 'Fill in to start a new group with this heading. Leave empty to continue the group above.', 'ovinx-addon' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'question',
			[
				'label'       => esc_html__( 'Question', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'Question', 'ovinx-addon' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'answer',
			[
				'label'   => esc_html__( 'Answer', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::WYSIWYG,
				'default' => '',
			]
		);

		$repeater->add_control(
			'open',
			[
				'label'        => esc_html__( 'Open by Default', 'ovinx-addon' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			]
		);

		$this->add_control(
			'faqs',
			[
				'label'       => esc_html__( 'Questions', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ group_title ? "[" + group_title + "] " : "" }}}{{{ question }}}',
				'default'     => $this->get_default_faqs(),
			]
		);

		$this->end_controls_section();

		/**
		 * Question form (right column)
		 */
		$this->start_controls_section(
			'section_form',
			[
				'label' => esc_html__( 'Question Form', 'ovinx-addon' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'show_form',
			[
				'label'        => esc_html__( 'Show Form Box', 'ovinx-addon' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'ovinx-addon' ),
				'label_off'    => esc_html__( 'Hide', 'ovinx-addon' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'form_title',
			[
				'label'       => esc_html__( 'Title', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Ask Your Question', 'ovinx-addon' ),
				'label_block' => true,
				'condition'   => [ 'show_form' => 'yes' ],
			]
		);

		$this->add_control(
			'shortcode',
			[
				'label'       => esc_html__( 'Form Shortcode', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'placeholder' => '[contact-form-7 id="..." title="..."]',
				'description' => esc_html__( 'Paste your Contact Form 7 shortcode here.', 'ovinx-addon' ),
				'condition'   => [ 'show_form' => 'yes' ],
			]
		);

		$this->end_controls_section();

		/**
		 * Style — group title
		 */
		$this->start_controls_section(
			'section_style_group',
			[
				'label' => esc_html__( 'Group Title', 'ovinx-addon' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'group_color',
			[
				'label'     => esc_html__( 'Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .faq-list .faq_title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'group_typography',
				'selector' => '{{WRAPPER}} .faq-list .faq_title',
			]
		);

		$this->end_controls_section();

		/**
		 * Style — question
		 */
		$this->start_controls_section(
			'section_style_question',
			[
				'label' => esc_html__( 'Question', 'ovinx-addon' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'question_color',
			[
				'label'     => esc_html__( 'Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .accordion-faq .accordion-action .text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'question_typography',
				'selector' => '{{WRAPPER}} .accordion-faq .accordion-action .text',
			]
		);

		$this->end_controls_section();

		/**
		 * Style — answer
		 */
		$this->start_controls_section(
			'section_style_answer',
			[
				'label' => esc_html__( 'Answer', 'ovinx-addon' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'answer_color',
			[
				'label'     => esc_html__( 'Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .accordion-faq .accordion-body, {{WRAPPER}} .accordion-faq .accordion-body p' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'answer_typography',
				'selector' => '{{WRAPPER}} .accordion-faq .accordion-body, {{WRAPPER}} .accordion-faq .accordion-body p',
			]
		);

		$this->end_controls_section();

		/**
		 * Style — form box
		 */
		$this->start_controls_section(
			'section_style_form',
			[
				'label'     => esc_html__( 'Form Box', 'ovinx-addon' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [ 'show_form' => 'yes' ],
			]
		);

		$this->add_control(
			'form_title_color',
			[
				'label'     => esc_html__( 'Title Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .form-get-v2 .form-title' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'form_title_bg',
			[
				'label'     => esc_html__( 'Title Background', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .form-get-v2 .form-title' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Group the flat repeater into [ [ 'title' => , 'items' => [ ... ] ], ... ].
	 */
	private function get_groups( array $faqs ) {
		$groups = [];

		foreach ( $faqs as $item ) {
			if ( empty( $item['question'] ) ) {
				continue;
			}

			if ( ! empty( $item['group_title'] ) || empty( $groups ) ) {
				$groups[] = [
					'title' => ! empty( $item['group_title'] ) ? $item['group_title'] : '',
					'items' => [],
				];
			}

			$groups[ count( $groups ) - 1 ]['items'][] = $item;
		}

		return $groups;
	}

	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$faqs     = ! empty( $settings['faqs'] ) && is_array( $settings['faqs'] ) ? $settings['faqs'] : [];
		$groups   = $this->get_groups( $faqs );

		$show_form = 'yes' === $settings['show_form'] && ( ! empty( $settings['form_title'] ) || ! empty( $settings['shortcode'] ) );

		if ( empty( $groups ) && ! $show_form ) {
			return;
		}

		$uid = $this->get_id();
		?>
		<section class="section-faq flat-spacing-mix-1 tf-faq">
			<div class="container">
				<div class="row gy-30">
					<div class="<?php echo $show_form ? 'col-lg-8' : 'col-12'; ?>">
						<ul class="faq-list">
							<?php foreach ( $groups as $g => $group ) :
								$wrap_id = 'faq-' . $uid . '-g' . $g;
								?>
								<li class="faq-item">
									<?php if ( '' !== $group['title'] ) : ?>
										<h4 class="faq_title"><?php echo esc_html( $group['title'] ); ?></h4>
									<?php endif; ?>
									<div class="faq_wrap" id="<?php echo esc_attr( $wrap_id ); ?>">
										<?php foreach ( $group['items'] as $i => $item ) :
											$item_id = $wrap_id . '-' . ( $i + 1 );
											$is_open = 'yes' === ( $item['open'] ?? '' );
											?>
											<div class="accordion-faq">
												<div class="accordion-action<?php echo $is_open ? '' : ' collapsed'; ?>" data-bs-target="#<?php echo esc_attr( $item_id ); ?>" role="button" data-bs-toggle="collapse" aria-expanded="<?php echo $is_open ? 'true' : 'false'; ?>" aria-controls="<?php echo esc_attr( $item_id ); ?>">
													<span class="text text-body-01"><?php echo esc_html( $item['question'] ); ?></span>
													<span class="ic-ar"><i class="icon-CaretDown"></i></span>
												</div>
												<div id="<?php echo esc_attr( $item_id ); ?>" class="collapse<?php echo $is_open ? ' show' : ''; ?>" data-bs-parent="#<?php echo esc_attr( $wrap_id ); ?>">
													<div class="accordion-body">
														<?php echo wp_kses_post( $item['answer'] ); ?>
													</div>
												</div>
											</div>
										<?php endforeach; ?>
									</div>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>

					<?php if ( $show_form ) : ?>
						<div class="col-lg-4">
							<div class="col-right_sidebar sticky-top" style="top: 15px;">
								<div class="form-get-v2 mb-40">
									<?php if ( ! empty( $settings['form_title'] ) ) : ?>
										<h6 class="form-title text-white text-center"><?php echo esc_html( $settings['form_title'] ); ?></h6>
									<?php endif; ?>
									<?php if ( ! empty( $settings['shortcode'] ) ) : ?>
										<?php echo do_shortcode( $settings['shortcode'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- shortcode output (Contact Form 7) is trusted, admin-configured markup ?>
									<?php endif; ?>
								</div>
							</div>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</section>
		<?php
	}
}
