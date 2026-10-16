<?php
class TFWC_Available_Filter extends WP_Widget {
    /**
     * Holds widget settings defaults, populated in constructor.
     *
     * @var array
     */
    protected $defaults;

    /**
     * Constructor
     *
     * @return TFWC_Available_Filter
     */
    public function __construct() {
        $this->defaults = array(
            'title' => __('Availability', 'ovinx'),
            'show_in_stock' => true,
            'show_out_stock' => true,
            'show_sale' => false,
        );
        parent::__construct(
            'tfwc_available_filter',
            esc_html__( 'TFWC - Available Filter', 'ovinx-addon' ),
            array(
                'classname'   => 'widget-facet',
                'description' => esc_html__( 'TFWC Available Filter.', 'ovinx-addon' )
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
        $show_in_stock = !empty( $show_in_stock ) ? (bool) $show_in_stock : false;
        $show_out_stock = !empty( $show_out_stock ) ? (bool) $show_out_stock : false;
        $show_sale = !empty( $show_sale ) ? (bool) $show_sale : false;

        $product_counts = $this->get_product_counts();

        // $args = array(
        //     'status' => 'publish',
        //     'limit' => -1,
        // );
        // $products = wc_get_products( $args );

        // $in_stock_count = 0;
        // $out_of_stock_count = 0;
        // $on_sale_count      = 0;

        // foreach ( $products as $product ) {
        //     if ( $product->get_stock_status() === 'instock' ) {
        //         $in_stock_count++;
        //     } else {
        //         $out_of_stock_count++;
        //     }

        //     if ( $product->is_on_sale() ) {
        //         $on_sale_count++;
        //     }
        // }

        $layout =themesflat_get_opt( 'shop_layout' );
        if (isset($_GET['sidebar'])){
            $layout = $_GET['sidebar'];
        }

        ob_start();

        if ($layout !== 'filter-top'):
        
        ?>

        <div class="widget-facet">
            <div class="facet-title fw-normal" data-bs-target="#availability" role="button" data-bs-toggle="collapse"
                aria-expanded="true" aria-controls="availability">
                <span class="h6"><?php esc_html_e($title); ?></span>
                <span class="icon icon-CaretDown"></span>
            </div>
            <div id="availability" class="collapse show">
                <ul class="collapse-body filter-group-check current-scrollbar">
                    <?php if($show_sale) : ?>
                    <li class="list-item">
                        <input type="checkbox" name="product_status" class="tf-check style-2" id="onSale" data-available="On Sale">
                        <label for="onSale" class="label">
                            <span class="cate-text"><?php esc_html_e( 'On Sale', 'ovinx' ); ?></span><span class="count-wrap">(<?php echo esc_html( $product_counts['sale'] ?? 0 ); ?>)</span>
                        </label>
                    </li>
                    <?php endif; ?>
                    <?php if($show_in_stock) : ?>
                    <li class="list-item">
                        <input type="checkbox" name="product_status" class="tf-check style-2" id="inStock" value="In Stock" data-available="In Stock" />
                        <label for="inStock" class="label">
                            <span class="cate-text"><?php esc_html_e( 'In Stock', 'ovinx' ); ?></span><span class="count-wrap">(<?php echo esc_html( $product_counts['in_stock'] ?? 0 ); ?>)</span>
                        </label>
                    </li>
                    <?php endif; ?>
                    <?php if($show_out_stock) : ?>
                    <li class="list-item">
                        <input type="checkbox" name="product_status" class="tf-check style-2" id="outStock" data-available="Out Stock">
                        <label for="outStock" class="label">
                            <span class="cate-text"><?php esc_html_e( 'Out of Stock', 'ovinx' ); ?></span><span class="count-wrap">(<?php echo esc_html( $product_counts['out_stock'] ?? 0 ); ?>)</span>
                        </label>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <?php else: ?>

        <div class="dropdown dropdown-filter">
            <div class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside" role="button">
                <span class="text-uppercase"><?php esc_html_e($title); ?></span>
                <span class="icon icon-CaretDown"></span>
            </div>
            <div class="dropdown-menu" id="availability" aria-labelledby="availability" style="">
                <ul class="filter-group-check">
                    <?php if($show_sale) : ?>
                        <li class="list-item">
                            <input type="checkbox" name="product_status" class="tf-check style-2" id="onSale" data-available="On Sale">
                            <label for="onSale" class="label">
                                <span class="cate-text"><?php esc_html_e( 'On Sale', 'ovinx' ); ?></span><span class="count-wrap">(<span class="count"><?php echo esc_html( $product_counts['sale'] ?? 0 ); ?></span>)</span>
                            </label>
                        </li>
                    <?php endif; ?>

                    <?php if($show_in_stock) : ?>
                        <li class="list-item">
                            <input type="checkbox" name="product_status" class="tf-check style-2" value="In Stock" id="inStock" data-available="In Stock">
                            <label for="inStock" class="label">
                                <span class="cate-text"><?php esc_html_e( 'In Stock', 'ovinx' ); ?></span><span class="count-wrap">(<span class="count"><?php echo esc_html( $product_counts['in_stock'] ?? 0 ); ?></span>)</span>
                            </label>
                        </li>
                    <?php endif; ?>
                    
                    <?php if($show_out_stock) : ?>
                        <li class="list-item disabled">
                            <input type="checkbox" name="product_status" class="tf-check style-2" id="outStock" data-available="Out Stock">
                            <label for="outStock" class="label">
                                <span class="cate-text"><?php esc_html_e( 'Out of Stock', 'ovinx' ); ?></span><span class="count-wrap">(<span class="count"><?php echo esc_html( $product_counts['out_stock'] ?? 0 ); ?></span>)</span>
                            </label>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <?php endif; ?>

		<?php echo ob_get_clean();
    }

    /**
     * Update widget
     */
    public function update( $new_instance, $old_instance ) {
        $instance               = $old_instance;
        $instance['title']      = strip_tags( $new_instance['title'] );
        $instance['show_sale']     = isset( $new_instance['show_sale'] ) ? (bool) $new_instance['show_sale'] : false;
        $instance['show_in_stock']     = isset( $new_instance['show_in_stock'] ) ? (bool) $new_instance['show_in_stock'] : false;
        $instance['show_out_stock']     = isset( $new_instance['show_out_stock'] ) ? (bool) $new_instance['show_out_stock'] : false;
        return $instance;
    }

    /**
     * Widget setting
     */
    public function form( $instance ) {
        $instance = wp_parse_args( $instance, $this->defaults );
        extract( $instance );
        
        $title = !empty($instance['title']) ? $instance['title'] : '';
        $show_sale = !empty( $show_sale ) ? (bool) $show_sale : false;
        $hide_empty = !empty( $show_in_stock ) ? (bool) $show_in_stock : false;
        $show_out_stock = !empty( $show_out_stock ) ? (bool) $show_out_stock : false;

    ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Title:'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" 
                name="<?php echo esc_attr($this->get_field_name('title')); ?>" 
                type="text" value="<?php echo esc_html($title); ?>" />
        </p>
    
        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_sale); ?> 
                    id="<?php echo esc_attr($this->get_field_id('show_sale')); ?>" 
                    name="<?php echo esc_attr($this->get_field_name('show_sale')); ?>" />
            <label for="<?php echo esc_attr($this->get_field_id('show_sale')); ?>">
                <?php _e('Show On Sale', 'ovinx-addon'); ?>
            </label>
        </p>

        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_in_stock); ?> 
                    id="<?php echo esc_attr($this->get_field_id('show_in_stock')); ?>" 
                    name="<?php echo esc_attr($this->get_field_name('show_in_stock')); ?>" />
            <label for="<?php echo esc_attr($this->get_field_id('show_in_stock')); ?>">
                <?php _e('Show In Stock', 'ovinx-addon'); ?>
            </label>
        </p>
    
        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_out_stock); ?> 
                    id="<?php echo esc_attr($this->get_field_id('show_out_stock')); ?>" 
                    name="<?php echo esc_attr($this->get_field_name('show_out_stock')); ?>" />
            <label for="<?php echo esc_attr($this->get_field_id('show_out_stock')); ?>">
                <?php _e('Show Out Stock', 'ovinx-addon'); ?>
            </label>
        </p>
    <?php
    }

    function get_product_counts() {
        $counts = [
            'sale'      => $this->get_product_count_sale(),
            'in_stock'  => $this->get_product_count([
                'key'   => '_stock_status',
                'value' => 'instock'
            ]),
            'out_stock' => $this->get_product_count([
                'key'   => '_stock_status',
                'value' => 'outofstock'
            ])
        ];
        return $counts;
    }

    function get_product_count_sale() {
        $product_ids_on_sale = wc_get_product_ids_on_sale();
    
        if (!empty($product_ids_on_sale)) {
            $args = [
                'post_type'      => 'product',
                'post_status'    => 'publish',
                'fields'         => 'ids',
                'post__in'       => $product_ids_on_sale,
                'tax_query'      => [
                    [
                        'taxonomy' => 'product_visibility',
                        'field'    => 'slug',
                        'terms'    => 'exclude-from-catalog',
                        'operator' => 'NOT IN',
                    ]
                ]
            ];
    
            $query = new WP_Query($args);
            $count = $query->found_posts;
            wp_reset_postdata();
    
            return $count;
        } else {
            return 0;
        } 
    }
    
    function get_product_count($meta_query = [], $tax_query = []) {
        $args = [
            'post_type'      => 'product',
            'post_status'    => 'publish',
            'fields'         => 'ids', 
            'meta_query'     => !empty($meta_query) ? [$meta_query] : [],
            'tax_query'      => !empty($tax_query) ? [$tax_query] : []
        ];
    
        $args['tax_query'][] = [
            'taxonomy' => 'product_visibility',
            'field'    => 'slug',
            'terms'    => 'exclude-from-catalog',
            'operator' => 'NOT IN',
        ];
    
        $query = new WP_Query($args);
        $count = $query->found_posts;
        wp_reset_postdata();
    
        return $count;
    }
}

add_action( 'widgets_init', 'themesflat_register_available_filter' );

/**
 * Register widget
 *
 * @return void
 * @since 1.0
 */
function themesflat_register_available_filter() {
    register_widget( 'TFWC_Available_Filter' );
}