<?php


namespace IconvertEmailMarketer\App\Core\Blocks;


use IlluminateAgnostic\Str\Support\Arr;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RowBlock extends BaseBlock {

	public function html() {

		$isStackedOnMobile = $this->getAttribute( 'isStackedOnMobile', false );
		$max_width         = $this->getMaxWidth();
		$width_attr        = "{$max_width['value']}{$max_width['unit']}";
		$style             = $this->getAttribute( '_style' );

		//vertical align applied only to td
		$vertical_align = Arr::get( $style, 'verticalAlign' );
		$td_style       = '';

		if ( ! empty( $vertical_align ) ) {
			$td_style .= " vertical-align: $vertical_align;";
		}
		ob_start();

		$table_class = $isStackedOnMobile == 1 ? 'row-content stack' : 'row-content';
		?>
		<table class="wp-block-extendstudio-row " align="center" width="100%" border="0" cellPadding="0" cellSpacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; margin: 0 auto 0 auto; width: 100%;  min-width:100%; <?php echo esc_attr( $this->styles['_block']->css() ); ?>">
			<tbody>
				<tr>
					<td>
						<table class="<?php echo esc_attr( $table_class ); ?>" align="center" width="<?php echo esc_attr( $width_attr ); ?>" border="0" cellPadding="0" cellSpacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; margin: 0 auto 0 auto; width: 100%;  <?php echo esc_attr( $this->calculateWidthAndMaxWidth() ); ?> <?php echo esc_attr( $this->styles['_style']->css() ); ?>">
							<tbody>
								<tr>
									<td valign="<?php echo esc_attr( $vertical_align ); ?>" style="line-height: 1; <?php echo esc_attr( $td_style ); ?>">
										<inner-blocks></inner-blocks>
									</td>

								</tr>
							</tbody>
						</table>
					</td>
				</tr>
			</tbody>
		</table>
		<?php

		return ob_get_clean();
	}
}

BlockRegistry::register( __DIR__, RowBlock::class );
