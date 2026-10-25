<?php

namespace IconvertEmailMarketer\App\Core\Admin\Actions;

use  IconvertEmailMarketer\App\Core\Structure\EmailTemplateCPT;
use IconvertEmailMarketer\App\Core\Structure\TemplateTags\ContactFormTemplateTags;

use IconvertEmailMarketer\App\Core\Structure\TemplateTags\PromoterTemplateTags;
if ( ! defined( 'ABSPATH' ) ) {exit;}

class Assets {



	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'load' ) );
		add_filter( 'print_scripts_array', array( $this, 'filterKubioHandles' ) );
		add_filter( 'print_styles_array',  array( $this, 'filterKubioHandles' ) );

	}

	public function isMailBuilderEditor() {
		if ( ! function_exists( 'get_current_screen' ) ) {
			return false;
		}
		$screen = get_current_screen();

		return $screen && 'icem-mail-tpl' === $screen->post_type;
	}

	public function isKubioHandle( $handle, $registry ) {
		if ( false !== stripos( $handle, 'kubio' ) ) {
			return true;
		}
		$item = isset( $registry->registered[ $handle ] ) ? $registry->registered[ $handle ] : null;

		return $item && is_string( $item->src ) && false !== stripos( $item->src, 'plugins/kubio' );
	}


	public function filterKubioHandles( $handles ) {
		if ( ! $this->isMailBuilderEditor() ) {
			return $handles;
		}

		$registry = 'print_scripts_array' === current_filter() ? wp_scripts() : wp_styles();

		return array_values( array_filter( $handles, function ( $handle ) use ( $registry ) {
			return ! $this->isKubioHandle( $handle, $registry );
		} ) );
	}
	/**
	 * Load the scripts/styles
	 *
	 * @return void
	 */
	public function load() {
		$current_screen = get_current_screen();
		$post           = get_post();

		if(!$this->isMailBuilderEditor()){
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$action = isset( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : null;

		if ( $action !== 'edit' ) {
			return;
		}

		$rest_path            = rest_get_route_for_post( $post );
		$post_type            = $post->post_type;
		$block_editor_context = new \WP_Block_Editor_Context( array( 'post' => $post ) );

		$preload_paths = array(
			'/wp/v2/types?context=view',
			'/wp/v2/taxonomies?context=view',
			add_query_arg(
				array(
					'context'  => 'edit',
					'per_page' => -1,
				),
				rest_get_route_for_post_type_items( 'wp_block' )
			),
			add_query_arg( 'context', 'edit', $rest_path ),
			sprintf( '/wp/v2/types/%s?context=edit', $post_type ),
			'/wp/v2/users/me',
			array(
				rest_get_route_for_post_type_items( 'attachment' ),
				'OPTIONS',
			),
			array(
				rest_get_route_for_post_type_items( 'page' ),
				'OPTIONS',
			),
			array(
				rest_get_route_for_post_type_items( 'wp_block' ),
				'OPTIONS',
			),
			sprintf( '%s/autosaves?context=edit', $rest_path ),
			'/wp/v2/settings',
		);

		block_editor_rest_api_preload( $preload_paths, $block_editor_context );

		$jsURL  = ICONVERTEM_URL;
		$jsPATH = ICONVERTEM_PATH;

		$current_screen->is_block_editor( true );
		$script_handle = 'iconvertem-richtext-scripts';

		$script_url        = $jsURL . '/build/admin/edit-mail/index.js';
		$script_asset_path = $jsPATH . '/build/admin/edit-mail/index.asset.php';
		$script_asset      = file_exists( $script_asset_path )
			? require $script_asset_path
			: array(
				'dependencies' => array(),
				'version'      => time(),
			);
		wp_enqueue_media();
		wp_enqueue_script( $script_handle, $script_url, $script_asset['dependencies'], $script_asset['version'] );

		// Inline the Editor Settings.
		$settings = array(
			'disableCustomColors'         => get_theme_support( 'disable-custom-colors' ),
			'disableCustomFontSizes'      => get_theme_support( 'disable-custom-font-sizes' ),
			'isRTL'                       => is_rtl(),
			'__experimentalBlockPatterns' => array(),
			'image_video_ratio'           => ICONVERTEM_URL . 'assets/images/video_ratio_16-9.gif',
			'countdown_gif'               => home_url(),
			'social_icons_base_url'       => ICONVERTEM_URL . 'assets/images/icons',
			'mail_container_style'        => $this->iconvertem_default_mail_container_style(),
			'__unstableResolvedAssets'    => _wp_get_iframed_editor_assets(),
			'entityData'                  => array(
				'baseURL'  => '/wp/v2/icem-mail-tpl',
				'postType' => 'icem-mail-tpl',
				'id'       => $post->ID,
			),
			'wc_tags_list'                => array(
				'contactForm7' => array(
					'label'  => 'Contact Form',
					'values' => ContactFormTemplateTags::groupTags(),
				),
				'promoter'     => array(
					'label'  => 'Promoter',
					'values' => PromoterTemplateTags::groupTags(),
				),
			),
			'admin_url'                   => admin_url(),
			'back_button_url'             => $this->getBackButtonUrl( $post ),
			'is_optimizer_active'         => function_exists( 'optimizer_is_installed' ),
			'is_woocommerce_active'       => is_plugin_active( 'woocommerce/woocommerce.php' ),
			'campaign_url'                => add_query_arg( 'post_type', 'icem-mail-tpl', admin_url( 'edit.php' ) ),
			'outlineMode'                 => true,
		);

		wp_add_inline_script( $script_handle, 'window.iconvertemRichText = ' . wp_json_encode( $settings ) . ';' );

		// Preload server-registered block schemas.
		wp_add_inline_script(
			'wp-blocks',
			'wp.blocks.unstable__bootstrapServerSideBlockDefinitions(' . wp_json_encode( get_block_editor_server_block_settings() ) . ');'
		);

		// Editor default styles.
		wp_enqueue_script( 'wp-format-library' );
		wp_enqueue_style( 'wp-format-library' );
		wp_enqueue_style( 'wp-block-editor' );

		// Styles.
		wp_enqueue_style(
			'iconvertem-style', // Handle.
			$jsURL . '/build/admin/edit-mail/style-index.css', // Block editor CSS.
			array(
				'wp-edit-blocks',
			),
			time()
		);

	}

	public function getBackButtonUrl( $post ) {
		if ( empty( $post ) ) {
			return admin_url();
		}
		$terms = get_the_terms( $post->ID, EmailTemplateCPT::TAXONOMY_SLUG );
		if ( empty( $terms ) || count( $terms ) < 1 ) {
			return admin_url();
		}
		$current_term = $terms[0];
		$page_path    = '';
		switch ( $current_term->slug ) {
			case EmailTemplateCPT::TAXONOMY_TRANSACTION_TYPE:
				$page_path = 'iconvertem-transactions-emails';
				break;
			case EmailTemplateCPT::TAXONOMY_NEWSLETTER_TYPE:
				$page_path = 'iconvertem-newsletters-emails';
				break;
		}

		$url = admin_url() . '/admin.php?page=' . ICONVERTEM_PAGE_ID . '#' . $page_path;
		return $url;

	}

	/**
	 * Get the admin assets url
	 *
	 * @param mixed $filename
	 * @return string
	 */
	public function assets_url( $filename ) {
		return ICONVERTEM_URL . 'assets/' . $filename;
	}

	public function iconvertem_default_mail_container_style() {
		 return (object) array(
			 'backgroundColor' => null,
			 'typography'      => array(
				 'weight'        => null,
				 'family'        => null,
				 'transform'     => null,
				 'style'         => null,
				 'decoration'    => null,
				 'lineHeight'    => array(
					 'value' => null,
					 'unit'  => '',
				 ),
				 'letterSpacing' => array(
					 'value' => null,
					 'unit'  => 'px',
				 ),
				 'size'          => array(
					 'value' => null,
					 'unit'  => 'px',
				 ),
				 'color'         => '#111111',
			 ),
			 'defaultRowWidth' => array(
				 'value' => 600,
				 'unit'  => 'px',
			 ),
		 );
	}

}
