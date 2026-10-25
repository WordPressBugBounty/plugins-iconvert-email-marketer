<?php


namespace IconvertEmailMarketer\Countdown;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class ICCountdown extends ICDefaultCountdown {

	public function setShowTextLabel( $showTextLabel ) {
		$this->showTextLabel = $showTextLabel;
	}
}
