<?php



namespace IconvertEmailMarketer\App\Core\Blocks;

use IconvertEmailMarketer\App\Core\Blocks\BaseBlock;
use IconvertEmailMarketer\App\Core\Blocks\BlockRegistry;
use IlluminateAgnostic\Str\Support\Arr;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class CellBlock extends BaseBlock {

	public function html() {
		$row_block = BlockRegistry::getInstance()->getParentBlock();

		$row_vertical_align = Arr::get( $row_block, 'attrs._style.verticalAlign' );

		$css = $this->styles['_style']->css();

		//backward compatible change. If row has vertical align use it. if not use what was saved for the column
		if ( ! empty( $row_vertical_align ) ) {
			//remove old vertical align
			$css  = preg_replace( '/vertical-align\s*:\s*[^;]+;/', '', $css );
			$css .= " vertical-align: $row_vertical_align;";
		}
		ob_start();
		?>
		<table class="wp-block-extendstudio-cell"  align="center" border="0" cellPadding="0" cellSpacing="0" role="presentation" width=" <?php echo esc_attr( $this->calculateWidth() ); ?>" style="display: inline-table; mso-table-lspace: 0pt;  margin: 0 auto 0 auto; mso-table-rspace: 0pt; <?php echo esc_attr( $css ); ?>">
			<tbody style="line-height: normal;">
				<tr>
					<td>
						<inner-blocks></inner-blocks>
					</td>
				</tr>
			</tbody>

		</table>

		<?php

		return ob_get_clean();
	}
}

BlockRegistry::register( __DIR__, CellBlock::class );
