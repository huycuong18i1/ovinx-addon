<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<div class="tf-flat-spacing">
    <div class="container"> 
        <div class="flat-animate-tab cls-tablist">
            <ul class="tab-cls" role="tablist">
            <?php $index = 0; ?>
            <?php foreach($categories as $key => $category) : ?>
                <li class="nav-tab-item" role="presentation">
                    <div data-bs-toggle="tab" class="tf-btn-tab hover-cursor-img <?php echo esc_attr( $index == 0 ? 'active' : ''); ?>" data-bs-target="#<?php echo esc_attr($key. "-" . $widget_id); ?>">
                        <span class="text h3">
                            <?php echo esc_html($category['name']); ?>
                            <span class="count">
                                <?php echo esc_html($category['count']); ?>
                            </span>
                        </span>
                        <a href="<?php echo esc_url($category['url']); ?>" class="icon icon-arrow-top-right"></a>
                        <div class="hover-image">
                            <img src="<?php echo esc_html($category['image']); ?>" class="lazyload" alt="Hover Image">
                        </div>
                    </div>
                </li>
                <?php $index++; ?>
            <?php endforeach; ?>
            </ul>
            <div class="tab-content wow fadeInUp">
            <?php $index = 0; ?>
            <?php foreach($categories as $key => $category) : ?>
                <div class="tab-pane <?php echo esc_attr( $index == 0 ? 'active show' : ''); ?>" id="<?php echo esc_attr($key. "-" . $widget_id); ?>" role="tabpanel">
                    <a href="<?php echo esc_url($category['url']); ?>" class="shape-image hover-img">
                        <div class="image img-style z-5 position-relative">
                            <img src="<?php echo esc_html($category['image']); ?>" alt="Banner" class="lazyload">
                        </div>
                        <span class="line-circle"></span>
                    </a>
                </div>
                <?php $index++; ?>
            <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>