<?php



namespace IconvertEmailMarketer;

use IconvertEmailMarketer\Admin\Admin;
use IconvertEmailMarketer\Api\ApiBootstrap;
use IconvertEmailMarketer\Core\Activation;
use IconvertEmailMarketer\Core\AssetsRegistry;
use IconvertEmailMarketer\Core\Singleton;
use IconvertEmailMarketer\Core\SvgFilter;
use IconvertEmailMarketer\Countdown\Countdown;
use IconvertEmailMarketer\TransactionalEmailsDispatchers\MainDispatcher;
use IconvertEmailMarketer\Admin\NotificationsManager;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Bootstrap {
	use Singleton;

	protected function __construct() {

		Activation::load();
		Admin::load();
		NotificationsManager::load();
		AssetsRegistry::load();
		SvgFilter::load();
		ApiBootstrap::load();
		MainDispatcher::load();
		Countdown::load();
	}


	public static function load() {

		return self::getInstance();
	}
}
