<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if ( ! is_user_logged_in() ) : ?>
	<li class="nav-account">
		<a href="#login" data-bs-toggle="offcanvas" class="nav-icon-item">
			<i class="icon icon-user"></i>
		</a>
	</li>
<?php else :
	global $current_user;
	wp_get_current_user();
	$user_info = wp_get_current_user();
	$user_login        = $current_user->user_login;
	$user_id           = $current_user->ID;
	$user_avatar       = get_the_author_meta( 'profile_image', $user_id );
	$no_avatar         = get_avatar_url( $user_id );
	$default_image_src = $no_avatar;
	
	$my_account_url = wc_get_page_permalink( 'myaccount' );

	?>
	<li class="nav-account">
		<a href="<?php echo esc_url($my_account_url) ?>" data-bs-toggle="offcanvas" class="nav-icon-item">
			<i class="icon icon-user"></i>
		</a>
	</li>	
<?php endif; ?>