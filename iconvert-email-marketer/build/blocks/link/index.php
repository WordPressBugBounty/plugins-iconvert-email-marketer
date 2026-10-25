<?php


namespace IconvertEmailMarketer\App\Core\Blocks;

use IconvertEmailMarketer\App\Core\Blocks\BaseBlock;
use IconvertEmailMarketer\App\Core\Blocks\BlockRegistry;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class LinkBlock extends BaseBlock {

	public function html() {
		$link = $this->getAttribute( 'link' );
		ob_start();
		?>
		<table class="wp-block-extendstudio-link" border="0" cellPadding="0" cellSpacing="0" role="presentation" width="100%" style="width: 100%; ">
			<tbody>
				<tr>
					<td valign="top">
						<div align="center" style="<?php echo esc_attr( $this->styles['_block']->css() ); ?>">
							<a href="<?php echo esc_attr( $link ); ?>" target="_blank" style="display: inline-block; word-break: break-word; <?php echo esc_attr( $this->styles['_style']->css() ); ?>">
								<?php echo wp_kses_post( $this->getAttribute( 'content' ) ); ?>
							</a>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
		<?php

		return ob_get_clean();
	}
}

BlockRegistry::register( __DIR__, LinkBlock::class );
