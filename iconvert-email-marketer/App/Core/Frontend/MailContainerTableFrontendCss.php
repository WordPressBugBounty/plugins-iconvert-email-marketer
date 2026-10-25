<?php

namespace  IconvertEmailMarketer\App\Core\Frontend;


use IconvertEmailMarketer\App\Core\Blocks\Style\StyleParser;

if ( ! defined( 'ABSPATH' ) ) {exit;}

class MailContainerTableFrontendCss {


	public static function getCss( $ic_email_template ) {
		if ( empty( $ic_email_template ) ) {
			return '';
		}

		$style = get_post_meta( $ic_email_template->ID, '_mail_container_style', true );
		if ( empty( $style ) ) {
			return '';
		}
		$parser = new StyleParser( $style );
		$css    = $parser->css();
		if ( empty( $css ) ) {
			return '';
		}
		return $css;
	}
}
