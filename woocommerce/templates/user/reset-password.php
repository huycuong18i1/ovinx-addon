<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<!-- Structure matches ovinx-html's 404.html #modalForgot modal (.modal-heading/.modal-main/.group-action
     are the real, styled classes — see css/styles.css). -->
<div class="modal modalCentered fade modal-log" id="forgotPassword">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <button type="button" class="btn-close-popup" data-bs-dismiss="modal" aria-label="<?php esc_attr_e( 'Close', 'ovinx-addon' ); ?>">
                <i class="icon icon-Close2"></i>
            </button>
            <div class="modal-heading text-center">
                <h4 class="title-pop"><?php esc_html_e( 'Forgot Password', 'ovinx-addon' ); ?></h4>
                <p class="desc-pop"><?php esc_html_e( 'Enter the e-mail address associated with your account. Click submit to have a password reset link e-mailed to you.', 'ovinx-addon' ); ?></p>
            </div>
            <div class="modal-main">
                <form method="post" enctype="multipart/form-data" class="tfwc-resset-password form-reset form-log">
                    <div class="form-content">
                        <fieldset class="tf-field">
                            <label for="popup_user_login" class="tf-lable"><?php esc_html_e( 'Your Email Address', 'ovinx-addon' ); ?></label>
                            <input type="email" name="user_login" id="popup_user_login" placeholder="<?php esc_attr_e( 'Email', 'ovinx-addon' ); ?>" required>
                        </fieldset>
                        <input type="hidden" name="action" value="reset_password_ajax">
                        <input type="hidden" name="tfwc_security_reset_password" value="<?php echo esc_attr( wp_create_nonce( 'tfwc_reset_password_ajax_nonce' ) ); ?>">
                        <div class="tfwc_messages_reset_password"></div>
                    </div>
                    <div class="group-action">
                        <button type="submit" class="tf-btn w-100">
                            <?php esc_html_e( 'Forgot Password', 'ovinx-addon' ); ?>
                        </button>
                        <p class="orther-log text-center">
                            <a href="#log" data-bs-toggle="modal" class="cl-text-main text-decoration-underline">
                                <?php esc_html_e( 'Back To Login', 'ovinx-addon' ); ?>
                            </a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
