<?php

namespace  IconvertEmailMarketer\App\Core\Blocks\Style\Properties;

if ( ! defined( 'ABSPATH' ) ) {exit;}


use IconvertEmailMarketer\Utils;

class Border extends CSSProperty {


	private $values = array();

	public function __construct( $value ) {
		parent::__construct( $value );
	}

	public function transform() {
		if ( empty( $this->value ) ) {
			return false;
		}

		foreach ( $this->value as $property => $value ) {
			$prefix = 'border';

			$cssString = $this->toCSString( $value, $property, $prefix . '-' . $property );

			if ( $cssString !== false ) {
				$this->values[] = $cssString;
			}
		}

		return implode( '; ', array_values( $this->values ) );
	}

	public function toCSString( $value, $name, $prefix = false ) {
		$value = $this->parseValue( $value );

		$propertyName = $this->propertyName( $prefix, false );
		if ( Utils::isEmptyButCanBeZero( $value ) || empty( $propertyName ) ) {
			return false;
		}

		return $this->propertyName( $prefix, false ) . ': ' . $value;
	}

	public function parseValue( $value ) {
		if ( ! is_array( $value ) ) {
			return false;
		}

		if ( ! isset( $value['width'] ) || empty( $value['width'] ) ) {
			return false;
		}

		$style = isset( $value['style'] ) ? $value['style'] : null;
		if ( empty( $style ) || $style === 'none' ) {
			return false;
		}

		$width_unit  = isset( $value['width']['unit'] ) ? $value['width']['unit'] : 'px';
		$width_value = isset( $value['width']['value'] ) ? $value['width']['value'] : 1;
		$width       = '';
		if ( ! Utils::isEmptyButCanBeZero( $width_value ) && ! Utils::isEmptyButCanBeZero( $width_unit ) ) {
			$width = $width_value . $width_unit;
		}
		$color = isset( $value['color'] ) ? $value['color'] : 'black';

		return $width . ' ' . $style . ' ' . $color;
	}


}
