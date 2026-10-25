<?php


namespace IconvertEmailMarketer\Api;


use IconvertEmailMarketer\Core\Singleton;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class ApiBootstrap {
	use Singleton;

	protected function __construct() {

		AdminPageApi::load();
	}


}
