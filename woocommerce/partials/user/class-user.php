<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

if (!class_exists('User_Public')) {
	class User_Public
	{
		const ACCESS_TOKEN = 'access_token';

		public $google_client;

		public function __construct( Google_Client $client ) {
			$this->google_client = $client;
		}


		function tfwc_enqueue_user_scripts()
		{
			wp_enqueue_script('jquery-validate', TF_PLUGIN_URL . 'woocommerce/assets/js/jquery.validate.min.js', array('jquery'), null, true);
			wp_enqueue_script('user-script', TF_PLUGIN_URL . '/woocommerce/assets/js/user.js', array('jquery'), null, true);
			wp_localize_script(
				'user-script',
				'user_script_variables',
				array(
					'ajaxUrl' => TF_AJAX_URL,
					'nonce' => wp_create_nonce('auth-ajax-nonce'),
					
				)
			);
		}

		public function tfwc_shortcode_register_form()
		{
			ob_start();
			include TF_THEME_PATH . '/user/register.php';
			return ob_get_clean();
		}

		public function tfwc_shortcode_login_form()
		{
			ob_start();
			include TF_THEME_PATH . '/user/login.php';
			return ob_get_clean();
		}

		public function tfwc_login_register_modal()
		{
			if (!is_user_logged_in()) {
				echo tfwc_get_template_with_arguments(
					'user/login.php',
					array()
				);

				echo tfwc_get_template_with_arguments(
					'user/register.php',
					array()
				);

				echo tfwc_get_template_with_arguments(
					'user/reset-password.php',
					array()
				);
			} else {
				echo tfwc_get_template_with_arguments(
					'user/dashboard.php',
					array()
				);
			}
		}


		public function tfwc_register_ajax_handler()
		{
			check_ajax_referer('auth-ajax-nonce', 'security');
			$username = sanitize_user($_POST['username']) ?? '';
			$email = sanitize_email($_POST['email']) ?? '';
			$phone_number = $_POST['phone'] ?? '';
			$password = $_POST['password'] ?? '';
			$confirm_password = $_POST['password2'] ?? '';

			$response = array(
				'status' => false,
				'message' => ''
			);

			header('Content-Type: application/json');

			if (empty($email) || empty($password) || empty($confirm_password) ) {
				$response['message'] = esc_html__('All fields are required.', 'ovinx-addon');
				echo json_encode($response);
				wp_die();
			}

			if( $password !== $confirm_password) {
				$response['message'] = esc_html__('Passwords do not match.', 'ovinx-addon');
				echo json_encode($response);
				wp_die();
			}

			if (username_exists($username)) {
				$response['message'] = esc_html__('Username already exists. Please choose a different username.', 'ovinx-addon');
				echo json_encode($response);
				wp_die();
			}

			if (!is_email($email)) {
				$response['message'] = esc_html__('Invalid email address.', 'ovinx-addon');
				echo json_encode($response);
				wp_die();
			}

			if (email_exists($email)) {
				$response['message'] = esc_html__('Email address already exists', 'ovinx-addon');
				echo json_encode($response);
				wp_die();
			}

			// If no errors, create user
			$user_id = wp_create_user($username, $password, $email);

			if (!is_wp_error($user_id)) {
				wp_set_current_user($user_id);
				wp_set_auth_cookie($user_id);
				wp_update_user(array('ID' => $user_id, 'role' => 'customer'));
				update_user_meta($user_id, 'user_phone_number', $phone_number);
				$response['status'] = true;
				$response['message'] = esc_html__('Your account was created, login now!', 'ovinx-addon');
				echo json_encode($response);
				wp_die();
			} else {
				// QA P2-D3: was print_r( $user_id ) (dumped the WP_Error object into the response) and never sent the JSON
				// message prepared below, so the register form got an unparsable reply.
				$response['message'] = esc_html__('Can\'t create a new account!', 'ovinx-addon');
				echo json_encode($response);
				wp_die();
			}

		}

		public function tfwc_login_ajax_handler()
		{
			check_ajax_referer('auth-ajax-nonce', 'security');
			$username = isset($_POST['username']) ? $_POST['username'] : '';
			$password = isset($_POST['password']) ? $_POST['password'] : '';
			$remember = isset($_POST['remember']) && $_POST['remember'] == '1' ? true : false;

			$response = array(
				'status' => false,
				'message' => null
			);

			header('Content-Type: application/json');

			$credentials = array(
				'user_login' => $username,
				'user_password' => $password,
				'remember'      => $remember
			);

			$user = wp_signon($credentials, false);

			if (is_wp_error($user)) {
				$response['message'] = esc_html__('Username or Password is invalid!', 'ovinx-addon');
				echo json_encode($response);
				wp_die();
			} else {
				wp_set_current_user($user->ID);
				wp_set_auth_cookie($user->ID);
				$response['message'] = esc_html__('Login Successfully!', 'ovinx-addon');
				$response['status'] = true;
				// My Account sends where it wants the visitor back (itself, so the dashboard shows); anything off-site falls back to home.
				$response['redirect_url'] = isset( $_POST['redirect'] ) ? wp_validate_redirect( esc_url_raw( wp_unslash( $_POST['redirect'] ) ), home_url() ) : home_url();
				echo json_encode($response);
				wp_die();
			}
		}

		public function tfwc_reset_password_ajax_handler() {
			check_ajax_referer( 'tfwc_reset_password_ajax_nonce', 'tfwc_security_reset_password' );
			$user_login = isset( $_POST['user_login'] ) ? wp_unslash( $_POST['user_login'] ) : '';

			if ( empty( $user_login ) ) {
				echo json_encode( array( 'success' => false, 'message' => esc_html__( 'Enter a username or email address.', 'ovinx-addon' ) ) );
				wp_die();
			}
			$login     = trim( $user_login );
			$user_data = get_user_by( 'login', $login );
			// Check user by username first
			if ( empty( $user_data ) ) {
				// Check user by email
				$user_data = get_user_by( 'email', $login );
				if ( empty( $user_data ) ) {
					echo json_encode( array( 'success' => false, 'message' => esc_html__( 'There is no user registered with that email or username.', 'ovinx-addon' ) ) );
					wp_die();
				}
			}
			$user_login = $user_data->user_login;
			$user_email = $user_data->user_email;
			$key        = get_password_reset_key( $user_data );
			if ( is_wp_error( $key ) ) {
				echo json_encode( array( 'success' => false, 'message' => $key->get_error_message() ) );
				wp_die();
			}

			$message = esc_html__( 'You have requested to reset your password.', 'ovinx-addon' ) . "\r\n\r\n";
			$message .= sprintf( esc_html__( 'Username: %s', 'ovinx-addon' ), $user_login ) . "\r\n\r\n";
			$message .= esc_html__( 'If you did not request a password reset, please ignore this email.', 'ovinx-addon' ) . "\r\n\r\n";
			$message .= esc_html__( 'To reset your password, visit the following address:', 'ovinx-addon' ) . "\r\n\r\n";
			// Same link WooCommerce's own "Reset password" email uses: the styled My Account page (form-reset-password.php),
				// not the bare wp-login.php screen.
				if ( function_exists( 'wc_get_page_permalink' ) && function_exists( 'wc_get_endpoint_url' ) ) {
					$reset_url = add_query_arg(
						array( 'key' => $key, 'id' => $user_data->ID ),
						wc_get_endpoint_url( 'lost-password', '', wc_get_page_permalink( 'myaccount' ) )
					);
				} else {
					$reset_url = site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user_login ), 'login' );
				}
				$message .= $reset_url . "\r\n";
			$subject = sprintf( esc_html__( 'Password Reset Request', 'ovinx-addon' ) );
			$subject = apply_filters( 'retrieve_password_title', $subject, $user_login, $user_data );
			$message = apply_filters( 'retrieve_password_message', $message, $key, $user_login, $user_data );
			if ( $message && ! wp_mail( $user_email, $subject, $message ) ) {
				echo json_encode( array( 'success' => false, 'message' => esc_html__( 'The email could not be sent.', 'ovinx-addon' ) . "<br />\n" . esc_html__( 'Possible reason: your host may have disabled the mail() function.', 'ovinx-addon' ) ) );
				wp_die();
			} else {
				echo json_encode( array( 'success' => true, 'message' => esc_html__( 'Please check your email for reset password!', 'ovinx-addon' ) ) );
				wp_die();
			}
		}

		public function tfwc_set_access_token_google() {
			check_ajax_referer( 'custom-ajax-nonce', 'security' );
			$response = array( 'status' => false );
			if ( isset( $_POST['code'] ) ) {
				$get_access_token = $this->google_client->tfwc_get_access_token( $_POST['code'] );
				session_start();
				$_SESSION[ self::ACCESS_TOKEN ] = isset( $get_access_token['access_token'] ) ? $get_access_token['access_token'] : null;
				$response['status']             = true;
			}
			echo json_encode( $response );
			wp_die();
		}

		public function tfwc_handle_google_login() {
			check_ajax_referer( 'custom-ajax-nonce', 'security' );
			$response = array( 'status' => false );
			header( 'Content-Type: application/json' );
			session_start();
			if ( $_SESSION[ self::ACCESS_TOKEN ] ) {
				$user = $this->google_client->tfwc_get_user_info( $_SESSION[ self::ACCESS_TOKEN ] );
				$this->tfwc_login_with_google_user( $user );
				$response['status']       = true;
				$response['redirect_url'] = '';
			}
			echo json_encode( $response );
			wp_die();
		}

		public function tfwc_login_with_google_user( $user ) {
			// if there is any error, send the error back to client
			if ( isset( $user->error ) ) {
				wp_send_json_error( $user->error_description );
			} else {
				// check if user already exists in WP user
				$first_name      = isset( $user['given_name'] ) ? $user['given_name'] : '';
				$last_name       = isset( $user['family_name'] ) ? $user['family_name'] : '';
				$user_picture    = isset( $user['picture'] ) ? $user['picture'] : '';
				$user_picture_id = tfwc_save_image_from_url( $user_picture );
				$name            = isset( $user['name'] ) ? $user['name'] : '';
				$email           = isset( $user['email'] ) ? $user['email'] : '';
				$email_exists    = email_exists( $email );
				if ( ! $email_exists ) {
					$random_password = wp_generate_password( $length = 12, $include_standard_special_chars = false );
					// insert the user as WP user
					$user_id = wp_insert_user( [ 
						'user_email'   => $email,
						'user_login'   => $email,
						'user_pass'    => $random_password,
						'display_name' => $name,
						'nickname'     => $name,
						'first_name'   => $first_name,
						'last_name'    => $last_name
					] );
					wp_update_user( array( 'ID' => $user_id, 'role' => 'subscriber' ) );
					//set user profile picture
					update_user_meta( $user_id, 'profile_image', $user_picture );
					update_user_meta( $user_id, 'profile_image_id', $user_picture_id );
				}

				// do login
				$user = get_user_by( 'email', $email );

				if ( ! is_wp_error( $user ) ) {
					wp_clear_auth_cookie();
					wp_set_current_user( $user->ID, $user->user_login );
					wp_set_auth_cookie( $user->ID );
				}
			}
		}

		public function tfwc_login_with_facebook_user( $user ) {
			if ( isset( $user->error ) ) {
				wp_send_json_error( $user->error_description );
				return;
			}
		
			$name = isset( $user['name'] ) ? sanitize_text_field( $user['name'] ) : '';
			$email = isset( $user['email'] ) ? sanitize_email( $user['email'] ) : '';
			$user_exists = username_exists( $name ) || email_exists( $email );
		
			if ( !$user_exists ) {
				$random_password = wp_generate_password( 12, false );
				$user_id = wp_insert_user([
					'user_email'   => $email,
					'user_login'   => $name,
					'user_pass'    => $random_password,
					'display_name' => $name,
					'nickname'     => $name
				]);
		
				wp_update_user([ 'ID' => $user_id, 'role' => 'subscriber' ]);
			}
		
			$user = get_user_by( 'login', $name );
		
			if ( ! is_wp_error( $user ) ) {
				wp_clear_auth_cookie();
				wp_set_current_user( $user->ID, $user->user_login );
				wp_set_auth_cookie( $user->ID );
			}
		}
		
		public function tfwc_get_user_info_facebook( $access_token ) {
			if ( empty( $access_token ) ) {
				return new WP_Error( 'invalid_token', 'Invalid access token provided.' );
			}
		
			$permissions = ['email', 'name'];
			$url = 'https://graph.facebook.com/me?access_token=' . $access_token . '&fields=' . implode(',', $permissions);
		
			
			$response = wp_remote_get( $url, [
				'headers' => [ 'Accept' => 'application/json' ]
			]);
		
			if ( is_wp_error( $response ) ) {
				return $response; 
			}
		
			$status = wp_remote_retrieve_response_code( $response );
			if ( $status !== 200 ) {
				return new WP_Error( 'facebook_error', 'Failed to retrieve user info from Facebook.' );
			}
		
			$user_info = json_decode( wp_remote_retrieve_body( $response ), true );
			return $user_info;
		}
		
		public function tfwc_handle_facebook_login() {
			
			check_ajax_referer( 'custom-ajax-nonce', 'security' );
		
			$response = [ 'status' => false ];
		
			
			if ( isset( $_POST['access_token'] ) ) {
				$access_token = sanitize_text_field( $_POST['access_token'] );
				
				
				$user_info = $this->tfwc_get_user_info_facebook( $access_token );
		
				if ( is_wp_error( $user_info ) ) {
					$response['message'] = $user_info->get_error_message();
				} else {
					
					$this->tfwc_login_with_facebook_user( $user_info );
		
					$response['status'] = true;
					$response['user'] = $user_info;
					$response['redirect_url'] = home_url(''); 
				}
			} else {
				$response['message'] = 'No access token provided.';
			}
		
			echo json_encode( $response );
			wp_die();  
		}

	}
}