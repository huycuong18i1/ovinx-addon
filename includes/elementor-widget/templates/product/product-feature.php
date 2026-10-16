<?php
/**
 * Product Feature widget template.
 *
 * Renders only the main single-product area for the product selected in the
 * Elementor widget. Tabs, upsells, related products and the page footer are
 * intentionally not rendered here.
 */

defined( 'ABSPATH' ) || exit;

global $product;

/*
 * Prefer the product explicitly supplied by the widget. wc_get_template()
 * extracts arguments into local scope, while theme/WooCommerce callbacks still
 * read the global product.
 */
if ( isset( $widget_product ) && $widget_product instanceof WC_Product ) {
	$product             = $widget_product;
	$GLOBALS['product'] = $widget_product;
}

if ( ! $product instanceof WC_Product ) {
	return;
}

if ( isset( $add_hooks ) && is_callable( $add_hooks ) ) {
	call_user_func( $add_hooks );
}

$gallery_layout = ! empty( $gallery_layout )
	? sanitize_key( $gallery_layout )
	: (
		function_exists( 'themesflat_get_opt' )
			? sanitize_key( themesflat_get_opt( 'tf_product_gallery_layout' ) )
			: 'left_thumb'
	);

$class_main      = apply_filters( 'tf_single_main_classes', 'tf-main-product' );
$class_container = apply_filters( 'tf_single_container_classes', 'container' );
$class_left_col  = apply_filters( 'tf_single_left_col_classes', 'col-md-6' );
$class_right_col = apply_filters( 'tf_single_right_col_classes', 'col-md-6' );
$class_row       = apply_filters( 'tf_single_row_classes', 'row' );

$widget_id      = isset( $widget_id ) ? sanitize_html_class( $widget_id ) : wp_unique_id( 'feature-' );
$style          = isset( $style ) ? sanitize_html_class( $style ) : 'style-01';
$thumbs_preview = isset( $thumbs_preview ) ? max( 1, min( 10, absint( $thumbs_preview ) ) ) : 6;

$section_classes = 'tf-single-product flat-single-product tf-product-feature feature-' .
	$style . ' gallery-' . sanitize_html_class( $gallery_layout );

if ( 'style-02' === $style ) {
	$section_classes .= ' banner-product-single style-5';
}

if ( ! empty( $settings['thumbs-overlay-position'] ) && 'yes' === $settings['thumbs-overlay-position'] ) {
	$section_classes .= ' banner-product-single style-thumbs-position flat-spacing';
}

if ( post_password_required( $product->get_id() ) ) {
	echo get_the_password_form(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	return;
}

/*
 * Do not call wc_print_notices() here.
 *
 * This widget can be rendered on any Elementor page. Printing notices here
 * would consume and clear the global WooCommerce notice queue for the rest of
 * the page.
 */
?>
<section
	id="product-feature-<?php echo esc_attr( $widget_id ); ?>"
	<?php wc_product_class( $section_classes, $product ); ?>
>
	<div class="<?php echo esc_attr( $class_main ); ?> section-image-zoom">
		<div class="<?php echo esc_attr( 'style-02' === $style ? 'container-full style-2' : $class_container ); ?>">
			<?php if ( 'style-02' === $style ) : ?>

				<div class="d-flex wrap-shop gap-10">
					<div class="left">
						<div class="tf-product-info-wrap position-relative mt-md-0">
							<div class="tf-zoom-main sticky-top"></div>

							<div class="tf-product-info-list other-image-zoom">
								<div class="tf-product-info-heading">
									<?php
									$GLOBALS['product'] = $product;
									do_action( 'woocommerce_single_product_summary' );

									$product_permalink = $product->get_permalink();

									if ( ! empty( $settings['show_button'] ) && 'yes' === $settings['show_button'] ) :
										?>
										<div class="ovinx-view-single-product-wrap">
											<a
												class="ovinx-view-single-product tf-btn-line-2 fw-semibold style-primary pb-4"
												href="<?php echo esc_url( $product_permalink ); ?>"
											>
												<?php echo esc_html( $settings['button_text'] ); ?>
											</a>
										</div>
										<?php
									endif;
									?>
								</div>
							</div>
						</div>
					</div>

					<div class="center">
						<?php
						$GLOBALS['product'] = $product;
						ob_start();
						do_action( 'woocommerce_before_single_product_summary' );
						$gallery_html = ob_get_clean();

						$updated_gallery_html = preg_replace(
							'/(class=(?:"|\')[^"\']*tf-product-media-thumbs[^"\']*(?:"|\')[^>]*data-preview=(?:"|\'))\d+((?:"|\'))/i',
							'${1}' . $thumbs_preview . '${2}',
							$gallery_html,
							1
						);

						if ( is_string( $updated_gallery_html ) ) {
							$gallery_html = $updated_gallery_html;
						}

						echo $gallery_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						?>
					</div>

					<?php if ( ! empty( $settings['decorative_image']['url'] ) ) : ?>
						<div class="right d-none d-xxl-block">
							<div class="box-img">
								<img
									src="<?php echo esc_url( $settings['decorative_image']['url'] ); ?>"
									alt="<?php echo esc_attr( $product->get_name() ); ?>"
								>
							</div>
						</div>
					<?php endif; ?>
				</div>

			<?php else : ?>

				<div class="<?php echo esc_attr( $class_row ); ?>">
					<div class="<?php echo esc_attr( $class_left_col ); ?> left-col gallery-col">
						<?php
						$GLOBALS['product'] = $product;
						ob_start();
						do_action( 'woocommerce_before_single_product_summary' );
						$gallery_html = ob_get_clean();

						/*
						 * The theme gallery template hardcodes data-preview="6".
						 * Replace only the first thumbnail slider value so the widget
						 * control works without modifying the theme template.
						 */
						$updated_gallery_html = preg_replace(
							'/(class=(?:"|\')[^"\']*tf-product-media-thumbs[^"\']*(?:"|\')[^>]*data-preview=(?:"|\'))\d+((?:"|\'))/i',
							'${1}' . $thumbs_preview . '${2}',
							$gallery_html,
							1
						);

						if ( is_string( $updated_gallery_html ) ) {
							$gallery_html = $updated_gallery_html;
						}

						echo $gallery_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						?>
					</div>

					<?php if ( 'middle_gallery' === $gallery_layout ) : ?>
						<div class="main-grid-b">
							<div class="main-product-info tf-product-info-list left sticky-top">
								<div class="tf-product-info-heading">
									<?php
									$GLOBALS['product'] = $product;
									do_action( 'tf_single_product_summary_left' );
									?>
								</div>
							</div>
						</div>
					<?php endif; ?>

					<div class="<?php echo esc_attr( $class_right_col ); ?> right-col product-main-col">
						<div class="tf-product-info-wrap sticky-top mt-md-0">
							<div class="tf-zoom-main"></div>

							<div class="tf-product-info-list other-image-zoom">
								<div class="tf-product-info-heading">
									<?php
									$GLOBALS['product'] = $product;
									do_action( 'woocommerce_single_product_summary' );

									$show_view_product = ! isset( $show_view_product ) || (bool) $show_view_product;
									$view_product_text = ! empty( $view_product_text )
										? sanitize_text_field( $view_product_text )
										: esc_html__( 'View Product', 'ovinx-addon' );
									$product_permalink = $product->get_permalink();

										 if($settings['show_button'] === 'yes' ):
										?>
										<div class="ovinx-view-single-product-wrap">
											<a
												class="ovinx-view-single-product tf-btn-line-2 fw-semibold style-primary pb-4"
												href="<?php echo esc_url( $product_permalink ); ?>"
											>
												<?php echo esc_html( $settings['button_text'] ); ?>
											</a>
										</div>
										<?php
									endif;
									?>
								</div>
							</div>
						</div>
					</div>
				</div>

			<?php endif; ?>
		</div>
	</div>
</section>

<?php
if ( isset( $remove_hooks ) && is_callable( $remove_hooks ) ) {
	call_user_func( $remove_hooks );
}

