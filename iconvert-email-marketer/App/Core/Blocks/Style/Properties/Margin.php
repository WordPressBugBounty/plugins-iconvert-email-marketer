<?php

namespace  IconvertEmailMarketer\App\Core\Blocks\Style\Properties;


if ( ! defined( 'ABSPATH' ) ) {exit;}

class Margin extends CSSProperty {


	private $values = array();

	public function __construct( $value ) {
		parent::__construct( $value );
	}

	public function transform() {
		if ( empty( $this->value ) ) {
			return false;
		}

		foreach ( $this->value as $property => $value ) {
			$prefix = 'margin';

			$cssString = $this->toCSString( $value, $property, $prefix . '-' . $property );

			if ( $cssString !== false ) {
				$this->values[] = $cssString;
			}
		}
		return $this->wrapCSS( array_values( $this->values ) );
	}

	public function toCSString( $value, $name, $prefix = false ) {
		$value = $this->parseValue( $value );

		if ( $value === false ) {
			return false;
		}

		return $this->propertyName( $prefix, false ) . ': ' . $value;
	}


}
