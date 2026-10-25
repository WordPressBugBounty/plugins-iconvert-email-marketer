<?php


namespace IconvertEmailMarketer\App\Core\Blocks;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ButtonBlock extends BaseBlock {



	public function __construct() {
	}
	public function html() {
		$link = $this->getAttribute( 'link' );

		ob_start();
		?>
		<table class="wp-block-extendstudio-button" border="0" cellPadding="0" cellSpacing="0" role="presentation" width="100%" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;  <?php echo esc_attr( $this->styles['_block']->css() ); ?>">
			<tbody>
				<tr>
					<td valign="top" align="" style="<?php echo esc_attr( $this->styles['_block']->css() ); ?>">
						<table style="display: inline-block">
							<tr>
								<td>
									<div align="center">
										<a href="<?php echo esc_attr( $link ); ?>" target="_blank" rel="noopener">
											<span  style="
												display: inline-block;
												word-break: break-word;
											<?php echo esc_attr( $this->styles['_style']->css() ); ?>
												" >
												<?php echo wp_kses_post( $this->getAttribute( 'content' ) ); ?>
											</span>

										</a>
									</div>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</tbody>
		</table>
		<?php

		return ob_get_clean();
	}

	//  public function wooButtonTargetFactory( $target ) {
	//      if ( ! $this->realData ) {
	//          return $this->getAttribute( 'link' );
	//      }
	//      switch ( $target ) {
	//          case 'reset_password':
	//              return wp_lostpassword_url();
	//          case 'my_account':
	//              return get_permalink( get_option( 'woocommerce_myaccount_page_id' ) );
	//          case 'order':
	//              return $this->order->get_view_order_url();
	//          case 'store':
	//              return get_permalink( wc_get_page_id( 'shop' ) );
	//          case 'cart':
	//              return wc_get_cart_url();
	//          default:
	//              return $this->getAttribute( 'link' );
	//      }
	//  }
}

BlockRegistry::register( __DIR__, ButtonBlock::class );
