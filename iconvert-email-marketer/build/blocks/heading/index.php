<?php


namespace IconvertEmailMarketer\App\Core\Blocks;

use IconvertEmailMarketer\App\Core\Blocks\BaseBlock;
use IconvertEmailMarketer\App\Core\Blocks\BlockRegistry;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class HeadingBlock extends BaseBlock {

	public function html() {
		$tagName = 'h' . $this->getAttribute( 'level', 2 );

		ob_start();
		?>
		<table class="wp-block-extendstudio-heading" width="100%" border="0" cellPadding="0" cellSpacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word; width: 100%; ">
			<tbody>
				<tr>
					<td style="<?php echo esc_attr( $this->styles['_block']->css() ); ?>">
						<div style="font-family: sans-serif">
							<<?php echo esc_html( $tagName ); ?> style="margin: 0; <?php echo esc_attr( $this->styles['_style']->css() ); ?>">
								<?php echo wp_kses_post( $this->getAttribute( 'content' ) ); ?>
							</<?php echo esc_html( $tagName ); ?>>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
		<?php

		return ob_get_clean();
	}
}

BlockRegistry::register( __DIR__, HeadingBlock::class );
