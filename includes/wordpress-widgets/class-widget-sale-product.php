<?php
class Themesflat_Sale_Products_Widget extends WP_Widget {

    function __construct() {
        parent::__construct(
            'themesflat_sale_products_widget', 
            esc_html__('TFWC - Product Sale', 'ovinx-addon'),
            array(
                'classname'   => 'themesflat_sale_products_widget widget-facet',
                'description' => esc_html__('Sale product display', 'ovinx-addon')
            )
        );
    }

    public function widget($args, $instance) {
        $title = !empty($instance['title']) ? $instance['title'] : 'On Sale';
        $limit = !empty($instance['limit']) ? $instance['limit'] : 2;
        $attribute_to_show = !empty($instance['attribute_to_show']) ? $instance['attribute_to_show'] : 'color'; 

        echo $args['before_widget'];

        if (!empty($instance['title'])) {
            $title_class = 'widget-title facet-title text-xl fw-medium';
            echo '<h4 class="' . esc_attr($title_class) . '" <span>' . esc_html($title) . '</span></h4>';
        }

        $sale_args = array(
            'post_type' => 'product', 
            'posts_per_page' => $limit, 
            'post_status' => 'publish', 
            'orderby' => 'date', 
            'order' => 'DESC', 
            'meta_query' => array(
                array(
                    'key' => '_sale_price',
                    'value' => 0,
                    'compare' => '>',
                    'type' => 'NUMERIC'
                )
            )
        );

        $sale_products = new WP_Query($sale_args);

        if ($sale_products->have_posts()) :
            echo '<ul class="collapse-body list-recent">';
            while ($sale_products->have_posts()) : $sale_products->the_post();      
                global $product;

                $thumbnail = get_the_post_thumbnail(get_the_ID(), 'thumbnail');

                $product_title = get_the_title();

                $attributes = $product->get_attributes();
                $attribute_value = '';
                $attribute_count = 0;

                if ($attribute_to_show && isset($attributes['pa_' . $attribute_to_show])) {
                    $attribute = $attributes['pa_' . $attribute_to_show];
                    $attribute_value = $attribute->get_options();
                    $attribute_count = count($attribute_value); 
                }

                echo '<li><div class="recent-blog-item">';
                echo '<a class="img-product" href="' . get_permalink() . '">';
                if ($thumbnail) {
                    echo $thumbnail ;
                }
                echo '</a> <div class="content">';
                echo '  <a href="' . get_permalink() . '" class="title text-md link fw-medium">' . esc_html($product_title) . '</a>';

                ?>
                <div class="price text-md fw-medium">
                    <?php if ($product->is_on_sale() ): ?>
                        <?php if ( $product->is_type( 'variable' ) ) : ?>
                        <span class="new-price"><?php echo (wc_price($product->get_price())); ?></span>
                        <span class="old-price"><?php echo (wc_price( $product->get_variation_regular_price( 'min' ) )); ?></span>
                    <?php else: ?>
                        <span class="new-price"><?php echo wc_price($product->get_sale_price()); ?></span>
                        <span class="old-price"><?php echo wc_price($product->get_regular_price()); ?></span>
                    <?php endif; ?>
                    <?php else : ?>
                        <span class="new-price"><?php echo wc_price($product->get_price()); ?></span>
                    <?php endif; ?>
                </div>
                <?php        
                if ($attribute_count > 0) {
                    echo '<p class="text-sm">' . $attribute_count .' ' . esc_html(str_replace('_', ' ', $attribute_to_show)) . ' available</p>';
                }

                echo '</div></div></li>';
            endwhile;
            echo '</ul>';
        else :
            echo '<p>No Sale products available.</p>';
        endif;

        wp_reset_postdata();

        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : 'On sale';
        $limit = !empty($instance['limit']) ? $instance['limit'] : 2;
        $attribute_to_show = !empty($instance['attribute_to_show']) ? $instance['attribute_to_show'] : 'color'; 

        $attributes = wc_get_attribute_taxonomies();
        $attribute_options = array();
        foreach ($attributes as $attribute) {
            $attribute_options[$attribute->attribute_name] = ucfirst(str_replace('_', ' ', $attribute->attribute_name));
        }
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php echo esc_html__('Title:', 'ovinx-addon')?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('limit'); ?>"><?php echo esc_html__('Number of products to show:', 'ovinx-addon')?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('limit'); ?>" name="<?php echo $this->get_field_name('limit'); ?>" type="number" value="<?php echo esc_attr($limit); ?>" min="1" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('attribute_to_show'); ?>"><?php echo esc_html__('Select Attribute to Display:', 'ovinx-addon')?></label>
            <select class="widefat" id="<?php echo $this->get_field_id('attribute_to_show'); ?>" name="<?php echo $this->get_field_name('attribute_to_show'); ?>">
                <option value=""><?php echo esc_html__('-- Select Attribute --', 'ovinx-addon')?></option>
                <?php
                foreach ($attribute_options as $slug => $name) {
                    echo '<option value="' . esc_attr($slug) . '" ' . selected($attribute_to_show, $slug, false) . '>' . esc_html($name) . '</option>';
                }
                ?>
            </select>
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = !empty($new_instance['title']) ? strip_tags($new_instance['title']) : 'On Sale';
        $instance['limit'] = !empty($new_instance['limit']) ? absint($new_instance['limit']) : 2;
        $instance['attribute_to_show'] = !empty($new_instance['attribute_to_show']) ? strip_tags($new_instance['attribute_to_show']) : 'color';

        return $instance;
    }
}

function register_sale_products_widget() {
    register_widget('Themesflat_Sale_Products_Widget');
}
add_action('widgets_init', 'register_sale_products_widget');
