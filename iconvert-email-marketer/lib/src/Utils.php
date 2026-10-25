<?php


namespace IconvertEmailMarketer;

use SiteLeads\Core\AssetsRegistry;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Utils {


	public static function getFilePath( $path ) {
		return ICONVERTEM_PATH . "$path";
	}

	public static function getUrl( $path ) {
		return ICONVERTEM_URL . "/$path";
	}


	public static function getAssetName( $name ) {
		return AssetsRegistry::getAssetHandle( $name );
	}

	public static function isMailBuilderAdminPage() {
		global $pagenow;

		if ( substr( $pagenow, 0, - 4 ) === 'admin' ) {
            // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$page = isset( $_REQUEST['page'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['page'] ) ) : false;

			$available_pages = array(
				ICONVERTEM_PAGE_ID,
			);

			return in_array( $page, $available_pages, true );
		}

		return false;
	}


	public static function getPromoterIsActive() {
		return defined( 'ICONVERTPR_VERSION' );
	}

	public static function getContactForm7IsActive() {
		return class_exists( '\WPCF7' );
	}

	public static function getPluginStatus( $plugin_file ) {
		if ( is_plugin_active( $plugin_file ) ) {
			return 'active';
		}
		// Check if file exists to determine if installed
		$installed_plugins = get_plugins();
		if ( isset( $installed_plugins[ $plugin_file ] ) ) {
			return 'installed';
		}
		return 'not-installed';
	}

	public static function getPluginActivationUrl( $plugin_file ) {
		return add_query_arg(
			array(
				'action'        => 'activate',
				'plugin'        => rawurlencode( $plugin_file ),
				'plugin_status' => 'all',
				'paged'         => '1',
				'_wpnonce'      => wp_create_nonce( 'activate-plugin_' . $plugin_file ),
			),
			admin_url( 'plugins.php' )
		);
	}


	public static function getAnotherSmtpIsActive() {
		return static::getWpMailSmtpIsActive() ||
			static::getPostSmtpIsActive() ||
			static::getFluentSmtpIsActive() ||
			static::getGoSmtpIsActive() ||
			static::getSureMailIsActive() ||
			static::getEasyWpSmtpIsActive();
	}
	public static function getWpMailSmtpIsActive() {
		return function_exists( 'wp_mail_smtp' );
	}
	public static function getPostSmtpIsActive() {
		return defined( 'POST_SMTP_BASE' );
	}

	public static function getEasyWpSmtpIsActive() {
		return defined( 'EasyWPSMTP_PLUGIN_VERSION' );
	}
	public static function getFluentSmtpIsActive() {
		return defined( 'FLUENTMAIL_PLUGIN_FILE' );
	}
	public static function getGoSmtpIsActive() {
		return defined( 'GOSMTP_VERSION' );
	}
	public static function getSureMailIsActive() {
		return defined( 'SUREMAILS_FILE' );
	}

	public static function isEmptyButCanBeZero( $value ) {
		return empty( $value ) && ( $value !== 0 && $value !== '0' );
	}
	/**
	 * Include mbstring polyfill.
	 *
	 */
	public static function includeMbstringPolyfill() {

		static $included = false;

		if ( $included === true ) {
			return;
		}

		require_once easy_wp_smtp()->plugin_path . '/vendor_prefixed/symfony/polyfill-mbstring/Mbstring.php';
		require_once easy_wp_smtp()->plugin_path . '/vendor_prefixed/symfony/polyfill-mbstring/bootstrap.php';

		$included = true;
	}
	public static function get_site_urls() {

		$args = array(
			'utm_theme'      => get_template(),
			'utm_childtheme' => get_stylesheet(),

		);

		return array(
			'homepage_url'      => 'https://iconvertemailmarketer.com',
			'features_url'      => 'https://iconvertemailmarketer.com',
			'upgrade_url'       => add_query_arg( $args, 'https://iconvertemailmarketer.com/#pricing' ),
			'try_starter_site'  => add_query_arg( $args, 'https://iconvertemailmarketer.com' ),
			'documentation_url' => add_query_arg( $args, 'https://iconvertemailmarketer.com' ),
			'contact_url'       => add_query_arg( $args, 'https://iconvertemailmarketer.com/#support' ),
		);
	}

	public static function get_provider_display_name( string $provider ): string {
		$names = array(
			'smtp'             => 'SMTP',
			'send-grid'        => 'SendGrid',
			'mail-jet'         => 'MailJet',
			'campaign-monitor' => 'Campaign Monitor',
			'send-in-blue'     => 'Brevo',
			'get-response'     => 'GetResponse',
		);
		return $names[ $provider ] ?? ucfirst( $provider );
	}

	public static function format_provider_message( string $provider, string $summary, string $detail = '' ): string {
		$label = $provider ? '<strong>' . esc_html( self::get_provider_display_name( $provider ) ) . '</strong>' : '<strong>Email</strong>';
		$out   = $label . ': ' . esc_html( $summary );
		if ( $detail !== '' ) {
			$out .= '<br><small>' . esc_html( $detail ) . '</small>';
		}
		return $out;
	}

}
