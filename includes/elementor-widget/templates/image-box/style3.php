<?php 
    $image_url = \Elementor\Group_Control_Image_Size::get_attachment_image_src(
        $settings['slider_image']['id'],
        'image_size',
        $settings
    );
?>	
<div class="d-flex">
    <div class="wg-cls hover-img wow fadeInUp animated widget-image-box">
        <?php if ( $image_url ) : ?>
            <a href="<?php echo esc_attr($settings['link_button']['url']); ?>" class="image img-style">
                <img src="<?php echo esc_url( $image_url ); ?>" alt="Image">
            </a>
        <?php else: ?>
            <a href="<?php echo esc_attr($settings['link_button']['url']); ?>" class="image img-style">
                <img src="<?php echo TF_PLUGIN_URL."includes/elementor-widget/assets/images/placeholder-image.jpg"; ?>" alt="Image">
            </a>
        <?php endif; ?>
        <div class="content">
            <?php if ( $settings['title'] ) : ?>
                <h3 class="title text-uppercase"><?php echo wp_kses_post($settings['title']); ?></h3>
            <?php endif; ?>
            <?php if ( $settings['button_text'] ) : ?>
                    <a href="<?php echo esc_attr($settings['link_button']['url']); ?>" class="tf-btn btn-def-2 type-black">
                        <?php echo wp_kses_post( $settings['button_text'] ); ?>
                        <i class="icon icon-arrow-top-right"></i>
                    </a>
            <?php endif; ?>
        </div>
    </div>
</div>