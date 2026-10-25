<?php


namespace IconvertEmailMarketer\EmailTypes;


use  IconvertEmailMarketer\App\Core\Structure\EmailTemplateCPT;
use IconvertEmailMarketer\App\Core\Structure\EmailTemplateSettings;
use IconvertEmailMarketer\Core\LodashBasic;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BaseEmail {

	/**
	 * The related post object.
	 *
	 * @var \WP_Post|null
	 */
	protected $emailPost = null;
	/**
	 * The subject of the email.
	 *
	 * @var string
	 */
	protected $emailSubject = null;

	/**
	 * The main content of the email.
	 *
	 * @var string|null
	 */
	protected $emailContent = null;

	public function __construct( $emailPost ) {
		$this->emailPost = $emailPost;
	}

	public function getEmailContent() {
		if ( ! empty( $this->emailContent ) ) {
			return $this->emailContent;
		}

		if ( empty( $this->emailPost ) ) {
			return null;
		}
		$this->emailContent = $this->emailPost->post_content;

		return $this->emailContent;
	}

	public function getEmailSubject() {
		if ( ! empty( $this->emailSubject ) ) {
			return $this->emailSubject;
		}
		if ( empty( $this->emailPost ) ) {
			return null;
		}

		$subject = get_post_meta( $this->emailPost->ID, EmailTemplateCPT::POST_META_EMAIL_SUBJECT, true );
		if ( empty( $subject ) ) {
			$subject = '';
		}

		$this->emailSubject = $subject;
		return $this->emailSubject;
	}

	public function getEmailPost() {
		return $this->emailPost;
	}

	public static function findById( $id ) {
		$post = get_post( $id );
		if ( empty( $post ) ) {
			return null;
		}

		return new static( $post );
	}
	/**
	 * Creates a new email post based on the provided options.
	 *
	 * @param array{
	 *     title: string,
	 *     template_id: int,
	 *     email_type: string,
	 *     email_subject: string
	 * } $options Associative array of email options.
	 *
	 * @return int|null;
	 */
	public static function createEmailPost( $options ) {
		$title          = LodashBasic::get( $options, 'title' );
		 $template_id   = LodashBasic::get( $options, 'template_id' );
		 $email_type    = LodashBasic::get( $options, 'email_type' );
		 $email_subject = LodashBasic::get( $options, 'email_subject' );

		$allowed_types    = array( EmailTemplateCPT::TAXONOMY_TRANSACTION_TYPE, EmailTemplateCPT::TAXONOMY_NEWSLETTER_TYPE );
		$email_type_is_ok = in_array( $email_type, $allowed_types );

		if ( empty( $title ) || ! $email_type_is_ok || ! $email_subject ) {
			return null;
		}
		$content = '';

		if ( ! empty( $template_id ) ) {
			$template_id = intval( $template_id );
			if ( ! is_nan( $template_id ) ) {

				$content = EmailTemplateCPT::getInstance()->getTemplateFromAPI( $template_id );
			}
		}

		//for blank
		if ( empty( $content ) ) {
			$content = '<!-- wp:extendstudio/mail-container {"key":"0.621752703753828","_style":{"color":"black","background":"","typography":{"weight":null,"family":null,"transform":null,"style":null,"decoration":null,"lineHeight":{"value":null,"unit":""},"letterSpacing":{"value":null,"unit":"px"},"size":{"value":null,"unit":"px"},"color":null},"defaultRowWidth":{"value":600,"unit":"px"}}} -->
							<!-- wp:extendstudio/row {"maxWidth":{"value":600,"unit":"px","updatedAtInsert":true}} -->
								<!-- wp:extendstudio/cell /-->
							<!-- /wp:extendstudio/row -->
						<!-- /wp:extendstudio/mail-container -->
						';
		}
		$post_id = wp_insert_post(
			array(
				'post_title'   => $title,
				'post_type'    => EmailTemplateCPT::getInstance()->getSlug(),
				'post_status'  => 'publish',
				'post_content' => $content,
			)
		);

		update_post_meta( $post_id, EmailTemplateCPT::POST_META_EMAIL_SUBJECT, $email_subject );
		wp_set_post_terms( $post_id, $email_type, EmailTemplateCPT::TAXONOMY_SLUG );

		return $post_id;
	}
	public static function updateEmailPost( $options ) {
		$post_id = LodashBasic::get( $options, 'post_id' );
		$title   = LodashBasic::get( $options, 'title' );

		$email_type    = LodashBasic::get( $options, 'email_type' );
		$email_subject = LodashBasic::get( $options, 'email_subject' );

		$allowed_types    = array( EmailTemplateCPT::TAXONOMY_TRANSACTION_TYPE, EmailTemplateCPT::TAXONOMY_NEWSLETTER_TYPE );
		$email_type_is_ok = in_array( $email_type, $allowed_types );

		if ( empty( $post_id ) || empty( $title ) || ! $email_type_is_ok || ! $email_subject ) {
			return null;
		}

		$changes = array(
			'ID'         => $post_id,
			'post_title' => $title,
		);

		wp_update_post( $changes );
		update_post_meta( $post_id, EmailTemplateCPT::POST_META_EMAIL_SUBJECT, $email_subject );

		return $post_id;
	}


	public function iconvertem_set_html_mail_content_type() {
		return 'text/html';
	}
	public function sendEmail( $mailToAddress = '', $show_dummy_data = false ) {
		if ( empty( $this->emailPost ) ) {
			return null;
		}
		if ( empty( $mailToAddress ) ) {
			return null;
		}
		$mails = array();
		if ( is_string( $mailToAddress ) ) {
			$mails = array_filter( array_map( 'trim', explode( "\n", $mailToAddress ) ) );
		} elseif ( is_array( $mailToAddress ) ) {
			$mails = $mailToAddress;
		}
		$emailSettings = EmailTemplateSettings::getInstance();

		$emailSettings->set( 'use_real_data', ! $show_dummy_data );
		$emailSettings->set( 'ic_email_template', $this->emailPost );

		add_filter( 'wp_mail_content_type', array( $this, 'iconvertem_set_html_mail_content_type' ) );
		$subject         = $this->getEmailSubject();
		$renderedContent = $this->getEmailRenderedContent();

		if ( empty( $renderedContent ) ) {
			return null;
		}
		$result = true;
		$this->load_required_php_mail_classes();
		foreach ( $mails as $email ) {
			try {
				$result = $result && wp_mail( $email, $subject, $renderedContent );
			} catch ( \Exception $e ) {
				$result = false;
			}
		}
		remove_filter( 'wp_mail_content_type', array( $this, 'iconvertem_set_html_mail_content_type' ) );

		return $result;
	}

	public function load_required_php_mail_classes() {
		require_once ABSPATH . WPINC . '/PHPMailer/PHPMailer.php';
		require_once ABSPATH . WPINC . '/PHPMailer/SMTP.php';
		require_once ABSPATH . WPINC . '/PHPMailer/Exception.php';
	}

	public function getEmailRenderedContent() {

		if ( empty( $this->emailPost ) ) {
			return null;
		}
		$params = array(
			'useRealData'       => true,
			'ic_email_template' => $this->emailPost,
		);
		extract( $params );
		ob_start();
		require ICONVERTEM_PATH . '/resources/views/frontend/template-preview.php';
		return ob_get_clean();

	}
}
