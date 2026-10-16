<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<section class="tf-flat-spacing">
    <div class="container">
        <div class="cls-accordion wow fadeInUp animated" id="accordionCls-<?php echo esc_attr($widget_id); ?>" style="visibility: visible; animation-name: fadeInUp;">

         <?php $index = 0; ?>
            <?php foreach($categories as $key => $category) : ?>
            <div class="widget-accordion">
                <div class="accordion-title <?php echo esc_attr( $index != 0 ? 'collapsed' : ''); ?>" data-bs-target="#<?php echo esc_attr($key. "-" . $widget_id); ?>" data-bs-toggle="collapse" aria-expanded="false" aria-controls="<?php echo esc_attr($key. "-" . $widget_id); ?>" role="button">
                    <span class="icon icon-arrow-right-down"></span>
                    <span><?php echo esc_html($category['name']); ?></span>
                </div>
                <div id="<?php echo esc_attr($key. "-" . $widget_id); ?>" class="collapse <?php echo esc_attr( $index == 0 ? 'show' : ''); ?>" data-bs-parent="#accordionCls-<?php echo esc_attr($widget_id); ?>" style="">
                    <div class="accordion-body">
                        <div class="image">
                            <a href="<?php echo esc_url($category['url']); ?>">
                                <img src="<?php echo esc_html($category['image']); ?>" class="lazyload" alt="<?php echo esc_html($category['name']); ?>" >
                            </a>
                        </div>
                        <div class="cls-info">
                            <a href="<?php echo esc_url($category['url']); ?>">
                                <h6 class="count-item"><?php echo esc_html($category['count']); ?><?php echo _n( ' ITEM', ' ITEMS', $category['count'], 'ovinx-addon' ) ?></h6>
                            </a>
                            <p class="text-main-4">
                                <?php echo wp_kses_post($category['description']); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <?php $index++; ?>
        <?php endforeach; ?>
        </div>
    </div>
</section>