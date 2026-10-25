<?php

namespace IconvertEmailMarketer\Admin;


use IconvertEmailMarketer\App\Core\Structure\EmailTemplatesRepository;
use IconvertEmailMarketer\App\Core\Structure\EmailTemplateCategoryRepository;
use IconvertEmailMarketer\App\Core\Structure\EmailTemplateCPT;
use IconvertEmailMarketer\Constants;
use IconvertEmailMarketer\Core\AssetsRegistry;
use IconvertEmailMarketer\Core\Singleton;
use IconvertEmailMarketer\Settings\EmailProviderSettings;
use IconvertEmailMarketer\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class Admin {
	use Singleton;

	const CONTACT_FORM_7_PLUGIN_FILE_NAME = 'contact-form-7/wp-contact-form-7.php';
	const CONTACT_FORM_7_PLUGIN_SLUG      = 'contact-form-7';
	const  PROMOTER_PLUGIN_FILE_NAME      = 'iconvert-promoter/iconvert-promo-plugin.php';
	const  PROMOTER_PRO_PLUGIN_FILE_NAME  = 'iconvert-pro-promoter/iconvert-promo-plugin.php';
	const PROMOTER_PLUGIN_SLUG            = 'iconvert-promoter';
	const PROMOTER_PRO_PLUGIN_SLUG        = 'iconvert-pro-promoter';


	protected function __construct() {

		$this->bootstrap();
	}

	public function bootstrap() {

		add_action( 'init', array( $this, 'registerSettingsPostType' ), 9 );
		add_action( 'admin_enqueue_scripts', array( $this, 'loadAdminScripts' ) );

		add_action( 'admin_menu', array( $this, 'addMenuPage' ) );
		add_filter( 'plugin_row_meta', array( $this, 'addBuildNumberToInPluginsList' ), 10, 4 );
		add_action( 'admin_notices', array( $this, 'addConflictSmtpNotice' ) );
	}

	public function registerSettingsPostType() {

		if ( post_type_exists( Constants::$settingPostType ) ) {
			return;
		}

		$args = array(
			'label'               => __( 'Email marketer settings', 'iconvert-email-marketer' ),
			'public'              => false,
			'show_ui'             => false,
			'show_in_rest'        => true,
			'show_in_menu'        => false,
			'show_in_nav_menus'   => false,
			'rewrite'             => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'rest_base'           => Constants::$settingPostType,
			'capabilities'        => array(
				'read'                   => 'edit_theme_options',
				'create_posts'           => 'edit_theme_options',
				'edit_posts'             => 'edit_theme_options',
				'edit_published_posts'   => 'edit_theme_options',
				'delete_published_posts' => 'edit_theme_options',
				'edit_others_posts'      => 'edit_theme_options',
				'delete_others_posts'    => 'edit_theme_options',
			),
			'map_meta_cap'        => true,
			'supports'            => array(
				'title',
				'editor',
				'revisions',
			),
		);

		register_post_type( Constants::$settingPostType, $args );
	}

	public function addConflictSmtpNotice() {
		if ( ! Utils::getAnotherSmtpIsActive() ) {
			return;
		}
		$mail_provider_name = '';
		if ( Utils::getWpMailSmtpIsActive() ) {
			$mail_provider_name = 'WP Mail SMTP';
		}
		if ( Utils::getPostSmtpIsActive() ) {
			$mail_provider_name = 'Post SMTP';
		}
		if ( Utils::getEasyWpSmtpIsActive() ) {
			$mail_provider_name = 'Easy WP SMTP';
		}
		if ( Utils::getFluentSmtpIsActive() ) {
			$mail_provider_name = 'FluentSMTP';
		}
		if ( Utils::getGoSmtpIsActive() ) {
			$mail_provider_name = 'GoSMTP';
		}
		if ( Utils::getSureMailIsActive() ) {
			$mail_provider_name = 'SureMail';
		}

		// translators: %s is the name of the conflicting mail provider plugin.
		$message = sprintf(
			// translators: %s is the name of the conflicting mail provider plugin.
			__( 'iConvert Email Marketer issue. Conflict with the plugin: %s. Only one Mail Provider plugin should be active. Having multiple email providers plugins active can result in unexpected behaviour.', 'iconvert-email-marketer' ),
			'<b>' . $mail_provider_name . '</b>'
		);
		echo esc_html( '<div class="error iconvertem-notice"><p>' . $message . '</p></div>' );
	}
	public function getNewTemplateBackendData() {

		$categories    = EmailTemplateCategoryRepository::getInstance();
		$templates     = EmailTemplatesRepository::getInstance();
		$terms         = $categories->all();
		$all_templates = $templates->all();
		if ( empty( $all_templates ) ) {
			$all_templates = array();
		}
		if ( empty( $terms ) ) {
			$terms = array();
		}
		$new_template_data = array(
			'terms'            => $terms,
			'templates'        => $all_templates,
			'defaultImage'     => ICONVERTEM_URL . 'assets/images/no-image-placeholder.svg',
			'blankImage'       => ICONVERTEM_URL . 'assets/images/no-image-placeholder.svg',

			'allCategories'    => implode( ', ', wp_list_pluck( $terms, 'slug' ) ),
		);

		return $new_template_data;
	}

	public function getPromoterPluginSlug() {
		$pro_plugin_status = Utils::getPluginStatus( static::PROMOTER_PRO_PLUGIN_FILE_NAME );
		if ( $pro_plugin_status !== 'not-installed' ) {
			return static::PROMOTER_PRO_PLUGIN_SLUG;
		} else {
			return static::PROMOTER_PLUGIN_SLUG;
		}
	}
	public function getPromoterPluginFileName() {
		$pro_plugin_status = Utils::getPluginStatus( static::PROMOTER_PRO_PLUGIN_FILE_NAME );
		if ( $pro_plugin_status !== 'not-installed' ) {
			return static::PROMOTER_PRO_PLUGIN_FILE_NAME;
		} else {
			return static::PROMOTER_PLUGIN_FILE_NAME;
		}
	}
	public function getPromoterStatus() {
		return Utils::getPluginStatus( $this->getPromoterPluginFileName() );
	}

	public function getBackendData() {
		$current_user = wp_get_current_user();

		$new_template_data     = $this->getNewTemplateBackendData();
		$mail_taxonomies_terms = EmailTemplateCPT::getInstance()->get_taxonomy_terms();

		$settings = array(
			'base_url'                             => site_url(),
			'admin_url'                            => admin_url(),
			'ajax_url'                             => admin_url( 'admin-ajax.php' ),
			'plugin_url'                           => ICONVERTEM_URL,
			'admin_plugins_url'                    => admin_url( 'plugins.php' ),
			'current_user_email'                   => $current_user->user_email,
			'ajax_nonce'                           => wp_create_nonce( 'ajaxNonce' ),
			'new_template_data'                    => $new_template_data,
			'main_taxonomies_terms'                => $mail_taxonomies_terms,
			'send_settings_from_email'             => EmailProviderSettings::getInstance()->getFromEmail(),
			'withInternalTools'                    => defined( 'ICONVERTEM_WITH_INTERNAL_TOOLS' ) && \ICONVERTEM_WITH_INTERNAL_TOOLS,
			'contact_form_7_plugin_slug'           => static::CONTACT_FORM_7_PLUGIN_SLUG,
			'contact_form_7_plugin_status'         => Utils::getPluginStatus( static::CONTACT_FORM_7_PLUGIN_FILE_NAME ),
			'contact_form_7_plugin_activation_url' => Utils::getPluginActivationUrl( static::CONTACT_FORM_7_PLUGIN_FILE_NAME ),
			'promoter_plugin_slug'                 => $this->getPromoterPluginSlug(),
			'promoter_plugin_status'               => Utils::getPluginStatus( $this->getPromoterPluginFileName() ),
			'promoter_plugin_activation_url'       => Utils::getPluginActivationUrl( $this->getPromoterPluginFileName() ),
			'noTemplatesImage' => ICONVERTEM_URL . 'assets/images/no-templates.svg',
			'logo_url' => ICONVERTEM_URL . 'assets/images/iconvert-email-marketer-logo.svg',
			'promoter_create_form_url'             => add_query_arg(
				array(
					'page'  => 'iconvertpr-promoter',
					'route' => 'promo.create',
				),
				admin_url( 'admin.php' )
			),
			'site_urls'                            => Utils::get_site_urls(),
		);

		return $settings;
	}

	public function loadBackendData() {
		$settings = $this->getBackendData();

		wp_add_inline_script(
			'jquery',
			sprintf(
				'window.iconvertemUtils = %s;',
				wp_json_encode( $settings )
			)
		);
	}

	public function loadAdminScripts() {
		AssetsRegistry::enqueueStyle( 'admin/admin-all-pages' );
		if ( ! Utils::isMailBuilderAdminPage() ) {
			return;
		}
		wp_enqueue_script( 'plugin-install' );
		wp_enqueue_script( 'updates' );
		AssetsRegistry::enqueueAssetGroup( 'admin/admin-pages' );

		wp_add_inline_script(
			AssetsRegistry::getAssetHandle( 'admin/admin-pages' ),
			sprintf(
				'window.iconvertemInit();'
			)
		);

		wp_enqueue_media();

		$this->loadBackendData();

		$preload_data = array(

			'/wp/v2/types?context=view',
			//this is needed for the product category and product slug
			'/wp/v2/types?context=edit',
			'/wp/v2/icem-settings-data?context=edit',
			'/wp/v2/templates?context=edit&per_page=-1',

		);

		$context = new \WP_Block_Editor_Context( array( 'name' => 'core/edit-site' ) );

		//Added in the 'wp-api-fetch' inline script
		if ( function_exists( 'block_editor_rest_api_preload' ) ) {
			\block_editor_rest_api_preload( $preload_data, $context );
		}
	}



	public function addMenuPage() {

		\add_menu_page(
			__( 'iConvert Email', 'iconvert-email-marketer' ),
			__( 'iConvert Email', 'iconvert-email-marketer' ),
			'manage_options',
			ICONVERTEM_PAGE_ID,
			array( $this, 'printAdminPageEntrypoint' ),
			ICONVERTEM_URL . '/assets/images/main-menu-icon.png',
			20
		);

		$this->addSubmenuPages();
	}

	public function addSubmenuPages() {
		$transaction_emails_slug = 'iconvertem-transactions-emails';
		$newsletters_emails_slug = 'iconvertem-newsletters-emails';
		$sending_settings_slug   = 'iconvertem-sending-settings';
		$email_log_slug          = 'iconvertem-email-log';
		$upgrade_to_pro_slug     = 'iconvertem-upgrade-to-pro';

		$submenu_slugs_array = array( $transaction_emails_slug, $newsletters_emails_slug, $sending_settings_slug, $email_log_slug, $upgrade_to_pro_slug );

		\add_submenu_page(
			ICONVERTEM_PAGE_ID,
			__( 'Newsletters', 'iconvert-email-marketer' ),
			__( 'Newsletters', 'iconvert-email-marketer' ),
			'manage_options',
			$newsletters_emails_slug,
			array( $this, 'printAdminPageEntrypoint' )
		);

		\add_submenu_page(
			ICONVERTEM_PAGE_ID,
			__( 'Sending Settings', 'iconvert-email-marketer' ),
			__( 'Sending Settings', 'iconvert-email-marketer' ),
			'manage_options',
			$sending_settings_slug,
			array( $this, 'printAdminPageEntrypoint' )
		);
		//        \add_submenu_page(
		//            ICONVERTEM_PAGE_ID,
		//            __( 'Email Log'),
		//            __( 'Email Log' ),
		//            'manage_options',
		//            $email_log_slug,
		//            array( $this, 'printAdminPageEntrypoint' ),
		//        );


		if ( apply_filters( 'iconvertem_feature_available_only_in_pro', true ) ) {
			\add_submenu_page(
				ICONVERTEM_PAGE_ID,
				__( 'Upgrade to PRO', 'iconvert-email-marketer' ),
				sprintf(
					'<span class="iconvertem-upgrade-menu-item"><span>%s</span>%s</span>',
					'<span class="dashicons dashicons-superhero-alt"></span>',
					__( 'Upgrade to PRO', 'iconvert-email-marketer' )
				),
				'manage_options',
				$upgrade_to_pro_slug,
				array( $this, 'printAdminPageEntrypoint' )
			);

			wp_add_inline_style(
				'admin-menu',
				'.iconvertem-upgrade-menu-item { display: flex; align-items: center; gap: 5px }' .
				'.iconvertem-upgrade-menu-item .dashicons { color: #ffc932; font-size: 24px; vertical-align: middle; margin: -0.15em .15em 0 0; }' .
				'*:not(.current):not(:hover) > .iconvertem-upgrade-menu-item .dashicons {opacity: 0.7; }'
			);

		}

		global $submenu;
		if ( isset( $submenu[ ICONVERTEM_PAGE_ID ] ) ) {
			foreach ( $submenu[ ICONVERTEM_PAGE_ID ] as $index => $submenu_item ) {
				$submenu_slug = $submenu_item[2];
				if ( $submenu_slug === ICONVERTEM_PAGE_ID ) {

					//update the submenu label for the submenu generated from the primary menu page
					$submenu[ ICONVERTEM_PAGE_ID ][ $index ][0] = __( 'Transactional emails', 'iconvert-email-marketer' );

					$submenu[ ICONVERTEM_PAGE_ID ][ $index ][2] = add_query_arg(
						array(
							'page' => ICONVERTEM_PAGE_ID,
						),
						admin_url( 'admin.php' )
					) . '#' . $transaction_emails_slug;
				}
				if ( in_array( $submenu_slug, $submenu_slugs_array ) ) {
					$submenu[ ICONVERTEM_PAGE_ID ][ $index ][2] = add_query_arg(
						array(
							'page' => ICONVERTEM_PAGE_ID,
						),
						admin_url( 'admin.php' )
					) . '#' . $submenu_slug;
				}
			}
		}
	}


	public function printAdminPageEntrypoint() {
		?>

		<div id="iconvertem-admin-page">

		</div>
		<?php
	}

	public function addBuildNumberToInPluginsList( $plugin_meta, $plugin_file ) {
		$entry_file = plugin_basename( ICONVERTEM_ENTRY_FILE );
		if ( $plugin_file === $entry_file ) {
			$plugin_meta[0] = "{$plugin_meta[0]} (build: " . ICONVERTEM_BUILD_NUMBER . ')';
		}

		return $plugin_meta;
	}
}
