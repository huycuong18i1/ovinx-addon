        <?php 
            $image_url = \Elementor\Group_Control_Image_Size::get_attachment_image_src(
                $settings['slider_image2']['id'],
                'image_size',
                $settings
            );
        ?>	
        <div class="widget-image-box">
            <div class="banner_V03 hover-img4">
                <?php if ( $image_url ) : ?>
                    <div class="bn-image img-style4">
                        <img src="<?php echo esc_url( $image_url ); ?>" alt="Image">
                    </div>
                <?php else: ?>
                    <div class="bn-image img-style4">
                        <img src="<?php echo TF_PLUGIN_URL."includes/elementor-widget/assets/images/placeholder-image.jpg"; ?>" alt="Image">
                    </div>
                <?php endif; ?>
                <div class="bn-content wow fadeInUp">
                    <?php if ( $settings['title2'] ) : ?>
                        <h1 class="fw-normal">
                            <a href="<?php echo esc_attr($settings['link_button2']['url']); ?>" class="title"><?php echo wp_kses_post($settings['title2']); ?></a>
                        </h1>
                    <?php endif; ?>
                    <?php if ( $settings['desc2'] ) : ?>
                        <p class="sub-title text-main-8 ">
                            <?php echo wp_kses_post($settings['desc2']); ?>
                        </p>
                    <?php endif; ?>
                    <?php if ( $settings['button_text2'] ) : ?>
                        <a href="<?php echo esc_attr($settings['link_button2']['url']); ?>" class="tf-btn btn-def-2 type-large px-0">
                            <?php echo wp_kses_post( $settings['button_text2'] ); ?>
                            <i class="icon icon-arrow-right-3"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <!-- /Banner Image Text -->