<?php

namespace  IconvertEmailMarketer\App\Core\Admin\Actions;

use IconvertEmailMarketer\App\Core\Structure\TemplateTags\ContactFormTemplateTags;

use IconvertEmailMarketer\App\Core\Structure\TemplateTags\PromoterTemplateTags;


if ( ! defined( 'ABSPATH' ) ) {exit;}

class EmailTemplateTags {

	public function __construct() {
		add_filter( 'iconvertem_parse_content', array( $this, 'parseContent' ), 10, 2 );
	}

	public function parseContent( $content, $useRealData ) {
		return  $this->parseEmailTags( $content, $useRealData );
	}

	private function parseEmailTags( $content, $useRealData ) {
		$templateTags = $this->templateTags( $useRealData );

		$result = preg_replace_callback(
			'~{([^}]*)}~',
			function ( $m ) use ( $templateTags ) {
				return isset( $templateTags[ $m[0] ] ) ? $templateTags[ $m[0] ] : $m[0];
			},
			$content
		);

		return $result;
	}

	private function templateTags( $useRealData ) {

		$contactFormTags = new ContactFormTemplateTags( $useRealData );
		$promoterTags    = new PromoterTemplateTags( $useRealData );
		return array_merge(
			$contactFormTags->tags(),
			$promoterTags->tags()
		);
	}
}
