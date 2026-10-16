<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Shared carousel options for every widget that has the
 * `items_desktop / items_laptop / items_tablet / items_mobile / items_mobile_sm`
 * controls (everything except the slideshow-style widgets):
 *
 * 1. "Slides Per Group" — how many slides one arrow click / swipe / dot moves
 *    the carousel by. Same idea as amerce's `data-pagination*` attributes, but
 *    driven by an Elementor option instead of hard-coded numbers:
 *    - `match` (default): per breakpoint, the group equals the number of items
 *      shown there (2 items on mobile => moves 2 at a time).
 *    - `custom`: one number per breakpoint.
 *
 * 2. "Slides Spacing" — gap (px) between slides, one value per breakpoint:
 *    - `default` (default): the spacing the template was designed with.
 *    - `custom`: one px value per breakpoint.
 *
 * Breakpoints follow `slider-core.js` `initSwiperInScope()`:
 * `<575`, `>=575`, `>=768`, `>=1200`, `>=1440` — emitted as `data-pagination`,
 * `-sm`, `-md`, `-lg`, `-xl` (group) and `data-space`, `-sm`, `-md`, `-lg`, `-xl`
 * (spacing).
 */
trait Ovinx_Swiper_Group_Controls {

	/**
	 * Breakpoint key => [ items control, per-breakpoint custom control, label, data attribute ].
	 */
	private function get_swiper_group_breakpoints() {
		return [
			'xs' => [ 'items_mobile_sm', 'group_mobile_sm', esc_html__( 'Group — Small Mobile (<575px)', 'ovinx-addon' ), 'data-pagination' ],
			'sm' => [ 'items_mobile', 'group_mobile', esc_html__( 'Group — Mobile (≥575px)', 'ovinx-addon' ), 'data-pagination-sm' ],
			'md' => [ 'items_tablet', 'group_tablet', esc_html__( 'Group — Tablet (≥768px)', 'ovinx-addon' ), 'data-pagination-md' ],
			'lg' => [ 'items_laptop', 'group_laptop', esc_html__( 'Group — Laptop (≥1200px)', 'ovinx-addon' ), 'data-pagination-lg' ],
			'xl' => [ 'items_desktop', 'group_desktop', esc_html__( 'Group — Desktop (≥1440px)', 'ovinx-addon' ), 'data-pagination-xl' ],
		];
	}

	/**
	 * Adds the "Slides Per Group" and "Slides Spacing" sections. Call it right
	 * after the section that holds the `items_*` controls.
	 *
	 * @param int[]|float[] $space_defaults  Spacing (px) the Custom fields start with, in the order
	 *                                       xs, sm, md, lg, xl. Same as the widget's design default.
	 * @param array         $space_condition Elementor `condition` for the spacing section, if any.
	 */
	private function register_swiper_group_controls( $space_defaults = [], $space_condition = [] ) {
		$this->start_controls_section(
			'section_swiper_group',
			[
				'label' => esc_html__( 'Slides Per Group', 'ovinx-addon' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'group_mode',
			[
				'label'       => esc_html__( 'Slide Per Group', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'options'     => [
					'match'  => esc_html__( 'Same as items shown', 'ovinx-addon' ),
					'custom' => esc_html__( 'Custom per breakpoint', 'ovinx-addon' ),
				],
				'default'     => 'match',
				'description' => esc_html__( 'How many slides move at once on arrow click, swipe or dot. "Same as items shown": 2 items on mobile moves 2 slides.', 'ovinx-addon' ),
			]
		);

		foreach ( $this->get_swiper_group_breakpoints() as $breakpoint ) {
			$this->add_control(
				$breakpoint[1],
				[
					'label'     => $breakpoint[2],
					'type'      => \Elementor\Controls_Manager::NUMBER,
					'min'       => 1,
					'max'       => 8,
					'default'   => 1,
					'condition' => [ 'group_mode' => 'custom' ],
				]
			);
		}

		$this->end_controls_section();

		$this->register_swiper_spacing_controls( $space_defaults, $space_condition );
	}

	/**
	 * Slides-per-group numbers per breakpoint: [ 'xs' => n, 'sm' => n, ... ].
	 *
	 * @param array $settings Widget settings. Missing/old widgets fall back to "match".
	 */
	private function get_swiper_group_values( $settings ) {
		$custom = ! empty( $settings['group_mode'] ) && 'custom' === $settings['group_mode'];
		$values = [];

		foreach ( $this->get_swiper_group_breakpoints() as $key => $breakpoint ) {
			$source = $custom ? $breakpoint[1] : $breakpoint[0];
			$number = isset( $settings[ $source ] ) ? (int) floor( (float) $settings[ $source ] ) : 1;

			$values[ $key ] = min( 8, max( 1, $number ) );
		}

		return $values;
	}

	/**
	 * The `data-pagination*` attribute string for the swiper wrapper.
	 */
	private function get_swiper_group_attrs( $settings ) {
		$attrs  = '';
		$values = $this->get_swiper_group_values( $settings );

		foreach ( $this->get_swiper_group_breakpoints() as $key => $breakpoint ) {
			$attrs .= ' ' . $breakpoint[3] . '="' . esc_attr( $values[ $key ] ) . '"';
		}

		return $attrs;
	}

	/**
	 * "Slides Spacing" section: gap between slides per breakpoint.
	 */
	private function register_swiper_spacing_controls( $defaults = [], $condition = [] ) {
		$defaults = array_values( (array) $defaults );
		$defaults = array_pad( $defaults, 5, isset( $defaults[0] ) ? $defaults[0] : 20 );

		$section = [
			'label' => esc_html__( 'Slides Spacing', 'ovinx-addon' ),
			'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
		];
		if ( ! empty( $condition ) ) {
			$section['condition'] = $condition;
		}

		$this->start_controls_section( 'section_swiper_spacing', $section );

		$this->add_control(
			'space_mode',
			[
				'label'       => esc_html__( 'Spacing', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'options'     => [
					'default' => esc_html__( 'Design default', 'ovinx-addon' ),
					'custom'  => esc_html__( 'Custom per breakpoint', 'ovinx-addon' ),
				],
				'default'     => 'default',
				'description' => esc_html__( 'Gap between slides in px. "Design default" keeps the spacing of the original design.', 'ovinx-addon' ),
			]
		);

		$labels = [
			'space_mobile_sm' => esc_html__( 'Spacing — Small Mobile (<575px)', 'ovinx-addon' ),
			'space_mobile'    => esc_html__( 'Spacing — Mobile (≥575px)', 'ovinx-addon' ),
			'space_tablet'    => esc_html__( 'Spacing — Tablet (≥768px)', 'ovinx-addon' ),
			'space_laptop'    => esc_html__( 'Spacing — Laptop (≥1200px)', 'ovinx-addon' ),
			'space_desktop'   => esc_html__( 'Spacing — Desktop (≥1440px)', 'ovinx-addon' ),
		];

		$i = 0;
		foreach ( $labels as $name => $label ) {
			$this->add_control(
				$name,
				[
					'label'     => $label,
					'type'      => \Elementor\Controls_Manager::NUMBER,
					'min'       => 0,
					'max'       => 100,
					'step'      => 1,
					'default'   => $defaults[ $i ],
					'condition' => [ 'space_mode' => 'custom' ],
				]
			);
			$i++;
		}

		$this->end_controls_section();
	}

	/**
	 * Spacing (px) per breakpoint: [ 'xs' => n, 'sm' => n, 'md' => n, 'lg' => n, 'xl' => n ].
	 *
	 * @param array $settings Widget settings.
	 * @param array $fallback Design-default spacing in the order xs, sm, md, lg, xl (used unless mode is custom).
	 */
	private function get_swiper_space_values( $settings, $fallback ) {
		$fallback = array_values( (array) $fallback );
		$fallback = array_pad( $fallback, 5, isset( $fallback[0] ) ? $fallback[0] : 0 );
		$keys     = [ 'xs', 'sm', 'md', 'lg', 'xl' ];
		$fields   = [ 'space_mobile_sm', 'space_mobile', 'space_tablet', 'space_laptop', 'space_desktop' ];
		$custom   = ! empty( $settings['space_mode'] ) && 'custom' === $settings['space_mode'];
		$values   = [];

		foreach ( $keys as $i => $key ) {
			$value = ( $custom && isset( $settings[ $fields[ $i ] ] ) && '' !== $settings[ $fields[ $i ] ] ) ? $settings[ $fields[ $i ] ] : $fallback[ $i ];

			$values[ $key ] = max( 0, (float) $value );
		}

		return $values;
	}

	/**
	 * The `data-space*` attribute string for the swiper wrapper.
	 */
	private function get_swiper_space_attrs( $settings, $fallback ) {
		$names = [ 'xs' => 'data-space', 'sm' => 'data-space-sm', 'md' => 'data-space-md', 'lg' => 'data-space-lg', 'xl' => 'data-space-xl' ];
		$attrs = '';

		foreach ( $this->get_swiper_space_values( $settings, $fallback ) as $key => $value ) {
			$attrs .= ' ' . $names[ $key ] . '="' . esc_attr( $value ) . '"';
		}

		return $attrs;
	}
}
