<?php
add_action( 'init', 'tf_register_postype_popup' );
function tf_register_postype_popup() {
    $labels = array(
        'name'               => __( 'Popup Builder', 'ovinx' ),
        'singular_name'      => __( 'Popup Builder', 'ovinx' ),
        'menu_name'          => __( 'Popup Builder', 'ovinx' ),
        'name_admin_bar'     => __( 'Popup Builder', 'ovinx' ),
        'add_new'            => __( 'Add New', 'ovinx' ),
        'add_new_item'       => __( 'Add New Popup', 'ovinx' ),
        'new_item'           => __( 'New Popup', 'ovinx' ),
        'edit_item'          => __( 'Edit Popup', 'ovinx' ),
        'view_item'          => __( 'View Popup', 'ovinx' ),
        'all_items'          => __( 'All Popups', 'ovinx' ),
        'search_items'       => __( 'Search Popups', 'ovinx' ),
        'not_found'          => __( 'No Popups found.', 'ovinx' ),
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
        'rewrite'            => array( 'slug' => 'popup_builder' ), 
    );

    register_post_type( 'popup_builder', $args );

    flush_rewrite_rules();
}



add_action( 'elementor/init', function() {
    add_post_type_support( 'popup_builder', 'elementor' );
});


?>