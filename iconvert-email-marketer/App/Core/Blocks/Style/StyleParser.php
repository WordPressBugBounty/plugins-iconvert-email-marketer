<?php

namespace  IconvertEmailMarketer\App\Core\Blocks\Style;

use  IconvertEmailMarketer\App\Core\Blocks\Style\Properties\Align;

use  IconvertEmailMarketer\App\Core\Blocks\Style\Properties\Background;
use  IconvertEmailMarketer\App\Core\Blocks\Style\Properties\BackgroundImage;
use  IconvertEmailMarketer\App\Core\Blocks\Style\Properties\Border;
use  IconvertEmailMarketer\App\Core\Blocks\Style\Properties\BorderRadius;
use  IconvertEmailMarketer\App\Core\Blocks\Style\Properties\CSSProperty;
use  IconvertEmailMarketer\App\Core\Blocks\Style\Properties\Margin;
use  IconvertEmailMarketer\App\Core\Blocks\Style\Properties\Padding;
use  IconvertEmailMarketer\App\Core\Blocks\Style\Properties\Typography;
use IconvertEmailMarketer\App\Core\Blocks\Style\Properties\Width;


if ( ! defined( 'ABSPATH' ) ) {exit;}


class StyleParser {
	// map styles
	private $styleAttributes = array();
	private $styleMapper     = array();
	private $transformers    = array();

	public function __construct( $styleAttributes ) {
		$this->styleAttributes = $styleAttributes;
		$this->styleTransformersMapper();
		$this->mapper();
	}

	public function get() {
		return $this->styleMapper;
	}

	public function getProperty( $propertyName ) {
		if ( array_key_exists( $propertyName, $this->styleAttributes ) ) {
			return $this->styleAttributes[ $propertyName ];
		}

		return iconvertem_data_get( $this->styleAttributes, $propertyName );

	}

	public function css() {
		$css = implode( ';', array_values( $this->styleMapper ) );

		if ( empty( $css ) ) {
			return '';
		}

		return "{$css};";
	}

	public function cssExclude( $excluded ) {
		$css_array = array();

		foreach ( $this->styleMapper as $key => $css ) {
			if ( ! in_array( $key, $excluded ) ) {
				$css_array[] = $css;
			}
		}

		if ( empty( $css_array ) ) {
			return '';
		}

		$css = implode( '; ', $css_array );
		return  "{$css};";

	}

	public function mapper() {
		foreach ( $this->styleAttributes as $style => $value ) {
			if ( $this->transformerExists( $style ) ) {
				$this->parseStyle( $style, $value );
			}
		}
	}

	public function parseStyle( $style, $value ) {

		if ( $this->transformers[ $style ] ) {
			$transformer = new $this->transformers[ $style ]( $value, $style );
		} else {
			$transformer = new CSSProperty( $value, $style );
		}

		$val = $transformer->transform();
		if ( ! empty( $val ) ) {
			$this->styleMapper[ $style ] = $val;
		}
	}

	public function styleTransformersMapper() {
		$this->transformers = array(
			'background'      => Background::class,
			'backgroundImage' => BackgroundImage::class,
			//'color'           => Color::class,
			'typography'      => Typography::class,
			'border'          => Border::class,
			'borderRadius'    => BorderRadius::class,
			'margin'          => Margin::class,
			'padding'         => Padding::class,
			'textAlign'       => Align::class,
			'verticalAlign'   => Align::class,
			'width'           => Width::class,
		);
	}

	public function transformerExists( $name ) {
		return array_key_exists( $name, $this->transformers );
	}
}
