<?php


namespace IconvertEmailMarketer\App\Core\Blocks;

use IconvertEmailMarketer\App\Core\Blocks\BaseBlock;
use IconvertEmailMarketer\App\Core\Blocks\BlockRegistry;
use IlluminateAgnostic\Str\Support\Arr;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class DividerBlock extends BaseBlock {

	/**
	 * @
	 */
	public function html() {
		$style       = $this->getAttribute( '_style' );
		$width_obj   = Arr::get( $style, 'width' );
		$width_value = '100%';
		if ( isset( $width_obj['value'] ) && ! empty( $width_obj['value'] ) && isset( $width_obj['unit'] ) && ! empty( $width_obj['unit'] ) ) {
			$width_value = $width_obj['value'] . $width_obj['unit'];
		}
		ob_start();
		?>
		<table class="wp-block-extendstudio-divider" border="0" cellPadding="0" cellSpacing="0" role="presentation" width="100%">
			<tbody>
				<tr>
					<td valign="top" style="<?php echo esc_attr( $this->styles['_block']->css() ); ?>">
						<div align="<?php echo esc_attr( $this->styles['_block']->getProperty( 'textAlign' ) ); ?>">
							<table border="0" cellPadding="0" cellSpacing="0" role="presentation" width="<?php echo esc_attr( $width_value ); ?>" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
								<tbody>
									<tr>
										<td style="line-height: 1px; font-size: 1px; <?php echo esc_attr( $this->getBorderStyle() ); ?>">
											<span style="word-break: break-word;"> </span>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
		<?php

		return ob_get_clean();
	}

	public function getBorderStyle() {
		$style              = $this->getAttribute( '_style' );
		$base_path          = 'border.top';
		$border_width_value = Arr::get( $style, "$base_path.width.value" );
		$border_width_unit  = Arr::get( $style, "$base_path.width.unit" );
		$border_style       = Arr::get( $style, "$base_path.style" );
		$border_color       = Arr::get( $style, "$base_path.color" );
		if ( empty( $border_width_value ) || empty( $border_width_unit ) || empty( $border_style ) || empty( $border_color ) ) {
			return '';
		}
		//return '';
		$border_width = $border_width_value . $border_width_unit;
		$test         = "border-top:  $border_width $border_style $border_color";
		return $test;
	}
}

BlockRegistry::register( __DIR__, DividerBlock::class );
