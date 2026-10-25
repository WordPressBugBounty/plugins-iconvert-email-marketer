<?php



namespace IconvertEmailMarketer\App\Core\Blocks;

use IconvertEmailMarketer\App\Core\Blocks\BaseBlock;
use IconvertEmailMarketer\App\Core\Blocks\BlockRegistry;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class SocialIconsBlock extends BaseBlock {

	public function html() {
		$baseURL = ICONVERTEM_URL . 'assets/images/icons';

		$icons      = $this->getAttribute( 'icons' );
		$iconsColor = $this->getAttribute( 'iconsColor' );
		$sizeIcons  = $this->getAttribute( 'sizeIcons' );
		$iconSize   = $sizeIcons['value'] . $sizeIcons['unit'];

		$iconStyle = 'width: ' . $iconSize . '; height: ' . $iconSize . ';';

		ob_start();
		?>
		<table class="wp-block-extendstudio-social-icons" border="0" cellPadding="0" cellSpacing="0" role="presentation" width="100%" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; ">
			<tbody>
				<tr>
					<td style="<?php echo esc_attr( $this->styles['_block']->css() ); ?>">
						<table  width="" border="0" cellPadding="0" cellSpacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; display: inline-block">
							<tr>
								<?php foreach ( $icons as $icon ) : ?>
									<td style="<?php echo esc_attr( $this->styles['_style']->css() ); ?>">
										<a style="display: inline-block" href="<?php echo esc_attr( isset( $icon['link'] ) ? $icon['link'] : '' ); ?>">
											<img src="<?php echo esc_attr( $this->iconURL( $baseURL, $iconsColor, $icon['type'] ) ); ?>"
												width="<?php echo esc_attr( $sizeIcons['value'] ); ?>"
												height="<?php echo esc_attr( $sizeIcons['value'] ); ?>"
												style="<?php echo esc_attr( $iconStyle ); ?>" alt="<?php echo esc_attr( $icon['title'] ); ?>"
												title=" <?php echo esc_attr( $icon['title'] ); ?>" />
										</a>
									</td>
								<?php endforeach; ?>
							</tr>
						</table>
					</td>
				</tr>
			</tbody>
		</table>
		<?php

		return ob_get_clean();
	}

	public function iconURL( $url, $iconsColor, $iconType ) {
		return $url . '/' . $iconsColor . '/' . $iconType . '.png';
	}
}

BlockRegistry::register( __DIR__, SocialIconsBlock::class );
