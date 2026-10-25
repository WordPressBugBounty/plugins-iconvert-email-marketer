<?php

namespace  IconvertEmailMarketer\App\Core\Blocks\Style\Properties;

if ( ! defined( 'ABSPATH' ) ) {exit;}

class BackgroundImage extends CSSProperty {

	public function __construct( $value ) {
		parent::__construct( $value );
	}

	public function transform() {
		if ( empty( $this->value ) ) {
			return '';
		}
		if ( \iconvertem_is_hex_color( $this->value ) ) {
			return 'background-color: ' . $this->value;
		}

		if ( \iconvertem_is_url( $this->value ) ) {
			$css = array(
				'background-repeat: no-repeat',
				'background-size: cover',
				'background-position: center',
				'background-image: url(' . $this->value . ')',
			);
			return $this->wrapCSS( $css );
		}

		if ( ! empty( $this->value ) ) {
			return 'background: ' . $this->value;
		}
	}


}
