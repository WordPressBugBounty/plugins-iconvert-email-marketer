<?php


namespace IconvertEmailMarketer\App\Core\Blocks;

use IconvertEmailMarketer\App\Core\Blocks\BaseBlock;
use IconvertEmailMarketer\App\Core\Blocks\BlockRegistry;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ImageBlock extends BaseBlock {

	public function html() {
		$imageLink = $this->getAttribute( 'imageLink' );
		$imageUrl  = $this->getAttribute( 'imageUrl' );

		ob_start();
		?>
		<table class="wp-block-extendstudio-image" width="100%" border="0" cellPadding="0" cellSpacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; ">
			<tr>
				<td style="<?php echo esc_attr( $this->styles['_block']->css() ); ?>">
					<div align="<?php echo esc_attr( $this->styles['_block']->getProperty( 'textAlign' ) ); ?>" style="line-height: 10px;">
						<a href="<?php echo esc_attr( $imageLink ); ?>" style="display: inline-block">
							<img src="<?php echo esc_attr( $imageUrl ); ?>" alt="<?php echo esc_attr( $imageUrl ); ?>" title="<?php echo esc_attr( $imageUrl ); ?>" width="<?php echo esc_attr( $this->styles['_style']->getProperty( 'width.value' ) ); ?>" style="display: inline-block; max-Width: 100%; height: auto; <?php echo esc_attr( $this->styles['_style']->css() ); ?>" />
						</a>
					</div>
				</td>
			</tr>
		</table>
		<?php

		return ob_get_clean();
	}
}

BlockRegistry::register( __DIR__, ImageBlock::class );
