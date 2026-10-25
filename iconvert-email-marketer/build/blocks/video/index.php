<?php



namespace IconvertEmailMarketer\App\Core\Blocks;

use IconvertEmailMarketer\App\Core\Blocks\BaseBlock;
use IconvertEmailMarketer\App\Core\Blocks\BlockRegistry;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class VideoBlock extends BaseBlock {

	public function html() {
		// echo '<pre>';
		// print_r($this->attributes);
		// echo '</pre>';
		$imageWidth  = $this->getAttribute( 'imageWidth' );
		$imageHeight = $this->getAttribute( 'imageHeight' );
		$playSize    = $this->getAttribute( 'playSize' );
		$playType    = $this->getAttribute( 'playType' );
		$playColor   = $this->getAttribute( 'playColor' );
		$videoUrl    = $this->getAttribute( 'videoUrl' );
		$imageUrl    = $this->getAttribute( 'imageUrl' );

		$wrapperClassSizer =
			$this->msoPlayTypeFactory( $playType, $playColor, $playSize, $imageWidth, $imageHeight, $videoUrl, $imageUrl, $this->wrapperClassVideoPreview() );
		ob_start();
		?>
		<table class="wp-block-extendstudio-video" width="100%" border="0" cellPadding="0" cellSpacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; ">
			<tbody>
			<tr style="box-sizing: content-box">
				<td width="100%" style="box-sizing: content-box; width: 100%">
					<!--[if (mso)|(IE)]><table width="<?php echo esc_attr( $imageWidth ); ?>" align="center" cellPadding="0" cellSpacing="0" role="presentation"><tr><td><![endif]-->
					<?php echo $wrapperClassSizer;  // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<!--[if (mso)|(IE)]></td></tr></table><![endif]-->
				</td>
			</tr>
			</tbody>
		</table>
		<?php

		return ob_get_clean();
	}

	public function wrapperClassVideoPreview() {
		$imageWidth      = $this->getAttribute( 'imageWidth' );
		$imageHeight     = $this->getAttribute( 'imageHeight' );
		$playSize        = $this->getAttribute( 'playSize' );
		$playType        = $this->getAttribute( 'playType' );
		$playColor       = $this->getAttribute( 'playColor' );
		$videoUrl        = $this->getAttribute( 'videoUrl' );
		$imageUrl        = $this->getAttribute( 'imageUrl' );
		$imageVideoRatio = ICONVERTEM_URL . 'assets/images/video_ratio_16-9.gif';

		ob_start();
		?>

		<a class="video-preview" href="<?php echo esc_attr( $videoUrl ); ?>" target="_blank" style="
			box-sizing: content-box;
			display: block;
			background-color: #5b5f66;
			background-image: radial-gradient(circle at center, #5b5f66, #1d1f21);
			margin: 0;
			text-decoration: none;
			">
			<div style="box-sizing: content-box">
				<table cellPadding="0" cellSpacing="0" border="0" width="100%" background="<?php echo esc_attr( $imageUrl ); ?>" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; box-sizing: content-box;   background-image: url('<?php echo esc_attr( $imageUrl ); ?>'); background-size: cover; background-position: center; background-repeat: no-repeat;  height: auto; min-width: 100%;">
					<tbody>
					<tr style="box-sizing: content-box">
						<td width="25%" style="box-sizing: content-box">
							<img src="<?php echo esc_attr( $imageVideoRatio ); ?>" alt="ratio" width="100%" border="0" style="display: block; box-sizing: content-box; height: auto; opacity: 0; visibility: hidden;" />
						</td>
						<td width="50%" align="center" valign="middle" style="box-sizing: content-box; vertical-align: middle;">
							<?php echo $this->playTypeFactory( $playType, $playColor, $playSize );// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</td>
						<td width="25%" style="box-sizing: content-box">&nbsp;</td>
					</tr>
					</tbody>
				</table>
			</div>
		</a>
		<?php
		return ob_get_clean();
	}

	public function playTypeFactory( $playType, $playColorType, $playSize ) {
		$playColors     = $this->playColorFactory( $playColorType );
		$colorPrimary   = $playColors[0];
		$colorSecondary = $playColors[1];

		$playSizes   = $this->playSizeFactory( $playSize, $playType );
		$height      = esc_attr( $playSizes['height'] );
		$width       = esc_attr( $playSizes['width'] );
		$padding     = esc_attr( $playSizes['padding'] );
		$borderWidth = esc_attr( $playSizes['borderWidth'] );

		$playTypes = $this->getPlayTypes();

		switch ( $playType ) {
			case $playTypes['rectangleFill']:
				return '<div class="play-button_outer"
                                        style="
                                            box-sizing: content-box;
                                            display: inline-block;
                                            vertical-align: middle;
                                            background: ' . $colorPrimary . ';
                                            border: 3px solid ' . $colorPrimary . ';
                                            height: ' . $height . ';
                                            width: ' . $width . ';
                                            border-radius: 3px;
                                        ">
                    <div style="box-sizing: content-box; padding: ' . $padding . ';">
                        <div class="play-button_inner"
                                style="
                                    box-sizing: content-box;
                                    border-style: solid;
                                    border-width: ' . $borderWidth . ';
                                    display: block;
                                    font-size: 0;
                                    height: 0;
                                    width: 0;
                                    border-color: transparent transparent transparent ' . $colorSecondary . ';
                                ">&nbsp;</div>
                    </div>
                </div>';

			case $playTypes['rectangleOutline']:
				return '<div class="play-button_outer"
                                        style="
                                            box-sizing: content-box;
                                            display: inline-block;
                                            vertical-align: middle;
                                            border: 3px solid ' . $colorPrimary . ';
                                            height: ' . $height . ';
                                            width: ' . $width . ';
                                            border-radius: 3px;
                                        ">
                    <div style="box-sizing: content-box; padding: ' . $padding . ';">
                        <div class="play-button_inner"
                                style="
                                    box-sizing: content-box;
                                    border-style: solid;
                                    border-width: ' . $borderWidth . ';
                                    display: block;
                                    font-size: 0;
                                    height: 0;
                                    width: 0;
                                    border-color: transparent transparent transparent ' . $colorPrimary . ';
                                ">&nbsp;</div>
                    </div>
                </div>';
			case $playTypes['circleFill']:
				return '<div class="play-button_outer"
                                        style="
                                            box-sizing: content-box;
                                            display: inline-block;
                                            vertical-align: middle;
                                            background: ' . $colorPrimary . ';
                                            border: 3px solid ' . $colorPrimary . ';
                                            height: ' . $height . ';
                                            width: ' . $width . ';
                                            border-radius: 100%;
                                        ">
                    <div style="box-sizing: content-box; padding: ' . $padding . ';">
                        <div class="play-button_inner"
                                style="
                                    box-sizing: content-box;
                                    border-style: solid;
                                    border-width: ' . $borderWidth . ';
                                    display: block;
                                    font-size: 0;
                                    height: 0;
                                    width: 0;
                                    border-color: transparent transparent transparent ' . $colorSecondary . ';
                                ">&nbsp;</div>
                    </div>
                </div>';
			case $playTypes['circleOutline']:
				return '<div class="play-button_outer"
                                        style="
                                            box-sizing: content-box;
                                            display: inline-block;
                                            vertical-align: middle;
                                            border: 3px solid ' . $colorPrimary . ';
                                            height: ' . $height . ';
                                            width: ' . $width . ';
                                            border-radius: 100%;
                                        ">
                    <div style="box-sizing: content-box; padding: ' . $padding . ';">
                        <div class="play-button_inner"
                                style="
                                    box-sizing: content-box;
                                    border-style: solid;
                                    border-width: ' . $borderWidth . ';
                                    display: block;
                                    font-size: 0;
                                    height: 0;
                                    width: 0;
                                    border-color: transparent transparent transparent ' . $colorPrimary . ';
                                ">&nbsp;</div>
                    </div>

                </div>';
			case $playTypes['squareFill']:
				return '<div class="play-button_outer"
                                        style="
                                            box-sizing: content-box;
                                            display: inline-block;
                                            vertical-align: middle;
                                            background: ' . $colorPrimary . ';
                                            border: 3px solid ' . $colorPrimary . ';
                                            height: ' . $height . ';
                                            width: ' . $width . ';
                                        ">
                    <div style="box-sizing: content-box; padding: ' . $padding . ';">
                        <div class="play-button_inner"
                                style="
                                    box-sizing: content-box;
                                    border-style: solid;
                                    border-width: ' . $borderWidth . ';
                                    display: block;
                                    font-size: 0;
                                    height: 0;
                                    width: 0;
                                    border-color: transparent transparent transparent ' . $colorSecondary . ';
                                ">&nbsp;</div>
                    </div>
                </div>';
			case $playTypes['squareOutline']:
				return '<div class="play-button_outer"
                                        style="
                                            box-sizing: content-box;
                                            display: inline-block;
                                            vertical-align: middle;
                                            border: 3px solid ' . $colorPrimary . ';
                                            height: ' . $height . ';
                                            width: ' . $width . ';
                                        ">
                    <div style="box-sizing: content-box; padding: ' . $padding . ';">
                        <div class="play-button_inner"
                                style="
                                    box-sizing: content-box;
                                    border-style: solid;
                                    border-width: ' . $borderWidth . ';
                                    display: block;
                                    font-size: 0;
                                    height: 0;
                                    width: 0;
                                    border-color: transparent transparent transparent ' . $colorPrimary . ';
                                ">&nbsp;</div>
                    </div>
                </div>';
			case $playTypes['arrow']:
				return '<div class="play-button_outer"
                                        style="
                                            box-sizing: content-box;
                                            display: inline-block;
                                            vertical-align: middle;
                                            height: ' . $height . ';
                                            width: ' . $width . ';
                                        ">
                    <div style="box-sizing: content-box; padding: ' . $padding . ';">
                        <div class="play-button_inner"
                                style="
                                    box-sizing: content-box;
                                    border-style: solid;
                                    border-width: ' . $borderWidth . ';
                                    display: block;
                                    font-size: 0;
                                    height: 0;
                                    width: 0;
                                    border-color: transparent transparent transparent ' . $colorPrimary . ';
                                ">&nbsp;</div>
                    </div>
                </div>';
			default:
				return '<div class="play-button_outer"
                                        style="
                                            box-sizing: content-box;
                                            display: inline-block;
                                            vertical-align: middle;
                                            background: ' . $colorPrimary . ';
                                            border: 3px solid ' . $colorPrimary . ';
                                            height: ' . $height . ';
                                            width: ' . $width . ';
                                            border-radius: 3px;
                                        ">
                    <div style="box-sizing: content-box; padding: ' . $padding . ';">
                        <div class="play-button_inner"
                                style="
                                    box-sizing: content-box;
                                    border-style: solid;
                                    border-width: ' . $borderWidth . ';
                                    display: block;
                                    font-size: 0;
                                    height: 0;
                                    width: 0;
                                    border-color: transparent transparent transparent ' . $colorSecondary . ';
                                ">&nbsp;</div>
                    </div>
                </div>';
		}
	}

	function playColorFactory( $playColorType ) {
		switch ( $playColorType ) {
			case 'light':
				return array( '#FFFFFF', '#000000' );
			case 'dark':
				return array( '#000000', '#FFFFFF' );
			case 'red':
				return array( '#FF0000', '#FFFFFF' );
			case 'blue':
				return array( '#00AEDF', '#FFFFFF' );
			default:
				return array( '#FF0000', '#FFFFFF' );
		}
	}

	function playSizeFactory( $playSize, $playType ) {
		$playTypes = $this->getPlayTypes();

		switch ( true ) {
			case $playSize === 'small' && $playType === $playTypes['circleFill']:
			case $playSize === 'small' && $playType === $playTypes['circleOutline']:
				return array(
					'height'      => '44px',
					'width'       => '44px',
					'padding'     => '11px 16.923076923076923px',
					'borderWidth' => '11px 0 11px 15px',
				);

			case $playSize === 'medium' && $playType === $playTypes['circleFill']:
			case $playSize === 'medium' && $playType === $playTypes['circleOutline']:
				return array(
					'height'      => '59px',
					'width'       => '59px',
					'padding'     => '14.75px 22.69230769230769px',
					'borderWidth' => '15px 0 15px 20px',
				);

			case $playSize === 'large' && $playType === $playTypes['circleFill']:
			case $playSize === 'large' && $playType === $playTypes['circleOutline']:
				return array(
					'height'      => '74px',
					'width'       => '74px',
					'padding'     => '18.5px 28.46153846153846px',
					'borderWidth' => '19px 0 19px 25px',
				);

			case $playSize === 'small' && $playType === $playTypes['rectangleFill']:
			case $playSize === 'small' && $playType === $playTypes['rectangleOutline']:
				return array(
					'height'      => '28.16px',
					'width'       => '44px',
					'padding'     => '5.866666666666666px 16.761904761904763px',
					'borderWidth' => '9px 0 9px 12px',
				);

			case $playSize === 'medium' && $playType === $playTypes['rectangleFill']:
			case $playSize === 'medium' && $playType === $playTypes['rectangleOutline']:
				return array(
					'height'      => '37.76px',
					'width'       => '59px',
					'padding'     => '7.866666666666666px 22.476190476190474px',
					'borderWidth' => '12px 0 12px 16px',
				);

			case $playSize === 'large' && $playType === $playTypes['rectangleFill']:
			case $playSize === 'large' && $playType === $playTypes['rectangleOutline']:
				return array(
					'height'      => '47.36px',
					'width'       => '74px',
					'padding'     => '9.866666666666667px 28.19047619047619px',
					'borderWidth' => '15px 0 15px 20px',
				);

			case $playSize === 'small' && $playType === $playTypes['squareFill']:
			case $playSize === 'small' && $playType === $playTypes['squareOutline']:
				return array(
					'height'      => '44px',
					'width'       => '44px',
					'padding'     => '11px 16.923076923076923px',
					'borderWidth' => '11px 0 11px 15px',
				);

			case $playSize === 'medium' && $playType === $playTypes['squareFill']:
			case $playSize === 'medium' && $playType === $playTypes['squareOutline']:
				return array(
					'height'      => '59px',
					'width'       => '59px',
					'padding'     => '14.75px 22.69230769230769px',
					'borderWidth' => '15px 0 15px 20px',
				);

			case $playSize === 'large' && $playType === $playTypes['squareFill']:
			case $playSize === 'large' && $playType === $playTypes['squareOutline']:
				return array(
					'height'      => '74px',
					'width'       => '74px',
					'padding'     => '18.5px 28.46153846153846px',
					'borderWidth' => '19px 0 19px 25px',
				);

			case $playSize === 'small' && $playType === $playTypes['arrow']:
				return array(
					'height'      => '44px',
					'width'       => '44px',
					'padding'     => '11px 16.923076923076923px',
					'borderWidth' => '11px 0 11px 15px',
				);

			case $playSize === 'medium' && $playType === $playTypes['arrow']:
				return array(
					'height'      => '59px',
					'width'       => '59px',
					'padding'     => '14.75px 22.69230769230769px',
					'borderWidth' => '15px 0 15px 20px',
				);

			case $playSize === 'large' && $playType === $playTypes['arrow']:
				return array(
					'height'      => '74px',
					'width'       => '74px',
					'padding'     => '18.5px 28.46153846153846px',
					'borderWidth' => '19px 0 19px 25px',
				);
			default:
				return array(
					'height'      => '59px',
					'width'       => '59px',
					'padding'     => '14.75px 22.69230769230769px',
					'borderWidth' => '15px 0 15px 20px',
				);
		}
	}

	public function getPlayTypes() {
		return array(
			'circleFill'       => 'circle-fill',
			'circleOutline'    => 'circle-outline',
			'rectangleFill'    => 'rectangle-fill',
			'rectangleOutline' => 'rectangle-outline',
			'squareFill'       => 'square-fill',
			'squareOutline'    => 'square-outline',
			'arrow'            => 'arrow',
		);
	}

	public function msoPlaySizesFactory( $playSize, $playType, $maxWidth, $maxHeight ) {
		$tagHeight   = 59;
		$tagWidth    = 59;
		$shapeHeight = 15;
		$shapeWidth  = 22;

		$playTypes = $this->getPlayTypes();

		switch ( true ) {
			case $playSize === 'small' && $playType === $playTypes['circleFill']:
			case $playSize === 'small' && $playType === $playTypes['circleOutline']:
				$tagHeight   = 59;
				$tagWidth    = 59;
				$shapeHeight = 15;
				$shapeWidth  = 22;
				break;

			case $playSize === 'medium' && $playType === $playTypes['circleFill']:
			case $playSize === 'medium' && $playType === $playTypes['circleOutline']:
				$tagHeight   = 59;
				$tagWidth    = 59;
				$shapeHeight = 30;
				$shapeWidth  = 21;
				break;

			case $playSize === 'large' && $playType === $playTypes['circleFill']:
			case $playSize === 'large' && $playType === $playTypes['circleOutline']:
				$tagHeight   = 74;
				$tagWidth    = 74;
				$shapeHeight = 37;
				$shapeWidth  = 26;
				break;

			case $playSize === 'small' && $playType === $playTypes['rectangleFill']:
			case $playSize === 'small' && $playType === $playTypes['rectangleOutline']:
				$tagHeight   = 31;
				$tagWidth    = 44;
				$shapeHeight = 20;
				$shapeWidth  = 18;
				break;

			case $playSize === 'medium' && $playType === $playTypes['rectangleFill']:
			case $playSize === 'medium' && $playType === $playTypes['rectangleOutline']:
				$tagHeight   = 42;
				$tagWidth    = 59;
				$shapeHeight = 27;
				$shapeWidth  = 24;
				break;

			case $playSize === 'large' && $playType === $playTypes['rectangleFill']:
			case $playSize === 'large' && $playType === $playTypes['rectangleOutline']:
				$tagHeight   = 53;
				$tagWidth    = 74;
				$shapeHeight = 33;
				$shapeWidth  = 30;
				break;

			case $playSize === 'small' && $playType === $playTypes['squareFill']:
			case $playSize === 'small' && $playType === $playTypes['squareOutline']:
				$tagHeight   = 44;
				$tagWidth    = 44;
				$shapeHeight = 22;
				$shapeWidth  = 15;
				break;

			case $playSize === 'medium' && $playType === $playTypes['squareFill']:
			case $playSize === 'medium' && $playType === $playTypes['squareOutline']:
				$tagHeight   = 59;
				$tagWidth    = 59;
				$shapeHeight = 30;
				$shapeWidth  = 21;
				break;

			case $playSize === 'large' && $playType === $playTypes['squareFill']:
			case $playSize === 'large' && $playType === $playTypes['squareOutline']:
				$tagHeight   = 74;
				$tagWidth    = 74;
				$shapeHeight = 37;
				$shapeWidth  = 26;
				break;

			case $playSize === 'small' && $playType === $playTypes['arrow']:
				$tagHeight   = null;
				$tagWidth    = null;
				$shapeHeight = 22;
				$shapeWidth  = 15;
				break;

			case $playSize === 'medium' && $playType === $playTypes['arrow']:
				$tagHeight   = null;
				$tagWidth    = null;
				$shapeHeight = 30;
				$shapeWidth  = 21;
				break;

			case $playSize === 'large' && $playType === $playTypes['arrow']:
				$tagHeight   = null;
				$tagWidth    = null;
				$shapeHeight = 37;
				$shapeWidth  = 26;
				break;

			default:
				$tagHeight   = 59;
				$tagWidth    = 59;
				$shapeHeight = 15;
				$shapeWidth  = 22;
				break;
		}

		$tagLeft   = $tagWidth ? round( ( $maxWidth - $tagWidth ) / 2 ) : null;
		$tagTop    = $tagHeight ? round( ( $maxHeight - $tagHeight ) / 2 ) : null;
		$shapeLeft = $shapeWidth ? round( ( $maxWidth - ( $shapeWidth - 3 ) ) / 2 ) : null;
		$shapeTop  = $shapeHeight ? round( ( $maxHeight - ( $shapeHeight - 1 ) ) / 2 ) : null;

		return array(
			'tagHeight'   => $tagHeight,
			'tagWidth'    => $tagWidth,
			'tagLeft'     => $tagLeft,
			'tagTop'      => $tagTop,
			'shapeHeight' => $shapeHeight,
			'shapeWidth'  => $shapeWidth,
			'shapeLeft'   => $shapeLeft,
			'shapeTop'    => $shapeTop,
		);
	}

	public function msoPlayTypeFactory( $playType, $playColorType, $playSize, $maxWidth, $maxHeight, $videoUrl, $imageUrl, $wrapperClassVideoPreview ) {
		$colorFactory        = $this->playColorFactory( $playColorType );
		$msoPlaySizesFactory = $this->msoPlaySizesFactory( $playSize, $playType, $maxWidth, $maxHeight );
		$playTypes           = $this->getPlayTypes();

		$colorPrimary   = $colorFactory[0];
		$colorSecondary = $colorFactory[1];

		$tagHeight   = esc_attr( $msoPlaySizesFactory['tagHeight'] );
		$tagWidth    = esc_attr( $msoPlaySizesFactory['tagWidth'] );
		$tagLeft     = esc_attr( $msoPlaySizesFactory['tagLeft'] );
		$tagTop      = esc_attr( $msoPlaySizesFactory['tagTop'] );
		$shapeHeight = esc_attr( $msoPlaySizesFactory['shapeHeight'] );
		$shapeWidth  = esc_attr( $msoPlaySizesFactory['shapeWidth'] );
		$shapeLeft   = esc_attr( $msoPlaySizesFactory['shapeLeft'] );
		$shapeTop    = esc_attr( $msoPlaySizesFactory['shapeTop'] );

		switch ( $playType ) {
			case $playTypes['rectangleFill']:
				return '<div
                    class="sizer"
                    align="center"
                    style="box-sizing: content-box; max-width: ' . $maxWidth . 'px; min-width: ' . $maxWidth . 'px">

                        <!--[if !vml]><!-->
                        ' . $wrapperClassVideoPreview . '
                        <!--<![endif]-->
                        <!--[if vml]>
                        <v:group
                            xmlns:v="urn:schemas-microsoft-com:vml"
                            xmlns:w="urn:schemas-microsoft-com:office:word"
                            coordsize="' . $maxWidth . ',' . $maxHeight . '"
                            coordorigin="0,0"
                            href="' . $videoUrl . '"
                            style="width:' . $maxWidth . ';height: ' . $maxHeight . '"
                        >
                            <v:rect
                            fill="t"
                            stroked="f"
                            style="position:absolute;width:' . $maxWidth . ';height:' . $maxHeight . '"
                            >
                            <v:fill
                            src="' . $imageUrl . '"
                            type="frame"
                            />
                            </v:rect>
                            <v:roundrect
                                    arcsize="5%"
                                    fill="t"
                                    strokecolor="' . $colorPrimary . '"
                                    strokeweight="3px"
                                    style="position:absolute;left:' . $tagLeft . ';top:' . $tagTop . ';width:' . $tagWidth . ';height:' . $tagHeight . '">
                                                <v:fill color="' . $colorPrimary . '" opacity="100%"/>
                                        </v:roundrect>
                            <v:shape
                            coordsize="24,32"
                            path="m,l,32,24,16,xe"
                            fillcolor="' . $colorSecondary . '"
                            stroked="f"
                            style="position:absolute;left:' . $shapeLeft . ';top:' . $shapeTop . ';width:' . $shapeWidth . ';height:' . $shapeHeight . ';"
                            />
                        </v:group>
                        <![endif]-->
                </div>';
			case $playTypes['rectangleOutline']:
				return '<div
                    class="sizer"
                    align="center"
                    style="box-sizing: content-box; max-width: ' . $maxWidth . 'px; min-width: ' . $maxWidth . 'px">
                        <!--[if !vml]><!-->
                        ' . $wrapperClassVideoPreview . '
                        <!--<![endif]-->
                        <!--[if vml]>
                        <v:group
                            xmlns:v="urn:schemas-microsoft-com:vml"
                            xmlns:w="urn:schemas-microsoft-com:office:word"
                            coordsize="' . $maxWidth . ',' . $maxHeight . '"
                            coordorigin="0,0"
                            href="' . $videoUrl . '"
                            style="width:' . $maxWidth . ';height:' . $maxHeight . '"
                        >
                            <v:rect
                            fill="t"
                            stroked="f"
                            style="position:absolute;width:' . $maxWidth . ';height:' . $maxHeight . '"
                            >
                            <v:fill
                            src="' . $imageUrl . '"
                            type="frame"
                            />
                            </v:rect>
                            <v:roundrect
                                    arcsize="5%"
                                    fill="t"
                                    strokecolor="' . $colorPrimary . '"
                                    strokeweight="3px"
                                    style="position:absolute;left:' . $tagLeft . ';top:' . $tagTop . ';width:' . $tagWidth . ';height:' . $tagHeight . '">
                                        <v:fill color="' . $colorPrimary . '" opacity="0%"/>
                                </v:roundrect>
                            <v:shape
                            coordsize="24,32"
                            path="m,l,32,24,16,xe"
                            fillcolor="' . $colorPrimary . '"
                            stroked="f"
                            style="position:absolute;left:' . $shapeLeft . ';top:' . $shapeTop . ';width:' . $shapeWidth . ';height:' . $shapeHeight . ';"
                            />
                        </v:group>
                        <![endif]-->
                </div>';

			case $playTypes['circleFill']:
				return '<div
                    class="sizer"
                    align="center"
                    style="box-sizing: content-box; max-width: ' . $maxWidth . 'px; min-width: ' . $maxWidth . 'px">

                        <!--[if !vml]><!-->
                        ' . $wrapperClassVideoPreview . '
                        <!--<![endif]-->
                        <!--[if vml]>
                        <v:group
                            xmlns:v="urn:schemas-microsoft-com:vml"
                            xmlns:w="urn:schemas-microsoft-com:office:word"
                            coordsize="' . $maxWidth . ',' . $maxHeight . '"
                            coordorigin="0,0"
                            href="' . $videoUrl . '"
                            style="width:' . $maxWidth . ';height:' . $maxHeight . ';"
                        >
                        <v:rect fill="t" stroked="f" style="position:absolute;width:' . $maxWidth . ';height:' . $maxHeight . ';">
                            <v:fill src="' . $imageUrl . '" type="frame"/>
                        </v:rect>
                        <v:oval
                            fill="t"
                            strokecolor="' . $colorPrimary . '"
                            strokeweight="3px"
                            style="position:absolute;left:' . $tagLeft . ';top:' . $tagTop . ';width:' . $tagWidth . ';height:' . $tagHeight . '"
                        >
                            <v:fill color="' . $colorPrimary . '" opacity="100%" />
                        </v:oval>
                        <v:shape
                            coordsize="24,32"
                            path="m,l,32,24,16,xe"
                            fillcolor="' . $colorSecondary . '"
                            stroked="f"
                            style="position:absolute;left:' . $shapeLeft . ';top:' . $shapeTop . ';width:' . $shapeWidth . ';height:' . $shapeHeight . ';"
                        />
                        </v:group>
                        <![endif]-->
                </div>';

			case $playTypes['circleOutline']:
				return '<div
                    class="sizer"
                    align="center"
                    style="box-sizing: content-box; max-width: ' . $maxWidth . 'px; min-width: ' . $maxWidth . 'px">

                        <!--[if !vml]><!-->
                        ' . $wrapperClassVideoPreview . '
                        <!--<![endif]-->
                        <!--[if vml]>
                        <v:group
                            xmlns:v="urn:schemas-microsoft-com:vml"
                            xmlns:w="urn:schemas-microsoft-com:office:word"
                            coordsize="' . $maxWidth . ',' . $maxHeight . '"
                            coordorigin="0,0"
                            href="' . $videoUrl . '"
                            style="width:' . $maxWidth . ';height:' . $maxHeight . ';"
                        >
                        <v:rect fill="t" stroked="f" style="position:absolute;width:' . $maxWidth . ';height:' . $maxHeight . ';">
                            <v:fill src="' . $imageUrl . '" type="frame"/>
                        </v:rect>
                        <v:oval
                            fill="t"
                            strokecolor="' . $colorPrimary . '"
                            strokeweight="3px"
                            style="position:absolute;left:' . $tagLeft . ';top:' . $tagTop . ';width:' . $tagWidth . ';height:' . $tagHeight . '"
                        >
                            <v:fill color="' . $colorPrimary . '" opacity="0%" />
                        </v:roundrect>
                        <v:shape
                            coordsize="24,32"
                            path="m,l,32,24,16,xe"
                            fillcolor="' . $colorPrimary . '"
                            stroked="f"
                            style="position:absolute;left:' . $shapeLeft . ';top:' . $shapeTop . ';width:' . $shapeWidth . ';height:' . $shapeHeight . ';"
                        />
                        </v:group>
                        <![endif]-->
                </div>';
			case $playTypes['squareFill']:
				return '<div
                    class="sizer"
                    align="center"
                    style="box-sizing: content-box; max-width: ' . $maxWidth . 'px; min-width: ' . $maxWidth . 'px">

                        <!--[if !vml]><!-->
                        ' . $wrapperClassVideoPreview . '
                        <!--<![endif]-->
                        <!--[if vml]>
                        <v:group
                            xmlns:v="urn:schemas-microsoft-com:vml"
                            xmlns:w="urn:schemas-microsoft-com:office:word"
                            coordsize="' . $maxWidth . ',' . $maxHeight . '"
                            coordorigin="0,0"
                            href="' . $videoUrl . '"
                            style="width:' . $maxWidth . 'px;height:' . $maxHeight . 'px;"
                        >
                        <v:rect fill="t" stroked="f" style="position:absolute;width:' . $maxWidth . ';height:' . $maxHeight . ';">
                            <v:fill src="' . $imageUrl . '" type="frame"/>
                        </v:rect>
                        <v:rect
                            fill="t"
                            strokecolor="' . $colorPrimary . '"
                            strokeweight="3px"
                            style="position:absolute;left:' . $tagLeft . ';top:' . $tagTop . ';width:' . $tagWidth . ';height:' . $tagHeight . '"
                        >
                            <v:fill color="' . $colorPrimary . '" opacity="100%" />
                        </v:rect>
                        <v:shape
                            coordsize="24,32"
                            path="m,l,32,24,16,xe"
                            fillcolor="' . $colorSecondary . '"
                            stroked="f"
                            style="position:absolute;left:' . $shapeLeft . ';top:' . $shapeTop . ';width:' . $shapeWidth . ';height:' . $shapeHeight . ';"
                        />
                        </v:group>
                        <![endif]-->
                </div>';

			case $playTypes['squareOutline']:
				return '<div
                    class="sizer"
                    align="center"
                    style="box-sizing: content-box; max-width: ' . $maxWidth . 'px; min-width: ' . $maxWidth . 'px">

                        <!--[if !vml]><!-->
                        ' . $wrapperClassVideoPreview . '
                        <!--<![endif]-->
                        <!--[if vml]>
                        <v:group
                            xmlns:v="urn:schemas-microsoft-com:vml"
                            xmlns:w="urn:schemas-microsoft-com:office:word"
                            coordsize="' . $maxWidth . ',' . $maxHeight . '"
                            coordorigin="0,0"
                            href="' . $videoUrl . '"
                            style="width:' . $maxWidth . 'px;height:' . $maxHeight . 'px;"
                        >
                        <v:rect fill="t" stroked="f" style="position:absolute;width:' . $maxWidth . ';height:' . $maxHeight . ';">
                            <v:fill src="' . $imageUrl . '" type="frame"/>
                        </v:rect>
                        <v:rect
                            fill="t"
                            strokecolor="' . $colorPrimary . '"
                            strokeweight="3px"
                            style="position:absolute;left:' . $tagLeft . ';top:' . $tagTop . ';width:' . $tagWidth . ';height:' . $tagHeight . '"
                        >
                            <v:fill color="' . $colorPrimary . '" opacity="0%" />
                        </v:rect>
                        <v:shape
                            coordsize="24,32"
                            path="m,l,32,24,16,xe"
                            fillcolor="' . $colorPrimary . '"
                            stroked="f"
                            style="position:absolute;left:' . $shapeLeft . ';top:' . $shapeTop . ';width:' . $shapeWidth . ';height:' . $shapeHeight . ';"
                        />
                        </v:group>
                        <![endif]-->

                </div>';
			case $playTypes['arrow']:
				return '<div
                    class="sizer"
                    align="center"
                    style="box-sizing: content-box; max-width: ' . $maxWidth . 'px; min-width: ' . $maxWidth . 'px">

                        <!--[if !vml]><!-->
                        ' . $wrapperClassVideoPreview . '
                        <!--<![endif]-->
                        <!--[if vml]>
                        <v:group
                            xmlns:v="urn:schemas-microsoft-com:vml"
                            xmlns:w="urn:schemas-microsoft-com:office:word"
                            coordsize="' . $maxWidth . ',' . $maxHeight . '"
                            coordorigin="0,0"
                            href="' . $videoUrl . '"
                            style="width:' . $maxWidth . 'px;height:' . $maxHeight . 'px;"
                        >
                            <v:rect fill="t" stroked="f" style="position:absolute;width:' . $maxWidth . ';height:' . $maxHeight . ';">
                                <v:fill src="' . $imageUrl . '" type="frame"/>
                            </v:rect>
                            <v:shape
                                coordsize="24,32"
                                path="m,l,32,24,16,xe"
                                fillcolor="#ffffff"
                                stroked="f"
                                style="position:absolute;left:' . $shapeLeft . ';top:' . $shapeTop . ';width:' . $shapeWidth . ';height:' . $shapeHeight . ';"
                            />
                        </v:group>
                    <![endif]-->
                </div>';
			default:
				return '<div
                    class="sizer"
                    align="center"
                    style="box-sizing: content-box; max-width: ' . $maxWidth . 'px; min-width: ' . $maxWidth . 'px">

                        <!--[if !vml]><!-->
                        ' . $wrapperClassVideoPreview . '
                        <!--<![endif]-->
                        <!--[if vml]>
                        <v:group
                            xmlns:v="urn:schemas-microsoft-com:vml"
                            xmlns:w="urn:schemas-microsoft-com:office:word"
                            coordsize="' . $maxWidth . ',' . $maxHeight . '"
                            coordorigin="0,0"
                            href="' . $videoUrl . '"
                            style="width:' . $maxWidth . ';height:' . $maxHeight . ';"
                        >
                            <v:rect
                            fill="t"
                            stroked="f"
                            style="position:absolute;width:' . $maxWidth . ';height:' . $maxHeight . ';"
                            >
                            <v:fill
                            src="' . $imageUrl . '"
                            type="frame"
                            />
                            </v:rect>
                            <v:oval
                            fill="t"
                            strokecolor="' . $colorPrimary . '"
                            strokeweight="3px"
                            style="position:absolute;left:' . $tagLeft . ';top:' . $tagTop . ';width:' . $tagWidth . ';height:' . $tagHeight . '"
                            >
                            <v:fill color="' . $colorPrimary . '" opacity="100%" />
                            </v:oval>
                            <v:shape
                            coordsize="24,32"
                            path="m,l,32,24,16,xe"
                            fillcolor="' . $colorSecondary . '"
                            stroked="f"
                            style="position:absolute;left:' . $shapeLeft . ';top:' . $shapeTop . ';width:' . $shapeWidth . ';height:' . $shapeHeight . ';"
                            />
                        </v:group>
                        <![endif]-->

                </div>';
		}
	}
}

BlockRegistry::register( __DIR__, VideoBlock::class );
