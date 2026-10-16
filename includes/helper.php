<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


function tfwc_get_template_with_arguments( $template_name, $arguments = array(), $template_path = '', $default_path = '' ) {
	if ( ! empty( $arguments ) && is_array( $arguments ) ) {
		extract( $arguments );
	}

	if ( ! $template_path ) {
		$template_path = TF_PLUGIN_PATH;
	}

	if ( ! $default_path ) {
		$default_path = TF_PLUGIN_PATH . '/woocommerce/templates/';
	}

	$template = locate_template( $template_name . '.php', false );

	// Get default template
	if ( ! $template ) {
		$template = $default_path . $template_name;
	}

	if ( ! file_exists( $template ) ) {
		return;
	}

	include( $template );
}



function tfwc_get_taxonomies( $category = 'category' ) {
	$category_posts_name = [];
	$category_posts      = get_terms(
		array(
			'taxonomy'   => $category,
			'orderby'    => 'name',
			'order'      => 'ASC',
			'hide_empty' => false,
		)
	);
	if ( ! empty( $category_posts ) && $category_posts != '' ) {
		foreach ( $category_posts as $category_post ) {
			// if(! empty( $category_post->slug) && ! empty( $category_post->name) ){
				$category_posts_name[ $category_post->slug ] = $category_post->name;
			// }
		}

	}
	return $category_posts_name;
}

function tfwc_get_number_text( $number, $many_text, $singular_text ) {
	if ( $number != 1 ) {
		return $many_text;
	} else {
		return $singular_text;
	}
}

function tf_get_template_widget_elementor( $template_name, $args = array(), $return = false ) {
	$template_file  = $template_name . '.php';
	$default_folder = TF_PLUGIN_PATH . 'includes/elementor-widget/';
	$theme_folder   = TF_PLUGIN_PATH;
	$template       = locate_template( $theme_folder . '/' . $template_file );
	if ( ! $template ) {
		$template = $default_folder . $template_file;
	}
	if ( ! empty( $args ) && is_array( $args ) ) {
		extract( $args );
	}
	if ( $return ) {
		ob_start();
	}
	if ( file_exists( $template ) ) {
		include $template;
	}
	if ( $return ) {
		return ob_get_clean();
	}
	return null;
}

function tfwc_image_resize_id( $images_id, $width = NULL, $height = NULL, $crop = true, $retina = false ) {
	$output    = '';
	$image_src = wp_get_attachment_image_src( $images_id, 'full' );
	if ( is_array( $image_src ) ) {
		$resize = tfwc_image_resize_url( $image_src[0], $width, $height, $crop, $retina );
		if ( $resize != null && is_array( $resize ) ) {
			$output = $resize['url'];
		}
	}
	return $output;
}

function tfwc_image_resize_url( $url, $width = NULL, $height = NULL, $crop = true, $retina = false ) {

	global $wpdb;

	if ( empty( $url ) )
		return new WP_Error( 'no_image_url', esc_html__( 'No image URL has been entered.', 'tf-car-listing' ), $url );

	if ( class_exists( 'Jetpack' ) && method_exists( 'Jetpack', 'get_active_modules' ) && in_array( 'photon', Jetpack::get_active_modules() ) ) {
		$args_crop = array(
			'resize' => $width . ',' . $height,
			'crop'   => '0,0,' . $width . 'px,' . $height . 'px'
		);
		$url       = jetpack_photon_url( $url, $args_crop );
	}

	// Get default size from database
	$width  = ( $width ) ? $width : get_option( 'thumbnail_width' );
	$height = ( $height ) ? $height : get_option( 'thumbnail_height' );

	// Allow for different retina sizes
	$retina = $retina ? ( $retina === true ? 2 : $retina ) : 1;

	// Get the image file path
	$file_path        = parse_url( $url );
	$file_path        = sanitize_text_field( $_SERVER['DOCUMENT_ROOT'] ) . $file_path['path'];
	$wp_upload_folder = wp_upload_dir();
	$wp_upload_folder = $wp_upload_folder['basedir'];
	$file_path        = explode( '/uploads/', $file_path );
	if ( is_array( $file_path ) ) {
		if ( count( $file_path ) > 1 ) {
			$file_path = $wp_upload_folder . '/' . $file_path[1];
		} elseif ( count( $file_path ) > 0 ) {
			$file_path = $wp_upload_folder . '/' . $file_path[0];
		} else {
			$file_path = '';
		}
	}

	// Check for Multisite
	if ( is_multisite() ) {
		global $blog_id;
		$blog_details = get_blog_details( $blog_id );
		$file_path    = str_replace( $blog_details->path . 'files/', '/wp-content/blogs.dir/' . $blog_id . '/files/', $file_path );
	}

	// Destination width and height variables
	$dest_width  = $width * $retina;
	$dest_height = $height * $retina;

	// File name suffix (appended to original file name)
	$suffix = "{$dest_width}x{$dest_height}";

	// Some additional info about the image
	$info = pathinfo( $file_path );
	$dir  = ! empty( $info['dirname'] ) ? $info['dirname'] : '';
	$ext  = ! empty( $info['extension'] ) ? $info['extension'] : '';
	$name = wp_basename( $file_path, ".$ext" );

	if ( 'bmp' == $ext ) {
		return new WP_Error( 'bmp_mime_type', esc_html__( 'Image is BMP. Please use either JPG or PNG.', 'tf-car-listing' ), $url );
	}

	// Suffix applied to filename
	$suffix = "{$dest_width}x{$dest_height}";

	$file_name = "{$name}-{$suffix}.{$ext}";
	$file_name = sanitize_file_name( $file_name );

	// Get the destination file name
	$dest_file_name = "{$dir}/{$file_name}";

	if ( ! file_exists( $dest_file_name ) ) {
		$query          = $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE guid='%s'", $url );
		$get_attachment = $wpdb->get_results( $query );
		if ( ! $get_attachment )
			return array( 'url' => $url, 'width' => $width, 'height' => $height );

		// Load Wordpress Image Editor
		$editor = wp_get_image_editor( $file_path );
		if ( is_wp_error( $editor ) )
			return array( 'url' => $url, 'width' => $width, 'height' => $height );

		// Get the original image size
		$size        = $editor->get_size();
		$orig_width  = $size['width'];
		$orig_height = $size['height'];

		$src_x = $src_y = 0;
		$src_w = $orig_width;
		$src_h = $orig_height;

		if ( $crop ) {

			$cmp_x = $orig_width / $dest_width;
			$cmp_y = $orig_height / $dest_height;

			// Calculate x or y coordinate, and width or height of source
			if ( $cmp_x > $cmp_y ) {
				$src_w = round( $orig_width / $cmp_x * $cmp_y );
				$src_x = round( ( $orig_width - ( $orig_width / $cmp_x * $cmp_y ) ) / 2 );
			} else if ( $cmp_y > $cmp_x ) {
				$src_h = round( $orig_height / $cmp_y * $cmp_x );
				$src_y = round( ( $orig_height - ( $orig_height / $cmp_y * $cmp_x ) ) / 2 );
			}

		}

		// Time to crop the image!
		$editor->crop( $src_x, $src_y, $src_w, $src_h, $dest_width, $dest_height );

		// Now let's save the image
		$saved = $editor->save( $dest_file_name );

		if ( is_a( $saved, 'WP_Error' ) ) {
			$image_array = array(
				'url'    => str_replace( wp_basename( $url ), wp_basename( $dest_file_name ), $url ),
				'width'  => $dest_width,
				'height' => $dest_height,
				'type'   => $ext
			);
		} else {
			$resized_url    = str_replace( wp_basename( $url ), wp_basename( $saved['path'] ), $url );
			$resized_width  = $saved['width'];
			$resized_height = $saved['height'];
			$resized_type   = $saved['mime-type'];

			$metadata = wp_get_attachment_metadata( $get_attachment[0]->ID );
			if ( isset( $metadata['image_meta'] ) ) {
				$metadata['image_meta']['resized_images'][] = $resized_width . 'x' . $resized_height;
				wp_update_attachment_metadata( $get_attachment[0]->ID, $metadata );
			}

			// Create the image array
			$image_array = array(
				'url'    => $resized_url,
				'width'  => $resized_width,
				'height' => $resized_height,
				'type'   => $resized_type
			);
		}

	} else {
		$image_array = array(
			'url'    => str_replace( wp_basename( $url ), wp_basename( $dest_file_name ), $url ),
			'width'  => $dest_width,
			'height' => $dest_height,
			'type'   => $ext
		);
	}

	// Return image array
	return $image_array;

}

function tfwc_save_image_from_url( $image_url ) {
	include_once( ABSPATH . 'wp-admin/includes/image.php' );
	$image_type = end( explode( '/', getimagesize( $image_url )['mime'] ) );
	$uniq_name  = date( 'dmY' ) . '' . (int) microtime( true );
	$filename   = $uniq_name . '.' . $image_type;

	$upload_dir  = wp_upload_dir();
	$upload_file = $upload_dir['path'] . '/' . $filename;
	$contents    = file_get_contents( $image_url );
	$save_file   = fopen( $upload_file, 'w' );
	fwrite( $save_file, $contents );
	fclose( $save_file );

	$wp_filetype = wp_check_filetype( basename( $filename ), null );
	$attachment  = array(
		'post_mime_type' => $wp_filetype['type'],
		'post_title'     => $filename,
		'post_content'   => '',
		'post_status'    => 'inherit'
	);

	$attach_id      = wp_insert_attachment( $attachment, $upload_file );
	$image_new      = get_post( $attach_id );
	$full_size_path = get_attached_file( $image_new->ID );
	$attach_data    = wp_generate_attachment_metadata( $attach_id, $full_size_path );
	wp_update_attachment_metadata( $attach_id, $attach_data );

	return $attach_id;
}

function enable_svg_uploads($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}
add_filter('upload_mimes', 'enable_svg_uploads');

if(!function_exists('tf_get_template_widget')){
    function tf_get_template_widget($template_name, $args = null, $return = false){
        $template_file = $template_name . '.php';
        $default_folder = plugin_dir_path(__FILE__) . 'elementor-widget/templates/';
        $theme_folder = apply_filters('tf_templates_folder', dirname(plugin_basename(__FILE__)));
        $template = locate_template($theme_folder . '/' . $template_file);
        if (!$template) {
            $template = $default_folder . $template_file;
        }
        if ($args && is_array($args)) {
            extract($args);
        }
        if ($return) {
            ob_start();
        }
        if (file_exists($template)) {
            include $template;
        }
        if ($return) {
            return ob_get_clean();
        }
        return null;
    }
}

// custom metabox author
add_action( 'show_user_profile', 'custom_author_extra_fields' );
add_action( 'edit_user_profile', 'custom_author_extra_fields' );

function custom_author_extra_fields( $user ) {
    ?>
    <h3><?php _e('Author Extra Information', 'ovinx'); ?></h3>
    <table class="form-table">
        <tr>
            <th><label for="author_duty"><?php _e('Duty / Position', 'ovinx'); ?></label></th>
            <td>
                <input type="text" name="author_duty" id="author_duty" 
                    value="<?php echo esc_attr( get_user_meta( $user->ID, 'author_duty', true ) ); ?>" 
                    class="regular-text" />
                <p class="description"><?php _e('Enter the authors title.', 'ovinx'); ?></p>
            </td>
        </tr>

        <tr>
            <th><label for="author_facebook"><?php _e('Facebook URL', 'ovinx'); ?></label></th>
            <td>
                <input type="url" name="author_facebook" id="author_facebook" 
                    value="<?php echo esc_url( get_user_meta( $user->ID, 'author_facebook', true ) ); ?>" 
                    class="regular-text" />
            </td>
        </tr>

        <tr>
            <th><label for="author_instagram"><?php _e('Instagram URL', 'ovinx'); ?></label></th>
            <td>
                <input type="url" name="author_instagram" id="author_instagram" 
                    value="<?php echo esc_url( get_user_meta( $user->ID, 'author_instagram', true ) ); ?>" 
                    class="regular-text" />
            </td>
        </tr>

        <tr>
            <th><label for="author_x"><?php _e('X (Twitter) URL', 'ovinx'); ?></label></th>
            <td>
                <input type="url" name="author_x" id="author_x" 
                    value="<?php echo esc_url( get_user_meta( $user->ID, 'author_x', true ) ); ?>" 
                    class="regular-text" />
            </td>
        </tr>
    </table>
    <?php
}

add_action( 'personal_options_update', 'save_custom_author_extra_fields' );
add_action( 'edit_user_profile_update', 'save_custom_author_extra_fields' );

function save_custom_author_extra_fields( $user_id ) {
    if ( !current_user_can( 'edit_user', $user_id ) ) {
        return false;
    }

    update_user_meta( $user_id, 'author_duty', sanitize_text_field( $_POST['author_duty'] ) );
    update_user_meta( $user_id, 'author_facebook', esc_url_raw( $_POST['author_facebook'] ) );
    update_user_meta( $user_id, 'author_instagram', esc_url_raw( $_POST['author_instagram'] ) );
    update_user_meta( $user_id, 'author_x', esc_url_raw( $_POST['author_x'] ) );
}

function allow_svg($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}
add_filter('upload_mimes', 'allow_svg');

function disable_conflict_swiper() {
    wp_dequeue_script('swiper'); 
    wp_deregister_script('swiper');
}
add_action('wp_enqueue_scripts', 'disable_conflict_swiper', 99);