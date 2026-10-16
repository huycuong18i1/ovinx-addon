        <!-- Banner Slider -->
        <div class="tf-slideshow widget-slider-1">
                <div dir="ltr" class="swiper tf-swiper tfc-swiper sw-slide-show slider_effect_fade" data-auto="<?php echo esc_attr($settings['autoplay_enable'] == 'yes' ? 'true' : 'false'); ?>" data-loop="<?php echo esc_attr($settings['loop_enable'] == 'yes' ? 'true' : 'false'); ?>" data-effect="fade"
                    data-delay="3000">
                    <div class="swiper-wrapper">

                        <?php foreach ($settings['slider_list'] as $slider):
                            $image_url = \Elementor\Group_Control_Image_Size::get_attachment_image_src(
								$slider['slider_image']['id'],
								'image_size',
								$settings
							);
                            ?>	
                            <div class="swiper-slide">
                                <div class="slider_wrap elementor-repeater-item-<?php echo esc_attr($slider['_id']);?>_content">
                                    <div class="sld-image">
                                        <?php if ( $image_url ) : ?>
                                            <img src="<?php echo esc_url( $image_url ); ?>" alt="Image">
                                        <?php else: ?>
                                            <img src="<?php echo TF_PLUGIN_URL."includes/elementor-widget/assets/images/slider-1.jpg"; ?>" alt="Image">
                                        <?php endif; ?>
                                    </div>
                                    <div class="sld-content">
                                        <div class="container">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="content-sld">
                                                        <?php if ( $slider['title'] ) : ?>
                                                            <div class="wrap-item fade-item fade-item-1">
                                                                <p class="title-sld-2">
                                                                    <?php echo wp_kses_post($slider['title']); ?>
                                                                </p>
                                                            </div>
                                                        <?php endif; ?>
                                                        <?php if ( $slider['desc'] ) : ?>
                                                            <div class="fade-item fade-item-2">
                                                                <p class="sub-title-sld">
                                                                    <?php echo wp_kses_post($slider['desc']); ?>
                                                                </p>
                                                            </div>
                                                        <?php endif; ?>
                                                        <?php if ( $slider['button_text'] ) : ?>
                                                            <div class="fade-item fade-item-3">
                                                                <a href="<?php echo esc_attr($slider['link_button']['url']); ?>" class="tf-btn type-large style-white-2">
                                                                    <?php echo wp_kses_post( $slider['button_text'] ); ?>
                                                                    <i class="icon-arrow-right fs-24"></i>
                                                                </a>
                                                            </div>
                                                         <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach;?>

                    </div>

                    <?php if ( $settings['bullet_enable'] == 'yes' ) : ?>
                        <div class="sw-dot-default style-white tf-sw-pagination"></div>
                    <?php endif; ?>

                </div>
        </div>
        <!-- /Banner Slider -->