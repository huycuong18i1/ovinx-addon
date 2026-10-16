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
<div class="tf-product-mini-view">
    <a href="<?php echo esc_url($product->get_permalink()); ?>" class="prd-image">
        <img class="lazyload" data-src="<?php echo esc_url($product_image); ?>" alt="<?php echo esc_attr($product->get_name()); ?>">
    </a>
    <div class="prd-content">
        <a href="<?php echo esc_url($product->get_permalink()); ?>" class="prd-name link">
            <?php echo esc_html($product->get_name()); ?>
        </a>
        <div class="price-wrap tf-price">
            <?php echo ( $product->get_price_html() ); ?>
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