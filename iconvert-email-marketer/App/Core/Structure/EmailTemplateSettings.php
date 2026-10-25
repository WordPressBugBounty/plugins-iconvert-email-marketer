<?php

namespace  IconvertEmailMarketer\App\Core\Structure;

if ( ! defined( 'ABSPATH' ) ) {exit;}

use  IconvertEmailMarketer\App\Core\Traits\IsSingleton;

class EmailTemplateSettings {
	use IsSingleton;
	static private $settings = array();

	public static function set( $name, $value ) {
		self::$settings[ $name ] = $value;
	}

	public function get( $name, $default = false ) {
		if ( isset( self::$settings[ $name ] ) ) {
			return self::$settings[ $name ];
		}

		return $default;
	}
}
