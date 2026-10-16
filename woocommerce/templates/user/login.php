<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( is_user_logged_in() ) {
	echo esc_html_e( 'You are logged in!', 'ovinx-addon' );
	return;
}

global $google_client;

$login_google_url =  $google_client->tfwc_get_authorization_url();


if (function_exists('themesflat_get_opt')) {
	$client_id = themesflat_get_opt('facebook_login_client_id');
	$client_secret = themesflat_get_opt('facebook_login_client_secret');
	$enable_login_fb = themesflat_get_opt('enable_login_fb');
	$enable_login_gg = themesflat_get_opt('enable_login_gg');

} else {
	$client_id = '';
	$client_secret = '';
	$enable_login_fb = 1;
	$enable_login_gg = 1;
}

if ( ! defined( 'FACEBOOK_APP_ID' ) ) {
    define( 'FACEBOOK_APP_ID', $client_id );
}

if ( ! defined( 'FACEBOOK_APP_SECRET' ) ) {
    define( 'FACEBOOK_APP_SECRET', $client_secret );
}

if ( ! defined( 'REDIRECT_URI' ) ) {
    define( 'REDIRECT_URI', home_url() );
}

$permissions = ['email'];

$login_facebook_url = 'https://www.facebook.com/v15.0/dialog/oauth' . '?'
			. 'client_id=' . FACEBOOK_APP_ID
			. '&redirect_uri=' . urlencode(REDIRECT_URI)
			. '&scope=' . implode(',', $permissions)
			. '&state=' .
			'&response_type=code';

?>

<!-- Structure matches ovinx-html's 404.html #login modal (.modal-heading/.modal-main/.field-bottom/.group-action
     are the real, styled classes — see css/styles.css). FB/Google sign-in has no ground-truth markup (added
     feature beyond the static export); kept functional, styled with existing real utility classes only. -->
<div class="modal modalCentered fade modal-log" id="log">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <button type="button" class="btn-close-popup" data-bs-dismiss="modal" aria-label="<?php esc_attr_e( 'Close', 'ovinx-addon' ); ?>">
                <i class="icon icon-Close2"></i>
            </button>
            <div class="modal-heading text-center">
                <h4 class="title-pop"><?php esc_html_e( 'Sign In', 'ovinx-addon' ); ?></h4>
                <p class="desc-pop"><?php esc_html_e( 'Sign in to access your personalized experience.', 'ovinx-addon' ); ?></p>
            </div>
            <div class="modal-main">
                <form id="tfwc_custom_login_form" method="post" enctype="multipart/form-data" class="tfwc-custom-login-form form-log">
                    <div class="form-content">
                        <div class="tfwc-message"></div>
                        <fieldset class="tf-field">
                            <label class="tf-lable"><?php esc_html_e( 'Username or email address', 'ovinx-addon' ); ?> <span class="text-primary">*</span></label>
                            <input type="text" name="username" placeholder="<?php esc_attr_e( 'Username or email address*', 'ovinx-addon' ); ?>" required>
                        </fieldset>
                        <fieldset class="tf-field password-wrapper">
                            <label class="tf-lable"><?php esc_html_e( 'Password', 'ovinx-addon' ); ?> <span class="text-primary">*</span></label>
                            <div class="password-wrapper w-100">
                                <span class="toggle-pass icon icon-EyeSlash"></span>
                                <input class="password-field" type="password" id="popup-login-password" name="password" placeholder="<?php esc_attr_e( 'Password', 'ovinx-addon' ); ?>" required>
                            </div>
                        </fieldset>
                        <fieldset class="field-bottom">
                            <div class="checkbox-wrap">
                                <input id="popup-login-remember" type="checkbox" class="tf-check" name="remember" value="1">
                                <label for="popup-login-remember"><?php esc_html_e( 'Remember me', 'ovinx-addon' ); ?></label>
                            </div>
                            <a href="#forgotPassword" data-bs-toggle="modal" class="link"><?php esc_html_e( 'Forgot Your Password?', 'ovinx-addon' ); ?></a>
                        </fieldset>

                        <?php if ( $enable_login_fb || $enable_login_gg ) : ?>
                            <div class="text-center">
                                <p class="text-caption-01 cl-text-mute mb-12"><?php esc_html_e( 'or sign up with', 'ovinx-addon' ); ?></p>
                                <div class="d-flex flex-column gap-12">
                                    <?php if ( $enable_login_fb ) : ?>
                                        <a href="<?php echo esc_url( $login_facebook_url ); ?>" class="tf-btn style-stroke w-100 animate-btn">
                                            <span class="icon">
                                                <img loading="lazy" width="24" height="24" src="<?php echo THEMESFLAT_LINK . 'images/logo/face.svg'; ?>" alt="Image">
                                            </span>
                                            <?php esc_html_e( 'Facebook', 'ovinx-addon' ); ?>
                                        </a>
                                    <?php endif; ?>

                                    <?php if ( $enable_login_gg ) : ?>
                                        <a href="<?php echo esc_url( $login_google_url ); ?>" class="tf-btn style-stroke w-100 animate-btn">
                                            <span class="icon">
                                                <img loading="lazy" width="24" height="24" src="<?php echo THEMESFLAT_LINK . 'images/logo/google.svg'; ?>" alt="Image">
                                            </span>
                                            <?php esc_html_e( 'Google', 'ovinx-addon' ); ?>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="group-action">
                        <button type="submit" class="tf-btn w-100">
                            <?php esc_html_e( 'Login', 'ovinx-addon' ); ?>
                        </button>
                        <a href="#register" data-bs-toggle="modal" class="tf-btn style-stroke">
                            <?php esc_html_e( 'Create Account', 'ovinx-addon' ); ?>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
