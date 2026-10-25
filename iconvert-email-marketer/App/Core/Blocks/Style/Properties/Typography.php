<?php

namespace  IconvertEmailMarketer\App\Core\Blocks\Style\Properties;

if ( ! defined( 'ABSPATH' ) ) {exit;}

class Typography extends CSSProperty {

	private $values     = array();
	private $fontPrefix = array( 'weight', 'family', 'style', 'size' );
	private $textPrefix = array( 'transform', 'decoration' );
	public function __construct( $value ) {
		parent::__construct( $value );
	}

	public function transform() {
		if ( empty( $this->value ) ) {
			return false;
		}

		foreach ( $this->value as $property => $value ) {
			$css_string = -1;
			$prefix     = false;

			if ( in_array( $property, $this->fontPrefix ) ) {
				$prefix = 'font';
			} elseif ( in_array( $property, $this->textPrefix ) ) {
				$prefix = 'text';
			}

			//remove ' and " from font family as it does not work on yahoo both mobile and desktop web
			if ( $property === 'family' && is_string( $value ) && ! empty( $value ) ) {
				$value = str_replace( array( "'", '"' ), '', $value );
			}
			$css_string = $this->toCSString( $value, $property, $prefix );

			if ( ! empty( $css_string ) ) {
				$this->values[] = $css_string;
			}
		}

		return $this->wrapCSS( array_values( $this->values ) );
	}


}
