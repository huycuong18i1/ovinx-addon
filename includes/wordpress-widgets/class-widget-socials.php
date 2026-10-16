<?php

class themesflat_socials extends WP_Widget {

    /**

     * Holds widget settings defaults, populated in constructor.

     *

     * @var array

     */

    protected $defaults;



    /**

     * Constructor

     *

     * @return themesflat_socials

     */

    function __construct() {

        $this->defaults = array(

            'title'         => '',

            'value'         => 'facebook',

        );

        parent::__construct(

            'widget_themesflat_socials',

            esc_html__( 'Themesflat - Socials', 'finwice' ),

            array(

                'classname'   => 'footer-info' ,

                'description' => esc_html__( 'Themesflat Socials.', 'finwice' )

            )

        );

    }



    /**

     * Display widget

     */

    function widget( $args, $instance ) {

        $instance = wp_parse_args( $instance, $this->defaults );

        extract( $instance );

        extract( $args );

        echo wp_kses_post( $before_widget );

        if ( !empty($title) ) echo wp_kses_post($before_title).esc_html($title).wp_kses_post($after_title);?>

        <?php $this->themesflat_render_social_widget('',$instance['value'],true);?>

        <?php echo wp_kses_post( $after_widget );

    }



    /**

     * Update widget

     */

    function update( $new_instance, $old_instance ) {

        $instance                   = $old_instance;

        $instance['title']          = strip_tags( $new_instance['title'] );

        $instance['value']          = ( $new_instance['value'] );

        

        return $instance;

    }



    /**

     * Widget setting

     */

    function form( $instance ) {

        wp_enqueue_script('themesflat_customizer_js');

        $instance = wp_parse_args( $instance, $this->defaults );

        $icons = $this->themesflat_available_social_icons_widget();

        $value = $instance['value'];

        $order = $icons['__ordering__'];

        if ( ! is_array( $value ) ) {

            $decoded_value = json_decode(str_replace('&quot;', '"', $value), true );

            $value = is_array( $decoded_value ) ? $decoded_value : array();

        }

        if ( isset( $value['__ordering__'] ) && is_array( $value['__ordering__'] ) )

            $order = $value['__ordering__'];

        ?>

        <p>

            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'finwice' ); ?></label>

            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>">

        </p>

        <div class="themesflat_widget_socials themesflat-options-control-social-icons">

            <ul class="themesflat_icons">

                <li class="item-properties">

                    <label>

                        <span class="input-title"></span>

                        <input type="text" class="input-field" />

                    </label>

                    <button type="button" class="button button-primary confirm"><i class="icon-finwice-Plus"></i></button>

                </li>

                <?php foreach ( $order as $id ):

                    $params = $icons[$id];                    

                    $link = isset( $value[$id] ) ? sprintf( 'data-link="%s"', esc_attr( $value[$id] ) ) : '';

                    ?>

                    <li class="item flat-<?php echo esc_attr( $id ) ?>" data-id="<?php echo esc_attr( $id ) ?>" <?php echo esc_attr($link) ?> data-title="<?php echo esc_attr( $params['title'] ) ?>">

                        <i class="<?php echo esc_attr( $params['iclass'] ) ?>"></i>

                    </li>

                <?php endforeach ?>

            </ul>

            <input type="hidden" id="typography-value"  name="<?php echo esc_attr($this->get_field_name('value'));?>"  value="<?php echo esc_attr(  $instance['value'] ) ?>" />

        </div>

    <?php

    }



    function themesflat_available_social_icons_widget() {

        $icons = apply_filters( 'themesflat_available_icons', array(
            'facebook'       => array( 'iclass' => 'icon-FacebookLogo', 'title' => 'Facebook','share_link'=> THEMESFLAT_PROTOCOL . '://www.facebook.com/sharer/sharer.php?u=' ),
            'twitter'        => array( 'iclass' => 'icon-XLogo', 'title' => 'Twitter','share_link' => THEMESFLAT_PROTOCOL . '://twitter.com/intent/tweet?url=' ),       
            'tiktok'          => array( 'iclass' => 'icon-TiktokLogo', 'title' => 'Tiktok','share_link' => ''),
            'instagram'      => array( 'iclass' => 'icon-InstagramLogo', 'title' => 'Instagram','share_link' => 'https://www.instagram.com/?url=' ),
            'youtube'        => array( 'iclass' => 'icon-YoutubeLogo', 'title' => 'Youtube','share_link' =>'' ),

           

            

        ) );



        $icons['__ordering__'] = array_keys( $icons );



        return $icons;

    }



    function themesflat_render_social_widget($prefix = '',$value='',$show_title=false) {

        if ($value == '') {

            $value = $this->themesflat_get_json_widget('social_links');

        }

        $class= array();

        $class[] = ($show_title == false ? 'themesflat-socials' : 'themesflat-shortcode-socials');



        if ( ! is_array( $value ) ) {

            $decoded_value = json_decode($value, true );

            $value = is_array( $decoded_value ) ? $decoded_value : array();

        }



        $icons = $this->themesflat_available_social_icons_widget();



        ?>

        <ul class="social-list <?php echo esc_attr(implode(" ", $class));?>">

            <?php

            foreach ( $value as $key => $val ) {

                if ($key != '__ordering__') {

                    $title = ($show_title == false ? '' : $icons[$key]['title']);

                    $icon = ($show_title == false ? '' : $icons[$key]['iclass']);

                    printf(

                        '<li class="%s">

                            <a href="%s" target="_blank" rel="noopener noreferrer" title="%s" class="link">

                                <i class="%s"></i>                            

                            </a>

                        </li>',

                        esc_attr( $key ),

                        esc_url( $val ),

                        esc_attr( $val ),

                        esc_attr( $icon ),

                        esc_html($title)

                    );

                }

        }

            ?>

        </ul><!-- /.social -->       

        <?php 

    }

}



add_action( 'widgets_init', 'themesflat_socials_widget' );



/**

 * Register widget

 *

 * @return void

 * @since 1.0

 */

function themesflat_socials_widget() {

    register_widget( 'themesflat_socials' );

}