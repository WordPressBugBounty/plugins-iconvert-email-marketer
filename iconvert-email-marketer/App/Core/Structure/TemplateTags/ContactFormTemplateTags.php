<?php

namespace  IconvertEmailMarketer\App\Core\Structure\TemplateTags;


use IconvertEmailMarketer\Core\LodashBasic;

if ( ! defined( 'ABSPATH' ) ) {exit;}

class ContactFormTemplateTags implements InterfaceTagsTemplate {
	use TemplateTagsTrait;
	const NAME        = '{contact_name}';
	const EMAIL       = '{contact_email}';
	const SUBJECT     = '{contact_subject}';
	const MESSAGE     = '{contact_message}';
	static $form_data = array();

	public function __construct( $useRealData ) {
		$this->init( $useRealData, DummyContactFormTemplateTags::class );
	}

	public static function storeContactFormCurrentEmailData( $form_data ) {
		if ( empty( $form_data ) || ! is_array( $form_data ) ) {
			return;
		}

		self::$form_data = $form_data;

	}

	public function tags() {
		return array(

			self::NAME    => $this->tagLibrary->getContactFormName(),
			self::EMAIL   => $this->tagLibrary->getContactFormEmail(),
			self::SUBJECT => $this->tagLibrary->getContactFormSubject(),
			self::MESSAGE => $this->tagLibrary->getContactFormMessage(),
		);
	}

	protected function getContactFormName() {
		return LodashBasic::get( static::$form_data, 'name', '' );
	}


	protected function getContactFormEmail() {
		return LodashBasic::get( static::$form_data, 'email', '' );
	}

	protected function getContactFormSubject() {
		return LodashBasic::get( static::$form_data, 'subject', '' );
	}
	protected function getContactFormMessage() {
		return LodashBasic::get( static::$form_data, 'message', '' );
	}

	public static function groupTags(): array {
		return array(

			self::NAME    => 'Name',
			self::EMAIL   => 'Email',
			self::SUBJECT => 'Subject',
			self::MESSAGE => 'Message',
		);
	}
}
