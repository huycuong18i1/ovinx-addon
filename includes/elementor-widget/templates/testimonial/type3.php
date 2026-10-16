<?php
    $slides_per_view_xs = !empty($settings['slidesPerView-xs']) ? $settings['slidesPerView-xs'] : 1;
    $slides_per_view_sm = !empty($settings['slidesPerView-sm']) ? $settings['slidesPerView-sm'] : 1;
    $slides_per_view_md = !empty($settings['slidesPerView-md']) ? $settings['slidesPerView-md'] : 1;
    $slides_per_view_xl = !empty($settings['slidesPerView-xl']) ? $settings['slidesPerView-xl'] : 1;
    
    $slides_per_group_xs = !empty($settings['slidesPerGroup-xs']) ? $settings['slidesPerGroup-xs'] : 1;
    $slides_per_group_sm = !empty($settings['slidesPerGroup-sm']) ? $settings['slidesPerGroup-sm'] : 1;
    $slides_per_group_md = !empty($settings['slidesPerGroup-md']) ? $settings['slidesPerGroup-md'] : 1;
    $slides_per_group_xl = !empty($settings['slidesPerGroup-xl']) ? $settings['slidesPerGroup-xl'] : 1;

    $space_between_mobile = !empty($settings['spacing_mobile']) ? $settings['spacing_mobile'] : 0;
    $space_between_tablet = !empty($settings['spacing_tablet']) ? $settings['spacing_tablet'] : 0;
    $space_between_desktop = !empty($settings['spacing']) ? $settings['spacing'] : 0;

    $carousel_pagination_desktop = !empty($settings['carousel-pagination']) ? $settings['carousel-pagination'] : false;
    $carousel_pagination_tablet =!empty($settings['carousel-pagination_tablet']) ? $settings['carousel-pagination_tablet'] : $settings['carousel-pagination'];
    $carousel_pagination_mobile =!empty($settings['carousel-pagination_mobile']) ? $settings['carousel-pagination_mobile'] : $settings['carousel-pagination'];
?>
<div class="widget-testimonial box-swiper sw-testimonial">
    <div class="swiper tfc-swiper" data-preview="<?php echo esc_attr($slides_per_view_xl); ?>" data-tablet="<?php echo esc_attr($slides_per_view_md); ?>" data-mobile-sm="<?php echo esc_attr($slides_per_view_sm); ?>" data-mobile="<?php echo esc_attr($slides_per_view_xs); ?>" data-space-lg="<?php echo esc_attr($space_between_desktop); ?>"
                        data-space-md="<?php echo esc_attr($space_between_tablet); ?>" data-space="<?php echo esc_attr($space_between_mobile); ?>" data-pagination="<?php echo esc_attr($slides_per_group_xs); ?>" data-pagination-sm="<?php echo esc_attr($slides_per_group_sm); ?>" data-pagination-md="<?php echo esc_attr($slides_per_group_md); ?>" data-pagination-lg="<?php echo esc_attr($slides_per_group_xl); ?>">
        <div class="swiper-wrapper">
            <?php foreach ($settings['carousel_list2'] as $carousel): ?>
            <div class="swiper-slide">
                <div class="testimonial-v03">
                    <div class="star-list">
                        <?php
                        $rating = intval( $carousel['star2'] );
                        for ($i = 1; $i <= 5; $i++) {
                            $active = ($i <= $rating) ? 'active' : '';
                            echo '<div class="ic-star-wrap '. $active .'">
                                    <i class="icon icon-star-5"></i>
                                </div>';
                        }
                        ?>
                    
                    </div>
                    <h4 class="tes-text text-primary description">
                        <?php echo wp_kses_post( $carousel['description2'] ); ?>
                    </h4>
                    <div class="tes-author">
                        <h6 class="author-name text-primary title">
                            <?php echo wp_kses_post( $carousel['name2'] ); ?>
                        </h6>
                        <p class="author-duty text-caption-01 position">
                             <?php echo wp_kses_post( $carousel['position2'] ); ?>
                        </p>
                    </div>
                </div>
            </div>
            <?php endforeach;?>
        </div>
        
       
        <div class="sw-dot-default tf-sw-pagination d-xl-none"></div>
    </div>
    <?php if ($settings['carousel_navigation'] == 'yes'): ?>
    <div class="group-btn group-btn-slider d-none d-sm-flex">
        <div class="tf-sw-nav small nav-prev-swiper">
            <i class="icon icon-caret-left"></i>
        </div>
        <div class="tf-sw-nav small nav-next-swiper">
            <i class="icon icon-caret-right"></i>
        </div>
    </div>
    <?php endif;?>
</div>