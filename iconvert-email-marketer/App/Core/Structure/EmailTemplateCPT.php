<?php

namespace  IconvertEmailMarketer\App\Core\Structure;



use IconvertEmailMarketer\App\Core\API\APIBase;
use  IconvertEmailMarketer\App\Core\Traits\HasTemplate;
use  IconvertEmailMarketer\App\Core\Traits\IsSingleton;

use IconvertEmailMarketer\EmailTypes\BaseEmail;
use IconvertEmailMarketer\EmailTypes\NewsletterEmail;
use IconvertEmailMarketer\EmailTypes\TransactionalEmail;
use IconvertEmailMarketer\Flags;
use IconvertEmailMarketer\Settings\EmailProviderSettings;

if ( ! defined( 'ABSPATH' ) ) {exit;}

class EmailTemplateCPT {

	use IsSingleton;
	use HasTemplate;

	const POST_TYPE                             = 'icem-mail-tpl';
	const TAXONOMY_SLUG                         = 'icem-mail-tpl-taxonomy-type';
	const TAXONOMY_TRANSACTION_TYPE             = 'icem-mail-tpl-taxonomy-type-transactional';
	const TAXONOMY_NEWSLETTER_TYPE              = 'icem-mail-tpl-taxonomy-type-newsletter';
	const POST_META_EMAIL_SUBJECT               = '_iconvertem_email_subject';
	const POST_META_EMAIL_SENDING_EVENT_TYPE    = '_iconvertem_email_sending_event_type';
	const POST_META_EMAIL_SENDING_EVENT_FORM_ID = '_iconvertem_email_sending_event_form_id';
	const POST_META_EMAIL_SENDING_EVENT_DATA    = '_iconvertem_email_sending_event_data';
	const POST_META_DISABLED                    = '_iconvertem_email_disabled';
	public function __construct() {
		$this->init();
	}

	public function init() {

		add_action( 'init', array( $this, 'register' ) );

		$this->filters();
		add_action( 'rest_api_init', array( $this, 'register_rest_api_data' ) );
		add_action( 'init', array( $this, 'register_taxonomies' ), 100 );

	}

	public function get_taxonomy_terms() {

		$taxonomy = static::TAXONOMY_SLUG;

		// Get all terms for your taxonomy.
		$terms = get_terms(
			array(
				'taxonomy'   => $taxonomy,
				'hide_empty' => false,
			)
		);

		// Simplify the data before sending it to JS.
		$term_data = array();

		if ( ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$term_data[] = array(
					'id'   => $term->term_id,
					'name' => $term->name,
					'slug' => $term->slug,
				);
			}
		}
		return $term_data;
	}

	//used in activation class
	public function init_taxonomy_terms() {
		if ( taxonomy_exists( static::TAXONOMY_SLUG ) ) {
			return;
		}

		if ( Flags::get( 'templatesTaxonomiesInited' ) ) {
			return;
		}

		Flags::set( 'templatesTaxonomiesInited', true );

		$default_terms = array(
			array(
				'name' => static::TAXONOMY_TRANSACTION_TYPE,
				'slug' => static::TAXONOMY_TRANSACTION_TYPE,
			),
			array(
				'name' => static::TAXONOMY_NEWSLETTER_TYPE,
				'slug' => static::TAXONOMY_NEWSLETTER_TYPE,
			),
		);
		$this->register_taxonomies();
		foreach ( $default_terms as $term_data ) {
			wp_insert_term( $term_data['name'], static::TAXONOMY_SLUG );
		}
	}
	public function register_taxonomies() {

		if ( taxonomy_exists( static::TAXONOMY_SLUG ) ) {
			return;
		}

		register_taxonomy(
			static::TAXONOMY_SLUG,
			array( static::POST_TYPE ),
			array(
				'public'            => false,
				'hierarchical'      => false,
				'labels'            => array(
					'name'          => __( 'Template Types', 'iconvert-email-marketer' ),
					'singular_name' => __( 'Template Type', 'iconvert-email-marketer' ),
				),
				'query_var'         => false,
				'rewrite'           => false,
				'show_ui'           => false,
				'_builtin'          => true,
				'show_in_nav_menus' => false,
				'show_in_rest'      => true,
			)
		);
	}
	public function register() {
		register_post_type(
			static::POST_TYPE,
			array(
				'label'                 => __( 'iConvert Email', 'iconvert-email-marketer' ),
				'labels'                => array(
					'singular_name' => __( 'Mail Template', 'iconvert-email-marketer' ),
					'add_new_item'  => __( 'Add new Mail Template', 'iconvert-email-marketer' ),
					'add_new'       => __( 'New Mail Template', 'iconvert-email-marketer' ),
					'new_item'      => __( 'New Mail Template', 'iconvert-email-marketer' ),
				),
				'public'                => true,
				'has_archive'           => false,
				'show_in_rest'          => true,
				'show_in_menu'          => false,
				'show_in_nav_menus'     => false,
				'exclude_from_search'   => true,
				'publicly_queryable'    => true,
				//'show_ui'      => false,
				'hierarchical'          => false,
				'rewrite'               => false,
				// 'rest_base'    => 'icem-mail-tpl/template',
				'rest_controller_class' => 'WP_REST_Posts_Controller',
				'capabilities'          => array(
					'read'                   => 'edit_theme_options',
					'create_posts'           => 'edit_theme_options',
					'edit_posts'             => 'edit_theme_options',
					'edit_published_posts'   => 'edit_theme_options',
					'delete_published_posts' => 'edit_theme_options',
					'edit_others_posts'      => 'edit_theme_options',
					'delete_others_posts'    => 'edit_theme_options',
				),
				'map_meta_cap'          => true,
				'supports'              => array(
					'title',
					'editor',
					'custom-fields',
				),
			)
		);

		register_post_meta(
			static::POST_TYPE,
			static::POST_META_EMAIL_SUBJECT,
			array(
				'type'         => 'string',
				'single'       => true,
				'show_in_rest' => true,
			)
		);
		register_post_meta(
			static::POST_TYPE,
			static::POST_META_EMAIL_SENDING_EVENT_TYPE,
			array(
				'type'         => 'string',
				'single'       => true,
				'show_in_rest' => true,
			)
		);
		register_post_meta(
			static::POST_TYPE,
			static::POST_META_EMAIL_SENDING_EVENT_FORM_ID,
			array(
				'type'         => 'int',
				'single'       => true,
				'show_in_rest' => true,
			)
		);
		register_post_meta(
			static::POST_TYPE,
			static::POST_META_DISABLED,
			array(
				'type'         => 'string',
				'single'       => true,
				'show_in_rest' => true,
			)
		);

	}


	public function getEmailTemplateSubject( $object ) {
		$post_id = is_object( $object ) ? $object->id : $object['id'];
		$subject = get_post_meta( $post_id, static::POST_META_EMAIL_SUBJECT, true );

		return $subject;
	}

	public function updateEmailTemplateSubject( $value, $object ) {
		$post_id  = $object->ID;
		$original = get_post_meta( $post_id, static::POST_META_EMAIL_SUBJECT, true );
		update_post_meta( $post_id, static::POST_META_EMAIL_SUBJECT, $value, $original );
	}



	public function getEmailTemplateSendingEventType( $object ) {
		$post_id = is_object( $object ) ? $object->id : $object['id'];
		$result  = get_post_meta( $post_id, static::POST_META_EMAIL_SENDING_EVENT_TYPE, true );

		return $result;
	}

	public function updateEmailTemplateSendingEventType( $value, $object ) {
		$post_id  = $object->ID;
		$original = get_post_meta( $post_id, static::POST_META_EMAIL_SENDING_EVENT_TYPE, true );
		update_post_meta( $post_id, static::POST_META_EMAIL_SENDING_EVENT_TYPE, $value, $original );
	}

	public function getEmailTemplateSendingEventFormId( $object ) {
		$post_id = is_object( $object ) ? $object->id : $object['id'];
		$result  = get_post_meta( $post_id, static::POST_META_EMAIL_SENDING_EVENT_FORM_ID, true );

		return $result;
	}

	public function updateEmailTemplateSendingEventFormId( $value, $object ) {
		$post_id  = $object->ID;
		$original = get_post_meta( $post_id, static::POST_META_EMAIL_SENDING_EVENT_FORM_ID, true );
		update_post_meta( $post_id, static::POST_META_EMAIL_SENDING_EVENT_FORM_ID, $value, $original );
	}



	public function getEmailTemplateDisabled( $object ) {
		$post_id = is_object( $object ) ? $object->id : $object['id'];
		$result  = get_post_meta( $post_id, static::POST_META_DISABLED, true );

		return $result;
	}

	public function updateEmailTemplateDisabled( $value, $object ) {
		$post_id  = $object->ID;
		$original = get_post_meta( $post_id, static::POST_META_DISABLED, true );
		update_post_meta( $post_id, static::POST_META_DISABLED, $value, $original );
	}


	public function getTemplateCrudParams( \WP_REST_Request $request ) {
		$title                 = sanitize_text_field( $request->get_param( 'title' ) ?? __( 'New Template', 'iconvert-email-marketer' ) );
		$template_id           = sanitize_text_field( $request->get_param( 'template_id' ) ?? null );
		$email_type            = sanitize_text_field( $request->get_param( 'email_type' ) ?? null );
		$email_subject         = sanitize_text_field( $request->get_param( 'email_subject' ) ?? null );
		$sending_event_type    = sanitize_text_field( $request->get_param( 'sending_event_type' ) ?? null );
		$sending_event_form_id = sanitize_text_field( $request->get_param( 'sending_event_form_id' ) ?? null );
		$sending_event_data    = sanitize_text_field( $request->get_param( 'sending_event_data' ) ?? null );
		$post_id               = sanitize_text_field( $request->get_param( 'post_id' ) ?? null );
		$data                  = array(
			'title'                 => $title,
			'template_id'           => $template_id,
			'email_type'            => $email_type,
			'email_subject'         => $email_subject,
			'sending_event_type'    => $sending_event_type,
			'sending_event_form_id' => $sending_event_form_id,
			'sending_event_data'    => $sending_event_data,
			'post_id'               => $post_id,
		);

		return $data;
	}

	public function createTemplateFromRestApi( \WP_REST_Request $request ) {

		$data = $this->getTemplateCrudParams( $request );

		$email_type                = $data['email_type'];
		$title                     = $data['title'];
		$new_mail_template_post_id = null;

		switch ( $email_type ) {
			case static::TAXONOMY_TRANSACTION_TYPE:
				$new_mail_template_post_id = TransactionalEmail::createEmailPost( $data );
				break;
			case static::TAXONOMY_NEWSLETTER_TYPE:
				$new_mail_template_post_id = NewsletterEmail::createEmailPost( $data );
		}

		if ( empty( $new_mail_template_post_id ) ) {
			return new \WP_Error(
				'create_failed',
				__( 'Failed to create template.', 'iconvert-email-marketer' ),
				array( 'status' => 500 )
			);
		}

		return new \WP_REST_Response(
			array(
				'success' => true,
				'message' => __( 'Template created successfully.', 'iconvert-email-marketer' ),
				'data'    => array(
					'id'    => $new_mail_template_post_id,
					'title' => $title,
				),
			),
			200 // HTTP status code
		);
	}

	public function updateTemplateFromRestApi( \WP_REST_Request $request ) {
		$data = $this->getTemplateCrudParams( $request );

		$email_type = $data['email_type'];

		$updated_mail_template_post_id = null;
		switch ( $email_type ) {
			case static::TAXONOMY_TRANSACTION_TYPE:
				$updated_mail_template_post_id = TransactionalEmail::updateEmailPost( $data );
				break;
			case static::TAXONOMY_NEWSLETTER_TYPE:
				$updated_mail_template_post_id = NewsletterEmail::updateEmailPost( $data );
		}

		if ( empty( $updated_mail_template_post_id ) ) {
			return new \WP_Error(
				'create_failed',
				__( 'Failed to update the template.', 'iconvert-email-marketer' ),
				array( 'status' => 500 )
			);
		}

		return new \WP_REST_Response(
			array(
				'success' => true,
				'message' => __( 'Template updated successfully.', 'iconvert-email-marketer' ),
			),
			200 // HTTP status code
		);
	}
	public function register_rest_api_data() {

		$namespace = 'iconvertem/v1';

		register_rest_route(
			$namespace,
			'/create-template',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'createTemplateFromRestApi' ),
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},

			)
		);

		register_rest_route(
			$namespace,
			'/update-template',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'updateTemplateFromRestApi' ),
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},

			)
		);

		register_rest_route(
			$namespace,
			'/send-test-email-for-template',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'sendTestEmailForTemplate' ),
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},

			)
		);

		register_rest_field(
			static::POST_TYPE,
			static::POST_META_EMAIL_SUBJECT,
			array(
				'get_callback'    => array( $this, 'getEmailTemplateSubject' ),
				'update_callback' => array( $this, 'updateEmailTemplateSubject' ),
			)
		);

		register_rest_field(
			static::POST_TYPE,
			static::POST_META_EMAIL_SENDING_EVENT_TYPE,
			array(
				'get_callback'    => array( $this, 'getEmailTemplateSendingEventType' ),
				'update_callback' => array( $this, 'updateEmailTemplateSendingEventType' ),
			)
		);

		register_rest_field(
			static::POST_TYPE,
			static::POST_META_EMAIL_SENDING_EVENT_FORM_ID,
			array(
				'get_callback'    => array( $this, 'getEmailTemplateSendingEventFormId' ),
				'update_callback' => array( $this, 'updateEmailTemplateSendingEventFormId' ),
			)
		);

		register_rest_field(
			static::POST_TYPE,
			static::POST_META_DISABLED,
			array(
				'get_callback'    => array( $this, 'getEmailTemplateDisabled' ),
				'update_callback' => array( $this, 'updateEmailTemplateDisabled' ),
			)
		);
	}


	public function sendTestEmailForTemplate( $request ) {
		$id = sanitize_text_field( $request->get_param( 'template-id' ) ?? '' );

		$mails_raw = sanitize_textarea_field( $request->get_param( 'mails' ) ?? '' );
		$mails     = array_filter( array_map( 'trim', explode( "\n", $mails_raw ) ) );

		if ( empty( $mails ) ) {
			wp_send_json_error( new \WP_Error( 'empty_emails', esc_html__( 'Recipients list is empty', 'iconvert-email-marketer' ) ) );
		}

		$email_obj      = BaseEmail::findById( $id );
		$mail_error     = null;
		$mail_fail_func = function( $error ) use ( &$mail_error ) {
			$mail_error = $error;
		};
		add_action( 'wp_mail_failed', $mail_fail_func, 10, 1 );
		$result = $email_obj->sendEmail( $mails, true );
		remove_action( 'wp_mail_failed', $mail_fail_func );
		// file_put_contents( ABSPATH . "/content.html", $content );
		$provider = EmailProviderSettings::getInstance()->getSelectedProvider() ?? '';

		if ( $result === true ) {
			return new \WP_REST_Response(
				array(
					'success' => true,
					'message' => \IconvertEmailMarketer\Utils::format_provider_message( $provider, __( 'Test email sent successfully.', 'iconvert-email-marketer' ) ),
				),
				200
			);
		}

		$detail = '';
		if ( ! empty( $mail_error ) && $mail_error instanceof \WP_Error ) {
			$detail = implode( ' ', $mail_error->get_error_messages() );
		}

		return new \WP_Error(
			'no_sent',
			\IconvertEmailMarketer\Utils::format_provider_message( $provider, __( 'Failed to send test email.', 'iconvert-email-marketer' ), $detail ),
			array( 'status' => 500 )
		);

	}
	public function filters() {
		add_filter(
			'user_can_richedit',
			function ( $default ) {

				global $post;
				if ( ! isset( $post ) || empty( $post ) ) {
					return false;
				}
				if ( $post->post_type === static::POST_TYPE ) {
					return false;
				}

				return $default;
			}
		);

		add_filter(
			'use_block_editor_for_post',
			function ( $use_block_editor, $post ) {
				if ( ! isset( $post ) || empty( $post ) ) {
					return false;
				}
				if ( $post->post_type === static::POST_TYPE ) {
					return false;
				}

				return $use_block_editor;
			},
			10,
			2
		);

		add_filter(
			'wp_editor_expand',
			function ( $supports, $post_type ) {
				if ( ! isset( $post_type ) || empty( $post_type ) ) {
					return false;
				}
				if ( $post_type === static::POST_TYPE ) {
					return false;
				}

				return $supports;
			},
			10,
			2
		);

		$self = $this;
		add_action(
			'edit_form_after_title',
			function ( $post ) use ( $self ) {
				if ( ! isset( $post ) || empty( $post ) ) {
					return;
				}
				if ( $post->post_type !== static::POST_TYPE ) {
					return;
				}

				add_action( 'admin_footer', array( $self, 'printEditPageEntrypoint' ) );

			}
		);
	}
	public function printEditPageEntrypoint() {
		?>

		<div id="iconvertem-richtext-block-editor">

		</div>
		<?php

	}


	public function getSlug() {
		return static::POST_TYPE;
	}


	private function updateBlockInnerBlocks( $block ) {

		$block['innerContent'] = array();
		$inner_blocks          = isset( $block['innerBlocks'] ) ? $block['innerBlocks'] : array();

		if ( count( $inner_blocks ) ) {
			$block['innerContent'] = array_fill( 0, count( $inner_blocks ), null );

			foreach ( $inner_blocks as $index => $inner_block ) {
				$block['innerBlocks'][ $index ] = $this->updateBlockInnerBlocks( $inner_block );
			}
		}
		return $block;
	}


	public function serialize_blocks( $blocks ) {
		$blocks = is_array( $blocks ) ? $blocks : array( $blocks );
		return implode( '', array_map( array( $this, 'serialize_block' ), $blocks ) );
	}

	private function serialize_block( $block ) {

		$block_content = '';

		$index = 0;
		foreach ( $block['innerContent'] as $chunk ) {
			$block_content .= is_string( $chunk ) ? $chunk : $this->serialize_block( $block['innerBlocks'][ $index++ ] );
		}

		if ( ! is_array( $block['attrs'] ) ) {
			$block['attrs'] = array();
		}

		return $this->get_comment_delimited_block_content(
			$block['blockName'],
			$block['attrs'],
			$block_content
		);
	}

	private function get_comment_delimited_block_content( $block_name, $block_attributes, $block_content ) {
		if ( is_null( $block_name ) ) {
			return $block_content;
		}

		$serialized_block_name = strip_core_block_namespace( $block_name );

		array_walk_recursive(
			$block_attributes,
			function( &$item ) {

				if ( is_string( $item ) ) {
					//escape " that is not already escaped
					$item = preg_replace( '/(?<!\\\\)"/', '\\"', $item );
					$item = str_replace( '\\u0022', '\\"', $item );
					$item = str_replace( '\\u002d\\u002d', '--', $item );
					$item = str_replace( '\\u003c', '<', $item );
					$item = str_replace( '\\u003e', '>', $item );
					$item = str_replace( '\\u0026', '&', $item );
					$item = str_replace( 'u0026amp;', '&', $item );
					$item = str_replace( 'u003c', '<', $item );
					$item = str_replace( 'u003e', '>', $item );
					$item = str_replace( 'u0022', '\\"', $item );

				}
			}
		);

		$serialized_attributes = empty( $block_attributes ) ? '' : wp_json_encode( $block_attributes, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . ' ';

		if ( empty( $block_content ) ) {
			return sprintf( '<!-- wp:%s %s/-->', $serialized_block_name, $serialized_attributes );
		}

		$serialized_block = sprintf(
			'<!-- wp:%s %s-->%s<!-- /wp:%s -->',
			$serialized_block_name,
			$serialized_attributes,
			$block_content,
			$serialized_block_name
		);

		return $serialized_block;
	}


	public function getTemplateFromAPI( $post_id ) {
		$api      = new APIBase( 'templates/get/' . $post_id );
		$response = $api->get( array() );

		$content = '';
		if ( $response['success'] == 1 ) {
			$content = $response['data'][0]['content'];
			$blocks  = parse_blocks( $content );

			foreach ( $blocks as $index => $block ) {
				$blocks[ $index ] = $this->updateBlockInnerBlocks( $block );
			}

			$content = $this->serialize_blocks( $blocks );
		}

		return  $content;
	}

	public function templatePath() {
		return self::getTemplatePath( 'frontend/template-preview' );
	}

}
