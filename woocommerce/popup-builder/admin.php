<?php
class TFWC_Popup_Builder {

private  $current_page_type = null;
private  $current_page_data = array();
private  $location_selection;

public function __construct() {              
    add_action( 'admin_enqueue_scripts', [ $this, 'admin_scripts' ] , 100 );  
    add_action( 'admin_action_edit', array( $this, 'initialize_options' ) );
    add_action( 'wp_ajax_tfwc_get_posts_by_query', array( $this, 'tfwc_get_posts_by_query' ) ); 
    add_action( 'add_meta_boxes', [ $this, 'popup_builder_register_metabox' ] );
    add_action( 'save_post', [ $this, 'popup_builder_save_meta' ] );
    // add_filter( 'single_template', [ $this, 'popup_builder_load_canvas_template' ] );   

    add_action( 'wp_footer', [ $this, 'display_popup_builder' ]);
}


public function admin_scripts() {
    wp_register_style( 'tf-select2', THEMESFLAT_LINK . 'css/admin/popup/select2.css' );
    wp_enqueue_style( 'tf-select2' );
    wp_register_style( 'tf-admin', THEMESFLAT_LINK . 'css/admin/popup/admin.css' );
    wp_enqueue_style( 'tf-admin' );

    wp_register_script( 'tf-select2', THEMESFLAT_LINK . 'js/admin/popup/select2.js', [ 'jquery' ], false, true );    
    wp_enqueue_script( 'tf-select2' );
    wp_register_script( 'tf-admin', THEMESFLAT_LINK . 'js/admin/popup/admin.js', [ 'jquery' ], false, true );
    wp_enqueue_script( 'tf-admin' );
    wp_register_script( 'tf-admin-rule', THEMESFLAT_LINK . 'js/admin/popup/admin-rule.js', [ 'jquery' ], false, true );
    wp_enqueue_script( 'tf-admin-rule' );
    $tfwc_localize_vars = array(
        'ajaxurl' =>    admin_url('admin-ajax.php'),
        'search'        => esc_html__( 'Search pages / post / categories', 'ovinx' ),
        'ajax_nonce'    => wp_create_nonce( 'tfwc-get-posts-by-query' ),
    );
    wp_localize_script( 'tf-admin-rule', 'tfwc_localize_vars', $tfwc_localize_vars );        
}

function tf_get_template_elementor($type = null) {
    $args = [
        'post_type' => 'elementor_library',
        'posts_per_page' => -1,
    ];
    if ($type) {
        $args['tax_query'] = [
            [
                'taxonomy' => 'elementor_library_type',
                'field' => 'slug',
                'terms' => $type,
            ],
        ];
    }
    $template = get_posts($args);
    $tpl = array();
    if (!empty($template) && !is_wp_error($template)) {
        foreach ($template as $post) {
            $tpl[$post->post_name] = $post->post_title;
        }
    }
    return $tpl;
}  

/*========================================= 
Post type center right
========================================= */

    public function popup_builder_register_metabox() {
        add_meta_box(
            'tfwc-meta-box',
            esc_html__( 'TF Popup Builder Options', 'ovinx' ), 
            [ $this, 'popup_builder_metabox_render'], 
            'popup_builder', 'normal', 'high' );
    }   

    public function popup_builder_metabox_render( $post ) {
        $values            = get_post_custom( $post->ID );
        $template_type     = isset( $values['tfwc_template_type'] ) ? esc_attr( $values['tfwc_template_type'][0] ) : '';
        $popup_width       = isset( $values['tfwc_popup_width'] ) ? esc_attr( $values['tfwc_popup_width'][0] ) : '';
    
        wp_nonce_field( 'tfwc_meta_nounce', 'tfwc_meta_nounce' );
        ?>
        <table class="tfwc-options-table widefat">
            <tbody>
                <tr class="tfwc-options-row type-of-template">
                    <td class="tfwc-options-row-heading">
                        <label for="tfwc_template_type"><?php esc_html_e( 'Position', 'ovinx' ); ?></label>
                    </td>
                    <td class="tfwc-options-row-content">
                        <select name="tfwc_template_type" id="tfwc_template_type">
                            <option value="" <?php selected( $template_type, '' ); ?>><?php esc_html_e( 'Select Option', 'ovinx' ); ?></option>
                            <option value="type_center" <?php selected( $template_type, 'type_center' ); ?>><?php esc_html_e( 'Center', 'ovinx' ); ?></option>
                            <option value="type_right" <?php selected( $template_type, 'type_right' ); ?>><?php esc_html_e( 'Right', 'ovinx' ); ?></option>
                        </select>
                    </td>
                </tr>    
                
                <tr class="tfwc-options-row width-of-template">
                    <td class="tfwc-options-row-heading">
                        <label for="tfwc_popup_width"><?php esc_html_e( 'Width', 'ovinx' ); ?></label>
                    </td>
                    <td class="tfwc-options-row-content">
                        <input type="number" name="tfwc_popup_width" id="tfwc_popup_width" value="<?php echo esc_attr( $popup_width ); ?>" placeholder="Enter width .eg 1000" />
                    </td>
                </tr>                
    
                <?php $this->popup_builder_metabox_rule(); ?>

                <tr class="tfwc-options-row timeout-of-template">
                    <td class="tfwc-options-row-heading">
                        <label for="tfwc_popup_timeout"><?php esc_html_e( 'Timeout (ms)', 'ovinx' ); ?></label>
                    </td>
                    <td class="tfwc-options-row-content">
                        <input type="number" name="tfwc_popup_timeout" id="tfwc_popup_timeout" value="<?php echo isset( $values['tfwc_popup_timeout'] ) ? esc_attr( $values['tfwc_popup_timeout'][0] ) : ''; ?>" placeholder="e.g. 5000 for 5 seconds" />
                    </td>
                </tr>
            </tbody>
        </table>
        <?php
    }
    

    public function popup_builder_metabox_rule() {  
        $include_locations = get_post_meta( get_the_id(), 'tfwc_template_include_locations', true );
        $exclude_locations = get_post_meta( get_the_id(), 'tfwc_template_exclude_locations', true );
        ?>
        <tr class="tfwc-target-rules-row tfwc-options-row">
            <td class="tfwc-target-rules-row-heading tfwc-options-row-heading">
                <label><?php esc_html_e( 'Display On', 'ovinx' ); ?></label>
            </td>
            <td class="tfwc-target-rules-row-content tfwc-options-row-content">
                <?php
                $this->target_rule_settings_field(
                    'tfwc-target-rules-location',
                    [
                        'title'          => esc_html__( 'Display Rules', 'ovinx' ),
                        'value'          => '[{"type":"basic-global","specific":null}]',
                        'tags'           => 'site,enable,target,pages',
                        'rule_type'      => 'display',
                        'add_rule_label' => esc_html__( 'Add Display Rule Group', 'ovinx' ),
                    ],
                    $include_locations
                );
                ?>
            </td>
        </tr>
        <tr class="tfwc-target-rules-row tfwc-options-row">
            <td class="tfwc-target-rules-row-heading tfwc-options-row-heading">
                <label><?php esc_html_e( 'Do Not Display On', 'ovinx' ); ?></label>
            </td>
            <td class="tfwc-target-rules-row-content tfwc-options-row-content">
                <?php
                $this->target_rule_settings_field(
                    'tfwc-target-rules-exclusion',
                    [
                        'title'          => esc_html__( 'Exclude On', 'ovinx' ),
                        'value'          => '[]',
                        'tags'           => 'site,enable,target,pages',
                        'add_rule_label' => esc_html__( 'Add Exclusion Rule Group', 'ovinx' ),
                        'rule_type'      => 'exclude',
                    ],
                    $exclude_locations
                );
                ?>
            </td>
        </tr> 
        <?php
    } 

    public function popup_builder_save_meta( $post_id ) {

        if ( isset( $_POST['tfwc_template_type'] ) ) {
            update_post_meta( $post_id, 'tfwc_template_type', esc_attr( $_POST['tfwc_template_type'] ) );
        }

        if ( isset( $_POST['tfwc_popup_width'] ) ) {
            update_post_meta( $post_id, 'tfwc_popup_width', esc_attr( $_POST['tfwc_popup_width'] ) );
        }

        if ( isset( $_POST['tfwc_popup_timeout'] ) ) {
            update_post_meta( $post_id, 'tfwc_popup_timeout', esc_attr( $_POST['tfwc_popup_timeout'] ) );
        }

        if ( ! isset( $_POST['tfwc_meta_nounce'] ) || ! wp_verify_nonce( $_POST['tfwc_meta_nounce'], 'tfwc_meta_nounce' ) ) {
            return;
        }
        
        if ( ! current_user_can( 'edit_posts' ) ) {
            return;
        }
        $target_locations = $this->get_format_rule_value( $_POST, 'tfwc-target-rules-location' );
        update_post_meta( $post_id, 'tfwc_template_include_locations', $target_locations );
        $target_exclusion = $this->get_format_rule_value( $_POST, 'tfwc-target-rules-exclusion' );        
        update_post_meta( $post_id, 'tfwc_template_exclude_locations', $target_exclusion );

        return false;
    }

    public function popup_builder_load_canvas_template( $single_template ) {
        global $post;

        if ( 'popup_builder' == $post->post_type ) {
            $elementor_canvas = ELEMENTOR_PATH . '/modules/page-templates/templates/canvas.php';

            if ( file_exists( $elementor_canvas ) ) {
                return $elementor_canvas;
            } else {
                return ELEMENTOR_PATH . '/includes/page-templates/canvas.php';
            }
        }

        return $single_template;
    }    

    public function get_template_id( $type ) {
        $option = [
            'location'  => 'tfwc_template_include_locations',
            'exclusion' => 'tfwc_template_exclude_locations',
        ];
    
        $tfwc_templates = $this->get_posts_by_conditions( 'popup_builder', $option );
        $matched_templates = [];
    
        foreach ( $tfwc_templates as $template ) {
            if ( get_post_meta( absint( $template['id'] ), 'tfwc_template_type', true ) === $type ) {
                $matched_templates[] = $template['id'];
            }
        }
    
        return $matched_templates; 
    }

    public function get_settings( $setting = '', $default = '' ) {
        if ( 'type_center' == $setting || 'type_right' == $setting ) {
            $templates = $this->get_template_id( $setting );
            $template = ! is_array( $templates ) ? $templates : $templates[0];
            return $template;
        }
    }
   
    public function display_popup_builder() {
        $types = [ 'type_center', 'type_right' ]; 
        foreach ( $types as $type ) {
            $this->tf_render_popup_builder( $type ); 
        }
    }


    public function tf_render_popup_builder( $type = 'type_center' ) {
        $template_ids = $this->get_template_id( $type );
    
        if ( empty( $template_ids ) || ! is_array( $template_ids ) ) {
            return;
        }
    
        $modal_classes = [
            'type_center' => 'modalCentered',
            'type_right'  => 'fullRight modal-before-you-leave',
        ];
    
        $modal_class = isset( $modal_classes[ $type ] ) ? $modal_classes[ $type ] : 'modalCentered';
    
        foreach ( $template_ids as $template_id ) {
            $container = \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $template_id );

            $popup_width = get_post_meta( $template_id, 'tfwc_popup_width', true );
            $modal_width_style = ! empty( $popup_width ) ? 'max-width: ' . esc_attr( $popup_width ) . 'px;' : 'max-width: 674px;';

            $popup_timeout = get_post_meta( $template_id, 'tfwc_popup_timeout', true );

            $popup_timeout = ! empty( $popup_timeout ) ?  esc_attr( $popup_timeout )  : 3000 ;


            if ( ! empty( $container ) ) {
                ?>
                <div class="modal <?php echo esc_attr( $modal_class ); ?> fade auto-popup" data-timeout="<?php echo esc_attr( $popup_timeout); ?>">
                    <div class="modal-dialog modal-dialog-centered" style="<?php echo $modal_width_style; ?>">
                        <div class="modal-content">
                            <span class="icon icon-close btn-hide-popup" data-bs-dismiss="modal"></span>
                            <?php echo $container; ?>
                        </div>
                    </div>
                </div>
                <?php
            }
        }
    }
        
    public function initialize_options() {
        $this->location_selection = $this->get_location_selections();
    }

    public  function get_location_selections2() {
        $args = array(
            'public'   => true,
            '_builtin' => true,
        );

        $post_types = get_post_types( $args, 'objects' );
    

        $args['_builtin'] = false;
        $custom_post_type = get_post_types( $args, 'objects' );

        $post_types = apply_filters( 'tfwc_location_rule_post_types', array_merge( $post_types, $custom_post_type ) );

        add_filter( 'tfwc_location_rule_post_types', function( $post_types ) {
            $exclude_post_types = [ 'attachment', 'post', 'tf_size_guide', 'tf_volume_discount', 'popup_builder', 'megamenu' ];
            foreach ( $exclude_post_types as $pt ) {
                unset( $post_types[ $pt ] );
            }
            return $post_types;
        } );

        $special_pages = array(
            'special-404'    => esc_html__( '404 Page', 'ovinx' ),
            'special-search' => esc_html__( 'Search Page', 'ovinx' ),
            'special-blog'   => esc_html__( 'Blog / Posts Page', 'ovinx' ),
            'special-front'  => esc_html__( 'Front Page', 'ovinx' ),
            'special-date'   => esc_html__( 'Date Archive', 'ovinx' ),
            'special-author' => esc_html__( 'Author Archive', 'ovinx' ),
        );

        $selection_options = array(
            'basic'         => array(
                'label' => esc_html__( 'Basic', 'ovinx' ),
                'value' => array(
                    'basic-global'    => esc_html__( 'Entire Website', 'ovinx' ),
                    'basic-singulars' => esc_html__( 'All Singulars', 'ovinx' ),
                    'basic-archives'  => esc_html__( 'All Archives', 'ovinx' ),
                ),
            ),

            'special-pages' => array(
                'label' => esc_html__( 'Special Pages', 'ovinx' ),
                'value' => $special_pages,
            ),
        );

        $args = array(
            'public' => true,
        );

        $taxonomies = get_taxonomies( $args, 'objects' );

        if ( ! empty( $taxonomies ) ) {
            foreach ( $taxonomies as $taxonomy ) {

                if ( 'post_format' == $taxonomy->name ) {
                    continue;
                }

                foreach ( $post_types as $post_type ) {
                    $post_opt = $this->get_post_target_rule_options( $post_type, $taxonomy );

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
            'label' => esc_html__( 'Specific Target', 'ovinx' ),
            'value' => array(
                'specifics' => esc_html__( 'Specific Pages / Posts / Taxonomies, etc.', 'ovinx' ),
            ),
        );

        return apply_filters( 'tfwc_display_on_list', $selection_options );
    }

    public function get_location_selections() {
        $args = array(
            ''   => true,
            '_builtin' => true,
        );
    
        $post_types = get_post_types( $args, 'objects' );
        unset( $post_types['attachment'] );
    
        $args['_builtin'] = false;
        $custom_post_type = get_post_types( $args, 'objects' );
    
        $post_types = apply_filters( 'tfwc_location_rule_post_types', array_merge( $post_types, $custom_post_type ) );
    
        $special_pages = array(
            'special-404'    => esc_html__( '404 Page', 'ovinx' ),
            'special-search' => esc_html__( 'Search Page', 'ovinx' ),
            'special-blog'   => esc_html__( 'Blog / Posts Page', 'ovinx' ),
            'special-front'  => esc_html__( 'Front Page', 'ovinx' ),
            'special-date'   => esc_html__( 'Date Archive', 'ovinx' ),
            'special-author' => esc_html__( 'Author Archive', 'ovinx' ),
        );
    
        if ( class_exists( 'WooCommerce' ) ) {
            $special_pages['special-woo-shop'] = esc_html__( 'WooCommerce Shop Page', 'ovinx' );
        }
    
        $selection_options = array(
            'basic'         => array(
                'label' => esc_html__( 'Basic', 'ovinx' ),
                'value' => array(
                    'basic-global'    => esc_html__( 'Entire Website', 'ovinx' ),
                    'basic-singulars' => esc_html__( 'All Singulars', 'ovinx' ),
                    'basic-archives'  => esc_html__( 'All Archives', 'ovinx' ),
                ),
            ),
    
            'special-pages' => array(
                'label' => esc_html__( 'Special Pages', 'ovinx' ),
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
            'label' => esc_html__( 'Specific Target', 'ovinx' ),
            'value' => array(
                'specifics' => esc_html__( 'Specific Pages / Posts / Taxonomies, etc.', 'ovinx' ),
            ),
        );
    
        return apply_filters( 'tfwc_display_on_list', $selection_options );
    }

    public  function get_location_by_key( $key ) {
        if ( ! isset( $this->location_selection ) || empty( $this->location_selection ) ) {
            $this->location_selection = $this->get_location_selections();
        }
        $location_selection = $this->location_selection;

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

    public  function target_rule_settings_field( $name, $settings, $value ) {
        $input_name     = $name;
        $type           = isset( $settings['type'] ) ? $settings['type'] : 'target_rule';
        $class          = isset( $settings['class'] ) ? $settings['class'] : '';
        $rule_type      = isset( $settings['rule_type'] ) ? $settings['rule_type'] : 'target_rule';
        $add_rule_label = isset( $settings['add_rule_label'] ) ? $settings['add_rule_label'] : esc_html__( 'Add Rule', 'ovinx' );
        $saved_values   = $value;
        $output         = '';

        if ( isset( $this->location_selection ) || empty( $this->location_selection ) ) {
            $this->location_selection = $this->get_location_selections();
        }
        $selection_options = $this->location_selection;

        $output .= '<script type="text/html" id="tmpl-tfwc-target-rule-' . $rule_type . '-condition">';
        $output .= '<div class="tfwc-target-rule-condition tfwc-target-rule-{{data.id}}" data-rule="{{data.id}}" >';
        $output .= '<span class="target_rule-condition-delete dashicons dashicons-dismiss"></span>';

        $output .= '<div class="target_rule-condition-wrap" >';
        $output .= '<select name="' . esc_attr( $input_name ) . '[rule][{{data.id}}]" class="target_rule-condition form-control tfwc-input">';
        $output .= '<option value="">' . esc_html__( 'Select', 'ovinx' ) . '</option>';

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
        $output .= '<select name="' . esc_attr( $input_name ) . '[specific][]" class="target-rule-select2 target_rule-specific-page form-control tfwc-input " multiple="multiple">';
        $output .= '</select>';
        $output .= '</div>';

        $output .= '</script>';

        $output .= '<div class="tfwc-target-rule-wrapper tfwc-target-rule-' . $rule_type . '-on-wrap" data-type="' . $rule_type . '">';
        $output .= '<div class="tfwc-target-rule-selector-wrapper tfwc-target-rule-' . $rule_type . '-on">';
        $output .= $this->generate_target_rule_selector( $rule_type, $selection_options, $input_name, $saved_values, $add_rule_label );
        $output .= '</div>';

        $output .= '</div>';

        echo $output;
    }

    public function get_post_target_rule_options( $post_type, $taxonomy ) {
        $post_key    = str_replace( ' ', '-', strtolower( $post_type->label ) );
        $post_label  = ucwords( $post_type->label );
        $post_name   = $post_type->name;
        $post_option = array();
    
        $post_option[ $post_name . '|all' ] = sprintf( esc_html__( 'All %s', 'ovinx' ), $post_label );
    
        if ( 'pages' !== $post_key ) {
            $post_option[ $post_name . '|all|archive' ] = sprintf( esc_html__( 'All %s Archive', 'ovinx' ), $post_label );
        }
    
        if ( in_array( $post_type->name, $taxonomy->object_type ) ) {
            $tax_label = ucwords( $taxonomy->label );
            $tax_name  = $taxonomy->name;
            $post_option[ $post_name . '|all|taxarchive|' . $tax_name ] = sprintf( esc_html__( 'All %s Archive', 'ovinx' ), $tax_label );
        }
    
        $posts = get_posts( [
            'post_type'      => $post_name,
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'orderby'        => 'title',
            'order'          => 'ASC',
        ] );
    
        if ( ! empty( $posts ) ) {
            foreach ( $posts as $post ) {
                $post_option[ $post_name . '|id|' . $post->ID ] = $post->post_title;
            }
        }
    
        $post_output['post_key'] = $post_key;
        $post_output['label']    = $post_label;
        $post_output['value']    = $post_option;
    
        return $post_output;
    }

    public  function generate_target_rule_selector( $type, $selection_options, $input_name, $saved_values, $add_rule_label ) {
        $output = '<div class="target_rule-builder-wrap">';

        if ( ! is_array( $saved_values ) || ( is_array( $saved_values ) && empty( $saved_values ) ) ) {
            $saved_values                = array();
            $saved_values['rule'][0]     = '';
            $saved_values['specific'][0] = '';
        }

        $index = 0;
       
        foreach ( $saved_values['rule'] as $index => $data ) {            
            $output .= '<div class="tfwc-target-rule-condition tfwc-target-rule-' . $index . '" data-rule="' . $index . '" >';

            $output .= '<span class="target_rule-condition-delete dashicons dashicons-dismiss"></span>';
            $output .= '<div class="target_rule-condition-wrap" >';
            $output .= '<select name="' . esc_attr( $input_name ) . '[rule][' . $index . ']" class="target_rule-condition form-control tfwc-input">';
            $output .= '<option value="">' . esc_html__( 'Select', 'ovinx' ) . '</option>';

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
            $output .= '<select name="' . esc_attr( $input_name ) . '[specific][]" class="target-rule-select2 target_rule-specific-page form-control tfwc-input " multiple="multiple">';

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
            $output .= '<a href="#" class="button">' . esc_html__( 'Add Exclusion Rule Group', 'ovinx' ) . '</a>';
            $output .= '</div>';
        }

        return $output;
    }

    public  function get_format_rule_value( $save_data, $key ) {
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

    public function get_current_page_type() {
        if ( null === $this->current_page_type ) {
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

            $this->current_page_data['ID'] = $current_id;
            $this->current_page_type       = $page_type;
        }

        return $this->current_page_type;
    }

    public  function get_meta_option_post( $post_type, $option ) {
        $page_meta = ( isset( $option['page_meta'] ) && '' != $option['page_meta'] ) ? $option['page_meta'] : false;

        if ( false !== $page_meta ) {
            $current_post_id = isset( $option['current_post_id'] ) ? $option['current_post_id'] : false;
            $meta_id         = get_post_meta( $current_post_id, $option['page_meta'], true );

            if ( false !== $meta_id && '' != $meta_id ) {
                $this->current_page_data[ $post_type ][ $meta_id ] = array(
                    'id'       => $meta_id,
                    'location' => '',
                );

                return $this->current_page_data[ $post_type ];
            }
        }

        return false;
    }

    function tfwc_get_posts_by_query() {

        check_ajax_referer( 'tfwc-get-posts-by-query', 'nonce' );

        $search_string = isset( $_POST['q'] ) ? sanitize_text_field( $_POST['q'] ) : '';
        $data          = array();
        $result        = array();

        $args = array(
            'public'   => true,
            '_builtin' => false,
        );

        $output     = 'names';
        $operator   = 'and';
        $post_types = get_post_types( $args, $output, $operator );

        unset( $post_types['popup_builder'] );

        $post_types['Posts'] = 'post';
        $post_types['Pages'] = 'page';

        foreach ( $post_types as $key => $post_type ) {
            $data = array();

            add_filter( 'posts_search', array( $this, 'search_only_titles' ), 10, 2 );

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
            'public' => true,
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

    public function parse_layout_display_condition( $post_id, $rules ) {
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

    public function get_posts_by_conditions( $post_type, $option ) {
        global $wpdb;
        global $post;

        $post_type = $post_type ? esc_sql( $post_type ) : esc_sql( $post->post_type );

        if ( is_array( $this->current_page_data ) && isset( $this->current_page_data[ $post_type ] ) ) {
            return apply_filters( 'tfwc_get_display_posts_by_conditions', $this->current_page_data[ $post_type ], $post_type );
        }

        $current_page_type = self::get_current_page_type();

        $this->current_page_data[ $post_type ] = array();

        $option['current_post_id'] = $this->current_page_data['ID'];
        $meta_center               = self::get_meta_option_post( $post_type, $option );

        if ( false === $meta_center ) {
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
                case '':
                    $current_post_id = get_the_id();
                    break;
            }

            $posts  = $wpdb->get_results( $query . ' AND (' . $meta_args . ')' . $orderby );            

            foreach ( $posts as $local_post ) {
                $this->current_page_data[ $post_type ][ $local_post->ID ] = array(
                    'id'       => $local_post->ID,
                    'location' => unserialize( $local_post->meta_value ),
                );
            }

            $option['current_post_id'] = $current_post_id;

            self::remove_exclusion_rule_posts( $post_type, $option );
        }

        return apply_filters( 'tfwc_get_display_posts_by_conditions', $this->current_page_data[ $post_type ], $post_type );
    }

    public function remove_exclusion_rule_posts( $post_type, $option ) {
        $exclusion       = isset( $option['exclusion'] ) ? $option['exclusion'] : '';
        $current_post_id = isset( $option['current_post_id'] ) ? $option['current_post_id'] : false;

        foreach ( $this->current_page_data[ $post_type ] as $c_post_id => $c_data ) {
            $exclusion_rules = get_post_meta( $c_post_id, $exclusion, true );
            $is_exclude      = $this->parse_layout_display_condition( $current_post_id, $exclusion_rules );

            if ( $is_exclude ) {
                unset( $this->current_page_data[ $post_type ][ $c_post_id ] );
            }
        }
    }

    

}