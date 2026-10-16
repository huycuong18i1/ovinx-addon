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

$style_origin = isset($_POST['product_style']) ? $_POST['product_style'] : '';
$_POST['product_style'] = 'style-1';

$product_classes = 'card-product card_product--V03';

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
    <div class="card_product-wrapper">
        <img class="lazyload" data-src="<?php echo esc_url($product_image); ?>" alt="<?php echo esc_attr($product->get_name()); ?>">
    </div>
    <div class="card_product-info">
        <div class="infor">
            <div class="info-product">
                <a href="<?php echo esc_url($product->get_permalink()); ?>"
                    class="name h5 fw-normal link text-line-clamp-2">
                    <?php echo esc_html($product->get_name()); ?>
                </a>
                <div class="price-wrap tf-price">
                    <?php echo ( $product->get_price_html() ); ?>
                </div>
            </div>
        </div>
        <div class="btn-action">
            <a href="#quickView" data-bs-toggle="modal"
                class="quickview tf_quickview_btn tf-btn-icon style-circle hover-tooltip"
                data-quantity="1"
                data-product_id="<?php echo esc_attr($product->get_id()); ?>"
            >
                <i class="icon icon-view"></i>
                <span class="tooltip">
                    <?php echo esc_html('Quick View', 'ovinx'); ?>
                </span>
            </a>
        </div>
    </div>
</div>

<?php
    if( !empty( $style_origin ) ){
        $_POST['product_style'] = $style_origin;
    }else{
        unset($_POST['product_style']);
    }
?>