<?php

namespace  IconvertEmailMarketer\App\Core\Structure;

use IconvertEmailMarketer\Settings\EmailProviderSettings;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ( ! defined( 'ABSPATH' ) ) {exit;}

class SendingOptions {


	public function __construct() {
		$this->init();
	}

	public function init() {

		add_action( 'rest_api_init', array( $this, 'register_rest_api' ) );
		// Override WordPress wp_mail with our SMTP settings
		add_action( 'phpmailer_init', array( $this, 'configure_wp_mail_smtp' ), PHP_INT_MAX, 1 );

		// Override wp_mail from name and from email
		add_filter( 'wp_mail_from', array( $this, 'custom_wp_mail_from' ), PHP_INT_MAX, 1 );
		add_filter( 'wp_mail_from_name', array( $this, 'custom_wp_mail_from_name' ), PHP_INT_MAX, 1 );


	}

	public function register_rest_api() {
		$namespace = 'iconvertem/v1';

		register_rest_route(
			$namespace,
			'/sending-options/test-connection',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'test_provider_connection' ),
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},

			)
		);
		register_rest_route(
			$namespace,
			'/sending-options/send-test-email',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'send_test_email' ),
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},

			)
		);
	}






	public function load_required_php_mail_classes() {
		require_once ABSPATH . WPINC . '/PHPMailer/PHPMailer.php';
		require_once ABSPATH . WPINC . '/PHPMailer/SMTP.php';
		require_once ABSPATH . WPINC . '/PHPMailer/Exception.php';
	}





	public function test_provider_connection() {

		$provider = $this->get_selected_provider();

		if ( ! $provider ) {
			return new \WP_Error(
				'error',
				'<strong>No provider selected.</strong><br><small>Choose an email provider before testing the connection.</small>',
				array( 'status' => 500 )
			);
		}
		$this->load_required_php_mail_classes();
		// Get form data from the unified form

		switch ( $provider ) {
			case 'smtp':
				$provider_settings = $this->get_provider_settings( 'smtp' );
				$host              = $provider_settings['host'] ?? '';
				$port              = $provider_settings['port'] ?? '';
				$username          = $provider_settings['username'] ?? '';
				$password          = $provider_settings['password'] ?? '';
				$encryption        = $provider_settings['encryption'] ?? '';

				if ( empty( $host ) || empty( $username ) || empty( $password ) ) {
					return new \WP_Error(
						'error',
						$this->format_provider_error( 'smtp', 'Missing required fields.', 'Please fill in Host, Username and Password.' ),
						array( 'status' => 500 )
					);

				}

				$mail = new PHPMailer( true );
				try {

					$mail->isSMTP();
					$mail->Host       = $host;
					$mail->SMTPAuth   = true;
					$mail->Username   = $username;
					$mail->Password   = $password;
					$mail->SMTPSecure = $encryption === 'none' ? false : $encryption;
					$mail->Port       = $port;
					$mail->Timeout    = 30;

					// Test connection
					if ( $mail->smtpConnect() ) {
						$mail->smtpClose();
						return new \WP_REST_Response(
							array(
								'success' => true,
								'message' => $this->format_provider_success( 'smtp', 'Connection successful.' ),
							),
							200
						);

					} else {
						return new \WP_Error(
							'error',
							$this->format_provider_error( 'smtp', 'Connection failed.' ),
							array( 'status' => 500 )
						);

					}
				} catch ( Exception $e ) {
					$error_msg = $e->getMessage();

					if ( strpos( $error_msg, 'Connection refused' ) !== false ) {
						$error_msg .= " - Check if port {$port} is blocked by your firewall or ISP.";
					} elseif ( strpos( $error_msg, 'getaddrinfo failed' ) !== false ) {
						$error_msg .= ' - DNS resolution failed. Check your internet connection.';
					} elseif ( strpos( $error_msg, 'Username/Password' ) !== false ) {
						$error_msg .= ' - Use App Password for Gmail, not your regular password.';
					}

					return new \WP_Error(
						'error',
						$this->format_provider_error( 'smtp', 'Connection failed.', $error_msg ),
						array( 'status' => 500 )
					);

				}
				break;

			default:
				return new \WP_Error(
					'error',
					$this->format_provider_error( $provider, 'Unknown provider.', $provider ),
					array( 'status' => 500 )
				);

		}

	}

	public function send_test_email() {

		$provider = $this->get_selected_provider();
		if ( empty( $provider ) ) {
			return new \WP_Error(
				'error',
				'<strong>No provider selected.</strong><br><small>Choose an email provider before sending a test email.</small>',
				array( 'status' => 500 )
			);

		}

		$test_email = $this->get_from_email() ?: get_option( 'admin_email' );

		if ( empty( $test_email ) ) {
			return new \WP_Error(
				'error',
				__( 'No email address available for testing. Please set From Email or check your WordPress admin email.', 'iconvert-email-marketer' ),
				array( 'status' => 500 )
			);

		}

		// Validate provider settings before attempting to send
		if ( $provider === 'smtp' ) {
			$smtp_settings = $this->get_smtp_settings();
			if ( empty( $smtp_settings['host'] ) || empty( $smtp_settings['username'] ) || empty( $smtp_settings['password'] ) ) {
				return new \WP_Error(
					'error',
					__( 'SMTP settings are incomplete. Please save your settings first.', 'iconvert-email-marketer' ),
					array( 'status' => 500 )
				);

			}
		}

		// Capture any wp_mail errors
		$mail_error = '';
		add_action(
			'wp_mail_failed',
			function( $wp_error ) use ( &$mail_error ) {
				$mail_error = $wp_error->get_error_message();
			}
		);

		// Send test email
		$subject  = 'Email Configuration Test - ' . current_time( 'Y-m-d H:i:s' );
		$message  = '<html><body>';
		$message .= '<h2>Email Configuration Test</h2>';
		$message .= '<p>This is a test email to verify your email configuration is working properly.</p>';
		$message .= '<table style="border-collapse:collapse;width:100%;margin:10px 0;">';
		$message .= '<tr><td style="padding:5px;"><strong>Provider:</strong></td><td style="padding:5px;">' . ucfirst( $provider ) . '</td></tr>';
		$message .= '<tr><td style="padding:5px;"><strong>From Name:</strong></td><td style="padding:5px;">' . ( $this->get_from_name() ?: 'Not Set' ) . '</td></tr>';
		$message .= '<tr><td style="padding:5px;"><strong>From Email:</strong></td><td style="padding:5px;">' . ( $this->get_from_email() ?: 'Not Set' ) . '</td></tr>';
		$message .= '<tr><td style="padding:5px;"><strong>Sent At:</strong></td><td style="padding:5px;">' . current_time( 'Y-m-d H:i:s' ) . '</td></tr>';
		$message .= '</table>';
		$message .= '<hr><p><small>This email was sent via the iConvert Email Marketer plugin.</small></p>';
		$message .= '</body></html>';

		$headers = array( 'Content-Type: text/html; charset=UTF-8' );
		$result  = wp_mail( $test_email, $subject, $message, $headers );

		if ( $result ) {
			return new \WP_REST_Response(
				array(
					'success' => true,
					// translators: %s - email
					'message' => sprintf( __( 'Test email sent successfully to %s ! Check your inbox.', 'iconvert-email-marketer' ), $test_email ),
				),
				200
			);

		} else {
			$error_message = 'Failed to send test email.';

			if ( ! empty( $mail_error ) ) {
				$error_message .= ' Error: ' . $mail_error;
			}

			return new \WP_Error(
				'error',
				$error_message,
				array( 'status' => 500 )
			);
		}
		return new \WP_Error(
			'error',
			'Unokwn Error',
			array( 'status' => 500 )
		);
	}






	/**
	 * Get current provider settings
	 */
	public function get_provider_settings( $provider ) {
		switch ( $provider ) {
			case 'smtp':
				return $this->get_smtp_settings();
			default:
				return array();
		}

	}


	public function get_smtp_settings() {
		return EmailProviderSettings::getInstance()->getSmtpSettings();

	}



	public function get_selected_provider() {
		return EmailProviderSettings::getInstance()->getSelectedProvider();
	}


	/**
	 * Get from name from global settings
	 */
	public function get_from_name() {
		return EmailProviderSettings::getInstance()->getFromName();

	}

	/**
	 * Get from email from global settings
	 */
	public function get_from_email() {
		return EmailProviderSettings::getInstance()->getFromEmail();

	}

	/**
	 * Configure PHPMailer to use our SMTP settings for all wp_mail() calls
	 * This is the MAIN function that overrides WordPress default mail behavior
	 */
	public function configure_wp_mail_smtp( $phpmailer ) {

		$selected_provider = $this->get_selected_provider() ?? '';

		if ( $selected_provider === 'smtp' ) {
			$smtp_settings = $this->get_smtp_settings();

			// Validate SMTP settings are complete
			if ( empty( $smtp_settings['host'] ) || empty( $smtp_settings['username'] ) || empty( $smtp_settings['password'] ) ) {
				return;
			}

			try {
				// Set to SMTP mode
				$phpmailer->isSMTP();
				$phpmailer->Host     = $smtp_settings['host'];
				$phpmailer->SMTPAuth = true;
				$phpmailer->Username = $smtp_settings['username'];
				$phpmailer->Password = $smtp_settings['password'];

				// Handle encryption properly
				if ( empty( $smtp_settings['encryption'] ) || $smtp_settings['encryption'] === 'none' ) {
					$phpmailer->SMTPSecure  = '';
					$phpmailer->SMTPAutoTLS = false;
				} else {
					$phpmailer->SMTPSecure  = $smtp_settings['encryption'];
					$phpmailer->SMTPAutoTLS = true;
				}

				$phpmailer->Port    = absint( $smtp_settings['port'] ?: 587 );
				$phpmailer->Timeout = 30;

			} catch ( Exception $e ) {
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
				error_log( 'SMTP configuration error: ' . $e->getMessage() );
			}
		}

		if ( $selected_provider ) {
			// try to set custom From headers if configured
			$from_email = $this->get_from_email();
			$from_name  = $this->get_from_name();

			if ( ! empty( $from_email ) && is_email( $from_email ) ) {
				try {
					$phpmailer->setFrom( $from_email, $from_name ? $from_name : get_bloginfo( 'name' ) );

				} catch ( Exception $e ) {
					// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
					error_log( 'PHPMAILER_INIT: Error setting from email: ' . $e->getMessage() );
				}
			}
		}

	}

	/**
	 * Override wp_mail from email
	 */
	public function custom_wp_mail_from( $from_email ) {

		$selected_provider = $this->get_selected_provider() ?? '';

		if ( ! $selected_provider ) {
			return $from_email;
		}

		$custom_from_email = $this->get_from_email() ?? '';

		// Only override if we have a custom from email set
		if ( ! empty( $custom_from_email ) && is_email( $custom_from_email ) ) {
			return $custom_from_email;
		}

		return $from_email;
	}

	/**
	 * Override wp_mail from name
	 */
	public function custom_wp_mail_from_name( $from_name ) {

		$selected_provider = $this->get_selected_provider() ?? '';

		if ( ! $selected_provider ) {
			return $from_name;
		}

		$custom_from_name = $this->get_from_name() ?? '';

		// Only override if we have a custom from name set
		if ( ! empty( $custom_from_name ) ) {
			return $custom_from_name;
		}

		return $from_name;
	}

	protected function format_provider_error( string $provider, string $summary, string $detail = '' ): string {
		return \IconvertEmailMarketer\Utils::format_provider_message( $provider, $summary, $detail );
	}

	protected function format_provider_success( string $provider, string $summary, string $detail = '' ): string {
		return \IconvertEmailMarketer\Utils::format_provider_message( $provider, $summary, $detail );
	}

}
