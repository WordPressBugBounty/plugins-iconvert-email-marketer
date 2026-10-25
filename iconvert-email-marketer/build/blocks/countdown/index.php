<?php



namespace IconvertEmailMarketer\App\Core\Blocks;

use IconvertEmailMarketer\App\Core\Blocks\BaseBlock;
use IconvertEmailMarketer\App\Core\Blocks\BlockRegistry;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class CountdownBlock extends BaseBlock {

	public function html() {
		$color = $this->styles['_style']->getProperty( 'typography.color' );
		if(empty($color)) {
			$color = '';
		}
		$background = $this->styles['_style']->getProperty( 'background' );
		if(empty($background)) {
			$background = '';
		}
		$args = array(
			'__iconvertem_print_countdown' => 1,
			'time'                         => $this->getAttribute( 'deadline' ),
			'background_color'             => str_replace( '#', '', $background ),
			'text_color'                   => str_replace( '#', '', $color ),
			'font_size'                    => $this->styles['_style']->getProperty( 'typography.size.value' ),
		);

		$countdownImage = home_url();

		$countdownImage = add_query_arg( $args, $countdownImage );

		ob_start();
		?>
		<table class="wp-block-extendstudio-countdown" width="100%" border="0" cellPadding="0" cellSpacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word; width: 100%; ">
			<tbody>
				<tr>
					<td>
						<div>
							<img src="<?php echo $countdownImage; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped  ?>" style="width: 100%"/>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
		<?php

		return ob_get_clean();
	}
}

BlockRegistry::register( __DIR__, CountdownBlock::class );
