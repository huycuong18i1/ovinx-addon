        <!-- Testimonial -->
         <?php
            $slides_per_view_xs = !empty($settings['slidesPerView-xs']) ? $settings['slidesPerView-xs'] : 1;
            $slides_per_view_sm = !empty($settings['slidesPerView-sm']) ? $settings['slidesPerView-sm'] : 2;
            $slides_per_view_md = !empty($settings['slidesPerView-md']) ? $settings['slidesPerView-md'] : 3;
            $slides_per_view_xl = !empty($settings['slidesPerView-xl']) ? $settings['slidesPerView-xl'] : 3;
            
            $slides_per_group_xs = !empty($settings['slidesPerGroup-xs']) ? $settings['slidesPerGroup-xs'] : 1;
            $slides_per_group_sm = !empty($settings['slidesPerGroup-sm']) ? $settings['slidesPerGroup-sm'] : 2;
            $slides_per_group_md = !empty($settings['slidesPerGroup-md']) ? $settings['slidesPerGroup-md'] : 3;
            $slides_per_group_xl = !empty($settings['slidesPerGroup-xl']) ? $settings['slidesPerGroup-xl'] : 3;

            $space_between_mobile = !empty($settings['spacing_mobile']) ? $settings['spacing_mobile'] : 0;
            $space_between_tablet = !empty($settings['spacing_tablet']) ? $settings['spacing_tablet'] : 0;
            $space_between_desktop = !empty($settings['spacing']) ? $settings['spacing'] : 0;

            $carousel_pagination_desktop = !empty($settings['carousel-pagination']) ? $settings['carousel-pagination'] : false;
            $carousel_pagination_tablet =!empty($settings['carousel-pagination_tablet']) ? $settings['carousel-pagination_tablet'] : $settings['carousel-pagination'];
            $carousel_pagination_mobile =!empty($settings['carousel-pagination_mobile']) ? $settings['carousel-pagination_mobile'] : $settings['carousel-pagination'];
         ?>
        <section class="flat-spacing-7 parallax-wrap widget-testimonial" >
            <div class="container">
                <div class="sect-top wow fadeInUp">

                    <?php if ( $settings['title_section'] ) : ?>
                        <h2 class="s-title font-2 text-center text-white text-capitalize"><?php echo wp_kses_post( $settings['title_section'] ); ?></h2>
                    <?php endif; ?>

                    <div class="group-pagination <?php echo esc_attr($carousel_pagination_desktop); ?>-desktop <?php echo esc_attr($carousel_pagination_tablet); ?>-tablet <?php echo esc_attr($carousel_pagination_mobile); ?>-mobile">
                        <div class="wrap-arrow">
                            <div class="group-btn-slider style-white">
                                <div class="nav-prev-swiper tf-sw-nav">
                                    <i class="icon-arrow-left"></i>
                                </div>
                                <div class="nav-next-swiper tf-sw-nav">
                                    <i class="icon-arrow-right"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="swiper tf-swiper tfc-swiper wow fadeInUp" data-preview="<?php echo esc_attr($slides_per_view_xl); ?>" data-tablet="<?php echo esc_attr($slides_per_view_md); ?>" data-mobile-sm="<?php echo esc_attr($slides_per_view_sm); ?>" data-mobile="<?php echo esc_attr($slides_per_view_xs); ?>" data-space-lg="<?php echo esc_attr($space_between_desktop); ?>"
                    data-space-md="<?php echo esc_attr($space_between_tablet); ?>" data-space="<?php echo esc_attr($space_between_mobile); ?>" data-pagination="<?php echo esc_attr($slides_per_group_xs); ?>" data-pagination-sm="<?php echo esc_attr($slides_per_group_sm); ?>" data-pagination-md="<?php echo esc_attr($slides_per_group_md); ?>" data-pagination-lg="<?php echo esc_attr($slides_per_group_xl); ?>">
                    <div class="swiper-wrapper">
						<?php foreach ($settings['carousel_list'] as $carousel): 
						    $product = wc_get_product($carousel['product_id']); 
                        ?>
                            <div class="swiper-slide">
                                <div class="box_testimonial--V02 wow fadeInLeft">
                                    <div class="box_testimonial-content">
                                        <div class="tes-head">
                                            <ul class="rate-wrap">
                                                <?php
                                                for ($i = 1; $i <= 5; $i++) {
                                                    $active_class = ($i <= $carousel['rating']) ? ' text-star' : '';
                                                    echo '<li><i class="icon-star' . $active_class . '"></i></li>';
                                                }
                                                ?>
                                            </ul>
                                            <div class="author-info">
                                                <?php if ( $carousel['name'] ) : ?>
                                                    <p class="name"><?php echo wp_kses_post( $carousel['name'] ); ?></p>
                                                <?php endif; ?>
                                                <?php if ( $carousel['badge'] ) : ?>
                                                    <p class="verify">

                                                        <svg width="11" height="11" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <g>
                                                                <path class="check-svg"
                                                                    d="M7 14C10.866 14 14 10.866 14 7C14 3.13401 10.866 0 7 0C3.13401 0 0 3.13401 0 7C0 10.866 3.13401 14 7 14Z"
                                                                    fill="#48B02C" />
                                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                                    d="M5.30268 8.96231L10.2357 4.02924C10.306 3.95923 10.4011 3.91992 10.5003 3.91992C10.5995 3.91992 10.6946 3.95923 10.7649 4.02924L11.2444 4.50877C11.3144 4.57902 11.3537 4.67416 11.3537 4.77334C11.3537 4.87251 11.3144 4.96765 11.2444 5.0379L6.30583 9.97097C6.23582 10.0393 6.14186 10.0776 6.04402 10.0776C5.94618 10.0776 5.85222 10.0393 5.78221 9.97097L5.30268 9.49145C5.23267 9.42119 5.19336 9.32606 5.19336 9.22688C5.19336 9.1277 5.23267 9.03256 5.30268 8.96231Z"
                                                                    fill="white" />
                                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                                    d="M3.76447 5.95293L6.7684 8.96238C6.83841 9.03263 6.87772 9.12776 6.87772 9.22694C6.87772 9.32612 6.83841 9.42126 6.7684 9.49151L6.29439 9.96553C6.22413 10.0355 6.129 10.0748 6.02982 10.0748C5.93064 10.0748 5.8355 10.0355 5.76525 9.96553L2.7558 6.96159C2.68579 6.89134 2.64648 6.7962 2.64648 6.69702C2.64648 6.59784 2.68579 6.50271 2.7558 6.43246L3.23533 5.95844C3.30558 5.88843 3.40072 5.84912 3.4999 5.84912C3.59908 5.84912 3.69421 5.88292 3.76447 5.95293Z"
                                                                    fill="white" />
                                                            </g>
                                                        </svg>
                                                        <?php echo wp_kses_post( $carousel['badge'] ); ?>
                                                    </p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="tes-text">
                                            <?php if ( $carousel['title'] ) : ?>
                                                <h5 class="title"><?php echo wp_kses_post( $carousel['title'] ); ?></h5>
                                            <?php endif; ?>
                                            <?php if ( $carousel['description'] ) : ?>
                                                <p class="text">
                                                    <?php echo wp_kses_post( $carousel['description'] ); ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <?php if($product): ?>
                                        <div class="box_testimonial-item">
                                            <div class="image-item">
                                                <img src="<?php echo esc_url(get_the_post_thumbnail_url($product->get_id(), 'ovinx-testimonial-style1')); ?>" alt="<?php echo esc_attr($product->get_name()); ?>">
                                            </div>
                                            <div class="info-item">
                                                <a href="<?php echo esc_url(get_permalink($product->get_id())); ?>" class="product-text link text-caption text-line-clamp-1 fw-medium">
                                                    <?php echo esc_html($product->get_name()); ?>
                                                </a>
                                                <p class="fw-medium price-product"> <?php echo wp_kses_post($product->get_price_html()); ?> </p>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach;?>
                    </div>

                    <div class="group-pagination <?php echo esc_attr($carousel_pagination_desktop); ?>-desktop <?php echo esc_attr($carousel_pagination_tablet); ?>-tablet <?php echo esc_attr($carousel_pagination_mobile); ?>-mobile">
                        <div class="wrap-bullet">
                            <div class="sw-dot-default style-white tf-sw-pagination"></div>
                        </div>
                    </div>

                </div>
            </div>
        </section>
        <!-- /Testimonial -->