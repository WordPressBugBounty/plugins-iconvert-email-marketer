<?php

namespace  IconvertEmailMarketer\App\Core;

use  IconvertEmailMarketer\App\Core\Traits\IsSingleton;


if ( ! defined( 'ABSPATH' ) ) {exit;}
/**
 * Registry
 * Central storage class
 */

class Registry {
	use IsSingleton;

	/**
	 * registry
	 *
	 * @var array
	 */
	private $registry = array();

	/**
	 * Gets a variable stored in the Registry
	 *
	 * @param  string $name
	 * @param  mixed $default
	 * @return mixed
	 */
	public function get( $name, $default = null ) {
		if ( array_key_exists( $name, $this->registry ) ) {
			return $this->registry[ $name ];
		}

		return $default;
	}

	/**
	 * Gets a variable stored in the Registry
	 *
	 * @param  string $name
	 * @param  mixed $value
	 * @return mixed
	 */
	public function set( $name, $value ) {
		$this->registry[ $name ] = $value;
	}

	/**
	 * Deletes a stored variable from the Registry
	 *
	 * @param  string $name
	 * @return void
	 */
	public function delete( $name ) {
		if ( array_key_exists( $name, $this->registry ) ) {
			unset( $this->registry[ $name ] );
		}
	}
}
