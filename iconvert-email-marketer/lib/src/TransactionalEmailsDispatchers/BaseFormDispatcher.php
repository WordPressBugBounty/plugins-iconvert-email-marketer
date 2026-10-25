<?php


namespace IconvertEmailMarketer\TransactionalEmailsDispatchers;


use  IconvertEmailMarketer\App\Core\Structure\EmailTemplateCPT;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class BaseFormDispatcher {


	const EVENT_TYPE = null;

	protected function __construct() {
	}


	public function getTemplatesLinkedToFormId( $formId ) {
		$query = new \WP_Query(
			array(
				'post_type'      => EmailTemplateCPT::POST_TYPE,
				'post_status'    => array( 'publish' ),
				'posts_per_page' => 1,
				'no_found_rows'  => true,
				// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				//              'tax_query'      => array(
				//                  array(
				//                      'taxonomy' => EmailTemplateCPT::TAXONOMY_SLUG,
				//                      'field'    => 'name',
				//                      'terms'    => array( EmailTemplateCPT::TAXONOMY_TRANSACTION_TYPE ),
				//                  ),
				//              ),

					'meta_query' => array(
						'relation' => 'AND',
						array(
							'key'     => EmailTemplateCPT::POST_META_EMAIL_SENDING_EVENT_TYPE,
							'value'   => static::EVENT_TYPE,
							'compare' => '=',
						),
						array(
							'key'     => EmailTemplateCPT::POST_META_EMAIL_SENDING_EVENT_FORM_ID,
							'value'   => $formId,
							'compare' => '=',
							'type'    => 'NUMERIC',
						),
						array(
							'relation' => 'OR',
							// Case 1: meta key does not exist
							array(
								'key'     => EmailTemplateCPT::POST_META_DISABLED,
								'compare' => 'NOT EXISTS',
							),
							// Case 2: meta key exists but value is not '1'
							array(
								'key'     => EmailTemplateCPT::POST_META_DISABLED,
								'value'   => '1',
								'compare' => '!=',
							),
						),
					),
			)
		);
		$posts = $query->get_posts();
		if ( count( $posts ) === 0 ) {
			return null;
		}
		return $posts[0];
	}
}
