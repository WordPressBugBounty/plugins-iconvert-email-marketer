<?php


namespace IconvertEmailMarketer\App\Core\Blocks;

use IconvertEmailMarketer\App\Core\Blocks\BaseBlock;
use IconvertEmailMarketer\App\Core\Blocks\BlockRegistry;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MailContainerBlock extends BaseBlock {

	public function html() {

		ob_start();

		?>
		<table class="nl-container" align="center" width="100%" border="0" cellPadding="0" cellSpacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; <?php echo esc_attr( $this->styles['_style']->css() ); ?>">
			<tbody>
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

BlockRegistry::register( __DIR__, MailContainerBlock::class );
