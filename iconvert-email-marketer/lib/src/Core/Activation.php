<?php



namespace IconvertEmailMarketer\Core;

use IconvertEmailMarketer\App\Core\Structure\EmailTemplateCPT;
use IconvertEmailMarketer\Constants;
use IconvertEmailMarketer\Flags;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Activation {
	use Singleton;

	protected function __construct() {
		register_activation_hook( ICONVERTEM_PAGE_FILE, array( $this, 'onActivate' ) );
	}


	public function onActivate() {
		$this->loadDefaultData();
		EmailTemplateCPT::getInstance()->init_taxonomy_terms();

		$is_pro = strpos( ICONVERTEM_PAGE_FILE, 'iconvert-email-marketer-pro' ) !== false;
		$this->setActivationTimestamp( $is_pro );
	}
	public function setActivationTimestamp( $is_pro = false ) {
		$key = $is_pro ? 'pro_activation_time' : 'activation_time';

		if ( ! Flags::get( $key ) ) {
			Flags::set( $key, time() );
		}
	}
	public function loadDefaultData() {
		$query = new \WP_Query(
			array(
				'post_type'     => Constants::$settingPostType,
				'post_status'   => array( 'draft', 'publish' ),
				'no_found_rows' => true,
				'post_per_page' => 1,

			)
		);

		if ( ! $query->have_posts() ) {
			$this->insertDefaultData();
		}
	}

	public function insertDefaultData() {
		$content     = file_get_contents( ICONVERTEM_PATH . '/defaults/default-data.json' );
		$postContent = json_encode( json_decode( $content, true ), JSON_FORCE_OBJECT );
		wp_insert_post(
			array(
				'post_content' => $postContent, // remove the pretty prints
				'post_status'  => 'publish',
				'post_type'    => Constants::$settingPostType,
				'post_name'    => Constants::$settingPostType,
				'post_title'   => __( 'Iconvert Email Data', 'iconvert-email-marketer' ),
			),
			true
		);
	}
}
