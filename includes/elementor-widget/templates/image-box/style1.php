<?php
   
    $slides_per_view_xs = !empty($settings['slidesPerView-xs']) ? $settings['slidesPerView-xs'] : 1;
    $slides_per_view_sm = !empty($settings['slidesPerView-sm']) ? $settings['slidesPerView-sm'] : 2;
    $slides_per_view_md = !empty($settings['slidesPerView-md']) ? $settings['slidesPerView-md'] : 3;
    $slides_per_view_xl = !empty($settings['slidesPerView-xl']) ? $settings['slidesPerView-xl'] : 4;
    
    $slides_per_group_xs = !empty($settings['slidesPerGroup-xs']) ? $settings['slidesPerGroup-xs'] : 1;
    $slides_per_group_sm = !empty($settings['slidesPerGroup-sm']) ? $settings['slidesPerGroup-sm'] : 2;
    $slides_per_group_md = !empty($settings['slidesPerGroup-md']) ? $settings['slidesPerGroup-md'] : 3;
    $slides_per_group_xl = !empty($settings['slidesPerGroup-xl']) ? $settings['slidesPerGroup-xl'] : 4;

    $space_between_mobile = !empty($settings['spacing_mobile']) ? $settings['spacing_mobile'] : 10;
    $space_between_tablet = !empty($settings['spacing_tablet']) ? $settings['spacing_tablet'] : 20;
    $space_between_desktop = !empty($settings['spacing']) ? $settings['spacing'] : 40;

    $carousel_pagination_desktop = !empty($settings['carousel-pagination']) ? $settings['carousel-pagination'] : false;
    $carousel_pagination_tablet =!empty($settings['carousel-pagination_tablet']) ? $settings['carousel-pagination_tablet'] : $settings['carousel-pagination'];
    $carousel_pagination_mobile =!empty($settings['carousel-pagination_mobile']) ? $settings['carousel-pagination_mobile'] : $settings['carousel-pagination'];
?>
<div class="widget-image-box wrap-content-imagebox bg-primary">
    <div class="swiper tfc-swiper " data-preview="<?php echo esc_attr($slides_per_view_xl); ?>" data-tablet="<?php echo esc_attr($slides_per_view_md); ?>" data-mobile-sm="<?php echo esc_attr($slides_per_view_sm); ?>" data-mobile="<?php echo esc_attr($slides_per_view_xs); ?>" data-space-lg="<?php echo esc_attr($space_between_desktop); ?>"
                        data-space-md="<?php echo esc_attr($space_between_tablet); ?>" data-space="<?php echo esc_attr($space_between_mobile); ?>" data-pagination="<?php echo esc_attr($slides_per_group_xs); ?>" data-pagination-sm="<?php echo esc_attr($slides_per_group_sm); ?>" data-pagination-md="<?php echo esc_attr($slides_per_group_md); ?>" data-pagination-lg="<?php echo esc_attr($slides_per_group_xl); ?>">
        <div class="swiper-wrapper">
            <?php foreach ($settings['carousel_list'] as $carousel): 
                $image_url = \Elementor\Group_Control_Image_Size::get_attachment_image_src(
                    $carousel['slider_image']['id'],
                    'image_size',
                    $settings
                );
                ?>
                <div class="swiper-slide">
                    <div class="wg-category-v03 hover-img4">
                        <?php if ( $image_url ) : ?>
                            <div class="cate-image img-style4">
                                <img src="<?php echo esc_url( $image_url ); ?>" alt="Image">
                            </div>
                        <?php else: ?>
                            <div class="cate-image img-style4">
                                <img src="<?php echo TF_PLUGIN_URL."includes/elementor-widget/assets/images/placeholder-image.jpg"; ?>" alt="Image">
                            </div>
                        <?php endif; ?>
                        
                        <div class="cate-content">
                            <a href="#" class="cate_name h3 fw-medium link-underline-white text-white title">
                                <?php echo wp_kses_post($carousel['title']); ?>
                            </a>
                            <p class="cate_desc text-white desc">
                                <?php echo wp_kses_post($carousel['desc']); ?>
                            </p>
                        </div>
                    </div>
                </div>   
            <?php endforeach; ?>     
        </div>
        <div class="group-pagination <?php echo esc_attr($carousel_pagination_desktop); ?>-desktop <?php echo esc_attr($carousel_pagination_tablet); ?>-tablet <?php echo esc_attr($carousel_pagination_mobile); ?>-mobile">
            <div class="wrap-pagination wrap-bullet">
                <div class="container">
                    <div class="sw-dot-default style-white tf-sw-pagination"></div>
                </div>
            </div>
        </div>
        
    </div>
</div>