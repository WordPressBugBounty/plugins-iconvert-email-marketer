<?php

namespace  IconvertEmailMarketer\App\Core\Blocks;

use IconvertEmailMarketer\Core\Singleton;

if ( ! defined( 'ABSPATH' ) ) {exit;}

class BlockRegistry {

	use Singleton;
	private $blocksStack               = array();
	private $lastBlocksByName          = array();
	protected $addedPreRenderBlockHook = false;



	public static function register( $block_dir, $handle_class, $args = array() ) {
		static::getInstance()->addPreRenderHook();
		add_action(
			'init',
			function() use ( $block_dir, $handle_class ) {
				register_block_type_from_metadata(
					$block_dir,
					array(
						'render_callback' => function( ...$args ) use ( $handle_class ) {
							$instance = new $handle_class();
							return call_user_func_array( array( $instance, 'init' ), $args );
						},
					)
				);
			}
		);
	}


	public function addPreRenderHook() {
		if ( $this->addedPreRenderBlockHook ) {
			return;
		}
		$instance                      = $this;
		$this->addedPreRenderBlockHook = true;
		add_filter(
			'pre_render_block',
			function( $x, $inner_block, $parent_block ) use ( $instance ) {
				$instance->addBlockToStack( $inner_block );
			},
			10,
			3
		);
	}

	function addBlockToStack( $parsed_block ) {
		if ( empty( $parsed_block ) || ! is_array( $parsed_block ) ) {
			return;
		}
		if ( ! isset( $parsed_block['blockName'] ) ) {
			return;
		}
		$name = $parsed_block['blockName'];
		if ( ! is_string( $name ) || ! str_contains( $name, 'extendstudio' ) ) {
			return;
		}
		if ( ! isset( $this->lastBlocksByName[ $name ] ) ) {
			$this->lastBlocksByName[ $name ] = array();
		}
		$this->lastBlocksByName[ $name ][] = $parsed_block;
		$this->blocksStack[]               = $parsed_block;
	}

	function removeBlockFromStack( $parsed_block ) {
		if ( empty( $parsed_block ) || ! is_array( $parsed_block ) ) {
			return;
		}
		if ( ! isset( $parsed_block['blockName'] ) ) {
			return;
		}
		$name = $parsed_block['blockName'];
		if ( ! is_string( $name ) || ! str_contains( $name, 'extendstudio' ) ) {
			return;
		}
		if ( isset( $this->lastBlocksByName[ $name ] ) ) {
			array_pop( $this->lastBlocksByName[ $name ] );
		}

		array_pop( $this->blocksStack );
	}


	function getParentBlock() {
		$count = count( $this->blocksStack );

		return $count > 1 ? $this->blocksStack[ $count - 2 ] : null;
	}
}
