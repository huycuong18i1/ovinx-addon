<?php
class TFWC_Price_Slider_Filter extends WP_Widget {
    /**
     * Holds widget settings defaults, populated in constructor.
     *
     * @var array
     */
    protected $defaults;

    /**
     * Constructor
     *
     * @return TFWC_Price_Slider_Filter
     */
    public function __construct() {
        $this->defaults = array(
            'title' 	=> 'Filter By Price',
            // 'min_value' => 0,
            // 'max_value' => 500,
        );
        parent::__construct(
            'tfwc_price_slider_filter',
            esc_html__( 'TFWC - Price Slider Filter', 'ovinx-addon' ),
            array(
                'classname'   => 'widget-facet',
                'description' => esc_html__( 'TFWC Price Slider Filter.', 'ovinx-addon' )
            )
        );
    }

    /**
     * Display widget
     */
    public function widget( $args, $instance ) {
        $instance = wp_parse_args( $instance, $this->defaults );
        extract( $instance );
        extract( $args );

        $title = !empty($title) ? $title : '';

        // $min = isset($instance['min_value']) ? intval($instance['min_value']) : 0;
        // $max = isset($instance['max_value']) ? intval($instance['max_value']) : 500;

        global $wpdb;

        // QA P5-05: only published products/variations — the lookup table also keeps drafts and trashed items,
        // which inflated the slider range (e.g. $1,234,568 from a draft test product).
        $prices = $wpdb->get_row("
            SELECT
                MIN(l.min_price) AS min_price,
                MAX(l.max_price) AS max_price
            FROM {$wpdb->wc_product_meta_lookup} l
            INNER JOIN {$wpdb->posts} p ON p.ID = l.product_id
            WHERE l.min_price > 0
              AND p.post_status = 'publish'
              AND p.post_type IN ('product', 'product_variation')
        ");

        $min = isset($prices->min_price) ? floor($prices->min_price) : 0;
        $max = isset($prices->max_price) ? ceil($prices->max_price) : 500;

        if ($min > $max) {
            $min = $min - $max;
            $max = $max + $min;
            $min = $max - $min;
            return;
        }

        $layout =themesflat_get_opt( 'shop_layout' );
        if (isset($_GET['sidebar'])){
            $layout = $_GET['sidebar'];
        }

        $currency_symbol = function_exists( 'get_woocommerce_currency_symbol' ) ? ( get_woocommerce_currency_symbol() ?: '$' ) : '$';


        $widget_id = ( $this->id );

        ob_start();
        
        if ($layout !== 'filter-top'):

        ?>

        <div class="widget-facet">
            <div class="facet-title fw-normal" data-bs-target="#ovinx-price-filter-<?php echo esc_attr($widget_id); ?>" role="button" data-bs-toggle="collapse" aria-expanded="true"
                aria-controls="ovinx-price-filter-<?php echo esc_attr($widget_id); ?>">
                <span class="h6"><?php esc_html_e($title ?? ''); ?></span>
                <span class="icon icon-CaretDown"></span> 
            </div>
            <div id="ovinx-price-filter-<?php echo esc_attr($widget_id); ?>" class="collapse show">
                <div class="collapse-body widget-price filter-price">
                    <div class="price-val-range" id="price-value-range-<?php echo esc_attr($widget_id); ?>" data-min="<?php echo esc_attr($min); ?>" data-max="<?php echo esc_attr($max); ?>"></div>
                    <div class="price-box tf-grid-layout tf-col-2">
                        <div class="box-wrap">
                            <!-- <p class="text-primary"><?php _e('Min price', 'ovinx-addon'); ?></p> -->
                            <div class="price-val_wrap">
                                <span class="cl-text-2 text-body-1"><?php echo esc_html($currency_symbol);?></span>
                                <div class="price-min-value price-val" id="price-min-value" data-currency="<?php echo esc_html($currency_symbol);?>"><?php echo esc_html($min); ?></div>
                            </div>
                        </div>
                        <div class="box-wrap">
                            <!-- <p class="text-primary"><?php _e('Max price', 'ovinx-addon'); ?></p> -->
                            <div class="price-val_wrap">
                                <span class="cl-text-2 text-body-1"><?php echo esc_html($currency_symbol);?></span>
                                <div class="price-max-value price-val" id="price-max-value" data-currency="<?php echo esc_html($currency_symbol);?>"><?php echo esc_html($max); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php endif; ?>

		<?php echo ( ob_get_clean() );
    }

    /**
     * Update widget
     */
    public function update( $new_instance, $old_instance ) {
        $instance               = $new_instance;
        $instance['title']      = strip_tags( $new_instance['title'] );
        return $instance;
    }

    /**
     * Widget setting
     */
    public function form( $instance ) {
        $instance = wp_parse_args( $instance, $this->defaults );
        extract( $instance );
        
        $title = !empty($title) ? $title : '';
        // $min_value = !empty( $min_value ) ? $min_value : 0;
        // $max_value = !empty( $max_value ) ? $max_value : 500;

        ?>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Title:'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" 
                name="<?php echo esc_attr($this->get_field_name('title')); ?>" 
                type="text" value="<?php echo esc_html($title); ?>" />
        </p>

        <?php return; ?>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('min_value')); ?>">
                <?php _e('Min rating', 'ovinx'); ?>
            </label>
            <input type="number"
                   class="small-text"
                   min="0" max=""
                   id="<?php echo esc_attr($this->get_field_id('min_value')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('min_value')); ?>"
                   value="<?php echo esc_attr($min_value); ?>">
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('max_value')); ?>">
                <?php _e('Max rating', 'ovinx'); ?>
            </label>
            <input type="number"
                   class="small-text"
                   min="0" max=""
                   id="<?php echo esc_attr($this->get_field_id('max_value')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('max_value')); ?>"
                   value="<?php echo esc_attr($max_value); ?>">
        </p>
        
    <?php
    }
}

add_action( 'widgets_init', 'themesflat_register_price_slider_filter' );

/**
 * Register widget
 *
 * @return void
 * @since 1.0
 */
function themesflat_register_price_slider_filter() {
    register_widget( 'TFWC_Price_Slider_Filter' );
}