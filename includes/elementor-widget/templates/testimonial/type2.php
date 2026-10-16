<?php
    $slides_per_view_xs = !empty($settings['slidesPerView-xs']) ? $settings['slidesPerView-xs'] : 1;
    $slides_per_view_sm = !empty($settings['slidesPerView-sm']) ? $settings['slidesPerView-sm'] : 2;
    $slides_per_view_md = !empty($settings['slidesPerView-md']) ? $settings['slidesPerView-md'] : 3;
    $slides_per_view_xl = !empty($settings['slidesPerView-xl']) ? $settings['slidesPerView-xl'] : 3;
    
    $slides_per_group_xs = !empty($settings['slidesPerGroup-xs']) ? $settings['slidesPerGroup-xs'] : 1;
    $slides_per_group_sm = !empty($settings['slidesPerGroup-sm']) ? $settings['slidesPerGroup-sm'] : 2;
    $slides_per_group_md = !empty($settings['slidesPerGroup-md']) ? $settings['slidesPerGroup-md'] : 3;
    $slides_per_group_xl = !empty($settings['slidesPerGroup-xl']) ? $settings['slidesPerGroup-xl'] : 3;

    $space_between_mobile = !empty($settings['spacing_mobile']) ? $settings['spacing_mobile'] : 10;
    $space_between_tablet = !empty($settings['spacing_tablet']) ? $settings['spacing_tablet'] : 15;
    $space_between_desktop = !empty($settings['spacing']) ? $settings['spacing'] : 30;

    $carousel_pagination_desktop = !empty($settings['carousel-pagination']) ? $settings['carousel-pagination'] : false;
    $carousel_pagination_tablet =!empty($settings['carousel-pagination_tablet']) ? $settings['carousel-pagination_tablet'] : $settings['carousel-pagination'];
    $carousel_pagination_mobile =!empty($settings['carousel-pagination_mobile']) ? $settings['carousel-pagination_mobile'] : $settings['carousel-pagination'];
?>
<div class="widget-testimonial">
    <div class="swiper tfc-swiper" data-preview="<?php echo esc_attr($slides_per_view_xl); ?>" data-tablet="<?php echo esc_attr($slides_per_view_md); ?>" data-mobile-sm="<?php echo esc_attr($slides_per_view_sm); ?>" data-mobile="<?php echo esc_attr($slides_per_view_xs); ?>" data-space-lg="<?php echo esc_attr($space_between_desktop); ?>"
                        data-space-md="<?php echo esc_attr($space_between_tablet); ?>" data-space="<?php echo esc_attr($space_between_mobile); ?>" data-pagination="<?php echo esc_attr($slides_per_group_xs); ?>" data-pagination-sm="<?php echo esc_attr($slides_per_group_sm); ?>" data-pagination-md="<?php echo esc_attr($slides_per_group_md); ?>" data-pagination-lg="<?php echo esc_attr($slides_per_group_xl); ?>">
        <div class="swiper-wrapper">
            <?php foreach ($settings['carousel_list2'] as $carousel): ?>
            <div class="swiper-slide">
                <div class="testimonial-v02">
                
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
                    <p class="tes-text text-primary description">
                        <?php echo wp_kses_post( $carousel['description2'] ); ?>
                    </p>
                    <div class="tes-author">
                        <p class="author_name text-caption-01 fw-medium text-primary title">
                            <?php echo wp_kses_post( $carousel['name2'] ); ?>
                        </p>
                        <div class="br-line"></div>
                        <p class="author_duty text-caption-01 position">
                            <?php echo wp_kses_post( $carousel['position2'] ); ?>
                        </p>
                    </div>
                </div>
            </div>
            <?php endforeach;?>
        </div>
        <div class="sw-dot-default tf-sw-pagination"></div>
    </div>
</div>