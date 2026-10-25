<?php


namespace IconvertEmailMarketer\EmailTypes;


use  IconvertEmailMarketer\App\Core\Structure\EmailTemplateCPT;
use IconvertEmailMarketer\Core\LodashBasic;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class TransactionalEmail extends BaseEmail {

	/**
	 * The event type for the email.
	 *
	 * @var string
	 */
	protected $sendingEventType = null;

	/**
	 * Optional form ID associated with the event.
	 *
	 * @var int|null
	 */
	protected $sendingEventFormId = null;

	/**
	 * Optional associative array containing event data. Here you can store information to detect the event we are tracking
	 *
	 * @var array|null
	 */
	protected $sendingEventData = null;

	/**
	 * Creates a new email post based on the provided options.
	 *
	 * @param array{
	 *     title: string,
	 *     template_id: int,
	 *     email_type: string,
	 *     email_subject: string,
	 *     sending_event_type: string,
	 *     sending_event_form_id: string|null,
	 *     sending_event_data: array[]|null,
	 * } $options Associative array of email options.
	 *
	 * @return int|null;
	 */
	public static function createEmailPost( $options ) {
		$sending_event_type    = LodashBasic::get( $options, 'sending_event_type' );
		$sending_event_form_id = LodashBasic::get( $options, 'sending_event_form_id' );
		$sending_event_data    = LodashBasic::get( $options, 'sending_event_data' );
		if ( empty( $sending_event_type ) ) {
			return null;
		}

		//we need a way to detect the actual event. It can be id or a data but it but most one of them
		if ( empty( $sending_event_form_id ) && $sending_event_data ) {
			return null;
		}
		$post_id = parent::createEmailPost( $options );

		update_post_meta( $post_id, EmailTemplateCPT::POST_META_EMAIL_SENDING_EVENT_TYPE, $sending_event_type );

		if ( ! empty( $sending_event_form_id ) ) {
			update_post_meta( $post_id, EmailTemplateCPT::POST_META_EMAIL_SENDING_EVENT_FORM_ID, $sending_event_form_id );
		}
		if ( ! empty( $sending_event_data ) ) {
			$event_data = $sending_event_data;
			if ( is_array( $event_data ) ) {
				$event_data = json_encode( $event_data );
			}
			if ( is_string( $event_data ) ) {
				update_post_meta( $post_id, EmailTemplateCPT::POST_META_EMAIL_SENDING_EVENT_DATA, $event_data );
			}
		}
		return $post_id;
	}
	public static function updateEmailPost( $options ) {
		$sending_event_type    = LodashBasic::get( $options, 'sending_event_type' );
		$sending_event_form_id = LodashBasic::get( $options, 'sending_event_form_id' );
		$sending_event_data    = LodashBasic::get( $options, 'sending_event_data' );
		if ( empty( $sending_event_type ) ) {
			return null;
		}

		//we need a way to detect the actual event. It can be id or a data but it but most one of them
		if ( empty( $sending_event_form_id ) && $sending_event_data ) {
			return null;
		}

		$post_id = parent::updateEmailPost( $options );

		update_post_meta( $post_id, EmailTemplateCPT::POST_META_EMAIL_SENDING_EVENT_TYPE, $sending_event_type );

		if ( ! empty( $sending_event_form_id ) ) {
			update_post_meta( $post_id, EmailTemplateCPT::POST_META_EMAIL_SENDING_EVENT_FORM_ID, $sending_event_form_id );
		}
		if ( ! empty( $sending_event_data ) ) {
			$event_data = $sending_event_data;
			if ( is_array( $event_data ) ) {
				$event_data = json_encode( $event_data );
			}
			if ( is_string( $event_data ) ) {
				update_post_meta( $post_id, EmailTemplateCPT::POST_META_EMAIL_SENDING_EVENT_DATA, $event_data );
			}
		}
		return $post_id;
	}


}
