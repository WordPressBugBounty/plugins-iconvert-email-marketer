<?php



namespace IconvertEmailMarketer\Countdown;



use IconvertEmailMarketer\Core\Singleton;
use IlluminateAgnostic\Str\Support\Arr;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Countdown {
	use Singleton;

	protected function __construct() {

		add_filter(
			'template_include',
			array( $this, 'onTemplateRedirect' ),
			1
		);
	}

	public function onTemplateRedirect( $template ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( Arr::has( $_REQUEST, '__iconvertem_print_countdown' ) ) {
			$this->printCountdown();
		}

		return $template;
	}
	public  function printCountdown() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$destTime = ! empty( $_GET['time'] ) ? new \DateTime( \sanitize_text_field( wp_unslash( $_GET['time'] ) ) ) : null;
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$backgroundColor = ! empty( $_GET['background_color'] ) ? \sanitize_text_field( wp_unslash( $_GET['background_color'] ) ) : null;
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$textColor = ! empty( $_GET['text_color'] ) ? \sanitize_text_field( wp_unslash( $_GET['text_color'] ) ) : null;
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$fontSize = ! empty( $_GET['font_size'] ) ? \sanitize_text_field( wp_unslash( $_GET['font_size'] ) ) : 40;

		$emailCountdown = new ICCountdown();
		$emailCountdown->setDestinationTime( $destTime );
		$emailCountdown->setTextColor( $textColor );
		$emailCountdown->setBackgroundColor( $backgroundColor );
		$emailCountdown->setShowTextLabel( false );

		$emailCountdown->setTextData( 'days', null, $fontSize );
		$emailCountdown->setTextData( 'hours', null, $fontSize );
		$emailCountdown->setTextData( 'minutes', null, $fontSize );
		$emailCountdown->setTextData( 'seconds', null, $fontSize );

		// content type gif
		header( 'Content-Type: image/gif' );
		// no caching of gif, so it gets reloaded every time
		header( 'Cache-Control: no-store, no-cache, must-revalidate, max-age=0' );
		header( 'Cache-Control: post-check=0, pre-check=0', false );
		header( 'Pragma: no-cache' );

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $emailCountdown->getGIFAnimation();
		die();
	}
}
