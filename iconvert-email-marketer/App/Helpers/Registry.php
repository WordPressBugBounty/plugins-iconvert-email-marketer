<?php
if ( ! defined( 'ABSPATH' ) ) {exit;}
/**
 * Helper functions for the Registry::class
 *
 */

if ( ! function_exists( 'iconvertem_registry_get' ) ) {
	/**
	 * iconvertem_registry_get Helper function to get a variable from the registry
	 *
	 * @param  string $name
	 * @param  mixed $default
	 * @return mixed value
	 */

	function iconvertem_registry_get( $name, $default = null ) {
		$registry = \IconvertEmailMarketer\App\Core\Registry::getInstance();
		return $registry->get( $name, $default );
	}
}

if ( ! function_exists( 'iconvertem_registry_set' ) ) {
	/**
	 * iconvertem_registry_set Helper function to set a variable into the registry
	 *
	 * @param  string $name
	 * @param  mixed $default
	 * @return void
	 */

	function iconvertem_registry_set( $name, $value ) {
		$registry = \IconvertEmailMarketer\App\Core\Registry::getInstance();
		$registry->set( $name, $value );
	}
}

if ( ! function_exists( 'iconvertem_registry_delete' ) ) {
	/**
	 * iconvertem_registry_delete Helper function to delete a variable fromt the registry
	 *
	 * @param  string $name
	 * @return void
	 */

	function iconvertem_registry_delete( $name ) {
		$registry = \IconvertEmailMarketer\App\Core\Registry::getInstance();
		$registry->delete( $name );
	}
}
