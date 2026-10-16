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

$product_classes = 'card-product style-large';

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

    <div class="card-product_wrapper">
        <a href="<?php echo esc_url( $product->get_permalink() ); ?>" class="product-img">
            <img class="img-product lazyload" data-src="<?php echo esc_url($product_image); ?>" alt="<?php echo esc_attr($product->get_name()); ?>">
        </a>
        <?php wc_get_template( 'tpl/product-base/badges.php' ); ?>
        <ul class="product-action_list">
            <li class="wishlist">
                <?php
                    if( class_exists('WCBoost\Wishlist\Wishlist') ){
                        echo do_shortcode( '[wcboost_wishlist_button]');
                    }
                ?>
            </li>
            <li>
                <a href="#quickView" data-bs-toggle="offcanvas"
                    class="hover-tooltip tooltip-left box-icon quickview tf_quickview_btn"
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

    </div>
    <div class="card-product_info">
        <div class="product_info_wrap">
            <?php tfwc_price(); ?>
            <a href="<?php echo esc_url($product->get_permalink()); ?>"
                class="name-product lh-28 fw-medium link-underline text-primary">
                <?php echo esc_attr($product->get_name()); ?>
            </a>
            <?php
            if ( wc_review_ratings_enabled() && $show_rating ) {
                tfwc_rating();
            }
            tfwc_category();
            ?>
        </div>

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