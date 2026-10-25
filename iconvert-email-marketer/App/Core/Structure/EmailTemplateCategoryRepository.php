<?php

namespace  IconvertEmailMarketer\App\Core\Structure;

use  IconvertEmailMarketer\App\Core\API\APIBase;
use  IconvertEmailMarketer\App\Core\Traits\IsSingleton;

if ( ! defined( 'ABSPATH' ) ) {exit;}

class EmailTemplateCategoryRepository {
	use IsSingleton;
	private $api_endpoint   = 'categories';
	private $slug           = 'icem-mail-tpl-cat';
	const ALL_TRANSIENT_KEY = 'iconvertem-all-templates-category';

	public function all() {
		$cached_data = get_transient( static::ALL_TRANSIENT_KEY );
		if ( ! empty( $cached_data ) &&
			( ! defined( 'ICONVERTEM_SKIP_TEMPLATE_CACHE' ) ||
				defined( 'ICONVERTEM_SKIP_TEMPLATE_CACHE' ) && ! \ICONVERTEM_SKIP_TEMPLATE_CACHE )
		) {
			return $cached_data;
		}

		$api      = new APIBase( 'categories/list' );
		$response = $api->get( array() );

		if ( $response['success'] == 1 ) {
			$data = $response['data'];
			set_transient( static::ALL_TRANSIENT_KEY, $data, DAY_IN_SECONDS );
			return $data;
		}

		return false;
	}

	public function getSlug() {
		return $this->slug;
	}
}
