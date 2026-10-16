<?php
use WCBoost\VariationSwatches\Admin\Term_Meta;

class TFWC_Attribute_Filter extends WP_Widget {
    /**
     * Holds widget settings defaults, populated in constructor.
     *
     * @var array
     */
    protected $defaults;

    /**
     * See the identical comment in TFWC_Category_Filter::$render_pass — `dynamic_sidebar('shop-sidebar')`
     * runs more than once per page load (mobile offcanvas panel + desktop sidebar/filter-top dropdown),
     * so this widget's HTML renders twice with duplicate `id="<attribute>-<term_id>"` otherwise, breaking
     * `<label for="...">` (binds to the first duplicate in DOM, not the one actually visible/clicked) and
     * the accordion's own `id="<attribute>"` collapse toggle.
     *
     * @var int
     */
    private static $render_pass = 0;

    /**
     * Constructor
     *
     * @return TFWC_Attribute_Filter
     */
    public function __construct() {
        $this->defaults = array(
            'title' 	=> 'Attribute', 
            'attribute' => '',
            'show_count' 	=> true,
            'multiple' 	=> false,
            'display_type' => 'label'
        );
        parent::__construct(
            'tfwc_attribute_filter',
            esc_html__( 'TFWC - Attribute Filter', 'ovinx-addon' ),
            array(
                'classname'   => 'widget-facet',
                'description' => esc_html__( 'TFWC Attribute Filter.', 'ovinx-addon' )
            )
        );
    }

    /**
     * Display widget
     */
    public function widget($args, $instance) {

        $title = !empty($instance['title']) ? apply_filters('widget_title', $instance['title']) : '';
        $attribute = !empty($instance['attribute']) ? wc_sanitize_taxonomy_name($instance['attribute']) : '';
        $show_count = isset($instance['show_count']) ? (bool) $instance['show_count'] : false;
        // Shop-filter checkboxes (size/color/etc) are expected to be multi-select by default (filter
        // by Size M OR L, not just one at a time) — the per-widget "Multiple Selection" admin toggle
        // this used to gate on is no longer honored, it's simply always on.
        $multiple = true;
        $display_type = !empty($instance['display_type']) ? $instance['display_type'] : '';

        if (!$attribute) {
            return;
        }

        // A saved widget instance can point at an attribute taxonomy that no longer exists
        // in this store (e.g. removed, or never created for this project) — get_taxonomy()
        // then returns false, not a taxonomy object, so render nothing instead of warning.
        if (!taxonomy_exists($attribute)) {
            return;
        }

        $attribute_type = $display_type ? $display_type : $this->get_attribute_type(str_replace('pa_', '', $attribute));

        $attribute_terms = get_terms( array(
            'taxonomy' => $attribute,
            'hide_empty' => true,
        ) );

        $taxonomy = get_taxonomy( $attribute );
        
        $attribute_label = $taxonomy->labels->singular_name;

        if( !empty($title) ) {
            $attribute_label = $title;
        }

        $layout =themesflat_get_opt( 'shop_layout' );
        if (isset($_GET['sidebar'])){
            $layout = $_GET['sidebar'];
        }

        self::$render_pass++;
        $uid = 'attr-filter-' . self::$render_pass . '-';

        ob_start();

        if ($layout !== 'filter-top'):

        ?>

        <div class="widget-facet">
            <div class="facet-title fw-normal" data-bs-target="#<?php echo esc_attr( $uid . $attribute ); ?>" role="button" data-bs-toggle="collapse"
                aria-expanded="true" aria-controls="<?php echo esc_attr( $uid . $attribute ); ?>">
                <span class="h6"><?php echo esc_html($attribute_label); ?></span>
                <span class="icon icon-CaretDown"></span>
            </div>
            <div id="<?php echo esc_attr( $uid . $attribute ); ?>" class="collapse show">
                <?php $this->display_attribute_html($attribute_terms, $show_count, $attribute_type, $attribute, $multiple, $uid); ?>
            </div>
        </div>

        <?php else: ?>
            <div class="dropdown dropdown-filter">
                <div class="dropdown-toggle" id="<?php echo esc_attr( $uid . 'attr-' . $attribute ); ?>" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside" role="button">
                    <span class="text-uppercase"><?php echo esc_html($attribute_label); ?></span>
                    <span class="icon icon-CaretDown"></span>
                </div>
                <div class="dropdown-menu" aria-labelledby="<?php echo esc_attr( $uid . 'attr-' . $attribute ); ?>">
                    <!-- <ul class="filter-group-check"> -->
                        <?php $this->display_attribute_html($attribute_terms, $show_count, $attribute_type, $attribute, $multiple, $uid); ?>
                    <!-- </ul> -->
                </div>
            </div>
        <?php endif; ?>

        <?php
        echo ob_get_clean();
    }

    /**
     * Update widget
     */
    public function update( $new_instance, $old_instance ) {
        $instance               = $old_instance;
        $instance['title']      = strip_tags( $new_instance['title'] );
        $instance['attribute'] = (!empty($new_instance['attribute'])) ? wc_sanitize_taxonomy_name($new_instance['attribute']) : '';
        $instance['show_count'] = isset($new_instance['show_count']) ? (bool) $new_instance['show_count'] : false;
        $instance['multiple'] = isset($new_instance['multiple']) ? (bool) $new_instance['multiple'] : false;
        $instance['display_type'] = (!empty($new_instance['display_type'])) ? sanitize_text_field($new_instance['display_type']) : ''; 
        return $instance;
    }

    /**
     * Widget setting
     */
    public function form( $instance ) {
        $instance = wp_parse_args( $instance, $this->defaults );
        extract( $instance );
        
        $title = !empty($instance['title']) ? $instance['title'] : '';
        $attribute = !empty($instance['attribute']) ? $instance['attribute'] : '';
        $show_count = isset($instance['show_count']) ? (bool) $instance['show_count'] : false;
        $multiple = isset($instance['multiple']) ? (bool) $instance['multiple'] : false;
        $display_type = !empty($instance['display_type']) ? $instance['display_type'] : '';
        $attributes = wc_get_attribute_taxonomies();

    ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Title:'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('attribute')); ?>"><?php _e('Attribute:'); ?></label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('attribute')); ?>" name="<?php echo esc_attr($this->get_field_name('attribute')); ?>">
                <?php foreach ($attributes as $attr) : ?>
                    <option value="pa_<?php echo esc_attr($attr->attribute_name); ?>" <?php selected($attribute, 'pa_' . $attr->attribute_name); ?>>
                        <?php echo esc_html($attr->attribute_label); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('display_type')); ?>"><?php _e('Display Type:'); ?></label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('display_type')); ?>" name="<?php echo esc_attr($this->get_field_name('display_type')); ?>">
                <option value="" <?php selected($display_type, ''); ?>><?php _e('Default (Based on Attribute Type)'); ?></option>
                <option value="color" <?php selected($display_type, 'color'); ?>><?php _e('Color'); ?></option>
                <option value="button" <?php selected($display_type, 'button'); ?>><?php _e('Button'); ?></option>
                <option value="label" <?php selected($display_type, 'label'); ?>><?php _e('Label'); ?></option>
                <option value="image" <?php selected($display_type, 'image'); ?>><?php _e('Image'); ?></option>
            </select>
        </p>
        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_count); ?> id="<?php echo esc_attr($this->get_field_id('show_count')); ?>" name="<?php echo esc_attr($this->get_field_name('show_count')); ?>" />
            <label for="<?php echo esc_attr($this->get_field_id('show_count')); ?>">
                <?php _e('Show Count', 'ovinx-addon'); ?>
            </label>
        </p>
        <p>
            <input class="checkbox" type="checkbox" <?php checked($multiple); ?> id="<?php echo esc_attr($this->get_field_id('multiple')); ?>" name="<?php echo esc_attr($this->get_field_name('multiple')); ?>" />
            <label for="<?php echo esc_attr($this->get_field_id('multiple')); ?>">
                <?php _e('Multiple Selection', 'ovinx-addon'); ?>
            </label>
        </p>
    <?php
    }

    public function display_attribute_html($attribute_terms, $show_count, $attribute_type, $attribute, $multiple = false, $uid = '') {

        $layout =themesflat_get_opt( 'shop_layout' );
        if (isset($_GET['sidebar'])){
            $layout = $_GET['sidebar'];
        }

        $multiple_classes = empty($multiple) ? '' : 'multiple-selection';

        ?>

        <?php if($attribute_type == 'color') : ?>
            <ul class="collapse-body filter-group-check current-scrollbar group-check-color <?php echo esc_attr($multiple_classes); ?>">
                <?php if ( is_array( $attribute_terms ) ) : ?>
                    <?php foreach ( $attribute_terms as $term ) : ?>
                        <li class="list-item tf-attribute-filter <?php echo esc_attr($attribute); ?>-item <?php echo esc_attr($attribute); ?>-check">
                            <fieldset class="field-color">
                                <input type="checkbox" name="<?php echo esc_attr($attribute); ?>" class="tf-check style-2" id="<?php echo esc_attr( $uid . $attribute . '-' . $term->term_id ); ?>" data-name="<?php echo esc_attr( $term->name ); ?>" data-value="<?php echo esc_attr( $term->term_id ); ?>">
                                <label for="<?php echo esc_attr( $uid . $attribute . '-' . $term->term_id ); ?>" class="color overflow-hidden">
                                    <?php
                                    if ( class_exists( '\WCBoost\VariationSwatches\Admin\Term_Meta' ) ) {
                                        $color = \WCBoost\VariationSwatches\Admin\Term_Meta::instance()->get_meta( $term->term_id,'color' );
                                    } else {
                                        $color = get_term_meta($term->term_id, 'color', true);
                                    }
                            
                                    $bg_color = $color ? esc_attr($color) : esc_attr(strtolower($term->slug));
                                    
                                    $class_white = '';
            
                                    if ( strtolower($bg_color) === '#ffffff' || strtolower($bg_color) === '#fff'  || strtolower($bg_color) === 'white' ) {
                                        $class_white = 'line';
                                    }
                                    ?>
                                    <span class="attribute-color-type d-flex w-100 h-100 <?php echo esc_attr($class_white); ?>" style="background:<?php echo esc_attr($bg_color); ?>"></span>
                                </label>
                            </fieldset>

                            <label for="<?php echo esc_attr( $uid . $attribute . '-' . $term->term_id ); ?>" class="label">
                                <span class="color-text"><?php echo esc_html( ucfirst( $term->name ) ); ?></span>
                                <?php if($show_count) : ?>
                                    <span class="count">(<span class="count"><?php echo esc_html( $term->count ); ?></span>)</span>
                                <?php endif; ?>
                            </label>
                            
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
            <?php return; ?>
        <?php endif; ?>

        <?php if($attribute_type == 'image') : ?>
            <ul class="collapse-body filter-group-check current-scrollbar <?php echo esc_attr($multiple_classes); ?>">
                <?php if ( is_array( $attribute_terms ) ) : ?>
                    <?php foreach ( $attribute_terms as $term ) : ?>
                        <li class="list-item tf-attribute-filter <?php echo esc_attr($attribute); ?>-item <?php echo esc_attr($attribute); ?>-check">
                            <input type="checkbox" name="<?php echo esc_attr($attribute); ?>" class="tf-check style-2" id="<?php echo esc_attr( $uid . $attribute . '-' . $term->term_id ); ?>" data-name="<?php echo esc_attr( $term->name ); ?>" data-value="<?php echo esc_attr( $term->term_id ); ?>">
                            <label for="<?php echo esc_attr( $uid . $attribute . '-' . $term->term_id ); ?>" class="label">
                                <?php
                                if ( class_exists( '\WCBoost\VariationSwatches\Admin\Term_Meta' ) ) {
                                    $image = \WCBoost\VariationSwatches\Admin\Term_Meta::instance()->get_meta( $term->term_id,'image' );
                                } else {
                                    $image = get_term_meta($term->term_id, 'image', true);
                                }
        
                                $image_url = $image ? wp_get_attachment_image_src( $image ) : '';
                                $image_url = $image_url ? $image_url[0] : WC()->plugin_url() . '/assets/images/placeholder.webp';
                                ?>
                                <?php if(!empty($image_url)) : ?>
                                    <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_html( ucfirst( $term->name ) ); ?>" class="img-check">
                                <?php endif; ?>
                                <span><?php echo esc_html( ucfirst( $term->name ) ); ?></span>
                                <?php if($show_count) : ?>
                                    <span class="count-wrap">[ <span class="count"><?php echo esc_html( $term->count ); ?></span> ]</span>
                                <?php endif; ?>
                            </label>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
            <?php return; ?>
        <?php endif; ?>

        <?php if($attribute_type == 'button') : ?>
            <?php if($layout != 'filter-top') : ?>
            <ul class="collapse-body filter-group-size filter-group-check attribute-button-type-list tf-attribute-filter-button-type <?php echo esc_attr($multiple_classes); ?>">
                <?php if ( is_array( $attribute_terms ) ) : ?>
                    <?php foreach ( $attribute_terms as $term ) : ?>
                        <?php

                            if ( class_exists( Term_Meta::class ) ) {
                                $label = Term_Meta::instance()->get_meta( $term->term_id, 'label' );
                            }

                            if( empty($label) ) {
                                $label = $term->name;
                            }

                        ?>
                        <div class="check-item tf-attribute-filter <?php echo esc_attr($attribute); ?>-item <?php echo esc_attr($attribute); ?>-check">
                            <input type="checkbox" name="<?php echo esc_attr($attribute); ?>" class="tf-check style-2 ip-size d-none" id="<?php echo esc_attr( $uid . $attribute . '-' . $term->term_id ); ?>" data-name="<?php echo esc_attr( $label ); ?>" data-value="<?php echo esc_attr( $term->term_id ); ?>">
                            <label for="<?php echo esc_attr( $uid . $attribute . '-' . $term->term_id ); ?>" class="label-size <?php echo (mb_strlen($label, 'UTF-8') > 3 ? 'over-size' : ''); ?>">
                                <span class="size-text fw-medium">
                                    <?php echo esc_html( ucfirst( $label ) ); ?>
                                </span>
                            </label>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
            <?php else : ?>
                <div class="filter-group-size filter-group-check flat-check-list tf-attribute-filter-button-type <?php echo esc_attr($multiple_classes); ?>">
                <?php foreach ( $attribute_terms as $term ) : ?>
                    <?php

                        if ( class_exists( Term_Meta::class ) ) {
                            $label = Term_Meta::instance()->get_meta( $term->term_id, 'label' );
                        }

                        if( empty($label) ) {
                            $label = $term->name;
                        }
                            
                    ?>
                    <div class="check-item size-item size-check tf-attribute-filter <?php echo esc_attr($attribute); ?>-item <?php echo esc_attr($attribute); ?>-check">
                        <input type="checkbox" name="<?php echo esc_attr($attribute); ?>" class="tf-check style-2 ip-size d-none" id="<?php echo esc_attr( $uid . $attribute . '-' . $term->term_id ); ?>" data-name="<?php echo esc_attr( $label ); ?>" data-value="<?php echo esc_attr( $term->term_id ); ?>">
                        <label for="<?php echo esc_attr( $uid . $attribute . '-' . $term->term_id ); ?>" class="label-size <?php echo (mb_strlen($label, 'UTF-8') > 3 ? 'over-size' : ''); ?>">
                            <span class="size-text fw-medium">
                                <?php echo esc_html( ucfirst( $label ) ); ?>
                            </span>
                        </label>
                    </div>
                <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <?php return; ?>
        <?php endif; ?>

        <ul class="collapse-body filter-group-check current-scrollbar <?php echo esc_attr($multiple_classes); ?>">
            <?php if ( is_array( $attribute_terms ) ) : ?>
                <?php foreach ( $attribute_terms as $term ) : ?>
                    <li class="list-item tf-attribute-filter <?php echo esc_attr($attribute); ?>-item <?php echo esc_attr($attribute); ?>-check">
                        <input type="checkbox" name="<?php echo esc_attr($attribute); ?>" class="tf-check style-2" id="<?php echo esc_attr( $uid . $attribute . '-' . $term->term_id ); ?>" data-name="<?php echo esc_attr( $term->name ); ?>" data-value="<?php echo esc_attr( $term->term_id ); ?>">
                        <label for="<?php echo esc_attr( $uid . $attribute . '-' . $term->term_id ); ?>" class="label">
                            <span><?php echo esc_html( ucfirst( $term->name ) ); ?></span>
                            <?php if($show_count) : ?>
                                <span class="count-wrap">[ <span class="count"><?php echo esc_html( $term->count ); ?></span> ]</span>
                            <?php endif; ?>
                        </label>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>

        <?php return; ?>

        <?php
    }

    public function get_attribute_type($attribute) {
        $attribute_slug = str_replace('pa_', '', $attribute);
        $all_attributes = wc_get_attribute_taxonomies();

        if (!empty($all_attributes)) {
            foreach ($all_attributes as $attr) {
                if ($attr->attribute_name === $attribute_slug) {
                    return $attr->attribute_type;
                    break;
                }
            }
        }
        return 'label';
    }
    
}

add_action( 'widgets_init', 'themesflat_register_attribute_filter' );

/**
 * Register widget
 *
 * @return void
 * @since 1.0
 */
function themesflat_register_attribute_filter() {
    register_widget( 'TFWC_Attribute_Filter' );
}