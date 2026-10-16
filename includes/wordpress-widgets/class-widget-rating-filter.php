<?php
if (!defined('ABSPATH')) {
    exit;
}

class TFWC_Rating_Filter extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'tfwc_rating_filter',
            esc_html__('TFWC - Rating Filter', 'ovinx-addon'),
            [
                'classname'   => 'widget-facet',
                'description' => esc_html__('Display customer rating filter', 'ovinx-addon')
            ]
        );
    }

    /**
     * Frontend output (ONLY UI)
     */
    public function widget($args, $instance) {

        $title = !empty($instance['title'])
            ? esc_html($instance['title'])
            : esc_html__('Customer Rating', 'ovinx-addon');

        $min = isset($instance['min_rating']) ? intval($instance['min_rating']) : 1;
        $max = isset($instance['max_rating']) ? intval($instance['max_rating']) : 4;

        if ($min > $max) {
            return;
        }

        // Load product IDs via WooCommerce API
        $product_ids = wc_get_products([
            'status' => 'publish',
            'limit'  => -1,
            'return' => 'ids',
        ]);

        if (empty($product_ids)) {
            return;
        }

        // Init count
        $counts = [];
        for ($i = $min; $i <= $max; $i++) {
            $counts[$i] = 0;
        }

        foreach ($product_ids as $product_id) {
            $product = wc_get_product($product_id);
            if (!$product) {
                continue;
            }

            $avg = (float) $product->get_average_rating();

            foreach ($counts as $rate => $val) {
                if ($avg >= $rate) {
                    $counts[$rate]++;
                }
            }
        }

        $layout =themesflat_get_opt( 'shop_layout' );
        if (isset($_GET['sidebar'])){
            $layout = $_GET['sidebar'];
        }
        if ($layout !== 'filter-top'):
        
        ?>
        <div class="widget-facet filter-rating">
            <div class="facet-title fw-normal"
                 data-bs-target="#rating"
                 role="button"
                 data-bs-toggle="collapse"
                 aria-expanded="true"
                 aria-controls="rating">
                <h6><?php echo $title; ?></h6>
                <span class="icon icon-CaretDown"></span>
            </div>

            <div id="rating" class="collapse show">
                <ul class="collapse-body filter-group-check current-scrollbar">

                    <?php for ($rate = $max; $rate >= $min; $rate--) : ?>
                        <?php if (empty($counts[$rate])) continue; ?>

                        <li class="list-item">
                            <input type="radio"
                                   name="rate"
                                   class="tf-check style-2 input-rating-filter"
                                   id="rate-<?php echo esc_attr($rate); ?>"
                                   value="<?php echo esc_attr($rate); ?>">

                            <label for="rate-<?php echo esc_attr($rate); ?>" class="label">
                                <?php echo $this->render_stars($rate); ?>
                                <span class="count">(<?php echo esc_html($counts[$rate]); ?>)</span>
                            </label>
                        </li>

                    <?php endfor; ?>

                </ul>
            </div>
        </div>
         <?php else : ?>
            <div class="dropdown dropdown-filter">
                <div class="dropdown-toggle" id="rating" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside" role="button">
                    <span class="text-uppercase"><?php esc_html_e( $title, 'ovinx' ); ?></span>
                    <span class="icon icon-CaretDown"></span>
                </div>
                <div class="dropdown-menu" aria-labelledby="rating" style="">
                    <ul class="filter-group-check">
                        <?php for ($rate = $max; $rate >= $min; $rate--) : ?>
                            <?php if (empty($counts[$rate])) continue; ?>

                            <li class="list-item">
                                <input type="radio"
                                    name="rate"
                                    class="tf-check style-2 input-rating-filter"
                                    id="rate-<?php echo esc_attr($rate); ?>"
                                    value="<?php echo esc_attr($rate); ?>">

                                <label for="rate-<?php echo esc_attr($rate); ?>" class="label">
                                    <?php echo $this->render_stars($rate); ?>
                                    <span class="count">(<?php echo esc_html($counts[$rate]); ?>)</span>
                                </label>
                            </li>

                        <?php endfor; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>
        <?php
    }

    /**
     * Render stars HTML
     */
    private function render_stars($rate) {
        ob_start();
        ?>
        <ul class="rate-list text-black">
            <?php for ($i = 1; $i <= 5; $i++) : ?>
                <li>
                    <i class="icon fs-16 <?php echo ($i <= $rate) ? 'icon-star-4' : 'icon-star'; ?>"></i>
                </li>
            <?php endfor; ?>
            <li class="text_up">&amp; up</li>
        </ul>
        <?php
        return ob_get_clean();
    }

    /**
     * Widget settings
     */
    public function form($instance) {

        $title = isset($instance['title']) ? $instance['title'] : '';
        $min   = isset($instance['min_rating']) ? intval($instance['min_rating']) : 1;
        $max   = isset($instance['max_rating']) ? intval($instance['max_rating']) : 4;
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">
                Title
            </label>
            <input class="widefat"
                   id="<?php echo esc_attr($this->get_field_id('title')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('title')); ?>"
                   type="text"
                   value="<?php echo esc_attr($title); ?>"
                   placeholder="Customer Rating">
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('min_rating')); ?>">
                <?php _e('Min rating', 'ovinx-addon'); ?>
            </label>
            <input type="number"
                   class="small-text"
                   min="1" max="5"
                   id="<?php echo esc_attr($this->get_field_id('min_rating')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('min_rating')); ?>"
                   value="<?php echo esc_attr($min); ?>">
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('max_rating')); ?>">
                <?php _e('Max rating', 'ovinx-addon'); ?>
            </label>
            <input type="number"
                   class="small-text"
                   min="1" max="5"
                   id="<?php echo esc_attr($this->get_field_id('max_rating')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('max_rating')); ?>"
                   value="<?php echo esc_attr($max); ?>">
        </p>
        <?php
    }

    public function update($new, $old) {
        return [
            'title'      => sanitize_text_field($new['title']),
            'min_rating' => intval($new['min_rating']),
            'max_rating' => intval($new['max_rating']),
        ];
    }
}

/**
 * Register widget
 */
add_action('widgets_init', function () {
    register_widget('TFWC_Rating_Filter');
});