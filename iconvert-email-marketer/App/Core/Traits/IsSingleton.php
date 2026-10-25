<?php
namespace  IconvertEmailMarketer\App\Core\Traits;

if ( ! defined( 'ABSPATH' ) ) {exit;}

trait IsSingleton {
	private static $instance = null;

	/**
	 * getInstance
	 *
	 * @return mixed
	 */
	public static function getInstance() {
		return isset( static::$instance )
			? static::$instance
			: static::$instance = new static();
	}
}
