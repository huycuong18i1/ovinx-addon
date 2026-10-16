<?php

add_action( 'init', 'tf_header_footer_post_type' );
add_action( 'add_meta_boxes',  'tf_header_footer_register_metabox'  );
add_action( 'wp_ajax_tfhf_get_posts_by_query',  'tfhf_get_posts_by_query' );    
add_action( 'admin_action_edit',  'initialize_options'  );
add_action( 'save_post',  'tf_header_footer_save_meta'  );
add_filter( 'single_template',  'tf_header_footer_load_canvas_template'  );
add_action( 'wp',  'hooks' ,100 );
add_action( 'admin_enqueue_scripts', 'admin_scripts'  , 100 );  

function admin_scripts() {
    wp_enqueue_style( 'tfhf-select2', plugins_url( 'css/select2.css', __FILE__ ) );
    wp_enqueue_style( 'tfhf-admin', plugins_url( 'css/admin.css', __FILE__ ) );

    wp_enqueue_script( 'tfhf-select2', plugins_url( 'js/select2.js', __FILE__ ), [ 'jquery' ], false, true );
    wp_enqueue_script( 'tfhf-admin', plugins_url( 'js/admin.js', __FILE__ ), [ 'jquery' ], false, true );
    wp_enqueue_script( 'tfhf-admin-rule', plugins_url( 'js/admin-rule.js', __FILE__ ), [ 'jquery' ], false, true );
    $tfhf_localize_vars = array(
        'ajaxurl' =>    admin_url('admin-ajax.php'),
        'search'        => esc_html__( 'Search pages / post / categories', 'themesflat-core' ),
        'ajax_nonce'    => wp_create_nonce( 'tfhf-get-posts-by-query' ),
    );
    wp_localize_script( 'tfhf-admin-rule', 'tfhf_localize_vars', $tfhf_localize_vars );   
    
}

function tf_header_footer_post_type() {
    $labels = array(
        'name'                  => esc_html__( 'TF Header - Footer ', 'ovinx-addon' ),
        'singular_name'         => esc_html__( 'TF Header - Footer ', 'ovinx-addon' ),
        'rewrite'               => array( 'slug' => esc_html__( 'TF Header - Footer ' ) ),
        'menu_name'             => esc_html__( 'TF Header - Footer ', 'ovinx-addon' ),
        'add_new'               => esc_html__( 'Add New', 'ovinx-addon' ),
        'add_new_item'          => esc_html__( 'Add New Template', 'ovinx-addon' ),
        'new_item'              => esc_html__( 'New Template Item', 'ovinx-addon' ),
        'edit_item'             => esc_html__( 'Edit Template Item', 'ovinx-addon' ),
        'view_item'             => esc_html__( 'View Template', 'ovinx-addon' ),
        'all_items'             => esc_html__( 'All Template', 'ovinx-addon' ),
        'search_items'          => esc_html__( 'Search Template', 'ovinx-addon' ),
        'not_found'             => esc_html__( 'No Template Items Found', 'ovinx-addon' ),
        'not_found_in_trash'    => esc_html__( 'No Template Items Found In Trash', 'ovinx-addon' ),
        'parent_item_colon'     => esc_html__( 'Parent Template:', 'ovinx-addon' ),
        'not_found'             => esc_html__( 'No Template found', 'ovinx-addon' ),
        'not_found_in_trash'    => esc_html__( 'No Template found in Trash', 'ovinx-addon' )
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true, 
        'publicly_queryable' => true, 
        'exclude_from_search' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'show_in_rest'       => true, 
        'menu_icon'          => 'dashicons-admin-page',
        'supports'           => array( 'title', 'editor' ,'thumbnail', 'elementor' ),
        'capability_type'    => 'post',
        'hierarchical'       => false,
        'has_archive'        => false,
        'rewrite'            => array( 'slug' => 'tf_header_footer' ), 
    );

    register_post_type( 'tf_header_footer', $args );
    flush_rewrite_rules();
    
}

function tf_header_footer_register_metabox() {
    add_meta_box(
        'tfhf-meta-box',
        esc_html__( 'TF Header Or Footer Options', 'ovinx-addon' ), 
       'tf_header_footer_metabox_render', 
        'tf_header_footer', 'normal', 'high' );
}   

function tf_header_footer_metabox_render( $post ) {
    $values            = get_post_custom( $post->ID );
    $template_type     = isset( $values['tfhf_template_type'] ) ? esc_attr( $values['tfhf_template_type'][0] ) : '';
    wp_nonce_field( 'tfhf_meta_nounce', 'tfhf_meta_nounce' );
    ?>
    <table class="tfhf-options-table widefat">
        <tbody>
            <tr class="tfhf-options-row type-of-template">
                <td class="tfhf-options-row-heading">
                    <label for="tfhf_template_type"><?php esc_html_e( 'Type of Template', 'ovinx-addon' ); ?></label>
                </td>
                <td class="tfhf-options-row-content">
                    <select name="tfhf_template_type" id="tfhf_template_type">
                        <option value="" <?php selected( $template_type, '' ); ?>><?php esc_html_e( 'Select Option', 'ovinx-addon' ); ?></option>
                        <option value="type_header" <?php selected( $template_type, 'type_header' ); ?>><?php esc_html_e( 'Header', 'ovinx-addon' ); ?></option>
                        <option value="type_footer" <?php selected( $template_type, 'type_footer' ); ?>><?php esc_html_e( 'Footer', 'ovinx-addon' ); ?></option>
                    </select>
                </td>
            </tr>

            <?php tf_header_footer_metabox_rule(); ?>
        </tbody>
    </table>
    <?php
}

function tf_header_footer_metabox_rule() {  
    $include_locations = get_post_meta( get_the_id(), 'tfhf_template_include_locations', true );
    $exclude_locations = get_post_meta( get_the_id(), 'tfhf_template_exclude_locations', true );
    ?>
    <tr class="tfhf-target-rules-row tfhf-options-row">
        <td class="tfhf-target-rules-row-heading tfhf-options-row-heading">
            <label><?php esc_html_e( 'Display On', 'ovinx-addon' ); ?></label>
        </td>
        <td class="tfhf-target-rules-row-content tfhf-options-row-content">
            <?php
            target_rule_settings_field(
                'tfhf-target-rules-location',
                [
                    'title'          => esc_html__( 'Display Rules', 'ovinx-addon' ),
                    'value'          => '[{"type":"basic-global","specific":null}]',
                    'tags'           => 'site,enable,target,pages',
                    'rule_type'      => 'display',
                    'add_rule_label' => esc_html__( 'Add Display Rule Group', 'ovinx-addon' ),
                ],
                $include_locations
            );
            ?>
        </td>
    </tr>
    <tr class="tfhf-target-rules-row tfhf-options-row">
        <td class="tfhf-target-rules-row-heading tfhf-options-row-heading">
            <label><?php esc_html_e( 'Do Not Display On', 'ovinx-addon' ); ?></label>
        </td>
        <td class="tfhf-target-rules-row-content tfhf-options-row-content">
            <?php
            target_rule_settings_field(
                'tfhf-target-rules-exclusion',
                [
                    'title'          => esc_html__( 'Exclude On', 'ovinx-addon' ),
                    'value'          => '[]',
                    'tags'           => 'site,enable,target,pages',
                    'add_rule_label' => esc_html__( 'Add Exclusion Rule Group', 'ovinx-addon' ),
                    'rule_type'      => 'exclude',
                ],
                $exclude_locations
            );
            ?>
        </td>
    </tr> 
    <?php
} 


function initialize_options() {
    $location_selection = get_location_selections();
}

function get_location_selections() {
    $args = array(
        ''   => true,
        '_builtin' => true,
    );

    $post_types = get_post_types( $args, 'objects' );
    unset( $post_types['attachment'] );

    $args['_builtin'] = false;
    $custom_post_type = get_post_types( $args, 'objects' );

    $post_types = apply_filters( 'tfhf_location_rule_post_types', array_merge( $post_types, $custom_post_type ) );

    $special_pages = array(
        'special-404'    => esc_html__( '404 Page', 'ovinx-addon' ),
        'special-search' => esc_html__( 'Search Page', 'ovinx-addon' ),
        'special-blog'   => esc_html__( 'Blog / Posts Page', 'ovinx-addon' ),
        'special-front'  => esc_html__( 'Front Page', 'ovinx-addon' ),
        'special-date'   => esc_html__( 'Date Archive', 'ovinx-addon' ),
        'special-author' => esc_html__( 'Author Archive', 'ovinx-addon' ),
    );

    if ( class_exists( 'WooCommerce' ) ) {
        $special_pages['special-woo-shop'] = esc_html__( 'WooCommerce Shop Page', 'ovinx-addon' );
    }

    $selection_options = array(
        'basic'         => array(
            'label' => esc_html__( 'Basic', 'ovinx-addon' ),
            'value' => array(
                'basic-global'    => esc_html__( 'Entire Website', 'ovinx-addon' ),
                'basic-singulars' => esc_html__( 'All Singulars', 'ovinx-addon' ),
                'basic-archives'  => esc_html__( 'All Archives', 'ovinx-addon' ),
            ),
        ),

        'special-pages' => array(
            'label' => esc_html__( 'Special Pages', 'ovinx-addon' ),
            'value' => $special_pages,
        ),
    );

    $args = array(
        '' => true,
    );

    $taxonomies = get_taxonomies( $args, 'objects' );

    if ( ! empty( $taxonomies ) ) {
        foreach ( $taxonomies as $taxonomy ) {

            if ( 'post_format' == $taxonomy->name ) {
                continue;
            }

            foreach ( $post_types as $post_type ) {
                $post_opt = get_post_target_rule_options( $post_type, $taxonomy );

                if ( isset( $selection_options[ $post_opt['post_key'] ] ) ) {
                    if ( ! empty( $post_opt['value'] ) && is_array( $post_opt['value'] ) ) {
                        foreach ( $post_opt['value'] as $key => $value ) {
                            if ( ! in_array( $value, $selection_options[ $post_opt['post_key'] ]['value'] ) ) {
                                $selection_options[ $post_opt['post_key'] ]['value'][ $key ] = $value;
                            }
                        }
                    }
                } else {
                    $selection_options[ $post_opt['post_key'] ] = array(
                        'label' => $post_opt['label'],
                        'value' => $post_opt['value'],
                    );
                }
            }
        }
    }

    $selection_options['specific-target'] = array(
        'label' => esc_html__( 'Specific Target', 'ovinx-addon' ),
        'value' => array(
            'specifics' => esc_html__( 'Specific Pages / Posts / Taxonomies, etc.', 'ovinx-addon' ),
        ),
    );

    return apply_filters( 'tfhf_display_on_list', $selection_options );
}




function tfhf_get_posts_by_query() {

    check_ajax_referer( 'tfhf-get-posts-by-query', 'nonce' );

    $search_string = isset( $_POST['q'] ) ? sanitize_text_field( $_POST['q'] ) : '';
    $data          = array();
    $result        = array();

    $args = array(
        ''   => true,
        '_builtin' => false,
    );

    $output     = 'names';
    $operator   = 'and';
    $post_types = get_post_types( $args, $output, $operator );

    unset( $post_types['tf_header_footer'] );

    $post_types['Posts'] = 'post';
    $post_types['Pages'] = 'page';

    foreach ( $post_types as $key => $post_type ) {
        $data = array();

        add_filter( 'posts_search',  'search_only_titles' , 10, 2 );

        $query = new \WP_Query(
            array(
                's'              => $search_string,
                'post_type'      => $post_type,
                'posts_per_page' => - 1,
            )
        );

        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                $title  = get_the_title();
                $title .= ( 0 != $query->post->post_parent ) ? ' (' . get_the_title( $query->post->post_parent ) . ')' : '';
                $id     = get_the_id();
                $data[] = array(
                    'id'   => 'post-' . $id,
                    'text' => $title,
                );
            }
        }

        if ( is_array( $data ) && ! empty( $data ) ) {
            $result[] = array(
                'text'     => $key,
                'children' => $data,
            );
        }
    }

    $data = array();

    wp_reset_postdata();

    $args = array(
        '' => true,
    );

    $output     = 'objects';
    $operator   = 'and';
    $taxonomies = get_taxonomies( $args, $output, $operator );

    foreach ( $taxonomies as $taxonomy ) {
        $terms = get_terms(
            $taxonomy->name,
            array(
                'orderby'    => 'count',
                'hide_empty' => 0,
                'name__like' => $search_string,
            )
        );

        $data = array();

        $label = ucwords( $taxonomy->label );

        if ( ! empty( $terms ) ) {
            foreach ( $terms as $term ) {
                $term_taxonomy_name = ucfirst( str_replace( '_', ' ', $taxonomy->name ) );

                $data[] = array(
                    'id'   => 'tax-' . $term->term_id,
                    'text' => $term->name . ' archive page',
                );

                $data[] = array(
                    'id'   => 'tax-' . $term->term_id . '-single-' . $taxonomy->name,
                    'text' => 'All singulars from ' . $term->name,
                );
            }
        }

        if ( is_array( $data ) && ! empty( $data ) ) {
            $result[] = array(
                'text'     => $label,
                'children' => $data,
            );
        }
    }

    wp_send_json( $result );
}

function search_only_titles( $search, $wp_query ) {
    if ( ! empty( $search ) && ! empty( $wp_query->query_vars['search_terms'] ) ) {
        global $wpdb;

        $q = $wp_query->query_vars;
        $n = ! empty( $q['exact'] ) ? '' : '%';

        $search = array();

        foreach ( (array) $q['search_terms'] as $term ) {
            $search[] = $wpdb->prepare( "$wpdb->posts.post_title LIKE %s", $n . $wpdb->esc_like( $term ) . $n );
        }

        if ( ! is_user_logged_in() ) {
            $search[] = "$wpdb->posts.post_password = ''";
        }

        $search = ' AND ' . implode( ' AND ', $search );
    }

    return $search;
}

function tf_header_footer_save_meta( $post_id ) {

    if ( isset( $_POST['tfhf_template_type'] ) ) {
        update_post_meta( $post_id, 'tfhf_template_type', esc_attr( $_POST['tfhf_template_type'] ) );
    }

    if ( ! isset( $_POST['tfhf_meta_nounce'] ) || ! wp_verify_nonce( $_POST['tfhf_meta_nounce'], 'tfhf_meta_nounce' ) ) {
        return;
    }
    
    if ( ! current_user_can( 'edit_posts' ) ) {
        return;
    }
    $target_locations = get_format_rule_value( $_POST, 'tfhf-target-rules-location' );
    update_post_meta( $post_id, 'tfhf_template_include_locations', $target_locations );
    $target_exclusion = get_format_rule_value( $_POST, 'tfhf-target-rules-exclusion' );        
    update_post_meta( $post_id, 'tfhf_template_exclude_locations', $target_exclusion );

    return false;
}


 
   
function tf_header_footer_load_canvas_template( $single_template ) {
    global $post;

    if ( 'tf_header_footer' == $post->post_type ) {
        $elementor_canvas = ELEMENTOR_PATH . '/modules/page-templates/templates/canvas.php';

        if ( file_exists( $elementor_canvas ) ) {
            return $elementor_canvas;
        } else {
            return ELEMENTOR_PATH . '/includes/page-templates/canvas.php';
        }
    }

    return $single_template;
}    

function tf_get_header_id() {
    $header_id = get_template_id( 'type_header' );

    if ( '' === $header_id ) {
        $header_id = false;
    }

    return apply_filters( 'tf_get_header_id', $header_id );
}

function tf_get_footer_id() {
    $footer_id = get_template_id( 'type_footer' );

    if ( '' === $footer_id ) {
        $footer_id = false;
    }

    return apply_filters( 'tf_get_footer_id', $footer_id );
}

function get_template_id( $type ) {
    $option = [
        'location'  => 'tfhf_template_include_locations',
        'exclusion' => 'tfhf_template_exclude_locations',
    ];

    $tfhf_templates = get_posts_by_conditions( 'tf_header_footer', $option );

    foreach ( $tfhf_templates as $template ) {
        if ( get_post_meta( absint( $template['id'] ), 'tfhf_template_type', true ) === $type ) {
            return $template['id'];
        }
    }

    return '';
    
}



function hooks() {
    if ( tf_header_enabled() ) { 
        add_action( 'get_header',  'tf_override_header'  ); 
        add_action( 'tf_header',  'tf_render_header'  );             
    }

    if ( tf_footer_enabled() ) {
        add_action( 'get_footer',  'tf_override_footer'  ); 
        add_action( 'tf_footer',  'tf_render_footer'  ); 
    }
}  

function tf_override_header() {
    require_once plugin_dir_path( __FILE__ ).'tfhf-header.php';
    $templates   = [];
    $templates[] = 'header.php';
    remove_all_actions( 'wp_head' );
    ob_start();
    locate_template( $templates, true );
    ob_get_clean();
}

function tf_override_footer() {
    require_once plugin_dir_path( __FILE__ ).'tfhf-footer.php';
    $templates   = [];
    $templates[] = 'footer.php';
    remove_all_actions( 'wp_footer' );
    ob_start();
    locate_template( $templates, true );
    ob_get_clean();
}

function get_header_content() {
    $tf_get_header_id = tf_get_header_id();
    echo \Elementor\Plugin::instance()->frontend->get_builder_content_for_display($tf_get_header_id);
}

function get_footer_content() {
    $tf_get_footer_id = tf_get_footer_id();
    echo \Elementor\Plugin::instance()->frontend->get_builder_content_for_display($tf_get_footer_id);
}

function tf_render_header() {
    ?>        
    <section class="site-header tf-custom-header" role="banner"> 
        <div class="tf-container"> 
            <div class="tf-row">
                <div class="tf-col">              
                <?php echo get_header_content(); ?>
                </div>
            </div>
        </div>
    </section>
    <?php
}

function tf_render_footer() {
    $class_bt = '';

    if ( is_product() ) {
        global $product;

        if ( ! $product instanceof WC_Product ) {
            $product = wc_get_product( get_the_ID() );
        }

        if ( $product instanceof WC_Product ) {
            if ( $product->is_type( 'external' ) 
                || $product->is_type( 'grouped' ) 
                || $product->is_type( 'bundle' ) 
                || $product->is_type( 'composite' ) ) {
                $class_bt = '';
            } elseif ( $product->is_type( 'simple' ) && ! $product->is_in_stock() ) {
                $class_bt = '';
            } else {
                $class_bt = 'footer-pb-2';
            }
        }
    }

    ?>
    <section class="site-footer tf-custom-footer <?php 
        if ( function_exists('themesflat_get_opt') && themesflat_get_opt('show_navigation_bar') == 1 ) {
            echo 'xl-pb-70 ';
        }
        echo esc_attr( $class_bt );
    ?>">
        <div class="tf-container"> 
            <div class="tf-row">
                <div class="tf-col">                
                <?php echo get_footer_content(); ?>
                </div>
            </div>
        </div>
    </section>
    <?php
}


   
function get_location_by_key( $key ) {
    if ( ! isset( $location_selection ) || empty( $location_selection ) ) {
        $location_selection = get_location_selections();
    }
    $location_selection = $location_selection;

    foreach ( $location_selection as $location_grp ) {
        if ( isset( $location_grp['value'][ $key ] ) ) {
            return $location_grp['value'][ $key ];
        }
    }

    if ( strpos( $key, 'post-' ) !== false ) {
        $post_id = (int) str_replace( 'post-', '', $key );
        return get_the_title( $post_id );
    }

    if ( strpos( $key, 'tax-' ) !== false ) {
        $tax_id = (int) str_replace( 'tax-', '', $key );
        $term   = get_term( $tax_id );

        if ( ! is_wp_error( $term ) ) {
            $term_taxonomy = ucfirst( str_replace( '_', ' ', $term->taxonomy ) );
            return $term->name . ' - ' . $term_taxonomy;
        } else {
            return '';
        }
    }

    return $key;
}

function target_rule_settings_field( $name, $settings, $value ) {
    $input_name     = $name;
    $type           = isset( $settings['type'] ) ? $settings['type'] : 'target_rule';
    $class          = isset( $settings['class'] ) ? $settings['class'] : '';
    $rule_type      = isset( $settings['rule_type'] ) ? $settings['rule_type'] : 'target_rule';
    $add_rule_label = isset( $settings['add_rule_label'] ) ? $settings['add_rule_label'] : esc_html__( 'Add Rule', 'ovinx-addon' );
    $saved_values   = $value;
    $output         = '';

    if ( isset( $location_selection ) || empty( $location_selection ) ) {
        $location_selection = get_location_selections();
    }
    $selection_options = $location_selection;

    $output .= '<script type="text/html" id="tmpl-tfhf-target-rule-' . $rule_type . '-condition">';
    $output .= '<div class="tfhf-target-rule-condition tfhf-target-rule-{{data.id}}" data-rule="{{data.id}}" >';
    $output .= '<span class="target_rule-condition-delete dashicons dashicons-dismiss"></span>';

    $output .= '<div class="target_rule-condition-wrap" >';
    $output .= '<select name="' . esc_attr( $input_name ) . '[rule][{{data.id}}]" class="target_rule-condition form-control tfhf-input">';
    $output .= '<option value="">' . esc_html__( 'Select', 'ovinx-addon' ) . '</option>';

    foreach ( $selection_options as $group => $group_data ) {
        $output .= '<optgroup label="' . $group_data['label'] . '">';
        foreach ( $group_data['value'] as $opt_key => $opt_value ) {
            $output .= '<option value="' . $opt_key . '">' . $opt_value . '</option>';
        }
        $output .= '</optgroup>';
    }
    $output .= '</select>';
    $output .= '</div>';

    $output .= '</div>';

    $output .= '<div class="target_rule-specific-page-wrap" style="display:none">';
    $output .= '<select name="' . esc_attr( $input_name ) . '[specific][]" class="target-rule-select2 target_rule-specific-page form-control tfhf-input " multiple="multiple">';
    $output .= '</select>';
    $output .= '</div>';

    $output .= '</script>';

    $output .= '<div class="tfhf-target-rule-wrapper tfhf-target-rule-' . $rule_type . '-on-wrap" data-type="' . $rule_type . '">';
    $output .= '<div class="tfhf-target-rule-selector-wrapper tfhf-target-rule-' . $rule_type . '-on">';
    $output .= generate_target_rule_selector( $rule_type, $selection_options, $input_name, $saved_values, $add_rule_label );
    $output .= '</div>';

    $output .= '</div>';

    echo $output;
}

function get_post_target_rule_options( $post_type, $taxonomy ) {
    $post_key    = str_replace( ' ', '-', strtolower( $post_type->label ) );
    $post_label  = ucwords( $post_type->label );
    $post_name   = $post_type->name;
    $post_option = array();

    $all_posts                          = sprintf( esc_html__( 'All %s', 'ovinx-addon' ), $post_label );
    $post_option[ $post_name . '|all' ] = $all_posts;

    if ( 'pages' != $post_key ) {

        $all_archive                                = sprintf( esc_html__( 'All %s Archive', 'ovinx-addon' ), $post_label );
        $post_option[ $post_name . '|all|archive' ] = $all_archive;
    }

    if ( in_array( $post_type->name, $taxonomy->object_type ) ) {
        $tax_label = ucwords( $taxonomy->label );
        $tax_name  = $taxonomy->name;

        $tax_archive = sprintf( esc_html__( 'All %s Archive', 'ovinx-addon' ), $tax_label );

        $post_option[ $post_name . '|all|taxarchive|' . $tax_name ] = $tax_archive;
    }

    $post_output['post_key'] = $post_key;
    $post_output['label']    = $post_label;
    $post_output['value']    = $post_option;

    return $post_output;
}

function generate_target_rule_selector( $type, $selection_options, $input_name, $saved_values, $add_rule_label ) {
    $output = '<div class="target_rule-builder-wrap">';

    if ( ! is_array( $saved_values ) || ( is_array( $saved_values ) && empty( $saved_values ) ) ) {
        $saved_values                = array();
        $saved_values['rule'][0]     = '';
        $saved_values['specific'][0] = '';
    }

    $index = 0;
    
    foreach ( $saved_values['rule'] as $index => $data ) {            
        $output .= '<div class="tfhf-target-rule-condition tfhf-target-rule-' . $index . '" data-rule="' . $index . '" >';

        $output .= '<span class="target_rule-condition-delete dashicons dashicons-dismiss"></span>';
        $output .= '<div class="target_rule-condition-wrap" >';
        $output .= '<select name="' . esc_attr( $input_name ) . '[rule][' . $index . ']" class="target_rule-condition form-control tfhf-input">';
        $output .= '<option value="">' . esc_html__( 'Select', 'ovinx-addon' ) . '</option>';

        foreach ( $selection_options as $group => $group_data ) {                
            $output .= '<optgroup label="' . $group_data['label'] . '">';
            foreach ( $group_data['value'] as $opt_key => $opt_value ) {

                $selected = '';
                
                if ( $data == $opt_key ) {
                    $selected = 'selected="selected"';
                }

                $output .= '<option value="' . $opt_key . '" ' . $selected . '>' . $opt_value . '</option>';
            }
            $output .= '</optgroup>';
        }
        $output .= '</select>';
        $output .= '</div>';

        $output .= '</div>';

        $output .= '<div class="target_rule-specific-page-wrap" style="display:none">';
        $output .= '<select name="' . esc_attr( $input_name ) . '[specific][]" class="target-rule-select2 target_rule-specific-page form-control tfhf-input " multiple="multiple">';

        if ( 'specifics' == $data && isset( $saved_values['specific'] ) && null != $saved_values['specific'] && is_array( $saved_values['specific'] ) ) {
            foreach ( $saved_values['specific'] as $data_key => $sel_value ) {

                if ( strpos( $sel_value, 'post-' ) !== false ) {
                    $post_id    = (int) str_replace( 'post-', '', $sel_value );
                    $post_title = get_the_title( $post_id );
                    $output    .= '<option value="post-' . $post_id . '" selected="selected" >' . $post_title . '</option>';
                }

                if ( strpos( $sel_value, 'tax-' ) !== false ) {
                    $tax_data = explode( '-', $sel_value );

                    $tax_id    = (int) str_replace( 'tax-', '', $sel_value );
                    $term      = get_term( $tax_id );
                    $term_name = '';

                    if ( ! is_wp_error( $term ) ) {
                        $term_taxonomy = ucfirst( str_replace( '_', ' ', $term->taxonomy ) );

                        if ( isset( $tax_data[2] ) && 'single' === $tax_data[2] ) {
                            $term_name = 'All singulars from ' . $term->name;
                        } else {
                            $term_name = $term->name . ' - ' . $term_taxonomy;
                        }
                    }

                    $output .= '<option value="' . $sel_value . '" selected="selected" >' . $term_name . '</option>';                        
                }
            }
        }
        $output .= '</select>';
        $output .= '</div>';
    }

    $output .= '</div>';

    $output .= '<div class="target_rule-add-rule-wrap">';
    $output .= '<a href="#" class="button" data-rule-id="' . absint( $index ) . '" data-rule-type="' . $type . '">' . $add_rule_label . '</a>';
    $output .= '</div>';

    if ( 'display' == $type ) {

        $output .= '<div class="target_rule-add-exclusion-rule">';
        $output .= '<a href="#" class="button">' . esc_html__( 'Add Exclusion Rule Group', 'ovinx-addon' ) . '</a>';
        $output .= '</div>';
    }

    return $output;
}

function get_format_rule_value( $save_data, $key ) {
    $meta_value = array();

    if ( isset( $save_data[ $key ]['rule'] ) ) {
        $save_data[ $key ]['rule'] = array_unique( $save_data[ $key ]['rule'] );
        if ( isset( $save_data[ $key ]['specific'] ) ) {
            $save_data[ $key ]['specific'] = array_unique( $save_data[ $key ]['specific'] );
        }

        $index = array_search( '', $save_data[ $key ]['rule'] );
        if ( false !== $index ) {
            unset( $save_data[ $key ]['rule'][ $index ] );
        }
        $index = array_search( 'specifics', $save_data[ $key ]['rule'] );
        if ( false !== $index ) {
            unset( $save_data[ $key ]['rule'][ $index ] );

            if ( isset( $save_data[ $key ]['specific'] ) && is_array( $save_data[ $key ]['specific'] ) ) {
                array_push( $save_data[ $key ]['rule'], 'specifics' );
            }
        }

        foreach ( $save_data[ $key ] as $meta_key => $value ) {
            if ( ! empty( $value ) ) {
                $meta_value[ $meta_key ] = array_map( 'esc_attr', $value );
            }
        }
        if ( ! isset( $meta_value['rule'] ) || ! in_array( 'specifics', $meta_value['rule'] ) ) {
            $meta_value['specific'] = array();
        }

        if ( empty( $meta_value['rule'] ) ) {
            $meta_value = array();
        }
    }

    return $meta_value;
}

function get_current_page_type() {
    static $current_page_type;
    if ( null === $current_page_type ) {
        $page_type  = '';
        $current_id = false;

        if ( is_404() ) {
            $page_type = 'is_404';
        } elseif ( is_search() ) {
            $page_type = 'is_search';
        } elseif ( is_archive() ) {
            $page_type = 'is_archive';

            if ( is_category() || is_tag() || is_tax() ) {
                $page_type = 'is_tax';
            } elseif ( is_date() ) {
                $page_type = 'is_date';
            } elseif ( is_author() ) {
                $page_type = 'is_author';
            } elseif ( function_exists( 'is_shop' ) && is_shop() ) {
                $page_type = 'is_woo_shop_page';
            }
        } elseif ( is_home() ) {
            $page_type = 'is_home';
        } elseif ( is_front_page() ) {
            $page_type  = 'is_front_page';
            $current_id = get_the_id();
        } elseif ( is_singular() ) {
            $page_type  = 'is_singular';
            $current_id = get_the_id();
        } else {
            $current_id = get_the_id();
        }

        $current_page_data['ID'] = $current_id;
        $current_page_type       = $page_type;
    }

    return $current_page_type;
}

function get_meta_option_post( $post_type, $option ) {
    $page_meta = ( isset( $option['page_meta'] ) && '' != $option['page_meta'] ) ? $option['page_meta'] : false;

    if ( false !== $page_meta ) {
        $current_post_id = isset( $option['current_post_id'] ) ? $option['current_post_id'] : false;
        $meta_id         = get_post_meta( $current_post_id, $option['page_meta'], true );

        if ( false !== $meta_id && '' != $meta_id ) {
            $current_page_data[ $post_type ][ $meta_id ] = array(
                'id'       => $meta_id,
                'location' => '',
            );

            return $current_page_data[ $post_type ];
        }
    }

    return false;
}


function parse_layout_display_condition( $post_id, $rules ) {
    $display           = false;
    $current_post_type = get_post_type( $post_id );

    if ( isset( $rules['rule'] ) && is_array( $rules['rule'] ) && ! empty( $rules['rule'] ) ) {
        foreach ( $rules['rule'] as $key => $rule ) {
            if ( strrpos( $rule, 'all' ) !== false ) {
                $rule_case = 'all';
            } else {
                $rule_case = $rule;
            }

            switch ( $rule_case ) {
                case 'basic-global':
                    $display = true;
                    break;

                case 'basic-singulars':
                    if ( is_singular() ) {
                        $display = true;
                    }
                    break;

                case 'basic-archives':
                    if ( is_archive() ) {
                        $display = true;
                    }
                    break;

                case 'special-404':
                    if ( is_404() ) {
                        $display = true;
                    }
                    break;

                case 'special-search':
                    if ( is_search() ) {
                        $display = true;
                    }
                    break;

                case 'special-blog':
                    if ( is_home() ) {
                        $display = true;
                    }
                    break;

                case 'special-front':
                    if ( is_front_page() ) {
                        $display = true;
                    }
                    break;

                case 'special-date':
                    if ( is_date() ) {
                        $display = true;
                    }
                    break;

                case 'special-author':
                    if ( is_author() ) {
                        $display = true;
                    }
                    break;

                case 'special-woo-shop':
                    if ( function_exists( 'is_shop' ) && is_shop() ) {
                        $display = true;
                    }
                    break;

                case 'all':
                    $rule_data = explode( '|', $rule );

                    $post_type     = isset( $rule_data[0] ) ? $rule_data[0] : false;
                    $archieve_type = isset( $rule_data[2] ) ? $rule_data[2] : false;
                    $taxonomy      = isset( $rule_data[3] ) ? $rule_data[3] : false;
                    if ( false === $archieve_type ) {
                        $current_post_type = get_post_type( $post_id );

                        if ( false !== $post_id && $current_post_type == $post_type ) {
                            $display = true;
                        }
                    } else {
                        if ( is_archive() ) {
                            $current_post_type = get_post_type();
                            if ( $current_post_type == $post_type ) {
                                if ( 'archive' == $archieve_type ) {
                                    $display = true;
                                } elseif ( 'taxarchive' == $archieve_type ) {
                                    $obj              = get_queried_object();
                                    $current_taxonomy = '';
                                    if ( '' !== $obj && null !== $obj ) {
                                        $current_taxonomy = $obj->taxonomy;
                                    }

                                    if ( $current_taxonomy == $taxonomy ) {
                                        $display = true;
                                    }
                                }
                            }
                        }
                    }
                    break;

                case 'specifics':
                    if ( isset( $rules['specific'] ) && is_array( $rules['specific'] ) ) {
                        foreach ( $rules['specific'] as $specific_page ) {
                            $specific_data = explode( '-', $specific_page );

                            $specific_post_type = isset( $specific_data[0] ) ? $specific_data[0] : false;
                            $specific_post_id   = isset( $specific_data[1] ) ? $specific_data[1] : false;
                            if ( 'post' == $specific_post_type ) {
                                if ( $specific_post_id == $post_id ) {
                                    $display = true;
                                }
                            } elseif ( isset( $specific_data[2] ) && ( 'single' == $specific_data[2] ) && 'tax' == $specific_post_type ) {
                                if ( is_singular() ) {
                                    $term_details = get_term( $specific_post_id );

                                    if ( isset( $term_details->taxonomy ) ) {
                                        $has_term = has_term( (int) $specific_post_id, $term_details->taxonomy, $post_id );

                                        if ( $has_term ) {
                                            $display = true;
                                        }
                                    }
                                }
                            } elseif ( 'tax' == $specific_post_type ) {
                                $tax_id = get_queried_object_id();
                                if ( $specific_post_id == $tax_id ) {
                                    $display = true;
                                }
                            }
                        }
                    }
                    break;

                default:
                    break;
            }

            if ( $display ) {
                break;
            }
        }
    }

    return $display;
}

function get_posts_by_conditions( $post_type, $option ) {
    static $current_page_data;
    global $wpdb;
    global $post;

    $post_type = $post_type ? esc_sql( $post_type ) : esc_sql( $post->post_type );

    if ( is_array( $current_page_data ) && isset( $current_page_data[ $post_type ] ) ) {
        return apply_filters( 'tfhf_get_display_posts_by_conditions', $current_page_data[ $post_type ], $post_type );
    }

    if ( ! isset( $current_page_data[ $post_type ] ) ) {
        $current_page_data[ $post_type ] = array();
    }

    $current_page_type = get_current_page_type();

    $option['current_post_id'] = isset( $current_page_data['ID'] ) ? $current_page_data['ID'] : false;
    $meta_header               = get_meta_option_post( $post_type, $option );

    if ( false === $meta_header ) {
        $current_post_type = esc_sql( get_post_type() );
        $current_post_id   = false;
        $q_obj             = get_queried_object();

        $location = isset( $option['location'] ) ? esc_sql( $option['location'] ) : '';

        $query = "SELECT p.ID, pm.meta_value FROM {$wpdb->postmeta} as pm
                    INNER JOIN {$wpdb->posts} as p ON pm.post_id = p.ID
                    WHERE pm.meta_key = '{$location}'
                    AND p.post_type = '{$post_type}'
                    AND p.post_status = 'publish'";

        $orderby = ' ORDER BY p.post_date DESC';

        $meta_args = "pm.meta_value LIKE '%\"basic-global\"%'";

        switch ( $current_page_type ) {
            case 'is_404':
                $meta_args .= " OR pm.meta_value LIKE '%\"special-404\"%'";
                break;
            case 'is_search':
                $meta_args .= " OR pm.meta_value LIKE '%\"special-search\"%'";
                break;
            case 'is_archive':
            case 'is_tax':
            case 'is_date':
            case 'is_author':
                $meta_args .= " OR pm.meta_value LIKE '%\"basic-archives\"%'";
                $meta_args .= " OR pm.meta_value LIKE '%\"{$current_post_type}|all|archive\"%'";

                if ( 'is_tax' == $current_page_type && ( is_category() || is_tag() || is_tax() ) ) {
                    if ( is_object( $q_obj ) ) {
                        $meta_args .= " OR pm.meta_value LIKE '%\"{$current_post_type}|all|taxarchive|{$q_obj->taxonomy}\"%'";
                        $meta_args .= " OR pm.meta_value LIKE '%\"tax-{$q_obj->term_id}\"%'";
                    }
                } elseif ( 'is_date' == $current_page_type ) {
                    $meta_args .= " OR pm.meta_value LIKE '%\"special-date\"%'";
                } elseif ( 'is_author' == $current_page_type ) {
                    $meta_args .= " OR pm.meta_value LIKE '%\"special-author\"%'";
                }
                break;
            case 'is_home':
                $meta_args .= " OR pm.meta_value LIKE '%\"special-blog\"%'";
                break;
            case 'is_front_page':
                $current_id      = esc_sql( get_the_id() );
                $current_post_id = $current_id;
                $meta_args      .= " OR pm.meta_value LIKE '%\"special-front\"%'";
                $meta_args      .= " OR pm.meta_value LIKE '%\"{$current_post_type}|all\"%'";
                $meta_args      .= " OR pm.meta_value LIKE '%\"post-{$current_id}\"%'";
                break;
            case 'is_singular':
                $current_id      = esc_sql( get_the_id() );
                $current_post_id = $current_id;
                $meta_args      .= " OR pm.meta_value LIKE '%\"basic-singulars\"%'";
                $meta_args      .= " OR pm.meta_value LIKE '%\"{$current_post_type}|all\"%'";
                $meta_args      .= " OR pm.meta_value LIKE '%\"post-{$current_id}\"%'";

                $taxonomies = get_object_taxonomies( $q_obj->post_type );
                $terms      = wp_get_post_terms( $q_obj->ID, $taxonomies );

                foreach ( $terms as $key => $term ) {
                    $meta_args .= " OR pm.meta_value LIKE '%\"tax-{$term->term_id}-single-{$term->taxonomy}\"%'";
                }

                break;
            case 'is_woo_shop_page':
                $meta_args .= " OR pm.meta_value LIKE '%\"special-woo-shop\"%'";
                break;
            case '':
                $current_post_id = get_the_id();
                break;
        }

        $posts  = $wpdb->get_results( $query . ' AND (' . $meta_args . ')' . $orderby );            

        foreach ( $posts as $local_post ) {
            if ( isset( $local_post->ID ) && isset( $local_post->meta_value ) ) {
                $current_page_data[ $post_type ][ $local_post->ID ] = array(
                    'id'       => $local_post->ID,
                    'location' => maybe_unserialize( $local_post->meta_value ),
                );
            }
        }

        $option['current_post_id'] = $current_post_id;

        remove_exclusion_rule_posts( $post_type, $option );
    }

    return apply_filters( 'tfhf_get_display_posts_by_conditions', $current_page_data[ $post_type ], $post_type );
}

function remove_exclusion_rule_posts( $post_type, $option ) {
    static $current_page_data;
    $exclusion       = isset( $option['exclusion'] ) ? $option['exclusion'] : '';
    $current_post_id = isset( $option['current_post_id'] ) ? $option['current_post_id'] : false;

    if ( ! isset( $current_page_data[ $post_type ] ) || ! is_array( $current_page_data[ $post_type ] ) ) {
        return;
    }

    foreach ( $current_page_data[ $post_type ] as $c_post_id => $c_data ) {
        $exclusion_rules = get_post_meta( $c_post_id, $exclusion, true );
        $is_exclude      = parse_layout_display_condition( $current_post_id, $exclusion_rules );

        if ( $is_exclude ) {
            unset( $current_page_data[ $post_type ][ $c_post_id ] );
        }
    }
}


if(!function_exists('tf_header_enabled')){
    function tf_header_enabled() {
        $header_id = get_settings2( 'type_header', '' );
        $status    = false;

        if ( '' !== $header_id ) {
            $status = true;
        }

        return apply_filters( 'tf_header_enabled', $status );
    }
}

if(!function_exists('tf_footer_enabled')){
    function tf_footer_enabled() {
        $header_id = get_settings2( 'type_footer', '' );
        $status    = false;

        if ( '' !== $header_id ) {
            $status = true;
        }

        return apply_filters( 'tf_footer_enabled', $status );
    }
}

function get_settings2( $setting = '', $default = '' ) {
    if ( 'type_header' == $setting || 'type_footer' == $setting ) {
        $templates = get_template_id( $setting );
        $template = ! is_array( $templates ) ? $templates : $templates[0];
        return $template;
    }
}
