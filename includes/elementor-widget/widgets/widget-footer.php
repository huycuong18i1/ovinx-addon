<?php

/**
 * Ground truth: `themes/ovinx-html/` `<footer class="tf-footer footer-v2 bg-surface">`
 * (top bar with newsletter + social, contact/menu columns, bottom bar).
 *
 * Reuses what the theme's default footer already provides instead of
 * duplicating it: logo (`site_logo`) and contact info (`footer_contact_phone` /
 * `_address` / `_map_url` / `_email` Customizer options) are used whenever the
 * matching widget field is left empty, and the link columns are real
 * WordPress menus (Appearance > Menus) rather than typed-in links. The
 * newsletter form is a Contact Form 7 shortcode — its form template supplies
 * the `.form-sub` markup, same convention as the theme's `tpl/footer/newsletter.php`.
 *
 * The mobile accordion (`.footer-heading-mobile` / `.tf-collapse-content`)
 * is handled by the theme's `main.js`, so this widget needs no script.
 */
class ovinx_Footer extends \Elementor\Widget_Base {

	public function get_name() {
		return 'ovinx_footer';
	}

	public function get_title() {
		return esc_html__( 'Ovinx Footer', 'ovinx-addon' );
	}

	public function get_icon() {
		return 'eicon-footer';
	}

	public function get_categories() {
		return [ 'ovinx_addons_core' ];
	}

	public function get_keywords() {
		return [ 'footer', 'newsletter', 'menu', 'contact', 'social' ];
	}

	// Style v3's top info boxes are a `.tfc-swiper` (see `slider-core.js`).
	public function get_script_depends() {
		return [ 'themesflat-swiper', 'slider-core' ];
	}

	public function get_style_depends() {
		return [ 'themesflat-swiper' ];
	}

	private function get_menu_options() {
		$options = [ '' => esc_html__( '— Select a menu —', 'ovinx-addon' ) ];
		$menus   = wp_get_nav_menus();

		if ( ! is_wp_error( $menus ) ) {
			foreach ( $menus as $menu ) {
				$options[ $menu->term_id ] = $menu->name;
			}
		}

		return $options;
	}

	protected function register_controls() {

		/**
		 * `v2` = `.footer-v2` (light, newsletter bar + social on top);
		 * `v3` = `.footer-v3` (dark, phone/location/email info boxes on top,
		 * logo + description, social row above the bottom bar) — `home-03.html`
		 * / `home-04.html`.
		 */
		$this->start_controls_section(
			'section_layout',
			[
				'label' => esc_html__( 'Footer Style', 'ovinx-addon' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'footer_style',
			[
				'label'   => esc_html__( 'Style', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'v2' => esc_html__( 'Style 1 — Newsletter Bar (light)', 'ovinx-addon' ),
					'v3' => esc_html__( 'Style 2 — Info Boxes (dark)', 'ovinx-addon' ),
				],
				'default' => 'v2',
			]
		);

		$this->end_controls_section();

		/**
		 * Top bar (`.footer-top`) — newsletter (v2) or contact info boxes (v3)
		 */
		$this->start_controls_section(
			'section_top',
			[
				'label' => esc_html__( 'Top Bar', 'ovinx-addon' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'show_top',
			[
				'label'        => esc_html__( 'Show Top Bar', 'ovinx-addon' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'ovinx-addon' ),
				'label_off'    => esc_html__( 'No', 'ovinx-addon' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'newsletter_title',
			[
				'label'       => esc_html__( 'Newsletter Title', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Subscribe To Newsletter', 'ovinx-addon' ),
				'label_block' => true,
				'condition'   => [
					'show_top'     => 'yes',
					'footer_style' => 'v2',
				],
			]
		);

		$this->add_control(
			'newsletter_shortcode',
			[
				'label'       => esc_html__( 'Newsletter Form Shortcode', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => '[contact-form-7 id="123" title="Newsletter"]',
				'label_block' => true,
				'description' => esc_html__( 'Contact Form 7 shortcode. Its form template should output the .form-sub markup (input + SUBSCRIBE button).', 'ovinx-addon' ),
				'condition'   => [
					'show_top'     => 'yes',
					'footer_style' => 'v2',
				],
			]
		);

		/**
		 * v3 info boxes reuse the Contact section's phone / address / email
		 * (Customizer fallback); only their labels are typed here.
		 */
		$this->add_control(
			'info_phone_label',
			[
				'label'       => esc_html__( 'Phone Box Label', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Phone Number', 'ovinx-addon' ),
				'label_block' => true,
				'condition'   => [
					'show_top'     => 'yes',
					'footer_style' => 'v3',
				],
			]
		);

		$this->add_control(
			'info_address_label',
			[
				'label'       => esc_html__( 'Location Box Label', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Our Location', 'ovinx-addon' ),
				'label_block' => true,
				'condition'   => [
					'show_top'     => 'yes',
					'footer_style' => 'v3',
				],
			]
		);

		$this->add_control(
			'info_email_label',
			[
				'label'       => esc_html__( 'Email Box Label', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Email Address', 'ovinx-addon' ),
				'label_block' => true,
				'condition'   => [
					'show_top'     => 'yes',
					'footer_style' => 'v3',
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Social links — v2 prints them in the top bar (needs Show Top Bar),
		 * v3 in its own row above the bottom bar.
		 */
		/**
		 * Style 2's row above the bottom bar (`home-03.html`): currency +
		 * language dropdowns on the left, social on the right. The dropdowns
		 * are the theme's own topbar partials (`tpl/topbar/currency.php`,
		 * `language.php` — WOOCS / WPML aware, wired by `main.js`).
		 */
		$this->start_controls_section(
			'section_switchers',
			[
				'label'     => esc_html__( 'Currency & Language', 'ovinx-addon' ),
				'tab'       => \Elementor\Controls_Manager::TAB_CONTENT,
				'condition' => [ 'footer_style' => 'v3' ],
			]
		);

		$this->add_control(
			'show_currency',
			[
				'label'        => esc_html__( 'Show Currency Switcher', 'ovinx-addon' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'ovinx-addon' ),
				'label_off'    => esc_html__( 'No', 'ovinx-addon' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'show_language',
			[
				'label'        => esc_html__( 'Show Language Switcher', 'ovinx-addon' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'ovinx-addon' ),
				'label_off'    => esc_html__( 'No', 'ovinx-addon' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_social',
			[
				'label'      => esc_html__( 'Social Links', 'ovinx-addon' ),
				'tab'        => \Elementor\Controls_Manager::TAB_CONTENT,
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[ 'name' => 'footer_style', 'operator' => '===', 'value' => 'v3' ],
						[ 'name' => 'show_top', 'operator' => '===', 'value' => 'yes' ],
					],
				],
			]
		);

		$social = new \Elementor\Repeater();

		$social->add_control(
			'icon',
			[
				'label'   => esc_html__( 'Icon', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::ICONS,
				'default' => [
					'value'   => 'icon-FacebookLogoStroke',
					'library' => 'ovinx_icon',
				],
			]
		);

		$social->add_control(
			'link',
			[
				'label'       => esc_html__( 'Link', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::URL,
				'label_block' => true,
				'default'     => [
					'url'         => 'https://www.facebook.com/',
					'is_external' => true,
				],
			]
		);

		$this->add_control(
			'social_items',
			[
				'label'       => esc_html__( 'Social Links', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $social->get_controls(),
				'title_field' => '{{{ link.url }}}',
				'default'     => [
					[
						'icon' => [ 'value' => 'icon-FacebookLogoStroke', 'library' => 'ovinx_icon' ],
						'link' => [ 'url' => 'https://www.facebook.com/', 'is_external' => true ],
					],
					[
						'icon' => [ 'value' => 'icon-InstagramLogoStroke', 'library' => 'ovinx_icon' ],
						'link' => [ 'url' => 'https://www.instagram.com/', 'is_external' => true ],
					],
					[
						'icon' => [ 'value' => 'icon-YoutubeLogo', 'library' => 'ovinx_icon' ],
						'link' => [ 'url' => 'https://www.youtube.com/', 'is_external' => true ],
					],
					[
						'icon' => [ 'value' => 'icon-XLogoFill', 'library' => 'ovinx_icon' ],
						'link' => [ 'url' => 'https://x.com/', 'is_external' => true ],
					],
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Contact column (`.footer-contact-2`) — empty fields fall back to
		 * the theme's Customizer footer options.
		 */
		$this->start_controls_section(
			'section_contact',
			[
				'label' => esc_html__( 'Contact', 'ovinx-addon' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'logo',
			[
				'label'       => esc_html__( 'Logo', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::MEDIA,
				'description' => esc_html__( 'Leave empty to use the site logo from the Customizer.', 'ovinx-addon' ),
			]
		);

		$this->add_control(
			'support_label',
			[
				'label'       => esc_html__( 'Support Label', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( '24/7 Support Center:', 'ovinx-addon' ),
				'label_block' => true,
				'condition'   => [ 'footer_style' => 'v2' ],
			]
		);

		$this->add_control(
			'description',
			[
				'label'     => esc_html__( 'Description', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::TEXTAREA,
				'default'   => esc_html__( 'We deliver reliable auto parts for strength and performance. Designed for everyday driving needs.', 'ovinx-addon' ),
				'condition' => [ 'footer_style' => 'v3' ],
			]
		);

		$this->add_control(
			'phone',
			[
				'label'       => esc_html__( 'Phone', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => esc_html__( 'Leave empty to use the Customizer footer phone.', 'ovinx-addon' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'address',
			[
				'label'       => esc_html__( 'Address', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => esc_html__( 'Leave empty to use the Customizer footer address.', 'ovinx-addon' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'map_url',
			[
				'label'       => esc_html__( 'Map Link', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => esc_html__( 'Leave empty to use the Customizer map URL.', 'ovinx-addon' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'email',
			[
				'label'       => esc_html__( 'Email', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => esc_html__( 'Leave empty to use the Customizer footer email.', 'ovinx-addon' ),
				'label_block' => true,
			]
		);

		$this->end_controls_section();

		/**
		 * Link columns (`.footer-col-block`)
		 */
		$this->start_controls_section(
			'section_columns',
			[
				'label' => esc_html__( 'Menu Columns', 'ovinx-addon' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$column = new \Elementor\Repeater();

		$column->add_control(
			'heading',
			[
				'label'       => esc_html__( 'Heading', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
			]
		);

		$column->add_control(
			'menu',
			[
				'label'       => esc_html__( 'Menu', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'options'     => $this->get_menu_options(),
				'default'     => '',
				'description' => esc_html__( 'Top-level items of the chosen menu are listed (Appearance > Menus).', 'ovinx-addon' ),
			]
		);

		$this->add_control(
			'columns',
			[
				'label'       => esc_html__( 'Columns', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $column->get_controls(),
				'title_field' => '{{{ heading }}}',
				'default'     => [
					[ 'heading' => esc_html__( 'Store Information', 'ovinx-addon' ) ],
					[ 'heading' => esc_html__( 'Our Services', 'ovinx-addon' ) ],
					[ 'heading' => esc_html__( 'Legal', 'ovinx-addon' ) ],
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Bottom bar (`.footer-bottom`)
		 */
		$this->start_controls_section(
			'section_bottom',
			[
				'label' => esc_html__( 'Bottom Bar', 'ovinx-addon' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'copyright',
			[
				'label'       => esc_html__( 'Copyright', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'description' => esc_html__( 'Leave empty for "©{year} {site name}. All Rights Reserved." (basic HTML allowed).', 'ovinx-addon' ),
			]
		);

		$links = new \Elementor\Repeater();

		$links->add_control(
			'text',
			[
				'label'       => esc_html__( 'Text', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
			]
		);

		$links->add_control(
			'link',
			[
				'label'       => esc_html__( 'Link', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::URL,
				'label_block' => true,
			]
		);

		$this->add_control(
			'bottom_links',
			[
				'label'       => esc_html__( 'Bottom Links', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $links->get_controls(),
				'title_field' => '{{{ text }}}',
				'default'     => [
					[ 'text' => esc_html__( 'Sitemap', 'ovinx-addon' ) ],
					[ 'text' => esc_html__( 'Accessibility', 'ovinx-addon' ) ],
					[ 'text' => esc_html__( 'Cookies Policy', 'ovinx-addon' ) ],
				],
			]
		);

		$this->end_controls_section();
	}

	private function get_logo_url( $settings ) {
		if ( ! empty( $settings['logo']['url'] ) ) {
			return $settings['logo']['url'];
		}

		if ( ! function_exists( 'themesflat_get_opt' ) ) {
			return '';
		}

		$elementor_logo = function_exists( 'themesflat_get_opt_elementor' ) ? themesflat_get_opt_elementor( 'site_logo' ) : '';

		if ( is_array( $elementor_logo ) && ! empty( $elementor_logo['url'] ) ) {
			return $elementor_logo['url'];
		}

		$logo = themesflat_get_opt( 'site_logo' );

		return is_string( $logo ) ? $logo : '';
	}

	/**
	 * Widget field first, then the theme's Customizer option of the same name.
	 */
	private function get_contact_value( $settings, $key, $option ) {
		if ( ! empty( $settings[ $key ] ) ) {
			return $settings[ $key ];
		}

		return function_exists( 'themesflat_get_opt' ) ? (string) themesflat_get_opt( $option ) : '';
	}

	/**
	 * Social icon links — `$list_class` differs per style (`style-2
	 * justify-content-end` in v2's top bar, plain in v3's own row).
	 */
	private function render_social_list( $settings, $list_class ) {
		$socials = ! empty( $settings['social_items'] ) && is_array( $settings['social_items'] ) ? $settings['social_items'] : [];

		if ( empty( $socials ) ) {
			return;
		}
		?>
		<div class="<?php echo esc_attr( $list_class ); ?>">
			<?php
			foreach ( $socials as $index => $item ) :
				if ( empty( $item['icon']['value'] ) ) {
					continue;
				}

				$key = 'social_link_' . $index;
				$this->add_link_attributes( $key, $item['link'] );
				// QA UX-006/UX-013: icon-only link needs a name; external links get noopener.
				$social_host = ! empty( $item['link']['url'] ) ? preg_replace( '/^www\./', '', (string) wp_parse_url( $item['link']['url'], PHP_URL_HOST ) ) : '';
				$this->add_render_attribute( $key, 'aria-label', '' !== $social_host ? $social_host : esc_html__( 'Social link', 'ovinx-addon' ) );
				if ( ! empty( $item['link']['is_external'] ) ) {
					$this->add_render_attribute( $key, 'rel', 'noopener noreferrer' );
				}
				?>
				<a <?php $this->print_render_attribute_string( $key ); ?>>
					<?php \Elementor\Icons_Manager::render_icon( $item['icon'], [ 'class' => 'icon', 'aria-hidden' => 'true' ] ); ?>
				</a>
			<?php endforeach; ?>
		</div>
		<?php
	}

	/**
	 * v3 top bar — `home-03.html` `.footer-top_wrap` swiper of `.box-ft-infor`
	 * (phone / location / email). Values are the Contact section's (Customizer
	 * fallback); a box with no value is skipped. Swiper tiers mapped from the
	 * ground truth's `data-view-sm=2 data-view-lg=3` onto `slider-core.js`'s
	 * breakpoints (1 → 2 at 575px → 3 at 1200px).
	 */
	private function render_info_top( $settings ) {
		$phone   = $this->get_contact_value( $settings, 'phone', 'footer_contact_phone' );
		$address = $this->get_contact_value( $settings, 'address', 'footer_contact_address' );
		$map_url = $this->get_contact_value( $settings, 'map_url', 'footer_contact_map_url' );
		$email   = $this->get_contact_value( $settings, 'email', 'footer_contact_email' );

		$boxes = [];

		if ( '' !== $phone ) {
			$boxes[] = [
				'icon'  => 'icon-PhoneCall',
				'label' => $settings['info_phone_label'],
				'text'  => $phone,
				'href'  => 'tel:' . preg_replace( '/[^0-9+]/', '', $phone ),
				'blank' => false,
			];
		}

		if ( '' !== $address ) {
			$boxes[] = [
				'icon'  => 'icon-MapPinLine',
				'label' => $settings['info_address_label'],
				'text'  => $address,
				'href'  => '' !== $map_url ? $map_url : '#',
				'blank' => true,
			];
		}

		if ( '' !== $email ) {
			$boxes[] = [
				'icon'  => 'icon-EnvelopeSimple',
				'label' => $settings['info_email_label'],
				'text'  => $email,
				'href'  => 'mailto:' . $email,
				'blank' => false,
			];
		}

		if ( empty( $boxes ) ) {
			return;
		}
		?>
		<div class="footer-top">
			<div class="container">
				<div class="footer-top_wrap">
					<div dir="ltr" class="swiper tf-swiper tfc-swiper"
						data-mobile="1" data-mobile-sm="2" data-tablet="2" data-preview="3"
						data-space="15" data-space-md="15" data-space-lg="20"
						data-pagination="1" data-pagination-sm="2" data-pagination-md="2" data-pagination-lg="3">
						<div class="swiper-wrapper">
							<?php foreach ( $boxes as $box ) : ?>
								<div class="swiper-slide">
									<div class="box-ft-infor">
										<i class="icon <?php echo esc_attr( $box['icon'] ); ?>"></i>
										<div class="ft-infor">
											<?php if ( ! empty( $box['label'] ) ) : ?>
												<h6 class="infor_name text-white"><?php echo esc_html( $box['label'] ); ?></h6>
											<?php endif; ?>
											<a href="<?php echo esc_url( $box['href'] ); ?>"<?php echo $box['blank'] ? ' target="_blank" rel="noopener"' : ''; ?> class="info_link text-body-02 cl-text-mute link-white link-underline"><?php echo esc_html( $box['text'] ); ?></a>
										</div>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
						<div class="sw-line-default style-white tf-sw-pagination"></div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	private function render_top( $settings ) {
		$shortcode = ! empty( $settings['newsletter_shortcode'] ) ? html_entity_decode( $settings['newsletter_shortcode'] ) : '';
		?>
		<div class="footer-top position-relative">
			<div class="container">
				<div class="footer-top_wrap">
					<div class="ft-top__left">
						<?php if ( ! empty( $settings['newsletter_title'] ) ) : ?>
							<h5><?php echo esc_html( $settings['newsletter_title'] ); ?></h5>
						<?php endif; ?>
					</div>
					<span class="br-line type-vertical lg-d-none"></span>
					<div class="ft-top__center">
						<?php if ( '' !== $shortcode ) : ?>
							<?php echo do_shortcode( $shortcode ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- shortcode output is escaped by its own plugin ?>
						<?php endif; ?>
					</div>
					<span class="br-line type-vertical lg-d-none"></span>
					<div class="ft-top__right">
						<?php $this->render_social_list( $settings, 'tf-list social-list style-2 justify-content-end' ); ?>
					</div>
				</div>
			</div>
			<span class="br-line fake-class bottom-0"></span>
		</div>
		<?php
	}

	/**
	 * v3's left column is just logo + description (contact info lives in the
	 * top info boxes instead).
	 */
	private function render_contact_v3( $settings ) {
		$logo = $this->get_logo_url( $settings );
		?>
		<div class="footer-contact-2">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="logo-site mb-12">
				<?php if ( '' !== $logo ) : ?>
					<img loading="lazy" width="141" height="36" src="<?php echo esc_url( $logo ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
				<?php else : ?>
					<?php bloginfo( 'name' ); ?>
				<?php endif; ?>
			</a>
			<?php if ( ! empty( $settings['description'] ) ) : ?>
				<p class="cl-text-mute"><?php echo esc_html( $settings['description'] ); ?></p>
			<?php endif; ?>
		</div>
		<?php
	}

	private function render_contact( $settings ) {
		$logo    = $this->get_logo_url( $settings );
		$phone   = $this->get_contact_value( $settings, 'phone', 'footer_contact_phone' );
		$address = $this->get_contact_value( $settings, 'address', 'footer_contact_address' );
		$map_url = $this->get_contact_value( $settings, 'map_url', 'footer_contact_map_url' );
		$email   = $this->get_contact_value( $settings, 'email', 'footer_contact_email' );
		?>
		<div class="footer-contact-2">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="logo-site">
				<?php if ( '' !== $logo ) : ?>
					<img loading="lazy" width="141" height="36" src="<?php echo esc_url( $logo ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
				<?php else : ?>
					<?php bloginfo( 'name' ); ?>
				<?php endif; ?>
			</a>
			<?php if ( ! empty( $settings['support_label'] ) ) : ?>
				<p class="text-secondary mb-4"><?php echo esc_html( $settings['support_label'] ); ?></p>
			<?php endif; ?>
			<?php if ( '' !== $phone ) : ?>
				<a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ); ?>" class="h4 cl-text-main link mb-8"><?php echo esc_html( $phone ); ?></a>
			<?php endif; ?>
			<?php if ( '' !== $address ) : ?>
				<a href="<?php echo esc_url( '' !== $map_url ? $map_url : '#' ); ?>" target="_blank" rel="noopener" class="text-secondary link-black link-underline mb-4"><?php echo esc_html( $address ); ?></a>
			<?php endif; ?>
			<?php if ( '' !== $email ) : ?>
				<a href="mailto:<?php echo esc_attr( $email ); ?>" class="text-secondary link-black link-underline"><?php echo esc_html( $email ); ?></a>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * v3 row between the columns and the bottom bar: currency/language on the
	 * left, social on the right (`ms-sm-auto` keeps social right when the
	 * left group is empty).
	 */
	private function render_middle_bar_v3( $settings ) {
		$show_currency = 'yes' === $settings['show_currency'];
		$show_language = 'yes' === $settings['show_language'];
		$has_social    = ! empty( $settings['social_items'] );

		if ( ! $show_currency && ! $show_language && ! $has_social ) {
			return;
		}

		// Same options as ground truth's `.tf-dropdown-select style-2 color-white`, opening upward.
		$switcher_args = [ 'extra_class' => 'style-2 color-white is-openup dropup' ];
		?>
		<div class="container">
			<div class="d-flex align-items-center justify-content-center justify-content-sm-between flex-wrap gap-20">
				<?php if ( $show_currency || $show_language ) : ?>
					<div class="tf-list gap-16">
						<?php
						if ( $show_currency ) {
							get_template_part( 'tpl/topbar/currency', null, $switcher_args );
						}
						if ( $show_language ) {
							get_template_part( 'tpl/topbar/language', null, $switcher_args );
						}
						?>
					</div>
				<?php endif; ?>
				<?php $this->render_social_list( $settings, 'tf-list social-list' . ( $show_currency || $show_language ? '' : ' ms-sm-auto' ) ); ?>
			</div>
		</div>
		<?php
	}

	private function render_column( $item, $is_v3 = false ) {
		$heading_class = $is_v3 ? 'footer-heading footer-heading-mobile text-white' : 'footer-heading footer-heading-mobile';
		$link_class    = $is_v3 ? 'cl-text-mute link-white link-underline' : 'text-secondary link-black link-underline';
		$menu_id       = ! empty( $item['menu'] ) ? absint( $item['menu'] ) : 0;
		$items   = $menu_id ? wp_get_nav_menu_items( $menu_id ) : [];

		if ( empty( $item['heading'] ) && empty( $items ) ) {
			return;
		}
		?>
		<div class="footer-col-block">
			<?php if ( ! empty( $item['heading'] ) ) : ?>
				<p class="<?php echo esc_attr( $heading_class ); ?>"><?php echo esc_html( $item['heading'] ); ?></p>
			<?php endif; ?>
			<div class="tf-collapse-content">
				<ul class="footer-menu-list">
					<?php
					if ( is_array( $items ) ) {
						foreach ( $items as $menu_item ) {
							if ( (int) $menu_item->menu_item_parent !== 0 ) {
								continue;
							}
							?>
							<li><a href="<?php echo esc_url( $menu_item->url ); ?>" class="<?php echo esc_attr( $link_class ); ?>"><?php echo esc_html( $menu_item->title ); ?></a></li>
							<?php
						}
					}
					?>
				</ul>
			</div>
		</div>
		<?php
	}

	private function render_bottom( $settings, $is_v3 = false ) {
		// v3 (dark): `.br-line` sits inside `.footer-bottom_wrap`, text is `cl-text-mute` / white.
		$text_class = $is_v3 ? 'cl-text-mute' : 'text-secondary';
		$link_class = $is_v3 ? 'cl-text-mute link-white link-underline' : 'text-secondary link-black link-underline';
		$line_class = $is_v3 ? ' bg-white_10' : '';
		$name_class = $is_v3 ? 'fw-medium text-white text-decoration-underline' : 'fw-medium cl-text-main text-decoration-underline';
		$links      = ! empty( $settings['bottom_links'] ) && is_array( $settings['bottom_links'] ) ? $settings['bottom_links'] : [];
		$links = array_values(
			array_filter(
				$links,
				static function ( $link ) {
					return ! empty( $link['text'] );
				}
			)
		);
		?>
		<div class="footer-bottom<?php echo $is_v3 ? '' : ' position-relative'; ?>">
			<?php if ( ! $is_v3 ) : ?>
				<span class="br-line fake-class top-0"></span>
			<?php endif; ?>
			<div class="container">
				<div class="footer-bottom_wrap<?php echo $is_v3 ? ' position-relative' : ''; ?>">
					<?php if ( $is_v3 ) : ?>
						<span class="br-line fake-class top-0 bg-white_10"></span>
					<?php endif; ?>
					<p class="text-copy <?php echo esc_attr( $text_class ); ?>">
						<?php if ( ! empty( $settings['copyright'] ) ) : ?>
							<?php echo wp_kses_post( $settings['copyright'] ); ?>
						<?php else : ?>
							&copy;<?php echo esc_html( wp_date( 'Y' ) ); ?>
							<span class="<?php echo esc_attr( $name_class ); ?>"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></span>. <?php esc_html_e( 'All Rights Reserved.', 'ovinx-addon' ); ?>
						<?php endif; ?>
					</p>
					<?php if ( ! empty( $links ) ) : ?>
						<div class="tf-list gap-12">
							<?php
							foreach ( $links as $index => $link ) :
								if ( $index > 0 ) :
									?>
									<span class="br-line type-vertical<?php echo esc_attr( $line_class ); ?>"></span>
									<?php
								endif;

								$key = 'bottom_link_' . $index;
								$this->add_link_attributes( $key, ! empty( $link['link']['url'] ) ? $link['link'] : [ 'url' => '#' ] );
								$this->add_render_attribute( $key, 'class', $link_class );
								?>
								<a <?php $this->print_render_attribute_string( $key ); ?>><?php echo esc_html( $link['text'] ); ?></a>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
	}

	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$columns  = ! empty( $settings['columns'] ) && is_array( $settings['columns'] ) ? $settings['columns'] : [];
		$is_v3    = 'v3' === ( $settings['footer_style'] ?? 'v2' );
		?>
		<footer class="tf-footer <?php echo $is_v3 ? 'footer-v3 background-1' : 'footer-v2 bg-surface'; ?> tf-ovinx-footer">
			<?php
			if ( 'yes' === $settings['show_top'] ) {
				if ( $is_v3 ) {
					$this->render_info_top( $settings );
				} else {
					$this->render_top( $settings );
				}
			}
			?>
			<div class="footer-inner">
				<div class="container">
					<div class="row gy-30">
						<div class="col-md-4 col-xl-3">
							<?php
							if ( $is_v3 ) {
								$this->render_contact_v3( $settings );
							} else {
								$this->render_contact( $settings );
							}
							?>
						</div>
						<div class="col-md-8 col-xl-9">
							<div class="footer-inner_wrap">
								<?php
								foreach ( $columns as $column ) {
									$this->render_column( $column, $is_v3 );
								}
								?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php
			if ( $is_v3 ) {
				$this->render_middle_bar_v3( $settings );
			}
			?>
			<?php $this->render_bottom( $settings, $is_v3 ); ?>
		</footer>
		<?php
	}
}
