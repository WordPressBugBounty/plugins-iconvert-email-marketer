<?php



namespace IconvertEmailMarketer\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class SvgFilter {
	use Singleton;

	protected function __construct() {
		add_action( 'wp_kses_allowed_html', array( $this, 'getSvgKsesAllowedElements' ) );
	}


	function getSvgKsesAllowedElements( $allowed_html = array() ) {
			$svg_elements = array(
				'svg'     =>
					array(
						'fill',
						'xmlns',
						'viewbox',
						'id',
						'data-name',
						'width',
						'height',
						'version',
						'xmlns:xlink',
						'x',
						'y',
						'enable-background',
						'xml:space',
					),
				'path'    =>
					array(
						'fill',
						'd',
						'id',
						'class',
						'data-name',
						'fill-rule',
						'clip-rule',
					),
				'g'       =>
					array(
						'fill',
						'id',
						'stroke',
						'stroke-width',
						'fill',
						'fill-rule',
						'transform',
					),
				'title'   =>
					array(),
				'polygon' =>
					array(
						'id',
						'points',
					),
				'rect'    =>
					array(
						'fill',
						'stroke',
						'x',
						'y',
						'width',
						'height',
						'transform',
						'rx',
					),
				'circle'  =>
					array(
						'fill',
						'cx',
						'cy',
						'r',
					),
				'ellipse' =>
					array(
						'fill',
						'cx',
						'cy',
						'rx',
						'ry',
					),
			);

			$shared_attrs = array( 'data-*', 'id', 'class' );

			foreach ( $svg_elements as $element => $attrs ) {
				if ( ! isset( $allowed_html[ $element ] ) ) {
					$allowed_html[ $element ] = array();
				}

				$allowed_html[ $element ] = array_merge( $allowed_html[ $element ], array_fill_keys( array_merge( $attrs, $shared_attrs ), true ) );
			}

			return $allowed_html;

	}



}
