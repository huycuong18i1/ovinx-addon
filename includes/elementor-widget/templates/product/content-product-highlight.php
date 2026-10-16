<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;

// Ensure visibility
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}

$attribute = $product->get_meta('_tfwc_product_card_second_attribute');
$second_attribute = $product->get_meta('_tfwc_product_card_attribute');
$attribute_max = $product->get_meta('_tfwc_product_card_second_attribute_max_number');
$second_attribute_max = $product->get_meta('_tfwc_product_card_attribute_max_number');

if (function_exists('themesflat_get_opt')) {
    if( empty($attribute) ){
        $attribute = themesflat_get_opt('attribute_second');
    }
    if( empty($second_attribute) ){
        $second_attribute = themesflat_get_opt('attribute_first');
    }

    if( empty($attribute_max) ){
        $attribute_max = themesflat_get_opt('attr2_count');
    }

    if( empty($second_attribute_max) ){
        $second_attribute_max = themesflat_get_opt('attr1_count');
    }
}

$style_origin = isset($_POST['product_style']) ? $_POST['product_style'] : '';
$_POST['product_style'] = 'style-1';

$product_classes = 'card-product card_product--V02 hover-img video-wrap';

if($product->get_stock_status() == "outofstock" && $show_out_stock ){
    $product_classes .= " out-of-stock";
}

if( empty($countdown_style) ){
    $countdown_style = 'style-1';
}

if( !empty( $has_carousel ) ){
    $product_classes.=' swiper-slide';
}

$wrapper_style = '';
$wrapper_classes = '';
if( !empty( $aspect_ratio) ){
    $wrapper_style .= "--image-card-ratio: $aspect_ratio";
    $wrapper_classes .= " card-custom-ratio";
}

$icon = !empty($button_icon['value']) ? $button_icon['value'] : "";
$product_image = wp_get_attachment_image_url($product->get_image_id(), 'full');
if( empty($product_image) ){
    $product_image = \Elementor\Utils::get_placeholder_image_src();
}
?>

<div <?php post_class($product_classes); ?> data-availability="<?php echo esc_attr($product->get_stock_status()); ?>" data-brand="<?php echo esc_attr($product->get_attribute('pa_brand')); ?>">
    <div class="card_product-wrapper card_product--V01<?php echo esc_attr($wrapper_classes); ?>" style="<?php echo esc_attr($wrapper_style); ?>">
        <a href="<?php echo esc_url($product->get_permalink()); ?>" class="product-img image img-style d-flex">
            <img class="img-product lazyload" data-src="<?php echo esc_url($product_image); ?>" alt="<?php echo esc_attr($product->get_name()); ?>">

            <?php 
            
            $attachment_ids = $product->get_gallery_image_ids();
            if (!empty($attachment_ids)) {
                $second_image = wp_get_attachment_image_url($attachment_ids[0], 'full');
            }else{
                $second_image = $product_image;
            }
            ?>
                <img class="img-hover lazyload" data-src="<?php echo esc_url($second_image); ?>" alt="<?php echo esc_attr($product->get_name()); ?>">
        </a>
        <?php if( !empty( $video_url['url'] ) ) : ?>
            <a href="<?php echo esc_url($video_url['url']); ?>"
                class="popup-youtube icon_play">
                <i class="icon-retangle"></i>
            </a>
        <?php endif; ?>
        
        <?php wc_get_template( 'tpl/product-base/badges.php' ); ?>

        <?php
        $end_time = null;

        if ( $product->is_type('variable') ) {
            $available_variations = $product->get_available_variations();

            if ( !empty($available_variations) ) {
                $first_variation_id = $available_variations[0]['variation_id'];
                $first_variation = wc_get_product( $first_variation_id );

                if ( $first_variation ) {
                    $end_time = $first_variation->get_date_on_sale_to();
                }
            }
        } else {
            $end_time = $product->get_date_on_sale_to();
        }

        if ( !empty($end_time) ) {
            $current_time = new DateTime();
            $interval = $current_time->diff($end_time);
            $seconds_remaining = $interval->days * 86400 + $interval->h * 3600 + $interval->i * 60 + $interval->s;

            if ( $seconds_remaining > 0 ) {
                ?>
                <div class="variant-box count-down <?php echo esc_attr($countdown_style); ?>">
                    <div class="countdown-V02">
                        <div class="js-countdown" data-timer="<?php echo esc_attr($seconds_remaining); ?>" data-labels="d : ,h : ,m : ,s"></div>
                    </div>
                </div>
                <?php
            }
        }
        ?>

       <?php
        $second_attrs = $product->get_attribute($second_attribute);
        if (!empty($second_attrs)) :
            $second_attrs_array = array_map('trim', explode(',', $second_attrs));
            if( !empty( $second_attribute_max ) ){
                $second_attribute_max = max(0, $second_attribute_max );
                // $second_attrs_array = array_slice($second_attrs_array, 0, $second_attribute_max);
            }
            $attr_label = str_replace('-', ' ', $second_attribute);
        ?>
            <?php if( count($second_attrs_array) > $second_attribute_max ): ?>
                <div class="variant-box">
                    <p class="size-box text-center text-caption">
                        <?php echo count($second_attrs_array); ?> <?php echo esc_html($attr_label . (count($second_attrs_array) == 1 ? '' : 's') ); ?> <?php _e('are available','ovinx'); ?>
                    </p>
                </div>
            <?php else: ?>
                <div class="variant-box">
                    <ul class="size-box">
                        <?php foreach ( $second_attrs_array as $second_attr) : ?>
                            <li class="size-item text-xs text-white-o"><?php echo esc_html(trim($second_attr)); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php
            if( $product->get_stock_status() == "instock" && !empty( $product->get_meta('_tfwc_sale_marquee_text') ) ){
                wc_get_template( 'tpl/product-base/sale-marquee.php' );
            }
        ?>
    </div>

    <div class="card_product-info">
        <?php
            $brands = wp_get_post_terms( $product->get_id(), 'product_brand' );

            if ( ! empty( $brands ) && ! is_wp_error( $brands ) && $show_brand) {
                $brand = $brands[0];
                ?>
                <p class="type text-xs text-dark fw-medium text-brand">
                    <?php echo esc_html( $brand->name ); ?>
                </p>
                <?php
            }
        ?>

        <a href="<?php echo esc_url($product->get_permalink()); ?>" class="name-product h5 fw-normal link text-line-clamp-2">
            <?php echo esc_html($product->get_name()); ?>
        </a>
        <p class="price-wrap fw-medium tf-price">
            <?php echo ( $product->get_price_html() ); ?>
        </p>
        <?php
            if ( wc_review_ratings_enabled() && $show_rating ) {
                $rating_count = $product->get_rating_count();
                $review_count = $product->get_review_count();
                $average      = $product->get_average_rating();
                $full_stars = floor($average);
                $percent = ($average - $full_stars) * 100;  
                
                if ( $rating_count > 0 ) : ?>
                    <div class="card-product-rating d-flex">
                        <div class="list-star">
                            <?php for($i= 0; $i < $full_stars; $i++) : ?>
                                <i class="icon icon-star full"></i>
                            <?php endfor; ?>
                            <?php if ( $full_stars != $average ) : ?>
                                <i class="icon icon-star empty">
                                    <span class="icon icon-star partial" style="width:<?php echo esc_attr($percent); ?>%;"></span>
                                </i>
                            <?php endif; ?>
                            <?php for($i= 0; $i < 5 - ceil($average) ; $i++) : ?>
                                <i class="icon icon-star empty"></i>
                            <?php endfor; ?>
                        </div>
                    </div>
                <?php endif; ?>
        <?php } ?>
        <?php
            if ($product->is_type('variable') && !empty($attribute) && $attribute != 'none') {
                if( !strpos($attribute,'pa_') ){
                    $attribute = 'pa_' . $attribute;
                }

                $attributes = $product->get_variation_attributes();
                if( !empty( $attributes[$attribute] ) ){
                    if( !empty( $attribute_max ) ){
                        $attribute_max = max(0, $attribute_max );
                        $attributes[$attribute] = array_slice($attributes[$attribute], 0, $attribute_max);
                    }
                    $args = [
                        'options'   => $attributes[$attribute],
                        'option__in'   => $attributes[$attribute],
                        'attribute' => $attribute,
                        'product'   => $product,
                        'name'      => $attribute,
                        'selected'  => $product->get_variation_default_attribute( $attribute ),
                    ];

                    if( class_exists('WCBoost\VariationSwatches\Swatches') ){
                        TFWC_Elementor_Widget_Addon::instance()->add_max_swatches();
                        $swatches_html = \WCBoost\VariationSwatches\Swatches::instance()->swatches_html('', $args);
                        echo $swatches_html;
                        TFWC_Elementor_Widget_Addon::instance()->remove_max_swatches();
                    }
                }
            }
        ?>
        
        <?php
        $stock_available  = 0; 
        $stock_sold       = 0; 
        
        $total_sales = get_post_meta( $product->get_id(), 'total_sales', true );
        $stock = get_post_meta($product->get_id(), '_stock', true );
        
        if (is_numeric($total_sales)) {
            $stock_sold = round(floatval($total_sales));
        }
        
        if (is_numeric($stock)) {
            $stock_available = round(floatval($stock));
        }
        
        if ($stock_available + $stock_sold > 0) {
            $percentage = round(($stock_sold / ($stock_available + $stock_sold)) * 100);
        } else {
            $percentage = 0; 
        }

        if( !empty($show_progress_sale) && $stock_available && $percentage > 0) {
            wc_get_template( 'tpl/product-base/progressbar.php', [
                'show_sold_count' => $show_sold_count
            ] );
            ?>
        <?php } ?>
        
        <?php wc_get_template( 'single-product/extras/progress-sale.php' ); ?>
    </div>
    <div class="cls-btn w-100">
        <a href="#quickView" data-bs-toggle="modal"
            class="quickview tf_quickview_btn tf-btn btn-cls tf-btn w-100"
            data-quantity="1"
            data-product_id="<?php echo esc_attr($product->get_id()); ?>"
        >
            <?php esc_html_e('Quick View', 'ovinx-addon'); ?>
        </a>
    </div>
</div>

<?php
    if( !empty( $style_origin ) ){
        $_POST['product_style'] = $style_origin;
    }else{
        unset($_POST['product_style']);
    }
?>