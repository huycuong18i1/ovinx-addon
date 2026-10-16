<?php
/**
 * Google API Client.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


if ( ! class_exists( 'Google_Client' ) ) {
	class Google_Client {
		const AUTHORIZE_URL = 'https://accounts.google.com/o/oauth2/auth';

		const TOKEN_URL = 'https://oauth2.googleapis.com/token';

		const API_BASE = 'https://www.googleapis.com';

		const SCOPE_EMAIL = 'https://www.googleapis.com/auth/userinfo.email';

		const SCOPE_PROFILE = 'https://www.googleapis.com/auth/userinfo.profile';

		public static $client_id;

		public static $client_secret;

		public static $access_token;

		private static $_instance = null;

	

		public static function get_instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		public function __construct() {
			// self::$client_id = themesflat_get_opt('google_login_client_id');
			// self::$client_secret = get_theme_mod('google_login_client_secret');
			
		}

		public function tfwc_get_authorization_url() {
			if (function_exists('themesflat_get_opt')) {
				$client_id = themesflat_get_opt('google_login_client_id');
				$client_secret = themesflat_get_opt('google_login_client_secret');
			} else {
				$client_id = '';
			}
			$client_args = [ 
				'client_id'     => $client_id,
				'redirect_uri'  => home_url(),
				'scope'         => self::SCOPE_EMAIL . ' ' . self::SCOPE_PROFILE,
				'access_type'   => 'online',
				'response_type' => 'code',
			];

			return self::AUTHORIZE_URL . '?' . http_build_query( $client_args );
		}

		public function tfwc_get_access_token( $code ) {
			if (function_exists('themesflat_get_opt')) {
				$client_id = themesflat_get_opt('google_login_client_id');
				$client_secret = themesflat_get_opt('google_login_client_secret');
			} else {
				$client_id = '';
				$client_secret = '';
			}
			$response = wp_remote_post(
				self::TOKEN_URL,
				[ 
					'headers' => [ 
						'Accept' => 'application/json'
					],
					'body'    => [ 
						'client_id'     => $client_id,
						'client_secret' => $client_secret,
						'redirect_uri'  => home_url(),
						'code'          => $code,
						'grant_type'    => 'authorization_code'
					]
				]
			);

			$status = wp_remote_retrieve_response_code( $response );
			if ( $status === 200 || $status === 201 ) {
				$content = json_decode( wp_remote_retrieve_body( $response ), true );
				return $content;
			}
			return wp_remote_retrieve_response_message( $response );
		}

		public function tfwc_get_user_info( $access_token ) {
			$user = wp_remote_get(
				trailingslashit( self::API_BASE ) . 'oauth2/v2/userinfo?access_token=' . $access_token,
				[ 
					'headers' => [ 
						'Accept' => 'application/json'
					]
				]
			);

			$status = wp_remote_retrieve_response_code( $user );
			if ( $status === 200 || $status === 201 ) {
				$user_info = json_decode( wp_remote_retrieve_body( $user ), true );
				return $user_info;
			}
			return wp_remote_retrieve_response_message( $user );
		}
	}
}