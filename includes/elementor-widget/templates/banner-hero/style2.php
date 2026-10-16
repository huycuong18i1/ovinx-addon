 <!-- Banner Home -->
        <section class="tf-slideshow widget-banner-hero banner-2">
            <div class="slider_wrap type-2">
                <div class="sld-image">
                    <div class="inner-left">
                        <img src="<?php echo esc_url( $settings["item_image_banner"]['url'] ); ?>"  alt="Banner">
                    </div>
                    <div class="inner-right">
                        <img src="<?php echo esc_url( $settings["item_image_banner_2"]['url'] ); ?>" alt="Image">
                    </div>
                </div>
                <div class="sld-content-2">
                    <div class="container">
                        <div class="content-sld text-center">
                            
                            <?php if ( ! empty( $settings['tag_text'] ) ) : ?>
                                <h6 class="tag text-white text-uppercase"><?php echo wp_kses_post( $settings['tag_text'] ); ?></h6>
                            <?php endif; ?>

                            
                            <?php if ( ! empty( $settings['main_title'] ) ) : ?>
                                <p class="title-sld-3 font-2 title">
                                    <?php echo wp_kses_post( $settings['main_title'] ); ?>
                                </p>
                            <?php endif; ?>

                        </div>
                        <div class="row">
                            <div class="col-sm-6 col-12 offset-sm-6">
                                <div class="content-sld-2">
                                    <?php if ( ! empty( $settings['sub_title'] ) ) : ?>
                                        <p class="sub-title-sld-2 sub tag">
                                           <?php echo wp_kses_post( $settings['sub_title'] ); ?>
                                        </p>
                                    <?php endif; ?>
                                    <?php if ( ! empty( $settings['btn1_text'] ) || ! empty( $settings['btn2_text'] ) ) : ?>
                                        <div class="btn-group">

                                        <?php if ( ! empty( $settings['btn1_text'] ) && ! empty( $settings['btn1_link']['url'] ) ) :
                                            $btn1_target = ! empty( $settings['btn1_link']['is_external'] ) ? ' target="_blank"' : '';
                                        ?>
                                            <a href="<?php echo esc_url( $settings['btn1_link']['url'] ); ?>"<?php echo $btn1_target; ?> class="tf-btn btn-1 type-large style-white-2">
                                                <?php echo wp_kses_post( $settings['btn1_text'] ); ?>
                                                <i class="icon-arrow-right fs-24"></i>
                                            </a>
                                        <?php endif; ?>

                                        <?php if ( ! empty( $settings['btn2_text'] ) && ! empty( $settings['btn2_link']['url'] ) ) :
                                            $btn2_target = ! empty( $settings['btn2_link']['is_external'] ) ? ' target="_blank"' : '';
                                        ?>
                                            <a href="<?php echo esc_url( $settings['btn2_link']['url'] ); ?>"<?php echo $btn2_target; ?> class="text-white link text-uppercase">
                                                <?php echo wp_kses_post( $settings['btn2_text'] ); ?>
                                            </a>
                                        <?php endif; ?>

                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="sld-img-item d-none d-xxl-block">
                    <img src="<?php echo esc_url( $settings["item_image_banner_small"]['url'] ); ?>" alt="Image">
                </div>
            </div>
        </section>
        <!-- /Banner Home -->