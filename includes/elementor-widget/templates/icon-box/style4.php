
<ul class="list-contact">
    <?php foreach ($settings['carousel_list4'] as $carousel): ?>
        <li class="item">
            <?php if (!empty($carousel['icon'])) : ?>
                <div class="ic-wrap ">
                    <?php \Elementor\Icons_Manager::render_icon($carousel['icon'], ['aria-hidden' => 'true','class' => 'icon']); ?>
                </div>
            <?php endif;?>
            <div class="info">
                <?php if (!empty($carousel['title'])) :?><p class="mini-title"><?php echo wp_kses_post( $carousel['title'] ); ?></p><?php endif;?>
                <?php if (!empty($carousel['description'])) :?>
                    <a href="#" class="h5 fw-medium text-primary link-underline">
                        <?php echo wp_kses_post( $carousel['description'] ); ?>
                    </a>
                <?php endif;?>
            </div>
        </li>
    <?php endforeach;?>
</ul>