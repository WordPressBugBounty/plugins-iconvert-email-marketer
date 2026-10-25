<?php


namespace IconvertEmailMarketer\TransactionalEmailsDispatchers;



use IconvertEmailMarketer\App\Core\Structure\TemplateTags\PromoterTemplateTags;
use IconvertEmailMarketer\Core\LodashBasic;
use IconvertEmailMarketer\Core\Singleton;
use IconvertEmailMarketer\EmailTypes\TransactionalEmail;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PromoterFormDispatcher extends BaseFormDispatcher {
	use Singleton;

	const EVENT_TYPE = 'afterPromoterSubmitFormSubmission';

	protected function __construct() {
		parent::__construct();
		add_action( 'iconvertpr_popup_subscribe_user', array( $this, 'onSubscribed' ) );
	}

	public function verifyNonce( $current_nonce, $popupID, $listID ) {
		add_filter(
			'nonce_user_logged_out',
			array( __CLASS__, 'nonceUserLoggedOut' )
		);

		$valid = false;
		if ( wp_verify_nonce( sanitize_text_field( wp_unslash( $current_nonce ) ), 'submit_form_' . $popupID . '_' . $listID ) ) {
			$valid = true;
		}

		remove_filter(
			'nonce_user_logged_out',
			array( __CLASS__, 'nonceUserLoggedOut' )
		);

		return $valid;
	}


	public function getIsValidNonce( $popupID ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$current_nonce = isset( $_POST['cspromo_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_POST['cspromo_wpnonce'] ) ) : '';

		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$listID = isset( $_POST['listID'] ) ? intval( sanitize_text_field( wp_unslash( $_POST['listID'] ) ) ) : 0;

		return $this->verifyNonce( $current_nonce, $popupID, $listID );
	}

	public function onSubscribed( $user_email ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$form_id = intval( sanitize_text_field( LodashBasic::get( $_POST, 'fields.popupID', 0 ) ) );

		//nonce check for all post params
		if ( ! $this->getIsValidNonce( $form_id ) ) {
			return;
		}

		if ( empty( $form_id ) || $form_id === 0 || is_nan( $form_id ) ) {
			return;
		}
		$mailTemplatePost = $this->getTemplatesLinkedToFormId( $form_id );
		if ( empty( $mailTemplatePost ) || ( ! $mailTemplatePost instanceof \WP_Post ) ) {
			return;
		}
		$form_data = array(
			'email' => $user_email,
		);
		PromoterTemplateTags::storeContactFormCurrentEmailData( $form_data );
		$mailObj = new TransactionalEmail( $mailTemplatePost );

		$mailObj->sendEmail( $user_email );
	}
}
