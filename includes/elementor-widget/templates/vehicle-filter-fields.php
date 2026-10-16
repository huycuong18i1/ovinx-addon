<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Shared "Product Search Filter" (`.filter-vehicle` / `.field-select` dropdowns
 * + `.btn-action_submit`) controls + rendering, used by both `ovinx_Slideshow`
 * (Product Search Filter sidebar) and `ovinx_Check_Fit` (standalone "Find Parts
 * For Your Vehicle" section) — same fields, same term lookup, same query-var
 * mapping, same button. Each widget still builds its own outer wrapper markup,
 * since that part genuinely differs between the two.
 */
trait Ovinx_Vehicle_Filter_Fields {

	private function get_vehicle_filter_attribute_options() {
		$attribute_options = [];

		if ( function_exists( 'wc_get_attribute_taxonomies' ) ) {
			foreach ( (array) wc_get_attribute_taxonomies() as $attribute ) {
				$attribute_options[ 'pa_' . $attribute->attribute_name ] = $attribute->attribute_label;
			}
		}

		return $attribute_options;
	}

	/**
	 * Returns the repeater control set for "Search Fields" (Label / Options
	 * From / Attribute) — each widget adds this as its own `search_fields`
	 * control so it keeps its own label/default.
	 */
	private function get_vehicle_filter_field_repeater_controls() {
		$field_repeater = new \Elementor\Repeater();

		$field_repeater->add_control(
			'label',
			[
				'label'       => esc_html__( 'Label', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Choose Option', 'ovinx-addon' ),
				'label_block' => true,
			]
		);

		$field_repeater->add_control(
			'source',
			[
				'label'   => esc_html__( 'Options From', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'category'  => esc_html__( 'Product Categories', 'ovinx-addon' ),
					'brand'     => esc_html__( 'Product Brand', 'ovinx-addon' ),
					'tag'       => esc_html__( 'Product Tags', 'ovinx-addon' ),
					'attribute' => esc_html__( 'Product Attribute', 'ovinx-addon' ),
				],
				'default' => 'category',
			]
		);

		$field_repeater->add_control(
			'attribute_taxonomy',
			[
				'label'     => esc_html__( 'Attribute', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'options'   => $this->get_vehicle_filter_attribute_options(),
				'condition' => [
					'source' => 'attribute',
				],
			]
		);

		return $field_repeater->get_controls();
	}

	private function get_default_vehicle_filter_fields() {
		return [
			[ 'label' => esc_html__( 'Choose Make', 'ovinx-addon' ), 'source' => 'category' ],
			[ 'label' => esc_html__( 'Choose Model', 'ovinx-addon' ), 'source' => 'category' ],
			[ 'label' => esc_html__( 'Choose Year', 'ovinx-addon' ), 'source' => 'category' ],
			[ 'label' => esc_html__( 'Choose Engine', 'ovinx-addon' ), 'source' => 'category' ],
		];
	}

	private function get_field_terms( $field, $hide_empty = true ) {
		$source = ! empty( $field['source'] ) ? $field['source'] : 'category';

		if ( 'attribute' === $source && ! empty( $field['attribute_taxonomy'] ) ) {
			$taxonomy = $field['attribute_taxonomy'];
		} elseif ( 'brand' === $source ) {
			$taxonomy = 'product_brand';
		} elseif ( 'tag' === $source ) {
			$taxonomy = 'product_tag';
		} else {
			$taxonomy = 'product_cat';
		}

		if ( ! taxonomy_exists( $taxonomy ) ) {
			return [];
		}

		$terms = get_terms(
			[
				'taxonomy'   => $taxonomy,
				'hide_empty' => (bool) $hide_empty,
			]
		);

		return is_wp_error( $terms ) ? [] : $terms;
	}

	private function get_field_query_var( $field ) {
		$source = ! empty( $field['source'] ) ? $field['source'] : 'category';

		if ( 'attribute' === $source && ! empty( $field['attribute_taxonomy'] ) ) {
			return 'filter_' . $field['attribute_taxonomy'];
		}

		if ( 'brand' === $source ) {
			return 'product_brand';
		}

		if ( 'tag' === $source ) {
			return 'product_tag';
		}

		return 'product_cat';
	}

	/**
	 * The real shop sidebar filter widgets (class-widget-category-filter.php,
	 * class-widget-brand-filter.php, class-widget-attribute-filter.php) use
	 * term SLUG as the checkbox value for category/brand, but term ID for
	 * attributes — mirror that exactly so a future "pre-check the matching
	 * sidebar checkbox from the URL" step can match without any slug<->id
	 * lookup.
	 */
	private function get_field_option_value( $field, $term ) {
		$source = ! empty( $field['source'] ) ? $field['source'] : 'category';

		return ( 'attribute' === $source ) ? $term->term_id : $term->slug;
	}

	/**
	 * Renders just the repeated `<fieldset class="field-select">` dropdowns —
	 * the caller wraps this in its own `.content.filter-vehicle` (both current
	 * callers use that exact class already, so this only emits the fieldsets).
	 */
	private function render_vehicle_filter_fields( $fields ) {
		ob_start();
		foreach ( $fields as $index => $field ) :
			$terms     = $this->get_field_terms( $field );
			$label     = ! empty( $field['label'] ) ? $field['label'] : esc_html__( 'Choose Option', 'ovinx-addon' );
			$query_var = $this->get_field_query_var( $field );
			?>
			<fieldset class="field-select">
				<div class="nice-select" data-query-var="<?php echo esc_attr( $query_var ); ?>">
					<div class="number-order text-label fw-semibold"><?php echo esc_html( $index + 1 ); ?></div>
					<span class="current text text-button-small"><?php echo esc_html( $label ); ?></span>
					<i class="icon icon-CaretDownV2"></i>
					<ul class="list text-label">
						<li class="option option-all disabled fw-semibold cl-text-main text-uppercase">
							<?php echo esc_html( $label ); ?>
						</li>
						<?php foreach ( $terms as $term ) : ?>
							<li class="option" data-value="<?php echo esc_attr( $this->get_field_option_value( $field, $term ) ); ?>">
								<?php echo esc_html( $term->name ); ?>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			</fieldset>
		<?php endforeach;
		return ob_get_clean();
	}

	private function render_vehicle_filter_button( $text, $disabled = false ) {
		if ( empty( $text ) ) {
			return '';
		}

		ob_start();
		?>
		<button type="button" class="btn-action_submit tf-btn"<?php echo $disabled ? ' disabled' : ''; ?>
			data-shop-url="<?php echo esc_url( function_exists( 'wc_get_page_id' ) ? get_permalink( wc_get_page_id( 'shop' ) ) : home_url( '/' ) ); ?>">
			<span class="text-caption-01"><?php echo esc_html( $text ); ?></span>
		</button>
		<?php
		return ob_get_clean();
	}

	/**
	 * Cascading variant (used by `ovinx_Check_Fit`): ONE source (category / brand
	 * / tag / attribute) drives every dropdown — select 1 lists the top-level
	 * terms, and picking one unlocks select 2 with that term's children, and so
	 * on (Make > Model > Year > Engine). Only hierarchical taxonomies
	 * (`product_cat`, `product_brand`) have children; flat ones (tags, attributes)
	 * just fill select 1 and leave the rest locked.
	 */
	private function get_cascade_field( $settings ) {
		return [
			'source'             => ! empty( $settings['source'] ) ? $settings['source'] : 'category',
			'attribute_taxonomy' => ! empty( $settings['attribute_taxonomy'] ) ? $settings['attribute_taxonomy'] : '',
			'hide_empty'         => ( ! isset( $settings['show_empty'] ) || 'yes' === $settings['show_empty'] ) ? false : true,
		];
	}

	private function build_cascade_tree( $field, $depth ) {
		$terms = $this->get_field_terms( $field, ! isset( $field['hide_empty'] ) || $field['hide_empty'] );

		if ( empty( $terms ) || $depth < 1 ) {
			return [];
		}

		$by_parent = [];
		foreach ( $terms as $term ) {
			$by_parent[ (int) $term->parent ][] = $term;
		}

		$build = function ( $parent_id, $level ) use ( &$build, $by_parent, $field, $depth ) {
			$nodes = [];

			if ( $level > $depth || empty( $by_parent[ $parent_id ] ) ) {
				return $nodes;
			}

			foreach ( $by_parent[ $parent_id ] as $term ) {
				$nodes[] = [
					'n' => html_entity_decode( $term->name, ENT_QUOTES, 'UTF-8' ),
					'v' => (string) $this->get_field_option_value( $field, $term ),
					'c' => $build( (int) $term->term_id, $level + 1 ),
				];
			}

			return $nodes;
		};

		return $build( 0, 1 );
	}

	/**
	 * Renders the locked/unlocked `.field-select` dropdowns for the cascading
	 * filter plus the JSON term tree `slider-core.js` reads to fill/unlock them.
	 * Only select 1 is populated server-side; the rest start `is-disable`.
	 */
	private function render_vehicle_cascade_fields( $field, $levels ) {
		$levels    = array_values( (array) $levels );
		$tree      = $this->build_cascade_tree( $field, count( $levels ) );
		$query_var = $this->get_field_query_var( $field );

		ob_start();
		foreach ( $levels as $index => $level ) :
			$label = ! empty( $level['label'] ) ? $level['label'] : esc_html__( 'Choose Option', 'ovinx-addon' );
			?>
			<fieldset class="field-select<?php echo ( 0 === $index && ! empty( $tree ) ) ? '' : ' is-disable'; ?>">
				<div class="nice-select" data-query-var="<?php echo esc_attr( $query_var ); ?>" data-label="<?php echo esc_attr( $label ); ?>">
					<div class="number-order text-label fw-semibold"><?php echo esc_html( $index + 1 ); ?></div>
					<span class="current text text-button-small"><?php echo esc_html( $label ); ?></span>
					<i class="icon icon-CaretDownV2"></i>
					<ul class="list text-label">
						<li class="option option-all disabled fw-semibold cl-text-main text-uppercase"><?php echo esc_html( $label ); ?></li>
						<?php if ( 0 === $index ) : foreach ( $tree as $i => $node ) : ?>
							<li class="option" data-index="<?php echo esc_attr( $i ); ?>" data-value="<?php echo esc_attr( $node['v'] ); ?>"><?php echo esc_html( $node['n'] ); ?></li>
						<?php endforeach; endif; ?>
					</ul>
				</div>
			</fieldset>
		<?php endforeach; ?>
		<script type="application/json" class="tf-vehicle-tree"><?php echo wp_json_encode( $tree, JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE ); ?></script>
		<?php
		return ob_get_clean();
	}

	/**
	 * Controls of the cascading filter (used by `ovinx_Check_Fit` and the Slideshow's Product Search
	 * Filter): ONE option source + the list of select levels. Rendering: render_vehicle_cascade_fields().
	 */
	private function register_vehicle_cascade_controls() {
		$this->add_control(
			'source',
			[
				'label'   => esc_html__( 'Options From', 'ovinx-addon' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'category'  => esc_html__( 'Product Categories', 'ovinx-addon' ),
					'brand'     => esc_html__( 'Product Brand', 'ovinx-addon' ),
					'tag'       => esc_html__( 'Product Tags', 'ovinx-addon' ),
					'attribute' => esc_html__( 'Product Attribute', 'ovinx-addon' ),
				],
				'default' => 'category',
			]
		);

		$this->add_control(
			'attribute_taxonomy',
			[
				'label'     => esc_html__( 'Attribute', 'ovinx-addon' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'options'   => $this->get_vehicle_filter_attribute_options(),
				'condition' => [
					'source' => 'attribute',
				],
			]
		);

		$this->add_control(
			'show_empty',
			[
				'label'        => esc_html__( 'Show Terms With No Products', 'ovinx-addon' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'ovinx-addon' ),
				'label_off'    => esc_html__( 'No', 'ovinx-addon' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'source_note',
			[
				'type'            => \Elementor\Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Select 1 lists the top-level terms; each next select unlocks with the children of the term chosen before it (e.g. Make > Model > Year > Engine). Only Categories and Brands can have children — Tags and Attributes are flat, so only the first select is filled.', 'ovinx-addon' ),
				'content_classes' => 'elementor-descriptor',
			]
		);

		$level_repeater = new \Elementor\Repeater();

		$level_repeater->add_control(
			'label',
			[
				'label'       => esc_html__( 'Label', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Choose Option', 'ovinx-addon' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'levels',
			[
				'label'       => esc_html__( 'Select Levels', 'ovinx-addon' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $level_repeater->get_controls(),
				'title_field' => '{{{ label }}}',
				'default'     => [
					[ 'label' => esc_html__( 'Choose Make', 'ovinx-addon' ) ],
					[ 'label' => esc_html__( 'Choose Model', 'ovinx-addon' ) ],
					[ 'label' => esc_html__( 'Choose Year', 'ovinx-addon' ) ],
					[ 'label' => esc_html__( 'Choose Engine', 'ovinx-addon' ) ],
				],
			]
		);
	}
}
