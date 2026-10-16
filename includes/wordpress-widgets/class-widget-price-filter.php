<?php
class TFWC_Price_Filter extends WP_Widget {
    /**
     * Holds widget settings defaults, populated in constructor.
     *
     * @var array
     */
    protected $defaults;

    /**
     * Constructor
     *
     * @return TFWC_Price_Filter
     */
    public function __construct() {
        $this->defaults = array(
            'title' 	=> 'Price',
            'hide_empty' => false,
            'show_count' 	=> true,
            'price_items' =>  [
                [
                    'price' => '500',
                    'label' => 'Under $500',
                    'operator' => '<=',
                ],
                [
                    'price' => '1000',
                    'label' => 'Under $1000',
                    'operator' => '<=',
                ],
                // [
                //     'price' => '1000-2000',
                //     'label' => '$1000 - $2000',
                //     'operator' => 'BETWEEN',
                // ],
                [
                    'price' => '2000',
                    'label' => 'Under $2000',
                    'operator' => '<=',
                ],
                [
                    'price' => '2000',
                    'label' => 'Over $2000',
                    'operator' => '>='
                ]
            ]
        );
        parent::__construct(
            'tfwc_price_filter',
            esc_html__( 'TFWC - Price Filter', 'ovinx-addon' ),
            array(
                'classname'   => 'widget-facet',
                'description' => esc_html__( 'TFWC Price Filter.', 'ovinx-addon' )
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
        $price_items =  !empty( $price_items ) ? $price_items : [];

        $layout =themesflat_get_opt( 'shop_layout' );
        if (isset($_GET['sidebar'])){
            $layout = $_GET['sidebar'];
        }


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
                <ul class="collapse-body filter-group-check current-scrollbar">
                    <?php
                    foreach($price_items as $key => $item) :
                        $price = !empty($item['price']) ? $item['price'] : '';
                        $label = !empty($item['label']) ? $item['label'] : '';
                        $operator = !empty($item['operator']) ? $item['operator'] : '';
                    ?>
                        <li class="list-item">
                            <input type="checkbox" name="price" class="tf-check style-2 ovinx-price-input" id="ovinx-price-item-<?php echo esc_attr($key); ?>" data-price="<?php echo esc_attr($price); ?>" data-operator="<?php echo esc_attr($operator); ?>" >
                            <label for="ovinx-price-item-<?php echo esc_attr($key); ?>" class="label">
                                <span><?php esc_html_e($label); ?></span>
                                <!-- <span class="count-wrap">[ <span class="count">20</span> ]</span> -->
                            </label>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <?php else: ?>
            <div class="dropdown dropdown-filter">
                <div class="dropdown-toggle" id="price" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside" role="button">
                    <span class="text-uppercase"><?php esc_html_e($title ?? ''); ?></span>
                    <span class="icon icon-CaretDown"></span>
                </div>
                <div id="ovinx-price-filter" class="dropdown-menu" aria-labelledby="price">
                    <ul class="filter-group-check">
                    <?php
                        foreach($price_items as $key => $item) :
                            $price = !empty($item['price']) ? $item['price'] : '';
                            $label = !empty($item['label']) ? $item['label'] : '';
                            $operator = !empty($item['operator']) ? $item['operator'] : '';
                        ?>
                            <li class="list-item">
                                <input type="checkbox" name="price" class="tf-check style-2 ovinx-price-input" id="ovinx-price-item-<?php echo esc_attr($key); ?>" data-price="<?php echo esc_attr($price); ?>" data-operator="<?php echo esc_attr($operator); ?>">
                                <label for="ovinx-price-item-<?php echo esc_attr($key); ?>" class="label">
                                    <span><?php esc_html_e($label); ?></span>
                                    <!-- <span class="count-wrap">[ <span class="count">20</span>]</span> -->
                                </label>
                            </li>
                        <?php endforeach; ?>
                    </ul>
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
        $instance['show_count']     = isset( $new_instance['show_count'] ) ? (bool) $new_instance['show_count'] : false;
        $instance['hide_empty']     = isset( $new_instance['hide_empty'] ) ? (bool) $new_instance['hide_empty'] : false;
        return $instance;
    }

    /**
     * Widget setting
     */
    public function form( $instance ) {
        $instance = wp_parse_args( $instance, $this->defaults );
        extract( $instance );
        
        $title = !empty($title) ? $title : '';
        $hide_empty = !empty( $hide_empty ) ? (bool) $hide_empty : false;
        $show_count = !empty( $show_count ) ? (bool) $show_count : false;
        $price_items =  !empty( $price_items ) ? $price_items : [];

        ?>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Title:'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" 
                name="<?php echo esc_attr($this->get_field_name('title')); ?>" 
                type="text" value="<?php echo esc_html($title); ?>" />
        </p>

        <?php

        foreach($price_items as $key => $item) :
            $price_items[$key]['price'] = !empty($item['price']) ? $item['price'] : '';
            $price_items[$key]['label'] = !empty($item['label']) ? $item['label'] : '';
            $price_items[$key]['operator'] = !empty($item['operator']) ? $item['operator'] : '';
        ?>
            <div class="price-item" style="margin-bottom:10px; padding: 15px; border: 1px solid #afa0d5;">
                <p>
                    <label for="<?php echo esc_attr($this->get_field_id('price_items') . '_' . $key . '_price'); ?>"><?php _e('Price:'); ?></label>
                    <input class="widefat" id="<?php echo esc_attr($this->get_field_id('price_items') . '_' . $key . '_price'); ?>" 
                        name="<?php echo esc_attr($this->get_field_name('price_items') . '[' . $key . '][price]'); ?>" 
                        type="number" value="<?php echo esc_html($item['price']); ?>" />
                </p>
                <p>
                    <label for="<?php echo esc_attr($this->get_field_id('price_items') . '_' . $key . '_label'); ?>"><?php _e('Label:'); ?></label>
                    <input class="widefat" id="<?php echo esc_attr($this->get_field_id('price_items') . '_' . $key . '_label'); ?>" 
                        name="<?php echo esc_attr($this->get_field_name('price_items') . '[' . $key . '][label]'); ?>" 
                        type="text" value="<?php echo esc_html($item['label']); ?>" />
                </p>
                <p>
                    <label for="<?php echo esc_attr($this->get_field_id('price_items') . '_' . $key . '_operator'); ?>"><?php _e('Operator:'); ?></label>
                    <select class="widefat" id="<?php echo esc_attr($this->get_field_id('price_items') . '_' . $key . '_operator'); ?>" 
                        name="<?php echo esc_attr($this->get_field_name('price_items') . '[' . $key . '][operator]'); ?>">
                        <option value=">" <?php selected($item['operator'], '>'); ?>>&lt;</option>
                        <option value="<" <?php selected($item['operator'], '<'); ?>>&gt;</option>
                        <option value="<=" <?php selected($item['operator'], '<='); ?>>&lt;=</option>
                        <option value=">=" <?php selected($item['operator'], '>='); ?>>&gt;=</option>
                        <option value="=" <?php selected($item['operator'], '='); ?>>=</option>
                    </select>
                </p>
            </div>

        <?php endforeach; ?>
        
    <?php
    }
}

add_action( 'widgets_init', 'themesflat_register_price_filter' );

/**
 * Register widget
 *
 * @return void
 * @since 1.0
 */
function themesflat_register_price_filter() {
    register_widget( 'TFWC_Price_Filter' );
}