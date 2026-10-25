<?php
if ( ! defined( 'ABSPATH' ) ) {exit;}
if ( ! function_exists( 'iconvertem_is_hex_color' ) ) {
	function iconvertem_is_hex_color( $value ) {
		if ( strstr( $value, '#' ) && ctype_digit( substr( $value, 1 ) ) ) {
			return true;
		}

		return false;
	}
}

if ( ! function_exists( 'iconvertem_is_url' ) ) {
	function iconvertem_is_url( $value ) {
		return filter_var( $value, FILTER_VALIDATE_URL );
	}
}

if ( ! function_exists( 'iconvertem_array_accessible' ) ) {
	/**
	 * Determine whether the given value is array accessible.
	 *
	 * @param  mixed  $value
	 *
	 * @return bool
	 */
	function iconvertem_array_accessible( $value ): bool {
		return is_array( $value ) || $value instanceof ArrayAccess;
	}
}

if ( ! function_exists( 'iconvertem_array_exists' ) ) {
	/**
	 * Determine if the given key exists in the provided array.
	 *
	 * @param  \ArrayAccess|array  $array
	 * @param  string|int  $key
	 *
	 * @return bool
	 */
	function iconvertem_array_exists( $array, $key ): bool {
		if ( $array instanceof ArrayAccess ) {
			return $array->offsetExists( $key );
		}

		return array_key_exists( $key, $array );
	}
}

if ( ! function_exists( 'iconvertem_array_collapse' ) ) {
	/**
	 * Collapse an array of arrays into a single array.
	 *
	 * @param  iterable  $array
	 *
	 * @return array
	 */
	function iconvertem_array_collapse( iterable $array ): array {
		$results = array();

		foreach ( $array as $values ) {
			if ( ! is_array( $values ) ) {
				continue;
			}

			$results[] = $values;
		}

		return array_merge( array(), ...$results );
	}
}

if ( ! function_exists( 'iconvertem_value' ) ) {
	/**
	 * Return the default value of the given value.
	 *
	 * @param  mixed $value
	 * @return mixed
	 */
	function iconvertem_value( $value ) {
		return $value instanceof Closure ? $value() : $value;
	}
}

if ( ! function_exists( 'iconvertem_data_get' ) ) {
	/**
	 * Get an item from an array or object using "dot" notation.
	 *
	 * @param  mixed  $target
	 * @param  string|array|int|null  $key
	 * @param  mixed  $default
	 * @return mixed
	 */
	function iconvertem_data_get( $target, $key, $default = null ) {
		if ( is_null( $key ) ) {
			return $target;
		}

		$key = is_array( $key ) ? $key : explode( '.', $key );

		foreach ( $key as $i => $segment ) {
			unset( $key[ $i ] );

			if ( is_null( $segment ) ) {
				return $target;
			}

			if ( $segment === '*' ) {
				if ( ! is_array( $target ) ) {
					return iconvertem_value( $default );
				}

				$result = array();

				foreach ( $target as $item ) {
					$result[] = iconvertem_data_get( $item, $key );
				}

				return in_array( '*', $key ) ? iconvertem_array_collapse( $result ) : $result;
			}

			if ( iconvertem_array_accessible( $target ) && iconvertem_array_exists( $target, $segment ) ) {
				$target = $target[ $segment ];
			} elseif ( is_object( $target ) && isset( $target->{$segment} ) ) {
				$target = $target->{$segment};
			} else {
				return iconvertem_value( $default );
			}
		}

		return $target;
	}
}
