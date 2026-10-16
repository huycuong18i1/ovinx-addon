<?php

    if( ! defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly.
    }

    if( empty( $category ) ){
        return;
    }

    $placeholder = \Elementor\Utils::get_placeholder_image_src();

    $image_style = '';

    if( !empty($aspect_ratio) ){
        $image_style = "aspect-ratio: $aspect_ratio !important;";
    }

    $icon = !empty($button_icon['value']) ? $button_icon['value'] : "";

    $box_classes.=' hover-img4 wow fadeInUp';

    if( !empty( $has_carousel ) ){
        $box_classes.=' swiper-slide';
    }
    $item_text = $category['count'] == 1 ? 'item' : 'items';
    
    if(empty($style)){
        $style = 'style-01';
    }

    if( $style == 'style-02'){
        $box_classes .= ' ';
    }
    
?>
<div class="<?php echo $box_classes; ?>" style="<?php echo !empty( $category['bg_color'] ) ? 'background-color: '. esc_attr( $category['bg_color'] ).';' : ''; ?>">

    <a href="<?php echo esc_url($category['url']); ?>" class="image-box">
        <div class="image img-style4" style="<?php echo esc_html($image_style); ?>">
            <img data-src="<?php echo esc_url($category['image'] ?? $placeholder); ?>" src="<?php echo esc_url($placeholder); ?>" alt="<?php echo esc_html($category['name']); ?>" class="lazyload">
        </div>
    </a>

    <div class="cat-content">
        <h5 class="cat-title">
            <a href="<?php echo esc_url($category['url']); ?>" class="name-cls link">
                <?php echo esc_html($category['name']); ?>
            </a>
        </h5>
        <?php if(!empty($number_count)) : ?>
            <span class="count">
                <?php
                    $count = (int) $category['count'];

                    printf(
                        esc_html( _n( '%d product', '%d products', $count, 'ovinx-addon' ) ),
                        $count
                    );

                ?>
            </span>
        <?php endif; ?>

        <?php if( !empty($cat_desc) && !empty( $category['description'] )) : ?>
            <p class="cate_desc">
                <?php _e( $category['description'] ); ?>
            </p>
        <?php endif; ?> 

    </div>
</div>
