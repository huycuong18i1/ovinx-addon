<!-- /Banner Testimonial -->
<section class="widget-testimonial section-testimonial tf-sw-thumbs">
    <div class="testimonial-v01">
        <div class="swiper sw-thumb">
            <div class="swiper-wrapper">
                <?php foreach ($settings['carousel_list'] as $carousel): ?>
                    <div class="swiper-slide">
                        <?php if ( $carousel['testimonial_image']['url'] ) : ?>
                            <div class="tes-image">
                                <img loading="lazy" src="<?php echo esc_attr($carousel['testimonial_image']['url']) ?>" alt="image">
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach;?>
            </div>
        </div>
        <div class="col-right">
            <div class="swiper sw-main-thumb">
                <div class="swiper-wrapper">
                    <?php foreach ($settings['carousel_list'] as $carousel): ?>
                    <div class="swiper-slide">
                        <div class="content">
                            <ul class="rate-list">
                                <?php
                                $rating = intval( $carousel['star'] );
                                for ($i = 1; $i <= 5; $i++) {
                                    $active = ($i <= $rating) ? 'text-white' : '';
                                    echo '<li><i class="icon icon-star-4 ' . $active . '"></i></li>';
                                }
                                ?>
                            </ul>
                            <?php if ( $carousel['description'] ) : ?>
                                 <h4 class="tes-text text-white description">
                                   <?php echo wp_kses_post( $carousel['description'] ); ?>
                                </h4>
                            <?php endif; ?>
                           
                            <?php if ( $carousel['name'] ) : ?>
                                <h5 class="tes-author text-white title"><?php echo wp_kses_post( $carousel['name'] ); ?></h5>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach;?>
                </div>
            </div>
            <?php if ( $settings['bullet_enable'] == 'yes' ) : ?>
                <div class="sw-pg-thumb"></div>
            <?php endif; ?>
           
        </div>
    </div>
</section>