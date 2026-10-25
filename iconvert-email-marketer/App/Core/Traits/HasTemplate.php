<?php



namespace  IconvertEmailMarketer\App\Core\Traits;


if ( ! defined( 'ABSPATH' ) ) {exit;}

trait HasTemplate {
	//main template folder, used to separate admin and frontend in different folders
	public static $folder = 'resources/views';
	// the main layout
	public static $__layout = 'admin/_layout';

	public static function getTemplatePath( $template ) {
		return ICONVERTEM_PATH . '/' . self::$folder . '/' . $template . '.php';
	}
	public static function layout( $layout = false, $args = array() ) {
		if ( $layout ) {
			self::template( $layout, $args );
		} else {
			self::template( self::$__layout, $args );
		}
	}

	public static function template( $template, $args = array(), $echo = true ) {
		extract( $args );
		ob_start();

		require self::getTemplatePath( $template );
		$str = ob_get_clean();

		if (
			! $echo
		) {
			return $str;
		}
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $str;
	}
}
