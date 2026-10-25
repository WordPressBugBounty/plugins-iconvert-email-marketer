<?php

namespace  IconvertEmailMarketer\App\Core\Structure;

if ( ! defined( 'ABSPATH' ) ) {exit;}

class EditorLoader {
	private $dir = './';
	public function __construct() {
		$this->init();
	}

	public function init() {
		$this->dir = ICONVERTEM_PATH . '/build/blocks/';
		$this->findBlockIndexFile( $this->dir );
	}

	public function findBlockIndexFile( $dir ) {
		$dirs = array_diff( scandir( $dir ), array( '.', '..' ) );

		foreach ( $dirs as $item ) {
			$currentFile = $dir . '/' . $item;

			if ( is_dir( $currentFile ) ) {
				$this->findBlockIndexFile( $currentFile );
			} else {
				if ( $item == 'index.php' ) {
					$this->loadFile( $currentFile );
				}
			}
		}
	}

	public function loadFile( $fileName ) {
		require_once( $fileName );
	}
}
