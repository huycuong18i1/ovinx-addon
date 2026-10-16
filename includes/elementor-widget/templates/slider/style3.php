        <!-- Baner Slider -->
        <div class="tf-slideshow">
            <div dir="ltr" class="swiper tf-swiper tfc-swiper sw-slide-show slider_effect_fade-md" data-preview="3" data-tablet="2" data-mobile-sm="1"
                data-mobile="1" data-pagination="1" data-pagination-sm="1" data-pagination-md="2" data-pagination-lg="3">
                <div class="swiper-wrapper">

                    <?php foreach ($settings['slider_list2'] as $slider):
                        $image_url = \Elementor\Group_Control_Image_Size::get_attachment_image_src(
					    	$slider['slider2_image']['id'],
					    	'image_size',
					    	$settings
					    );
                    ?>	

                    <div class="swiper-slide">
                        <div class="slider_wrap style-2 elementor-repeater-item-<?php echo esc_attr($slider['_id']);?>_content">
                            <span class="sld-overlay fade-item fade-box"></span>
                            <div class="sld-image">
                                <?php if ( $image_url ) : ?>
                                    <img src="<?php echo esc_url( $image_url ); ?>" alt="Image">
                                <?php else: ?>
                                    <img src="<?php echo TF_PLUGIN_URL."includes/elementor-widget/assets/images/slider-1.jpg"; ?>" alt="Image">
                                <?php endif; ?>
                            </div>
                            <div class="sld-content">
                                <div class="container">
                                    <div class="content-sld text-center">
                                        <?php if ( $slider['subtitle2'] ) : ?>
                                            <div class="fade-item fade-item-1">
                                                <p class="text-white text-uppercase subtitle"><?php echo wp_kses_post($slider['subtitle2']); ?></p>
                                            </div>
                                        <?php endif; ?>
                                        <?php if ( $slider['title2'] ) : ?>
                                            <div class="wrap-item fade-item fade-item-2">
                                                <h2 class="title-sld-4">
                                                    <?php echo wp_kses_post($slider['title2']); ?>
                                                </h2>
                                            </div>
                                        <?php endif; ?>
                                        <?php if ( $slider['button2_text'] ) : ?>
                                            <div class="fade-item fade-item-3">
                                                <a href="<?php echo esc_attr($slider['link_button2']['url']); ?>" class="tf-btn type-large style-white-2">
                                                    <?php echo wp_kses_post( $slider['button2_text'] ); ?>
                                                    <i class="icon-arrow-right fs-24"></i>
                                                </a>
                                            </div>
                                         <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php endforeach;?>

                </div>
                <div class="sw-dot-default style-white tf-sw-pagination"></div>
            </div>
        </div>
        <!-- /Baner Slider -->