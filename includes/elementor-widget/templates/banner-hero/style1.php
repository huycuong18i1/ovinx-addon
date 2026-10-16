        <!-- Hero Banner -->
        <div class="hero-banner ctn-hover widget-banner-hero">
            <div class="container">
                <div class="hero-main">

                    <?php for ( $i = 1; $i <= 4; $i++ ) :
                        $img = $settings["item_image_$i"] ?? [];
                        if ( ! empty( $img['url'] ) ) :
                            $classes = "img_item item-$i hover-repel";
                            if ( $i === 2 || $i === 3 ) {
                                $classes .= ' d-none d-lg-block';
                                $animation = $i === 2 ? 'fadeInLeftBottom' : 'fadeInRightBottom';
                            } else {
                                $animation = $i === 1 ? 'fadeInLeft' : 'fadeInRight';
                            }
                            ?>
                            <div class="<?php echo esc_attr( $classes ); ?>">
                                <img src="<?php echo esc_url( $img['url'] ); ?>" alt="Image <?php echo $i; ?>" class="wow <?php echo esc_attr( $animation ); ?>">
                            </div>
                        <?php endif;
                    endfor; ?>

                    <div class="hero-content">
                        <?php if ( ! empty( $settings['tag_text'] ) ) : ?>
                            <h6 class="tag"><?php echo wp_kses_post( $settings['tag_text'] ); ?></h6>
                        <?php endif; ?>

                        <?php if ( ! empty( $settings['main_title'] ) ) : ?>
                            <p class="title text-hero font-2"><?php echo wp_kses_post( $settings['main_title'] ); ?></p>
                        <?php endif; ?>

                        <?php if ( ! empty( $settings['sub_title'] ) ) : ?>
                            <p class="sub"><?php echo wp_kses_post( $settings['sub_title'] ); ?></p>
                        <?php endif; ?>

                        <?php if ( ! empty( $settings['btn1_text'] ) || ! empty( $settings['btn2_text'] ) ) : ?>
                            <div class="btn-group">
                                <?php if ( ! empty( $settings['btn1_text'] ) && ! empty( $settings['btn1_link']['url'] ) ) :
                                    $btn1_target = ! empty( $settings['btn1_link']['is_external'] ) ? ' target="_blank"' : '';
                                ?>
                                    <a href="<?php echo esc_url( $settings['btn1_link']['url'] ); ?>"<?php echo $btn1_target; ?> class="tf-btn btn-1 text-uppercase type-large">
                                        <?php echo esc_html( $settings['btn1_text'] ); ?>
                                        <i class="icon-arrow-right-3 fs-24"></i>
                                    </a>
							    <?php endif; ?>

                                <?php if ( ! empty( $settings['btn2_text'] ) && ! empty( $settings['btn2_link']['url'] ) ) :
                                    $btn2_target = ! empty( $settings['btn2_link']['is_external'] ) ? ' target="_blank"' : '';
                                ?>
                                    <a href="<?php echo esc_url( $settings['btn2_link']['url'] ); ?>"<?php echo $btn2_target; ?> class="link text-uppercase">
                                        <?php echo esc_html( $settings['btn2_text'] ); ?>
                                    </a>
							    <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </div>
        <!-- /Hero Banner -->