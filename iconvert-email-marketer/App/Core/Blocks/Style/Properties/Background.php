<?php

namespace  IconvertEmailMarketer\App\Core\Blocks\Style\Properties;

if ( ! defined( 'ABSPATH' ) ) {exit;}

class Background extends CSSProperty {

	public function __construct( $value ) {
		parent::__construct( $value );
	}

	public function transform() {
		if ( empty( $this->value ) ) {
			return '';
		}
			return 'background-color: ' . $this->value;
	}


}
