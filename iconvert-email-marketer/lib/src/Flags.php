<?php


namespace IconvertEmailMarketer;

use IconvertEmailMarketer\Core\LodashBasic;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Flags {
	private static $instance = null;
	private $flags           = array();
	private $is_dirty_value  = false;


	private function __construct() {

		$this->flags = get_option( 'iconvertem_instance_flags', array() );
		add_action( 'shutdown', array( $this, 'save' ) );

		//$default_settings = require_once ESPRESSO_ROOT_PATH . '/defaults/settings.php';
		$default_settings         = array();
		$this->flags['_settings'] = array_replace_recursive(
			$default_settings,
			LodashBasic::get( $this->flags, '_settings', array() )
		);
	}

	/**
	 * @param string $flag
	 * @param mixed $value
	 *
	 */
	public static function set( $flag, $value ) {
		static::getInstance()->setFlag( $flag, $value );
	}

	/**
	 * @param string $flag
	 * @param mixed $value
	 *

	 */
	public function setFlag( $flag, $value ) {
		$this->withFlags( 'set', $flag, $value );
	}

	/**
	 * @param $action
	 * @param null $flag
	 * @param null $data
	 *
	 * @return mixed|null
	 */
	private function withFlags( $action, $flag = null, $data = null ) {
		if ( $action === 'get-all' ) {
			return $this->flags;
		}

		if ( $action === 'get' ) {
			if ( isset( $this->flags[ $flag ] ) ) {
				return $this->flags[ $flag ];
			}

			$espresso_flags_defaults = apply_filters( 'iconvertem/instance-flags-default', array() );

			if ( isset( $espresso_flags_defaults[ $flag ] ) ) {
				return $espresso_flags_defaults[ $flag ];
			}

			return $data;
		}

		if ( $action === 'set' ) {
			$this->flags[ $flag ] = $data;
			$this->is_dirty_value = true;
			$this->save();
			return $data;
		}

		if ( $action === 'delete' ) {
			if ( isset( $this->flags[ $flag ] ) ) {
				unset( $this->flags[ $flag ] );
				$this->is_dirty_value = true;
			}
			$this->save();
			return null;
		}
	}

	/**
	 * @return static
	 */
	private static function getInstance() {

		if ( ! self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * @param string $flag
	 *
	 */
	public static function delete( $flag ) {
		static::getInstance()->deleteFlag( $flag );
	}

	/**
	 * @param string $flag
	 *
	 */
	public function deleteFlag( $flag ) {
		$this->withFlags( 'delete', $flag );
	}

	/**
	 * @param string $flag
	 * @param mixed $fallback
	 *
	 * @return mixed|null
	 */
	public static function get( $flag, $fallback = null ) {
		return static::getInstance()->getFlag( $flag, $fallback );
	}

	/**
	 * @param string $flag
	 * @param mixed $fallback
	 *
	 * @return mixed|null
	 */
	public function getFlag( $flag, $fallback = null ) {
		return $this->withFlags( 'get', $flag, $fallback );
	}

	public function save() {
		if ( $this->is_dirty_value ) {
			update_option( 'iconvertem_instance_flags', $this->flags, false );
			$this->is_dirty_value = false;
		}
	}

	public static function touch( $flag ) {
		static::set( $flag, time() );
	}

	public static function getSetting( $path, $fallback = null ) {
		$settings = static::getSettings();
		return LodashBasic::get( $settings, $path, $fallback );
	}

	public static function getSettings() {
		return static::get( '_settings', array() );
	}

	public static function setSettings( $settings ) {
		static::set( '_settings', $settings );
	}

	public static function setSetting( $path, $value ) {
		$settings = static::getSettings();
		LodashBasic::set( $settings, $path, $value );
		static::setSettings( $settings );
	}
}
