<?php

namespace  IconvertEmailMarketer\App\Core\Structure\TemplateTags;


if ( ! defined( 'ABSPATH' ) ) {exit;}

use IconvertEmailMarketer\Core\LodashBasic;

class PromoterTemplateTags implements InterfaceTagsTemplate {
	use TemplateTagsTrait;

	const EMAIL = '{promo_sub_email}';

	static $form_data = array();

	public function __construct( $useRealData ) {
		$this->init( $useRealData, DummyPromoterTemplateTags::class );
	}

	public static function storeContactFormCurrentEmailData( $form_data ) {
		if ( empty( $form_data ) || ! is_array( $form_data ) ) {
			return;
		}

		self::$form_data = $form_data;

	}

	public function tags() {
		return array(

			self::EMAIL => $this->tagLibrary->getEmail(),

		);
	}


	protected function getEmail() {
		return LodashBasic::get( static::$form_data, 'email', '' );
	}



	public static function groupTags(): array {
		return array(
			self::EMAIL => 'Email',
		);
	}
}
