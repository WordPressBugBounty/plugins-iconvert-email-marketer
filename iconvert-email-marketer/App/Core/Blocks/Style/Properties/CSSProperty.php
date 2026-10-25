<?php

namespace  IconvertEmailMarketer\App\Core\Blocks\Style\Properties;

use IconvertEmailMarketer\Utils;

if ( ! defined( 'ABSPATH' ) ) {exit;}

class CSSProperty {

	protected $value = false;
	protected $name  = '';

	public function __construct( $value, $name = '' ) {
		$this->value = $value;
		$this->name  = $name;
	}

	public function transform() {
		if ( empty( $this->name ) ) {
			return '';
		}

		return $this->toCSString( $this->value, $this->name );
	}

	public function toCSString( $value, $name, $prefix = false ) {
		$value = $this->parseValue( $value );

		if ( $value === false ) {
			return false;
		}
		$computedValue = $this->parseValue( $value );
		$propertyName  = $this->propertyName( $name, $prefix );
		if ( Utils::isEmptyButCanBeZero( $computedValue ) || empty( $propertyName ) ) {
			return false;
		}
		return "$propertyName: $computedValue";
	}

	public function wrapCSS( $array, $separator = '; ' ) {
		if ( ! $array ) {
			return '';
		}
		return implode( $separator, $array );
	}

	public function propertyName( $name, $prefix ) {
		$name = static::snakeToCss( $name );

		if ( $prefix ) {
			return $prefix . '-' . $name;
		}

		return $name;
	}

	public function parseValue( $value ) {
		if ( is_array( $value ) ) {
			return $this->parseValueWithUnit( $value );
		}

		if ( ! $value || trim( $value ) == '' ) {
			return false;
		}

		return $value;
	}

	public function parseValueWithUnit( $value ) {
		if ( ! isset( $value['value'] ) || Utils::isEmptyButCanBeZero( $value['value'] ) ) {
			return false;
		}

		return $value['value'] . $value['unit'];
	}

	public static function snakeToCss( $string ) {
		return strtolower( preg_replace( '/(?<!^)[A-Z]/', '-$0', $string ) );
	}
}
