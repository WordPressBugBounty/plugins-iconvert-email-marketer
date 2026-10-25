<?php


namespace IconvertEmailMarketer\Api;


use IconvertEmailMarketer\App\Core\Structure\EmailTemplateCPT;
use IconvertEmailMarketer\Core\Singleton;
use IconvertEmailMarketer\Settings\EmailProviderSettings;
use IconvertEmailMarketer\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class AdminPageApi {
	use Singleton;

	protected function __construct() {

		$this->bootstrap();
	}

	public function bootstrap() {
		add_action(
			'rest_api_init',
			array( $this, 'registerRestApiEndpoints' )
		);
		$this->addEncriptionOfSettingsForApiFilters();
	}

	public function addEncriptionOfSettingsForApiFilters() {
		add_filter(
			'rest_prepare_icem-settings-data',
			array( $this, 'decryptSettingsDataForApi' ),
			10,
			3
		);
		add_filter(
			'rest_pre_insert_icem-settings-data',
			array( $this, 'encryptSettingsDataForApi' ),
			10,
			2
		);
	}

	public function decryptSettingsDataForApi( $response, $post, $request ) {
		// Only decrypt for admin screens
		if ( ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
			return $response;
		}
		$data = $response->get_data();

		$json = json_decode( $data['content']['raw'], true );

		if ( ! $json ) {
			return $response;
		}
		$json = EmailProviderSettings::decryptApiKeysAndAndPasswordsFromPostArray( $json );

		// replace content
		$data['content']['raw'] = wp_json_encode( $json );

		$response->set_data( $data );

		return $response;
	}
	public function encryptSettingsDataForApi( $prepared_post, $request ) {
		// Only decrypt for admin screens
		if ( ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
			return $prepared_post;
		}

		$json = json_decode( $prepared_post->post_content, true );

		if ( ! $json ) {
			return $prepared_post;
		}
		$json = EmailProviderSettings::encryptApiKeysAndAndPasswordsFromPostArray( $json );

		$prepared_post->post_content = wp_json_encode( $json );

		return $prepared_post;
	}

	public function registerRestApiEndpoints() {
		$namespace = 'iconvertem/v1';
		register_rest_route(
			$namespace,
			'/contact-form-7/forms',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'getContactForm7Forms' ),
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},

			)
		);
		register_rest_route(
			$namespace,
			'/promoter/forms',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'getPromoterFormsRestResponse' ),
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},

			)
		);
		register_rest_route(
			$namespace,
			'/templates',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'getTemplatesPaginated' ),
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},

			)
		);
	}

	public function getPromoterForms() {
		if ( ! Utils::getPromoterIsActive() ) {
			return false;
		}

		$args = array(
			'post_type'      => 'cs-promo-popups',
			'posts_per_page' => - 1,
		);

		$query = new \WP_Query( $args );

		if ( ! $query->have_posts() ) {
			return array();
		}

		$posts               = $query->posts;
		$ignored_popup_types = array();

		$filtered_posts = array_filter(
			$posts,
			function ( $post ) use ( $ignored_popup_types ) {
				$subscribe_block_search_term = 'wp:cspromo/subscribe';
				//only get ones with subscribe block
				if ( ! str_contains( $post->post_content, $subscribe_block_search_term ) ) {
					return false;
				}
				$popup_type = get_post_meta( $post->ID, 'popup_type', true );

				return ! in_array( $popup_type, $ignored_popup_types, true );
			}
		);

		$mapped = array();

		foreach ( $filtered_posts as $post ) {
			$mapped[] = array(
				'label' => $post->post_title,
				'value' => $post->ID,
			);
		}

		return $mapped;
	}

	public function getTemplatesPaginated( \WP_REST_Request $request ) {
		$type     = sanitize_text_field( $request->get_param( 'type' ) ) ?? null;
		$per_page = sanitize_text_field( $request->get_param( 'per_page' ) ) ?? 10;
		$page     = sanitize_text_field( $request->get_param( 'page' ) ) ?? 1;
		$search   = sanitize_text_field( $request->get_param( 'search' ) ) ?? null;

		$allowed_types = array( EmailTemplateCPT::TAXONOMY_TRANSACTION_TYPE, EmailTemplateCPT::TAXONOMY_NEWSLETTER_TYPE );
		$type_is_ok    = in_array( $type, $allowed_types );
		if ( ! $type_is_ok ) {
			return new \WP_Error(
				'email_type_not_ok',
				__( 'Email type provided is not allowed', 'iconvert-email-marketer' ),
				array( 'status' => 400 )
			);
		}

		$common_query_args = array(
			'post_type'   => EmailTemplateCPT::POST_TYPE,
			'post_status' => array( 'publish' ),
			'orderby'     => array(
				'date' => 'DESC',
				'ID'   => 'DESC',
			),
			'order'       => 'DESC',
			// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
			'tax_query'   => array(
				array(
					'taxonomy' => EmailTemplateCPT::TAXONOMY_SLUG,
					'field'    => 'name',
					'terms'    => array( $type ),
				),
			),
		);
		if ( ! empty( $search ) ) {
			$common_query_args['s']              = $search;
			$common_query_args['search_columns'] = array( 'post_title' );
		}
		$total_count_query = array_merge(
			$common_query_args,
			array(

				'posts_per_page' => 1,
				'fields'         => 'ids',
				'no_found_rows'  => false,

			)
		);
		$total_result = new \WP_Query(
			$total_count_query
		);

		$total = $total_result->found_posts;

		$templates_query = array_merge(
			$common_query_args,
			array(
				'posts_per_page' => $per_page,
				'paged'          => $page,
			)
		);

		$templates_query_results = new \WP_Query(
			$templates_query
		);
		$posts                   = $templates_query_results->posts;

		$templates = array();

		foreach ( $posts as $post ) {
			$post_id       = $post->ID;
			$new_item_data = array(
				'id'                                      => $post->ID,
				'title'                                   => array(
					'raw'      => $post->post_title,
					'rendered' => $post->post_title,
				),
				'date'                                    => $post->post_date,
				'date_gmt'                                => $post->post_date_gmt,
				'slug'                                    => $post->post_name,
				'modified'                                => $post->post_modified,
				'modified_gmt'                            => $post->post_modified_gmt,
				'type'                                    => $post->post_type,
				'link'                                    => get_permalink( $post->ID ),
				'globalStyle'                             => get_post_meta( $post_id, '_mail_container_style', true ),

				'icem-mail-tpl-taxonomy-type'             => wp_get_post_terms( $post_id, EmailTemplateCPT::TAXONOMY_SLUG, array( 'fields' => 'ids' ) ),
				'_iconvertem_email_sending_event_form_id' => get_post_meta( $post_id, '_iconvertem_email_sending_event_form_id', true ),
				'_iconvertem_email_sending_event_type'    => get_post_meta( $post_id, '_iconvertem_email_sending_event_type', true ),
				'_iconvertem_email_subject'               => get_post_meta( $post_id, '_iconvertem_email_subject', true ),
				'_iconvertem_email_disabled'              => get_post_meta( $post_id, '_iconvertem_email_disabled', true ),

			);
			$templates[] = $new_item_data;
		}

		$result = array(
			'total' => $total,
			'items' => $templates,
		);
		return new \WP_REST_Response(
			array(
				'success' => true,
				'message' => '',
				'data'    => $result,
			),
			200
		);
	}
	public function getPromoterFormsRestResponse() {

		$forms = $this->getPromoterForms();
		if ( empty( $forms ) ) {
			$forms = array();
		}

		return new \WP_REST_Response(
			array(
				'success' => true,
				'message' => '',
				'data'    => $forms,
			),
			200
		);
	}
	public function getContactForm7Forms() {
		$forms = array();
		if ( Utils::getContactForm7IsActive() ) {
			$contact_form7_items = array();
			$args                = array(
				'post_type'      => 'wpcf7_contact_form',
				'posts_per_page' => - 1,
			);
			if ( $data = get_posts( $args ) ) {
				foreach ( $data as $key ) {
					$contact_form7_items[] = array(
						'label' => $key->post_title,
						'value' => $key->ID,
					);
				}
			}
			if ( count( $contact_form7_items ) > 0 ) {
				$forms = $contact_form7_items;
			}
		}

		return new \WP_REST_Response(
			array(
				'success' => true,
				'message' => '',
				'data'    => $forms,
			),
			200
		);

	}
}
