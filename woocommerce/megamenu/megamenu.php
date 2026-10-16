<?php
add_action( 'init', 'tf_register_postype_megamenu' );
function tf_register_postype_megamenu() {
    $labels = array(
        'name'               => __( 'Mega Menu', 'ovinx-addon' ),
        'singular_name'      => __( 'Mega Menu', 'ovinx-addon' ),
        'menu_name'          => __( 'Mega Menu', 'ovinx-addon' ),
        'name_admin_bar'     => __( 'Mega Menu', 'ovinx-addon' ),
        'add_new'            => __( 'Add New', 'ovinx-addon' ),
        'add_new_item'       => __( 'Add New Mega Menu', 'ovinx-addon' ),
        'new_item'           => __( 'New Mega Menu', 'ovinx-addon' ),
        'edit_item'          => __( 'Edit Mega Menu', 'ovinx-addon' ),
        'view_item'          => __( 'View Mega Menu', 'ovinx-addon' ),
        'all_items'          => __( 'All Mega Menus', 'ovinx-addon' ),
        'search_items'       => __( 'Search Mega Menus', 'ovinx-addon' ),
        'not_found'          => __( 'No Mega Menus found.', 'ovinx-addon' ),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true, 
        'publicly_queryable' => true, 
        'exclude_from_search' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'show_in_rest'       => true, 
        'menu_icon'          => 'dashicons-screenoptions',
        'supports'           => array( 'title', 'editor' ),
        'capability_type'    => 'post',
        'hierarchical'       => false,
        'has_archive'        => false,
        'rewrite'            => array( 'slug' => 'megamenu' ), 
    );

    register_post_type( 'megamenu', $args );
    flush_rewrite_rules();
}


add_action( 'elementor/init', function() {
    add_post_type_support( 'megamenu', 'elementor' );
});


class TF_MegaMenu_Walker extends Walker_Nav_Menu {

/**
 * Cấp 0→1 dùng .sub-menu (hiện khi hover .menu-item, css/styles.css dòng ~4209).
 * Cấp ≥1 dùng .has-menu-lv2/.sub-menu-lv2 (hiện khi hover riêng .has-menu-lv2, dòng ~4827)
 * - PHẢI khác class nhau, nếu không mọi cấp cùng dùng .sub-menu sẽ khiến hover cấp 0
 * hiện luôn TẤT CẢ submenu con cháu cùng lúc (do .menu-item:hover .sub-menu là descendant selector).
 */
function start_lvl( &$output, $depth = 0, $args = null ) {
    if ( 0 === $depth ) {
        $output .= '<div class="sub-menu"><ul class="sub-menu_list">';
    } else {
        $output .= '<div class="sub-menu-lv2"><ul class="sub-menu_list">';
    }
}

    function end_lvl( &$output, $depth = 0, $args = null ) {
        $output .= '</ul></div>';
    }

    function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
        $classes = empty($item->classes) ? array() : (array) $item->classes;

        // Nếu là menu cấp 0
        if ( $depth === 0 ) {

            $is_mega = ! empty( $item->mega_menu_enabled ) && $item->mega_menu_enabled === '1';
            $has_children = in_array( 'menu-item-has-children', $classes, true );

            // Mega Menu check
            if ( $is_mega ) {
                $classes[] = 'tf-megamenu';
                if ( !empty($item->mega_menu_position) && $item->mega_menu_position === 'relative' ) {
                    $classes[] = 'tf-megamenu-relative';
                }
            } else {
                // .sub-menu (cấp 1) định vị theo chính <li> này (position:relative bắt buộc
                // để absolute .sub-menu không lệch trồi ra ngoài, css/styles.css dòng ~4268).
                // Mega menu KHÔNG thêm class này để panel absolute escape ra ancestor rộng hơn (.tf-header).
                $classes[] = 'position-relative';
            }

            if ( in_array('current-menu-item', $classes) ) {
                $classes[] = 'active';
            }

            $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
            $id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
            $id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

            $output .= '<li' . $id . ' class="' . esc_attr( $class_names ) . '">';

            // link
            $atts           = array();
            $atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
            $atts['target'] = ! empty( $item->target )     ? $item->target     : '';
            $atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
            $atts['href']   = ! empty( $item->url )        ? $item->url        : '';

            $attributes = '';
            foreach ( $atts as $attr => $value ) {
                if ( ! empty( $value ) ) {
                    $value       = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
                    $attributes .= ' ' . $attr . '="' . $value . '"';
                }
            }

            $title = apply_filters( 'the_title', $item->title, $item->ID );
            $arrow = ( $has_children || $is_mega ) ? '<i class="icon icon-CaretDownV2"></i>' : '';
            $output .= '<a' . $attributes . ' class="item-link"><span class="text">' . esc_html( $title ) . '</span>' . $arrow . '</a>';

            // Mega Menu Content
            if ( $is_mega ) {
                $width_class = '';
                $inline_style = '';

                if ( $item->mega_menu_width_type === 'full' ) {
                    $width_class = 'mega-full';
                } elseif ( $item->mega_menu_width_type === 'custom' && ! empty( $item->mega_menu_custom_width ) ) {
                    $width_class = 'mega-custom';
                    $inline_style = ' style="width:' . esc_attr( $item->mega_menu_custom_width ) . 'px;left:'. esc_attr($item->mega_menu_left_offset) .'"';
                    if ( ! empty( $item->mega_menu_position ) && $item->mega_menu_position === 'relative' ) {
                        $inline_style = ' style="width:' . esc_attr( $item->mega_menu_custom_width ) . 'px !important;left:'. esc_attr($item->mega_menu_left_offset) .'"';
                    }
                }

                $output .= '<div class="sub-menu mega-menu mega-product ' . esc_attr( $width_class ) . '"' . $inline_style . '>';

                if ( ! empty( $item->mega_menu_block_id ) ) {                              
                    $document = Elementor\Plugin::$instance->documents->get_doc_for_frontend( $item->mega_menu_block_id );
                    if ( ! $document || ! $document->is_built_with_elementor() ) {
                        $content = apply_filters( 'the_content', get_post_field( 'post_content', $item->mega_menu_block_id ) );           
                    } else {
                        $content = "";
                        if ( class_exists( "\\Elementor\\Plugin" ) ) {
                            $pluginElementor  = \Elementor\Plugin::instance();
                            $content = $pluginElementor->frontend->get_builder_content_for_display( $item->mega_menu_block_id );  
                        }
                    }
                    $output .= $content;
                }

                $output .= '</div>';
            }

        } else {
            // Submenu item
            $classes = empty($item->classes) ? array() : (array) $item->classes;
            $has_children = in_array( 'menu-item-has-children', $classes, true );

            if ( $has_children ) {
                $classes[] = 'has-menu-lv2';
            }

            // active cho submenu
            if (
                in_array('current-menu-item', $classes) ||
                in_array('current-menu-parent', $classes) ||
                in_array('current-menu-ancestor', $classes)
            ) {
                $classes[] = 'active';
            }

            $class_names = join(' ', array_filter($classes));

            $output .= '<li class="' . esc_attr($class_names) . '">';

            $link_class = 'sub-menu_link' . ( $has_children ? ' has-icon' : '' );
            $arrow = $has_children ? '<i class="icon icon-CaretRightV2"></i>' : '';

            $output .= '<a href="' . esc_url( $item->url ) . '" class="' . esc_attr( $link_class ) . '">
                <span class="text">' . esc_html($item->title) . '</span>' . $arrow . '
            </a>';
        }
    }

    function end_el( &$output, $item, $depth = 0, $args = null ) {
        $output .= '</li>';
    }
}
?>