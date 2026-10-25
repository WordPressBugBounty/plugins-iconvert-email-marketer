<?php



namespace IconvertEmailMarketer\TransactionalEmailsDispatchers;



use IconvertEmailMarketer\App\Core\Structure\TemplateTags\ContactFormTemplateTags;
use IconvertEmailMarketer\Core\Singleton;
use IconvertEmailMarketer\EmailTypes\TransactionalEmail;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class ContactForm7Dispatcher extends BaseFormDispatcher {
	use Singleton;

	const EVENT_TYPE = 'afterContactForm7Submission';

	protected function __construct() {
		parent::__construct();
		add_action( 'wpcf7_mail_sent', array( $this, 'onContactForm7MailSent' ) );
	}

	public function getSendingFormData() {
		if ( ! class_exists( '\WPCF7_Submission' ) ) {
			return null;
		}
		// Get the submission data
		$submission = \WPCF7_Submission::get_instance();

		if ( ! $submission ) {
			return;
		}

		$posted_data = $submission->get_posted_data();

		$user_email = '';
		$user_name  = '';
		$subject    = '';
		$message    = '';

		foreach ( $posted_data as $key => $value ) {
			if ( is_email( $value ) ) {
				$user_email = sanitize_email( $value );
			}
			if ( is_string( $key ) && str_contains( $key, 'subject' ) && ! empty( $value ) ) {
				$subject = sanitize_text_field( $value );
			}
			if ( is_string( $key ) && str_contains( $key, 'name' ) && ! empty( $value ) ) {
				$user_name = sanitize_text_field( $value );
			}
			if ( is_string( $key ) && str_contains( $key, 'message' ) && ! empty( $value ) ) {
				$message = sanitize_text_field( $value );
			}
		}

		$data = array(
			'email'   => $user_email,
			'name'    => $user_name,
			'subject' => $subject,
			'message' => $message,
		);
		return $data;
	}


	public function onContactForm7MailSent( $contact_form ) {
		$form_id = $contact_form->id();

		if ( empty( $form_id ) ) {
			return;
		}
		$mailTemplatePost = $this->getTemplatesLinkedToFormId( $form_id );
		if ( empty( $mailTemplatePost ) || ( ! $mailTemplatePost instanceof \WP_Post ) ) {
			return;
		}
		$mailObj = new TransactionalEmail( $mailTemplatePost );

		$form_data = $this->getSendingFormData();
		if ( empty( $form_data ) ) {
			return;
		}
		$user_email = $form_data['email'] ?? null;
		ContactFormTemplateTags::storeContactFormCurrentEmailData( $form_data );
		if ( empty( $user_email ) ) {
			return;
		}

		$mailObj->sendEmail( $user_email );
	}
}
