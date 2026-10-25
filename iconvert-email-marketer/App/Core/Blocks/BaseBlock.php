<?php

namespace  IconvertEmailMarketer\App\Core\Blocks;

use  IconvertEmailMarketer\App\Core\Blocks\Style\StyleParser;

if ( ! defined( 'ABSPATH' ) ) {exit;}

class BaseBlock {

	protected $attributes = array();
	protected $content    = '';
	protected $block      = array();
	protected $styles     = array();

	public function styleParser( $style ) {
		$style = $this->getAttribute( $style, array() );

		return new StyleParser( $style );
	}

	public function init( $attributes, $content, $block ) {

		$this->attributes = $attributes;
		$this->content    = $content;
		$this->block      = $block;

		$this->styles = array(
			'_block' => $this->styleParser( '_styleBlock' ),
			'_style' => $this->styleParser( '_style' ),
		);

		$result = $this->render();
		BlockRegistry::getInstance()->removeBlockFromStack( $block->parsed_block );

		return $result;
	}

	public function addStyle( $styleName ) {
		$this->styles[ $styleName ] = $this->styleParser( $styleName );
	}

	public function render() {
		$html = $this->html();
		$html = str_replace( '<inner-blocks></inner-blocks>', $this->content, $html );
		return $html;
	}

	public function getAttribute( $name, $default = false ) {
		if ( is_array( $this->attributes ) && array_key_exists( $name, $this->attributes ) ) {
			return $this->attributes[ $name ];
		}

		return $default;
	}

	public function html() {
		return $this->content;
	}

	public function getMaxWidth() {
		return $this->getAttribute(
			'maxWidth',
			array(
				'value' => 100,
				'unit'  => '%',
			)
		);
	}

	public function calculateWidthAndMaxWidth() {
		$max_width = $this->getAttribute(
			'maxWidth',
			array(
				'value' => 100,
				'unit'  => '%',
			)
		);
		return "
            max-width: {$max_width['value']}{$max_width['unit']}; width: {$max_width['value']}{$max_width['unit']};
        ";
	}

	/**
	TODO needs investing. FOr some templates like the one with baby clothes you have width with unit 600px for other you have without
	 * unit
	 */
	public function calculateWidth() {
		$max_width = $this->getAttribute( 'width', '100%' );
		if ( is_float( $max_width ) || is_int( $max_width ) ) {
			$max_width .= '';
		}
		if ( ! str_contains( $max_width, '%' ) ) {
			return "{$max_width}%";
		}
		return "{$max_width}";
	}

}
