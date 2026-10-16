<?php 
    $slides_per_view_xs = !empty($settings['slidesPerView-xs']) ? $settings['slidesPerView-xs'] : 1;
    $slides_per_view_sm = !empty($settings['slidesPerView-sm']) ? $settings['slidesPerView-sm'] : 2;
    $slides_per_view_md = !empty($settings['slidesPerView-md']) ? $settings['slidesPerView-md'] : 3;
    $slides_per_view_xl = !empty($settings['slidesPerView-xl']) ? $settings['slidesPerView-xl'] : 4;
    
    $slides_per_group_xs = !empty($settings['slidesPerGroup-xs']) ? $settings['slidesPerGroup-xs'] : 1;
    $slides_per_group_sm = !empty($settings['slidesPerGroup-sm']) ? $settings['slidesPerGroup-sm'] : 2;
    $slides_per_group_md = !empty($settings['slidesPerGroup-md']) ? $settings['slidesPerGroup-md'] : 3;
    $slides_per_group_xl = !empty($settings['slidesPerGroup-xl']) ? $settings['slidesPerGroup-xl'] : 4;

    $space_between_mobile = !empty($settings['spacing_mobile']) ? $settings['spacing_mobile'] : 0;
    $space_between_tablet = !empty($settings['spacing_tablet']) ? $settings['spacing_tablet'] : 0;
    $space_between_desktop = !empty($settings['spacing']) ? $settings['spacing'] : 0;
?>

<div class="tf-swiper tfc-swiper swiper sw-iconbox widget-icon-box" data-preview="<?php echo esc_attr($slides_per_view_xl); ?>" data-tablet="<?php echo esc_attr($slides_per_view_md); ?>" data-mobile-sm="<?php echo esc_attr($slides_per_view_sm); ?>" data-mobile="<?php echo esc_attr($slides_per_view_xs); ?>" data-space-lg="<?php echo esc_attr($space_between_desktop); ?>"
    data-space-md="<?php echo esc_attr($space_between_tablet); ?>" data-space="<?php echo esc_attr($space_between_mobile); ?>" data-pagination="<?php echo esc_attr($slides_per_group_xs); ?>" data-pagination-sm="<?php echo esc_attr($slides_per_group_sm); ?>" data-pagination-md="<?php echo esc_attr($slides_per_group_md); ?>" data-pagination-lg="<?php echo esc_attr($slides_per_group_xl); ?>">
    <div class="swiper-wrapper">

    <?php foreach ($settings['carousel_list2'] as $carousel): ?>	
        <div class="swiper-slide">
            <div class="wg-benefit tf-box-icon">
                <?php if (!empty($carousel['icon'])) : ?>
                    <div class="ic-wrap">
                        <?php \Elementor\Icons_Manager::render_icon($carousel['icon'], ['aria-hidden' => 'true','class' => 'icon']); ?>
                    </div>
                <?php endif;?>
                <div class="br-line"></div>
                <div class="benefit-infor">
                    <?php if (!empty($carousel['title'])) :?>
                        <h4 class="benefit_name text-primary title">
                            <?php echo wp_kses_post( $carousel['title'] ); ?>
                        </h4>
                    <?php endif;?>
                    <?php if (!empty($carousel['description'])) :?>
                        <p class="benefit_sub sub">
                           <?php echo wp_kses_post( $carousel['description'] ); ?>
                        </p>
                    <?php endif;?>
                </div>
            </div>
        </div>
    <?php endforeach;?>

    </div>
    <?php if ( $settings['bullet_enable'] == 'yes' ) : ?>
        <div class="sw-dot-default style-white tf-sw-pagination"></div>
    <?php endif; ?>
</div>