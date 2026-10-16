<?php

/**
 * Ground truth's Style 1 (`index.html`) / Style 3 (`shop-sub-collection.html`)
 * cards are a byte-for-byte match of the theme's own existing
 * `tpl/related-post.php` carousel card (`.article-blog.style-2` — image+tag
 * via `tpl/feature-post-related.php`, date+author via
 * `tpl/entry-content/entry-content-meta-related.php`, "Read More" via
 * `tpl/entry-content/entry-content-readmore-related.php`) — those are reused
 * here via `get_template_part()` instead of re-implementing the same markup,
 * so real post data (thumbnail, real category tag, real date/author,
 * customizer-controlled "Read More" label) can never drift from every other
 * place those cards already render on the site. Only the title anchor is
 * rendered inline (ground truth here is a flat `<a class="entry-title h5">`,
 * not `entry-content-title-related.php`'s `<h5><a>...</a></h5>` — a real,
 * deliberate markup difference from that file's one existing caller).
 */
class ovinx_Blog extends \Elementor\Widget_Base {

	use Ovinx_Swiper_Group_Controls;

	public function get_name() {
		return 'ovinx_blog';
	}

	public function get_title() {
		return esc_html__( 'Ovinx Blog', 'ovinx-addon' );
	}

	public function get_icon() {
		return 'eicon-posts-carousel';
	}

	public function get_categories() {
		return [ 'ovinx_addons_core' ];
	}

	public function get_keywords() {
		return [ 'blog', 'post', 'carousel', 'news' ];
	}

	public function get_script_depends() {
		return [ 'themesflat-swiper', 'slider-core' ];
	}

	public function get_style_depends() {
		return [ 'themesflat-swiper' ];
	}

	private function get_category_options() {
		$options = [ '' => esc_html__( 'All Categories', 'ovinx-addon' ) ];

		$terms = get_categories( [ 'hide_empty' => false ] );

		foreach ( $terms as $term ) {
			$options[ $term->term_id ] = $term->name;
		}

		return $options;
	}

	/**
	 * Same "bounded, non-AJAX SELECT2" convention already used by
	 * `widget-banner-feature.php`/`widget-category.php` for products — see
	 * the comment there on why `tfwc_select2`'s AJAX handler isn't used.
	 */
	private function get_posts_options() {
		$options = [];

		$posts = get_posts(
			[
				'post_type'      => 'post',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'orderby'        => 'title',
				'order'          => 'ASC',
			]
		);

		foreach ( $posts as $post ) {
			$options[ $post->ID ] = $post->post_title;
		}

		return $options;
	}

	private function get_default_blog_link() {
		$page_for_posts = (int) get_option( 'page_for_posts' );

		if ( $page_for_posts ) {
			return get_permalink( $page_for_posts );
		}

		$archive = get_post_type_archive_link( 'post' );

		return $archive ? $archive : home_url( '/' );
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
					'style_1' => esc_html__( 'Style 1 — Full Card (Latest Posts)', 'ovinx-addon' ),
					'style_2' => esc_html__( 'Style 2 — Minimal Card (Curated Posts)', 'ovinx-addon' ),
					'style_3' => esc_html__( 'Style 3 — List Card Grid (Latest Posts)', 'ovinx-addon' ),
				],
				'default' => 'style_1',
			]
		);

		$this->end_controls_section();

		/**
		 * Heading — Style 1/3 only (Style 2's ground truth has none).
		 */
		$this->start_controls_section(
			'section_heading',
			[
				'label'     => esc_html__( 'Heading', 'ovinx-addon' ),
				'tab'       => \Elementor\Controls_Manager::TAB_CONTENT,
				'condition' => [
					'style!' => 'style_2',
				],
			]
		);

		$this->add_control(
			'title',
			[
				'label'       => esc_html__( 'Title', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Blog And Tips Auto Parts', 'ovinx-addon' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'description',
			[
				'label'   => esc_html__( 'Description', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => esc_html__( 'Unleash automotive excellence with our precision crafted parts and tools.', 'ovinx-addon' ),
			]
		);

		$this->end_controls_section();

		/**
		 * Posts — Style 1/3 (auto-query latest posts).
		 */
		$this->start_controls_section(
			'section_posts',
			[
				'label'     => esc_html__( 'Posts', 'ovinx-addon' ),
				'tab'       => \Elementor\Controls_Manager::TAB_CONTENT,
				'condition' => [
					'style!' => 'style_2',
				],
			]
		);

		$this->add_control(
			'category_filter',
			[
				'label'   => esc_html__( 'Category', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => $this->get_category_options(),
				'default' => '',
			]
		);

		$this->add_control(
			'posts_count',
			[
				'label'   => esc_html__( 'Number of Posts', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 20,
				'default' => 6,
			]
		);

		$this->add_control(
			'show_excerpt_full',
			[
				'label'        => esc_html__( 'Show Excerpt', 'ovinx-addon' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'ovinx-addon' ),
				'label_off'    => esc_html__( 'No', 'ovinx-addon' ),
				'return_value' => 'yes',
				'default'      => '',
			]
		);

		$this->end_controls_section();

		/**
		 * Slides — Style 2 only (curated posts + custom CTA).
		 */
		$this->start_controls_section(
			'section_slides',
			[
				'label'     => esc_html__( 'Slides', 'ovinx-addon' ),
				'tab'       => \Elementor\Controls_Manager::TAB_CONTENT,
				'condition' => [
					'style' => 'style_2',
				],
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'post_id',
			[
				'label'       => esc_html__( 'Post', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'label_block' => true,
				'options'     => $this->get_posts_options(),
				'default'     => '',
			]
		);

		/**
		 * Ground truth's Style 2 CTA ("Browse All Parts" / "Browse
		 * Maintenance") links to the blog archive, not the post itself, with
		 * wording that differs per slide — there's no WordPress field this
		 * maps to, so (like Category widget Style 4's sale-badge text) it
		 * stays admin-typed; left blank it falls back to a sensible default.
		 */
		$repeater->add_control(
			'cta_text',
			[
				'label'       => esc_html__( 'CTA Text (optional)', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'description' => esc_html__( 'Leave blank to use "Browse All Parts".', 'ovinx-addon' ),
			]
		);

		$repeater->add_control(
			'cta_link',
			[
				'label'       => esc_html__( 'CTA Link (optional)', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::URL,
				'label_block' => true,
				'description' => esc_html__( 'Leave blank to link to the blog archive.', 'ovinx-addon' ),
			]
		);

		$this->add_control(
			'blog_slides',
			[
				'label'       => esc_html__( 'Slides', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ post_id ? post_id : "— choose a post —" }}}',
				'default'     => [ [], [], [] ],
			]
		);

		$this->add_control(
			'show_excerpt',
			[
				'label'        => esc_html__( 'Show Excerpt', 'ovinx-addon' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'ovinx-addon' ),
				'label_off'    => esc_html__( 'No', 'ovinx-addon' ),
				'return_value' => 'yes',
				'default'      => 'yes',
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
				'default' => 3,
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
				'default' => 2,
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

		/**
		 * Only Style 3's ground truth uses Swiper's `grid` module
		 * (`data-grid="2"`, 2 rows × 2 cols = 4 cards on screen at once) —
		 * `slider-core.js` already supports `data-grid` natively (unlike the
		 * dead `data-view-*` attributes fixed in the Category widget), so no
		 * JS change is needed, just wiring this control through.
		 */
		$this->add_control(
			'grid_rows',
			[
				'label'     => esc_html__( 'Grid Rows', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 1,
				'max'       => 3,
				'default'   => 2,
				'condition' => [
					'style' => 'style_3',
				],
			]
		);

		$this->end_controls_section();

		$this->register_swiper_group_controls( [ 15, 15, 15, 20, 30 ] );

		/**
		 * Style tab
		 */
		$this->start_controls_section(
			'section_style_heading',
			[
				'label'     => esc_html__( 'Heading', 'ovinx-addon' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'style!' => 'style_2',
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__( 'Title Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .sect-heading .s-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'desc_color',
			[
				'label'     => esc_html__( 'Description Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .sect-heading .text-body-02' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_card',
			[
				'label' => esc_html__( 'Card', 'ovinx-addon' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'entry_title_color',
			[
				'label'     => esc_html__( 'Title Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .article-blog .entry-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'entry_desc_color',
			[
				'label'     => esc_html__( 'Description Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .article-blog .entry-desc' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .sw-dot-default .swiper-pagination-bullet' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'pagination_color_active',
			[
				'label'     => esc_html__( 'Active Bullet Color', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .sw-dot-default .swiper-pagination-bullet-active' => 'background: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Style 1 (`article-blog style-2`) / Style 3 (`article-blog
	 * blog-style_list`) card — real post data throughout, via the theme's
	 * own related-posts card template parts.
	 */
	private function render_full_card( $card_class, $title_extra_class, $show_excerpt = false ) {
		ob_start();
		?>
		<div class="swiper-slide">
			<div class="article-blog <?php echo esc_attr( $card_class ); ?> hover-img">
				<?php get_template_part( 'tpl/feature-post-related' ); ?>
				<div class="blog-content">
					<div class="d-grid gap-8 mb-16">
						<?php get_template_part( 'tpl/entry-content/entry-content-meta-related' ); ?>
						<a href="<?php echo esc_url( get_permalink() ); ?>" class="entry-title h5 link-underline cl-text-main<?php echo esc_attr( $title_extra_class ); ?>">
							<?php the_title(); ?>
						</a>
						<?php if ( $show_excerpt ) : ?>
							<?php add_filter( 'excerpt_more', 'themesflat_excerpt_not_more' ); ?>
							<p class="entry-desc cl-text-2 text-line-clamp-3"><?php echo wp_kses_post( get_the_excerpt() ); ?></p>
						<?php endif; ?>
					</div>
					<?php get_template_part( 'tpl/entry-content/entry-content-readmore-related' ); ?>
				</div>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Style 2 card — image only (no category tag, regardless of the site's
	 * global "meta elements" Customizer setting, since ground truth never
	 * shows one here), real title + real excerpt, admin-typed CTA.
	 */
	private function render_minimal_card( $item, $show_excerpt = true ) {
		if ( empty( $item['post_id'] ) ) {
			return '';
		}

		$post = get_post( absint( $item['post_id'] ) );

		if ( ! $post || 'publish' !== $post->post_status ) {
			return '';
		}

		$permalink = get_permalink( $post );
		$thumb     = get_the_post_thumbnail( $post, 'themesflat-blog', [ 'loading' => 'lazy' ] );
		$cta_text  = ! empty( $item['cta_text'] ) ? $item['cta_text'] : esc_html__( 'Browse All Parts', 'ovinx-addon' );
		$cta_url   = ! empty( $item['cta_link']['url'] ) ? $item['cta_link']['url'] : $this->get_default_blog_link();

		ob_start();
		?>
		<div class="swiper-slide">
			<div class="article-blog style-2 st2-v2 hover-img">
				<a href="<?php echo esc_url( $permalink ); ?>" class="blog-image img-style">
					<?php echo wp_kses_post( $thumb ); ?>
				</a>
				<div class="blog-content">
					<a href="<?php echo esc_url( $permalink ); ?>" class="entry-title h5 link-underline cl-text-main"><?php echo esc_html( get_the_title( $post ) ); ?></a>
					<?php if ( $show_excerpt ) : ?>
						<p class="entry-desc"><?php echo esc_html( get_the_excerpt( $post ) ); ?></p>
					<?php endif; ?>
					<a href="<?php echo esc_url( $cta_url ); ?>" class="tf-btn-line style-min">
						<span class="fw-semibold text-uppercase"><?php echo esc_html( $cta_text ); ?></span>
					</a>
				</div>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$style    = ! empty( $settings['style'] ) ? $settings['style'] : 'style_1';

		$slides_html = '';

		if ( 'style_2' === $style ) {
			$items = ! empty( $settings['blog_slides'] ) && is_array( $settings['blog_slides'] ) ? $settings['blog_slides'] : [];

			$show_excerpt = 'yes' === ( $settings['show_excerpt'] ?? 'yes' );

			foreach ( $items as $item ) {
				$slides_html .= $this->render_minimal_card( $item, $show_excerpt );
			}
		} else {
			$args = [
				'post_type'      => 'post',
				'post_status'    => 'publish',
				'posts_per_page' => ! empty( $settings['posts_count'] ) ? absint( $settings['posts_count'] ) : 6,
			];

			if ( ! empty( $settings['category_filter'] ) ) {
				$args['cat'] = absint( $settings['category_filter'] );
			}

			$query        = new WP_Query( $args );
			$card_class   = ( 'style_3' === $style ) ? 'blog-style_list' : 'style-2';
			$title_extra  = ( 'style_3' === $style ) ? ' text-line-clamp-2' : '';
			$show_excerpt = 'yes' === ( $settings['show_excerpt_full'] ?? '' );

			if ( $query->have_posts() ) {
				while ( $query->have_posts() ) {
					$query->the_post();
					$slides_html .= $this->render_full_card( $card_class, $title_extra, $show_excerpt );
				}
				wp_reset_postdata();
			}
		}

		if ( empty( $slides_html ) ) {
			return;
		}

		/**
		 * Ground truth's Style 1/2 use `data-space-xl="30" data-space-lg="20"
		 * data-space="15"`, Style 3 uses `data-space-xl="30" data-space-md="20"
		 * data-space="30"` — `slider-core.js` has no `space-xl` breakpoint
		 * (see the Category widget's Style 2/4 comments on this same issue),
		 * so these are translated onto the breakpoints it actually supports.
		 */
		// Design defaults in the order xs, sm, md, lg, xl (the HTML's data-space / -xl, filled in between).
		$space_attrs = $this->get_swiper_space_attrs( $settings, 'style_3' === $style ? [ 30, 30, 20, 20, 30 ] : [ 15, 15, 15, 20, 30 ] );

		$grid_attr = ( 'style_3' === $style && ! empty( $settings['grid_rows'] ) && (int) $settings['grid_rows'] > 1 )
			? ' data-grid="' . esc_attr( $settings['grid_rows'] ) . '"'
			: '';
		?>
			<div class="container">
				<?php if ( 'style_2' !== $style && ( ! empty( $settings['title'] ) || ! empty( $settings['description'] ) ) ) : ?>
					<div class="sect-heading text-center">
						<?php if ( 'style_3' === $style ) : ?>
							<?php if ( ! empty( $settings['title'] ) ) : ?>
								<h4 class="s-title"><?php echo esc_html( $settings['title'] ); ?></h4>
							<?php endif; ?>
							<?php if ( ! empty( $settings['description'] ) ) : ?>
								<p class="s-desc text-body-02"><?php echo esc_html( $settings['description'] ); ?></p>
							<?php endif; ?>
						<?php else : ?>
							<?php if ( ! empty( $settings['title'] ) ) : ?>
								<p class="s-title h4"><?php echo esc_html( $settings['title'] ); ?></p>
							<?php endif; ?>
							<?php if ( ! empty( $settings['description'] ) ) : ?>
								<p class="text-body-02"><?php echo esc_html( $settings['description'] ); ?></p>
							<?php endif; ?>
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
					<?php echo $space_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built with esc_attr() in the trait ?>
					<?php echo $grid_attr; /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from esc_attr() above */ ?>
				>
					<div class="swiper-wrapper">
						<?php echo $slides_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from escaped output in render_full_card()/render_minimal_card() ?>
					</div>
					<div class="sw-dot-default tf-sw-pagination"></div>
				</div>
			</div>
		<?php
	}
}
