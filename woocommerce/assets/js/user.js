(function ($) {
    'use strict';
    /* Login / Register / Forgot password — bound by CLASS and delegated, because the same forms exist twice:
       in the header popups (#log / #register / #forgotPassword, printed in wp_footer) and on the guest My Account
       page (woocommerce/myaccount/form-login.php), and ids can't be unique across both. */
    var setBusy = function ($form, busy) {
        $form.find('button[type="submit"]').prop('disabled', busy).toggleClass('disabled', busy);
    };

    var handleLogin = function () {
        $(document).on('submit', '.tfwc-custom-login-form', function (e) {
            e.preventDefault();
            var form = $(this);
            var message = form.find('.tfwc-message');

            if (this.checkValidity && !this.checkValidity()) {
                this.reportValidity();
                return;
            }

            $.ajax({
                type: 'POST',
                url: user_script_variables.ajaxUrl,
                dataType: 'json',
                data: form.serialize() + '&action=custom_login&security=' + user_script_variables.nonce,
                beforeSend: function () {
                    setBusy(form, true);
                    message.empty().append('<span class="success text-success"> Loading </span>');
                },
                success: function (response) {
                    if (response.status) {
                        message.empty().append('<span class="success text-success">' + response.message + '</span>');
                        window.location.href = response.redirect_url;
                    } else {
                        setBusy(form, false);
                        message.empty().append('<span class="error text-danger">' + response.message + '</span>');
                    }
                },
                error: function (xhr, status, error) {
                    setBusy(form, false);
                    message.empty().append('<span class="error text-danger">' + (error || status) + '</span>');
                }
            });
        });
    };

    var handleRegister = function () {
        $(document).on('submit', '.tfwc-custom-register-form', function (e) {
            e.preventDefault();
            var form = $(this);
            var message = form.find('.tfwc-message');

            if (this.checkValidity && !this.checkValidity()) {
                this.reportValidity();
                return;
            }

            $.ajax({
                type: 'POST',
                url: user_script_variables.ajaxUrl,
                dataType: 'json',
                data: form.serialize() + '&action=custom_register&security=' + user_script_variables.nonce,
                beforeSend: function () {
                    setBusy(form, true);
                    message.empty().append('<span class="success text-success"> Loading </span>');
                },
                success: function (response) {
                    if (response.status) {
                        message.empty().append('<span class="success text-success">' + response.message + '</span>');
                        setTimeout(function () {
                            window.location.reload();
                        }, 1000);
                    } else {
                        setBusy(form, false);
                        message.empty().append('<span class="error text-danger">' + response.message + '</span>');
                    }
                },
                error: function (xhr, status, error) {
                    setBusy(form, false);
                    message.empty().append('<span class="error text-danger">' + (error || status) + '</span>');
                }
            });
        });
    };

    var handleResetPassword = function () {
        $(document).on('submit', '.tfwc-resset-password', function (e) {
            e.preventDefault();
            var form = $(this);
            var message = form.find('.tfwc_messages_reset_password');

            if (this.checkValidity && !this.checkValidity()) {
                this.reportValidity();
                return;
            }

            $.ajax({
                type: 'POST',
                url: user_script_variables.ajaxUrl,
                dataType: 'json',
                data: form.serialize(),
                beforeSend: function () {
                    setBusy(form, true);
                    message.empty().append('<span class="success text-success"> Loading </span>');
                },
                success: function (response) {
                    setBusy(form, false);
                    if (response.success) {
                        message.empty().append('<span class="success text-success">' + response.message + '</span>');
                    } else {
                        message.empty().append('<span class="error text-danger">' + response.message + '</span>');
                    }
                },
                error: function (xhr, status, error) {
                    setBusy(form, false);
                    message.empty().append('<span class="error text-danger">' + (error || status) + '</span>');
                }
            });
        });
    };
    var setAccessTokenGoogle = function () {
        var urlParams = new URLSearchParams(window.location.search);
        var code = urlParams.get('code');
        if (code) {
            $.ajax({
                type: 'post',
                url: ajax_object.ajaxUrl,
                dataType: 'json',
                data: {
                    'action': 'set_access_token_google',
                    'security': ajax_object.nonce,
                    'code': code
                },
                success: function (res) {
                    if (res.status) {
                        handleLoginGoogle();
                    }
                },
                error: function (error) {
                    console.log(error);
                }
            })
        }
    }

    var handleLoginGoogle = function () {
        $.ajax({
            type: 'post',
            url: ajax_object.ajaxUrl,
            dataType: 'json',
            data: {
                'action': 'handle_google_login',
                'security': ajax_object.nonce,
            },
            success: function (res) {
                if (res.status) {
                    window.location.href = res.redirect_url;
                }
            },
            error: function (error) {
                console.log(error);
            }
        })
    }

    var handleLoginFacebook = function () {
		$('.btn-login-fb').on('click', function (e) {			
			var urlParams = new URLSearchParams(window.location.search);
			var code = urlParams.get("code");
			console.log(code);
			$.ajax({
				type: "post",
				url: ajax_object.ajaxUrl,
				dataType: "json",
				data: {
					action: "handle_facebook_login",
					security: ajax_object.nonce,
					code: code,
				},
				success: function (res) {
					console.log(res);
					if (res.status) {
						// window.location.href = res.redirect_url;
					}
				},
				error: function (error) {
					console.log(error);
				},
			});
		});
	};

    /* Show Password 
    -------------------------------------------------------------------------*/
    var showPassword = function () {
        $(".toggle-pass").on("click", function () {
            const wrapper = $(this).closest(".password-wrapper");
            const input = wrapper.find(".password-field");
            const icon = $(this);

            if (input.attr("type") === "password") {
                input.attr("type", "text");
                icon.removeClass("icon-EyeSlash").addClass("icon-EyeOpen");
            } else {
                input.attr("type", "password");
                icon.removeClass("icon-EyeOpen").addClass("icon-EyeSlash");
            }
        });
    };

    jQuery(document).ready(function () {
        handleLogin();        
        handleRegister();
        handleResetPassword();
        setAccessTokenGoogle();
        handleLoginFacebook();
        showPassword();
    })
})(jQuery);