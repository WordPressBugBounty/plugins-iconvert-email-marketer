<?php

namespace  IconvertEmailMarketer\App\Core\Blocks\Style\Properties;

if ( ! defined( 'ABSPATH' ) ) {exit;}

use IconvertEmailMarketer\Utils;

class BorderRadius extends CSSProperty {


	private $values = array();

	public function __construct( $value ) {
		parent::__construct( $value );
	}

	public function transform() {
		if ( empty( $this->value ) ) {
			return false;
		}
		if ( isset( $this->value['top'] ) && ! Utils::isEmptyButCanBeZero( $this->value['top'] ) ) {
			$this->values[] = $this->toCSString( $this->value['top'], 'border-top-left-radius' );
		}

		if ( isset( $this->value['right'] ) && ! Utils::isEmptyButCanBeZero( $this->value['right'] ) ) {
			$this->values[] = $this->toCSString( $this->value['right'], 'border-top-right-radius' );
		}
		if ( isset( $this->value['left'] ) && ! Utils::isEmptyButCanBeZero( $this->value['left'] ) ) {
			$this->values[] = $this->toCSString( $this->value['left'], 'border-bottom-left-radius' );
		}
		if ( isset( $this->value['bottom'] ) && ! Utils::isEmptyButCanBeZero( $this->value['bottom'] ) ) {
			$this->values[] = $this->toCSString( $this->value['bottom'], 'border-bottom-right-radius' );
		}

		return $this->wrapCSS( array_values( $this->values ) );
	}


}
