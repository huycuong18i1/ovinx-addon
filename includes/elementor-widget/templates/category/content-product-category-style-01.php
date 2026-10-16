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

    $box_classes.='  wow fadeInUp';

    if( !empty( $has_carousel ) ){
        $box_classes.=' swiper-slide';
    }
    $item_text = $category['count'] == 1 ? 'item' : 'items';
    
    if(!empty($bg_type)){
        $style_bg = (!empty( $category['bg_color'] ) && $bg_type == 'default' ) ? 'background-color: '. esc_attr( $category['bg_color'] ).';' : '';
    }
    
?>
<div class="<?php echo $box_classes; ?>" style="<?php echo esc_attr($style_bg); ?>">
    <a href="<?php echo esc_url($category['url']); ?>" class="image-box wow fadeInUp">
        <div class="image img-style" style="<?php echo esc_html($image_style); ?>">
            <img data-src="<?php echo esc_url($category['image'] ?? $placeholder ); ?>" src="<?php echo esc_url($placeholder); ?>" alt="<?php echo esc_html($category['name']); ?>" class="lazyload">
        </div>
    <!-- </a> -->
    </a>
    <div class="cat-content">
        <a href="<?php echo esc_url($category['url']); ?>" class="name-cls cat-name">
            <?php echo esc_html($category['name']); ?>
        </a>

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

        <?php if(!empty($cat_desc)) : ?>
            <p class="cate_desc">
                <?php _e( $category['description'] ); ?>
            </p>
        <?php endif; ?> 

        <a href="<?php echo esc_url($category['url']); ?>" class="cat-link btn-cls text-xs">
            <span>
                <?php esc_html_e( 'style-02' == $style ? $category['name'] : ($button_text) ); ?>
            </span>
            <?php if (!empty($icon['url'])) : ?>
                <img src="<?php echo esc_attr($icon['url']); ?>" alt="Icon" class="icon" />
            <?php elseif(!empty($icon)) : ?>
                <i class="icon <?php echo esc_attr($icon); ?>"></i>
            <?php endif; ?>
        </a>
    </div>
</div>
