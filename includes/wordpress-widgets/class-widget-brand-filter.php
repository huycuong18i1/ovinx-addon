<?php

class TFWC_Brand_Filter extends WP_Widget {

    protected $defaults;

    /**
     * See the identical comment in TFWC_Category_Filter::$render_pass — `dynamic_sidebar('shop-sidebar')`
     * runs more than once per page load (mobile offcanvas panel + desktop sidebar/filter-top dropdown),
     * so this widget's HTML renders twice with duplicate `id="brand-<term_id>"` otherwise, breaking
     * `<label for="...">` (binds to the first duplicate in DOM, not the one actually visible/clicked).
     *
     * @var int
     */
    private static $render_pass = 0;

    public function __construct() {
        $this->defaults = array(
            'title'      => __('Brands', 'ovinx-addon'),
            'limit'      => 7,
            'hide_empty' => true,
            'show_count' => true,
        );

        parent::__construct(
            'tfwc_brand_filter',
            esc_html__( 'TFWC - Brand Filter', 'ovinx-addon' ),
            array(
                'classname'   => 'widget-facet',
                'description' => esc_html__( 'TFWC Brand Filter.', 'ovinx-addon' )
            )
        );
    }

    public function widget( $args, $instance ) {
        $instance = wp_parse_args( $instance, $this->defaults );
        extract( $instance );
        extract( $args );

        $title      = !empty( $title ) ? $title : '';
        $hide_empty = !empty( $hide_empty ) ? (bool) $hide_empty : false;
        $show_count = !empty( $show_count ) ? (bool) $show_count : false;
        $limit      = !empty( $limit ) ? absint( $limit ) : 0;

        $brands = get_terms( array(
            'taxonomy'   => 'product_brand',
            'orderby'    => 'name',
            'hide_empty' => $hide_empty,
            'number'     => $limit
        ) );

        
        $layout = themesflat_get_opt( 'shop_layout' );
        if ( isset($_GET['sidebar']) ){
            $layout = $_GET['sidebar'];
        }

        self::$render_pass++;
        $uid = 'brand-filter-' . self::$render_pass . '-';

        ob_start();

        if ($layout !== 'filter-top'):
        ?>

        <div class="widget-facet">
            <div class="facet-title fw-normal" data-bs-target="#<?php echo esc_attr( $uid . 'brand' ); ?>" role="button" data-bs-toggle="collapse"
                aria-expanded="true" aria-controls="<?php echo esc_attr( $uid . 'brand' ); ?>">
                <span class="h6"><?php esc_html_e( $title, 'ovinx' ); ?></span>
                <span class="icon icon-CaretDown"></span>
            </div>

            <div id="<?php echo esc_attr( $uid . 'brand' ); ?>" class="collapse show">
                <ul class="collapse-body filter-group-check current-scrollbar multiple-selection">
                    <?php foreach ( $brands as $brand ) : ?>
                        <?php
                            $checked = '';
                            if ( isset($_GET['brand']) && $_GET['brand'] === $brand->slug ) {
                                $checked = 'checked';
                            }

                            $disabled = $brand->count == 0 ? 'disabled' : '';
                        ?>
                        <li class="list-item <?php echo esc_attr($disabled); ?>">
                            <input
                                type="checkbox"
                                name="brand"
                                class="tf-check style-2 tf_filter_brand"
                                data-brand="<?php echo esc_attr( $brand->slug ); ?>"
                                id="<?php echo esc_attr( $uid . 'brand-' . $brand->term_id ); ?>"
                                <?php echo $checked; ?>
                            >

                            <label for="<?php echo esc_attr( $uid . 'brand-' . $brand->term_id ); ?>" class="label link">
                                <?php if($show_count) : ?>
                                    <span class="brand-text"><?php echo esc_html( $brand->name ); ?></span>
                                    <span class="count-wrap">(<span class="count"><?php echo esc_html( $brand->count ); ?></span>)</span>
                                <?php else: ?>
                                    <span class="brand-text"><?php echo esc_html( $brand->name ); ?></span>
                                <?php endif; ?>
                            </label>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <?php else : ?>

        <div class="dropdown dropdown-filter">
            <div class="dropdown-toggle" id="<?php echo esc_attr( $uid . 'brand' ); ?>" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside" role="button">
                <span class="text-uppercase"><?php esc_html_e( $title, 'ovinx' ); ?></span>
                <span class="icon icon-CaretDown"></span>
            </div>

            <div class="dropdown-menu" aria-labelledby="<?php echo esc_attr( $uid . 'brand' ); ?>">
                <ul class="filter-group-check multiple-selection">
                    <?php foreach ( $brands as $brand ) : ?>
                        <?php
                            $checked = '';
                            if ( isset($_GET['brand']) && $_GET['brand'] === $brand->slug ) {
                                $checked = 'checked';
                            }

                            $disabled = $brand->count == 0 ? 'disabled' : '';
                        ?>
                        <li class="list-item <?php echo esc_attr($disabled); ?>">
                            <input
                                type="checkbox"
                                name="brand"
                                class="tf-check style-2 tf_filter_brand"
                                data-brand="<?php echo esc_attr( $brand->slug ); ?>"
                                id="<?php echo esc_attr( $uid . 'brand-' . $brand->term_id ); ?>"
                                <?php echo $checked; ?>
                            >

                            <label for="<?php echo esc_attr( $uid . 'brand-' . $brand->term_id ); ?>" class="label">
                                <span class="brand-text"><?php echo esc_html( $brand->name ); ?></span>
                                <?php if($show_count) : ?>
                                    <span class="count-wrap">(<span class="count"><?php echo esc_html( $brand->count ); ?></span>)</span>
                                <?php endif; ?>
                            </label>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <?php endif;

        echo ob_get_clean();
    }

    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title']      = strip_tags( $new_instance['title'] );
        $instance['limit']      = intval($new_instance['limit']);
        $instance['show_count'] = isset( $new_instance['show_count'] ) ? (bool) $new_instance['show_count'] : false;
        $instance['hide_empty'] = isset( $new_instance['hide_empty'] ) ? (bool) $new_instance['hide_empty'] : false;
        return $instance;
    }

    public function form( $instance ) {
        $instance = wp_parse_args( $instance, $this->defaults );
        extract( $instance );

        $title      = !empty($title) ? $title : '';
        $hide_empty = !empty( $hide_empty ) ? (bool) $hide_empty : false;
        $show_count = !empty( $show_count ) ? (bool) $show_count : false;
        $limit      = !empty( $limit ) ? absint( $limit ) : 0;
        ?>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Title:'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" 
                name="<?php echo esc_attr($this->get_field_name('title')); ?>" 
                type="text" value="<?php echo esc_html($title); ?>" />
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('limit')); ?>"><?php _e('Show Number Brand:'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('limit')); ?>" 
                name="<?php echo esc_attr($this->get_field_name('limit')); ?>" 
                type="number" min="1" value="<?php echo esc_attr($limit); ?>" />
        </p>

        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_count); ?> 
                id="<?php echo esc_attr($this->get_field_id('show_count')); ?>" 
                name="<?php echo esc_attr($this->get_field_name('show_count')); ?>" />
            <label for="<?php echo esc_attr($this->get_field_id('show_count')); ?>">
                <?php _e('Show Number', 'ovinx-addon'); ?>
            </label>
        </p>

        <p>
            <input class="checkbox" type="checkbox" <?php checked($hide_empty); ?> 
                id="<?php echo esc_attr($this->get_field_id('hide_empty')); ?>" 
                name="<?php echo esc_attr($this->get_field_name('hide_empty')); ?>" />
            <label for="<?php echo esc_attr($this->get_field_id('hide_empty')); ?>">
                <?php _e('Hide brand no product', 'ovinx-addon'); ?>
            </label>
        </p>

        <?php
    }
}

add_action( 'widgets_init', 'themesflat_register_brand_filter' );

function themesflat_register_brand_filter() {
    register_widget( 'TFWC_Brand_Filter' );
}