<?php

namespace  IconvertEmailMarketer\App\Core\Structure\TemplateTags;

use  IconvertEmailMarketer\App\Core\Structure\EmailTemplateSettings;

if ( ! defined( 'ABSPATH' ) ) {exit;}

trait TemplateTagsTrait {
	protected $useRealData = false;
	protected $tagLibrary  = null;

	protected function init( $useRealData, $dummyTemplateTagLibrary = false ) {
		$this->useRealData = $useRealData;

		if ( $this->useRealData || $dummyTemplateTagLibrary === false ) {
			$this->tagLibrary = $this;
		} else {
			$this->tagLibrary = new $dummyTemplateTagLibrary;
		}
	}
	/**
	 *
	 * @return \WC_Email
	 *
	 */
	protected function getEmail() {
		$emailSettings = EmailTemplateSettings::getInstance();
		$actionArgs    = $emailSettings->get( 'template_action_args', false );

		if ( $actionArgs !== false && isset( $actionArgs['email'] ) ) {
			return $actionArgs['email'];
		}

		return false;
	}


	/**
	 *
	 * @return string
	 *
	 */
	public function getCustomerNote() {
		$emailSettings = EmailTemplateSettings::getInstance();
		$actionArgs    = $emailSettings->get( 'template_action_args', false );

		if ( $actionArgs !== false && isset( $actionArgs['customer_note'] ) ) {
			return $actionArgs['customer_note'];
		}

		return '';
	}

	protected function getEmailObject() {
		if ( $this->getEmail() ) {
			return $this->getEmail()->object;
		}

		return false;
	}


	/**
	 *
	 * @return \WC_Order
	 */
	protected function getOrder() {
		$email_object = $this->getEmailObject();

		if ( $email_object instanceof \WC_Order ) {
			return $email_object;
		}

		return null;
	}

	/**
	 * @return \WC_Customer|null
	 */
	protected function getCustomer() {
		$email_object = $this->getEmailObject();
		$customer     = false;

		if ( $email_object instanceof \WP_User ) {
			$customer = new \WC_Customer( $email_object->ID );
		}

		// phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.FoundInControlStructure
		if ( $order = $this->getOrder() ) {
			if ( $order->get_customer_id() ) {
				$customer = new \WC_Customer( $order->get_customer_id() );
			} else {
				$customer = new \WC_Customer();
				$customer->set_first_name( $order->get_billing_first_name() );
				$customer->set_last_name( $order->get_billing_last_name() );
				$customer->set_email( $order->get_billing_email() );
			}
		}

		if ( ! $customer ) {
			$email = $this->getEmail();
			if ( property_exists( $email, 'to' ) ) {
				$email = $email->to;
				$user  = get_user_by( 'email', $email );
				if ( $user ) {
					$customer = new \WC_Customer( $user->ID );
				}
			}
		}

		return $customer;
	}
}
