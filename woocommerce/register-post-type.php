<?php

add_action('woocommerce_init', 'register_tf_post_type');

function register_tf_post_type(){

    if(!function_exists('themesflat_get_opt')){
        return;
    }
    
    if(themesflat_get_opt('tf_volume_discount')){
        $volume_discount_args = array(
            'labels' => array(
                'name' => 'Volume Discounts',
                'singular_name' => 'Volume Discount',
                'add_new' => 'Add New',
                'add_new_item' => 'Add New Volume Discount',
                'edit_item' => 'Edit Volume Discount',
                'new_item' => 'New Volume Discount',
                'view_item' => 'View Volume Discount',
                'search_items' => 'Search Volume Discounts',
                'not_found' => 'No volume discounts found',
                'not_found_in_trash' => 'No volume discounts found in Trash',
            ),
            'public' => true,
            'show_in_menu'  => 'edit.php?post_type=product',
            'show_in_rest' => true,
            'supports' => array('title', 'revisions'),
            'rewrite' => false,
        );
        
        register_post_type('tf_volume_discount', $volume_discount_args);
    }
    
    if(themesflat_get_opt('tf_size_guide')){
        $size_guide_args = array(
            'labels' => array(
                'name' => 'Size Guides',
                'singular_name' => 'Size Guide',
                'add_new' => 'Add New',
                'add_new_item' => 'Add New Size Guide',
                'edit_item' => 'Edit Size Guide',
                'new_item' => 'New Size Guide',
                'view_item' => 'View Size Guide',
                'search_items' => 'Search Size Guides',
                'not_found' => 'No size guides found',
                'not_found_in_trash' => 'No size guides found in Trash',
            ),
            'public' => true,
            // 'has_archive' => true,
            'show_in_menu'  => 'edit.php?post_type=product',
            'show_in_rest' => true,
            'supports' => array('title', 'revisions'),
            'rewrite' => false,
        );
        
        register_post_type('tf_size_guide', $size_guide_args);
    }   
}

add_action('add_meta_boxes', 'tf_add_meta_boxes');

function tf_add_meta_boxes() {

    if(!function_exists('themesflat_get_opt') || !class_exists('Woo_Single_Product_Hook') ){
        return;
    }

    if(themesflat_get_opt('tf_size_guide')){
        add_meta_box(
            'size_guide_table',
            'Size Guide Table',
            array( Woo_Single_Product_Hook::instance() , 'render_size_guide_metabox'),
            'tf_size_guide',
            'normal',
            'high'
        );
    }

    if(themesflat_get_opt('tf_volume_discount')){
        add_meta_box(
            'volume_discount_box',
            'Volume Discount List',
            array( Woo_Single_Product_Hook::instance() , 'render_volume_discount_metabox'),
            'tf_volume_discount',
            'normal',
            'high'
        );
    }
}