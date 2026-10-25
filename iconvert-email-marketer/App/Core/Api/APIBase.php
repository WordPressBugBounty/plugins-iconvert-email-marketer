<?php

namespace  IconvertEmailMarketer\App\Core\API;

if ( ! defined( 'ABSPATH' ) ) {exit;}

class APIBase {

	/**
	 * api_url
	 *
	 * @var string
	 */
	private $api_url = '';

	/**
	 * headers
	 *
	 * @var array
	 */
	private $headers = array();

	/**
	 * __construct
	 *
	 * @param  string $endpoint
	 * @param  bool $send_store_id
	 * @param  bool $with_auth
	 * @return void
	 */
	public function __construct( $endpoint ) {
		$this->api_url = $this->getEndpointURL( $endpoint );

	}

	public function getEndpointURL( $endpoint ) {
		return ICONVERTEM_API_URL . '/' . $endpoint;
	}

	/**
	 * POST request
	 *
	 * @param  array $payload
	 * @return mixed
	 */
	public function post( array $payload = array() ) {
		// optimizer_error_log($this->api_url);
		$request = \wp_remote_post(
			$this->api_url,
			array(
				'body'    => $this->buildBody( $payload ),
				'headers' => $this->headers,
			)
		);

		return $this->response( $request );
	}

	/**
	 * GET request
	 *
	 * @param  mixed $args
	 * @return mixed
	 */
	public function get( array $args ) {
		$request = \wp_remote_get( $this->buildQueryURL( $args ), array( 'headers' => $this->headers ) );

		return $this->response( $request );
	}

	/**
	 * DELETE request
	 *
	 * @param  mixed $args
	 * @return void
	 */
	public function delete( array $args ) {
		$request = \wp_remote_request(
			$this->buildQueryURL( $args ),
			array(
				'method'  => 'DELETE',
				'headers' => $this->headers,
			)
		);

		return $this->response( $request );
	}

	/**
	 * PATCH request
	 *
	 * @param  mixed $args
	 * @return void
	 */
	public function patch( array $args ) {
		$request = \wp_remote_request(
			$this->buildQueryURL( array() ),
			array(
				'method'  => 'PATCH',
				'body'    => $this->buildBody( $args ),
				'headers' => $this->headers,
			)
		);

		return $this->response( $request );
	}

	/**
	 * Build the request body
	 *
	 * @param  mixed $payload
	 * @return void
	 */
	private function buildBody( $payload ) {
		return $payload;
	}

	/**
	 * Build the query URL
	 *
	 * @param  mixed $args
	 * @return void
	 */
	private function buildQueryURL( $args ) {
		$get_args = implode( '/', $args );

		$url = $this->api_url;

		if ( $get_args ) {
			$url = $url . '/' . $get_args;
		}

		return $url;
	}

	/**
	 * Parse and format response
	 *
	 * @param  string $request
	 * @return array
	 */
	public function response( $request ) {
		// error_log('API RESPONSE: '. $this->api_url);
		// error_log(print_r($request, true));
		if ( wp_remote_retrieve_response_code( $request ) === 422 ) {
			return array(
				'success' => false,
				'errors'  => $request['errors'],
			);
		}
		if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) < 200 || wp_remote_retrieve_response_code( $request ) >= 300 ) {
			return array(
				'success' => false,
			);
		}

		$body = json_decode( $request['body'], true );
		// error_log(print_r($body, true));
		// error_log('API RESPONSE');
		return array(
			'success' => true,
			'data'    => isset( $body['data'] ) ? $body['data'] : array(),
		);
	}
}
