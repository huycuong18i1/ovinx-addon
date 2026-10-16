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

$product_classes = 'card-product card_product--V01 type-space-35 bundle-hover-item';

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
    <div class="card-product_wrapper <?php echo esc_attr($wrapper_classes); ?>" style="<?php echo esc_attr($wrapper_style); ?>">
        <a href="<?php echo esc_url($product->get_permalink()); ?>" class="product-img">
            <img class="img-product lazyload" data-src="<?php echo esc_url($product_image); ?>" alt="<?php echo esc_attr($product->get_name()); ?>">
        </a>
        <?php
        if( $product->get_stock_status() == "outofstock" && !empty($show_out_stock)){
            ?>
                <a href="#unavailable" data-bs-toggle="modal" class="variant-box stock bg-main link text-white">
                    <p class="text-center d-none d-md-block">
                        <?php echo esc_html(themesflat_get_opt('out_stock_btn_text')); ?>
                    </p>
                </a>
        <?php } ?>

        <?php wc_get_template( 'tpl/product-base/badges.php' ); ?>  

        <?php if( $product->is_on_backorder() || $product->is_in_stock() || !$show_out_stock ) : ?>
        <ul class="product-action_list">
            <li class="wishlist">
                <?php
                    if( class_exists('WCBoost\Wishlist\Wishlist') ){
                        echo do_shortcode( '[wcboost_wishlist_button]');
                    }
                ?>
            </li>
            <li>
                <a href="#quickView" data-bs-toggle="offcanvas" class="hover-tooltip  tooltip-left  box-icon quickview tf_quickview_btn"
                    data-quantity="1"
                    data-product_id="<?php echo esc_attr($product->get_id()); ?>"
                >
                    <span class="icon icon-eye"></span>
                    <span class="tooltip"><?php esc_html_e('Quick View', 'ovinx-addon'); ?></span>
                </a>
            </li>
            <?php
                if( class_exists('WCBoost\ProductsCompare\Plugin') ){
                    echo do_shortcode( '[wcboost_compare_button product_id="' . esc_attr($product->get_id()) . '"]' );
                }
            ?>
        </ul>
        <?php endif; ?>

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

            if ( $seconds_remaining > 0 && empty($show_out_stock) ) {
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
        if ( !empty($second_attrs) && empty($show_out_stock) ) :
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

    <div class="card-product_info">
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
        <?php

            tfwc_price();
            tfwc_title();
            if ( wc_review_ratings_enabled() && $show_rating ) {
                tfwc_rating();
            }
            tfwc_category();
            // tfwc_description();
            // tfwc_attribute_second();
            // tfwc_progressbar();
            // tfwc_in_out_stock();
        ?>

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
        
        <?php

        $args = array(
            'quantity'   => 1,
            'class'      => implode(
                ' ',
                array_filter(
                    array(
                        'button',
                        'product_type_' . $product->get_type(),
                        $product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
                        $product->supports( 'ajax_add_to_cart' ) ? 'ajax_add_to_cart' : '',
                    )
                )
            ),
            'attributes' => array(
                'data-product_id'  => $product->get_id(),
                'data-product_sku' => $product->get_sku(),
                'aria-label'       => $product->add_to_cart_description(),
                'rel'              => 'nofollow',
            ),
        );

            set_query_var( 'tf_card_product_args', $args );

            wc_get_template( 'tpl/product-base/btn-add-to-cart.php' );

            set_query_var( 'tf_card_product_args', false );
        ?>

    </div>
</div>

<?php
    if( !empty( $style_origin ) ){
        $_POST['product_style'] = $style_origin;
    }else{
        unset($_POST['product_style']);
    }
?>