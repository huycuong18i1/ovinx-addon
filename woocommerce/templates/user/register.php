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

<!-- Structure matches ovinx-html's 404.html #register modal (.modal-heading/.modal-main/.group-action are the
     real, styled classes — see css/styles.css). Terms-agreement checkbox and FB/Google sign-up have no
     ground-truth markup (added features beyond the static export); kept functional, styled with existing
     real utility classes only. -->
<div class="modal modalCentered fade modal-log" id="register">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <button type="button" class="btn-close-popup" data-bs-dismiss="modal" aria-label="<?php esc_attr_e( 'Close', 'ovinx-addon' ); ?>">
                <i class="icon icon-Close2"></i>
            </button>
            <div class="modal-heading text-center">
                <h4 class="title-pop"><?php esc_html_e( 'Create Account', 'ovinx-addon' ); ?></h4>
                <p class="desc-pop"><?php esc_html_e( 'Be part of our growing family of new customers!', 'ovinx-addon' ); ?></p>
            </div>
            <div class="modal-main">
                <form id="tfwc_custom_register_form" class="tfwc-custom-register-form form-log">
                    <div class="form-content">
                        <fieldset class="tf-field">
                            <label for="popup-reg-username" class="tf-lable"><?php esc_html_e( 'Username', 'ovinx-addon' ); ?> <span class="text-primary">*</span></label>
                            <input type="text" name="username" id="popup-reg-username" placeholder="<?php esc_attr_e( 'Username', 'ovinx-addon' ); ?>" required>
                        </fieldset>
                        <fieldset class="tf-field">
                            <label for="popup-reg-email" class="tf-lable"><?php esc_html_e( 'Email', 'ovinx-addon' ); ?> <span class="text-primary">*</span></label>
                            <input type="email" name="email" id="popup-reg-email" placeholder="<?php esc_attr_e( 'Email', 'ovinx-addon' ); ?>" required>
                        </fieldset>
                        <fieldset class="tf-field password-wrapper">
                            <label for="popup-reg-password" class="tf-lable"><?php esc_html_e( 'Password', 'ovinx-addon' ); ?> <span class="text-primary">*</span></label>
                            <div class="password-wrapper w-100">
                                <span class="toggle-pass icon icon-EyeSlash"></span>
                                <input class="password-field" type="password" id="popup-reg-password" name="password" placeholder="<?php esc_attr_e( 'Password', 'ovinx-addon' ); ?>" required>
                            </div>
                        </fieldset>
                        <fieldset class="tf-field password-wrapper">
                            <label for="popup-reg-password2" class="tf-lable"><?php esc_html_e( 'Confirm password', 'ovinx-addon' ); ?> <span class="text-primary">*</span></label>
                            <div class="password-wrapper w-100">
                                <span class="toggle-pass icon icon-EyeSlash"></span>
                                <input class="password-field" type="password" id="popup-reg-password2" name="password2" placeholder="<?php esc_attr_e( 'Confirm Password', 'ovinx-addon' ); ?>" required>
                            </div>
                        </fieldset>

                        <?php if ( function_exists( 'themesflat_get_opt' ) ) : ?>
                            <fieldset class="field-bottom">
                                <div class="checkbox-wrap">
                                    <input id="popup-reg-agree" type="checkbox" class="tf-check">
                                    <label for="popup-reg-agree" class="text-caption-01">
                                        <?php echo wp_kses_post( themesflat_get_opt( 'register_policy' ) ); ?>
                                    </label>
                                </div>
                            </fieldset>
                        <?php endif; ?>

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
                            <?php esc_html_e( 'Create Account', 'ovinx-addon' ); ?>
                        </button>
                        <a href="#log" data-bs-toggle="modal" class="tf-btn style-stroke">
                            <?php esc_html_e( 'Login', 'ovinx-addon' ); ?>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
