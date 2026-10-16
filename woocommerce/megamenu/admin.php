<?php
add_action( 'wp_nav_menu_item_custom_fields', 'tf_add_fields_menu', 10, 4 );
function tf_add_fields_menu( $item_id, $item, $depth, $args ) {
    if ( $depth !== 0 ) return;
    
    $locations = get_nav_menu_locations();
    $primary_menu_id = isset($locations['primary']) ? $locations['primary'] : false;

    if (!$primary_menu_id) return;

    if (isset($_GET['menu']) && intval($_GET['menu']) !== $primary_menu_id) {
        return;
    }

    $enabled = get_post_meta( $item_id, '_menu_item_mega_menu_enabled', true );
    $width_type = get_post_meta( $item_id, '_menu_item_mega_menu_width_type', true );
    $custom_width = get_post_meta( $item_id, '_menu_item_mega_menu_custom_width', true );
    $position = get_post_meta( $item_id, '_menu_item_mega_menu_position', true );
    $left_offset = get_post_meta( $item_id, '_menu_item_mega_menu_left_offset', true );
    $block_id = get_post_meta( $item_id, '_menu_item_mega_menu_block_id', true );

    $blocks = get_posts([
        'post_type' => 'megamenu',
        'numberposts' => -1
    ]);
    ?>
    <div class="field-mega-menu-toggle">
        <p class="description-wide">
            <label>
                <input type="checkbox"
                       class="menu-item-mega-menu-checkbox"
                       name="menu-item-mega-menu[<?php echo esc_attr( $item_id ); ?>]"
                       value="1" <?php checked( $enabled, '1' ); ?> />
                <?php _e('Enable Mega Menu','ovinx-addon'); ?>
            </label>
        </p>
    </div>

    <div class="field-mega-menu-advanced" style="<?php echo $enabled === '1' ? '' : 'display:none;'; ?>">
        <p class="description-wide">
            <label>
                <?php _e('Select Width','ovinx-addon'); ?>
                <select class="menu-item-mega-menu-width-select" name="menu-item-mega-menu-width-type[<?php echo esc_attr( $item_id ); ?>]">
                    <option value="full" <?php selected( $width_type, 'full' ); ?>><?php _e('Full Width','ovinx-addon'); ?></option>
                    <option value="custom" <?php selected( $width_type, 'custom' ); ?>><?php _e('Custom Width','ovinx-addon'); ?></option>
                </select>
            </label>
        </p>

        <p class="description-wide field-custom-width" style="<?php echo $width_type === 'custom' ? '' : 'display:none;'; ?>">
            <label>
                <?php _e('Enter Custom Width (px)','ovinx-addon'); ?>
                <input type="text"
                       name="menu-item-mega-menu-custom-width[<?php echo esc_attr( $item_id ); ?>]"
                       value="<?php echo esc_attr( $custom_width ); ?>"
                       placeholder="e.g. 1000" />
            </label>
        </p>

        <p class="description-wide">
            <label>
                <?php _e('Position', 'ovinx-addon'); ?>
                <select class="menu-item-mega-menu-position" name="menu-item-mega-menu-position[<?php echo esc_attr($item_id); ?>]">
                    <option value="static" <?php selected( $position, 'static' ); ?>><?php _e('Static', 'ovinx-addon'); ?></option>
                    <option value="relative" <?php selected( $position, 'relative' ); ?>><?php _e('Relative', 'ovinx-addon'); ?></option>
                </select>
                
            </label>
        </p>

        <p class="description-wide field-left-offset" style="<?php echo $position === 'relative' ? '' : 'display:none;'; ?>">
            <label>
                <?php _e('Left Offset (px or %)', 'ovinx-addon'); ?>
                <input type="text"
                    name="menu-item-mega-menu-left-offset[<?php echo esc_attr($item_id); ?>]"
                    value="<?php echo esc_attr($left_offset); ?>"
                    placeholder="e.g. 100px or 10%" />
            </label>
            <p><?php _e('Position static is center sub menu', 'ovinx-addon'); ?></p>
        </p>

        <p class="description-wide">
            <label>
                <?php _e('Select Mega Menu','ovinx-addon'); ?>
                <select name="menu-item-mega-menu-block-id[<?php echo esc_attr( $item_id ); ?>]">
                    <option value=""><?php _e('-- Select Menu --','ovinx-addon'); ?></option>
                    <?php foreach ( $blocks as $block ) : ?>
                        <option value="<?php echo $block->ID; ?>" <?php selected( $block_id, $block->ID ); ?>>
                            <?php echo esc_html( $block->post_title ); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
            <br/>
            <a href="<?php echo admin_url( 'post-new.php?post_type=megamenu' ); ?>" target="_blank">
                <?php _e('Add New Mega Menu','ovinx-addon'); ?>
            </a>
        </p>
    </div>
    <?php
}

add_action( 'wp_update_nav_menu_item', 'tf_save_field_megamenu', 10, 3 );
function tf_save_field_megamenu( $menu_id, $menu_item_db_id, $args ) {
    update_post_meta( $menu_item_db_id, '_menu_item_mega_menu_enabled',
        isset( $_POST['menu-item-mega-menu'][ $menu_item_db_id ] ) ? '1' : '0' );

    update_post_meta( $menu_item_db_id, '_menu_item_mega_menu_width_type',
        sanitize_text_field( $_POST['menu-item-mega-menu-width-type'][ $menu_item_db_id ] ?? '' ) );

    update_post_meta( $menu_item_db_id, '_menu_item_mega_menu_custom_width',
        sanitize_text_field( $_POST['menu-item-mega-menu-custom-width'][ $menu_item_db_id ] ?? '' ) );

    update_post_meta( $menu_item_db_id, '_menu_item_mega_menu_block_id',
        intval( $_POST['menu-item-mega-menu-block-id'][ $menu_item_db_id ] ?? 0 ) );

    update_post_meta( $menu_item_db_id, '_menu_item_mega_menu_position',
        sanitize_text_field( $_POST['menu-item-mega-menu-position'][ $menu_item_db_id ] ?? '' ) );

    update_post_meta( $menu_item_db_id, '_menu_item_mega_menu_left_offset',   
        sanitize_text_field( $_POST['menu-item-mega-menu-left-offset'][ $menu_item_db_id ] ?? '' ) );   
}

add_filter( 'wp_nav_menu_objects', 'tf_render_megamenu', 10, 2 );
function tf_render_megamenu( $items, $args ) {
    foreach ( $items as &$item ) {
        $item->mega_menu_enabled = get_post_meta( $item->ID, '_menu_item_mega_menu_enabled', true );
        $item->mega_menu_width_type = get_post_meta( $item->ID, '_menu_item_mega_menu_width_type', true );
        $item->mega_menu_custom_width = get_post_meta( $item->ID, '_menu_item_mega_menu_custom_width', true );
        $item->mega_menu_block_id = get_post_meta( $item->ID, '_menu_item_mega_menu_block_id', true );
        $item->mega_menu_position = get_post_meta( $item->ID, '_menu_item_mega_menu_position', true );
        $item->mega_menu_left_offset = get_post_meta( $item->ID, '_menu_item_mega_menu_left_offset', true );
    }
    return $items;
}

add_action( 'admin_footer', 'tf_megamenu_scrips' );
function tf_megamenu_scrips() {
    $screen = get_current_screen();
    if ( $screen && $screen->base === 'nav-menus' ) :
        ?>
        <script>
        jQuery(document).ready(function ($) {
            function toggleMegaFields(wrapper) {
                const checkbox = wrapper.find('.menu-item-mega-menu-checkbox');
                const advanced = wrapper.find('.field-mega-menu-advanced');
                if (checkbox.is(':checked')) {
                    advanced.show();
                } else {
                    advanced.hide();
                }
            }

            function toggleCustomWidth(wrapper) {
                const select = wrapper.find('.menu-item-mega-menu-width-select');
                const customField = wrapper.find('.field-custom-width');
                if (select.val() === 'custom') {
                    customField.show();
                } else {
                    customField.hide();
                }
            }

            $('.menu-item-settings').each(function () {
                const wrapper = $(this);
                toggleMegaFields(wrapper);
                toggleCustomWidth(wrapper);

                wrapper.on('change', '.menu-item-mega-menu-checkbox', function () {
                    toggleMegaFields(wrapper);
                });

                wrapper.on('change', '.menu-item-mega-menu-width-select', function () {
                    toggleCustomWidth(wrapper);
                });
            });

            $('.menu-item-mega-menu-position').on('change', function () {
                var container = $(this).closest('.menu-item-settings');
                if ($(this).val() === 'relative') {
                    container.find('.field-left-offset').slideDown();
                } else {
                    container.find('.field-left-offset').slideUp();
                }
            });
        });
        </script>
        <?php
    endif;
}
?>