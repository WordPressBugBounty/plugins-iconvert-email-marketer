<?php


namespace IconvertEmailMarketer\Core;

use IconvertEmailMarketer\Constants;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class DataHelper {

	protected $settingPath = null;

	/**
	 * @var array|null All the settings from the admin page of the plugin plugin
	 */
	protected static $pluginData     = null;
	protected static $pluginDataPost = null;
	protected $data                  = null;

	public function __construct( $settingPath = null ) {
		if ( $settingPath ) {
			$this->settingPath = $settingPath;
		}
	}

	public function setStyle() {
		/**
		 * Implement if needed
		 */
	}

	public function setProp( $path, $value, $options = array() ) {
		$mergedOptions = $this->getMergedOptions( $options );
		$absolutePath  = $this->getAbsolutePropPath( $path, $mergedOptions );
		$this->setData( $absolutePath, $value, $mergedOptions );
	}

	public function getStyle( $path, $defaultValue = null, $options = array() ) {
		$mergedOptions = $this->getMergedOptions( $options );
		$absolutePath  = $this->getAbsoluteStylePath( $path, $mergedOptions );

		return $this->getData( $absolutePath, $defaultValue, $mergedOptions );
	}

	public function getProp( $path, $defaultValue = null, $options = array() ) {
		$mergedOptions = $this->getMergedOptions( $options );
		$absolutePath  = $this->getAbsolutePropPath( $path, $mergedOptions );

		return $this->getData( $absolutePath, $defaultValue, $mergedOptions );
	}

	public function getMergedOptions( $options ) {
		$defaultOptions = array(
			'styledComponent' => null,
			'media'           => 'desktop',
			'state'           => 'normal',
			'fromRoot'        => false,
			'unset'           => false,
		);

		return LodashBasic::merge( $defaultOptions, $options );
	}

	public function getAbsoluteStylePath( $path, $options = array() ) {
		return $this->getAbsolutePath( $path, array_merge( $options, array( 'prefix' => 'style' ) ) );
	}

	public function getAbsolutePropPath( $path, $options = array() ) {
		return $this->getAbsolutePath( $path, array_merge( $options, array( 'prefix' => 'props' ) ) );
	}

	public function getAbsolutePath( $relativePath, $options = array() ) {
		list ( 'prefix' => $prefix, 'media' => $media, 'state' => $state, 'styledComponent' => $styledComponent ) = $options;

		$paths = array( $prefix );
		if ( $styledComponent ) {
			$paths[] = "descendants.$styledComponent";
		}
		if ( $media !== 'desktop' ) {
			$paths[] = "media.$media";
		}
		if ( $state !== 'normal' ) {
			$paths[] = "state.$state";
		}

		//in case of ''
		if ( $relativePath ) {
			$paths[] = $relativePath;
		}
		$joinedPaths = implode( '.', $paths );

		return $joinedPaths;
	}

	public function getData( $path, $defaultValue, $options ) {
		//list('fromRoot' => $fromRoot) = $options;
		$source = $this->getSourceData();

		// if (fromRoot) {
		//     source = this.data;
		// } else {
		//     source = this.getMergedData();
		// }

		return LodashBasic::get( $source, $path, $defaultValue );
	}

	public function setData( $path, $newValue, $options ) {
		$this->getSourceData();

		LodashBasic::set( $this->data, $path, $newValue );
	}

	public function getSourceData() {

		if ( ! $this->settingPath ) {
			throw new \Exception( sprintf( 'settingPath property is not set in %s', esc_html( get_class( $this ) ) ) );
		}

		if ( ! $this->data ) {
			$pluginData = static::getPluginData();
			$this->data = LodashBasic::get( $pluginData, $this->settingPath, array() );
		}

		return $this->data;
	}

	public static function getPluginData() {
		if ( ! static::$pluginData ) {
			static::$pluginData = static::getDataFromDatabase();
		}

		return static::$pluginData;
	}

	public static function getPluginDataPost() {
		static::getPluginData();

		return static::$pluginDataPost;
	}



	public static function getDataFromDatabase() {
		$query      = new \WP_Query(
			array(
				'post_type'     => Constants::$settingPostType,
				'post_status'   => array( 'draft', 'publish' ),
				'no_found_rows' => true,
				'post_per_page' => 1,
			)
		);
		$pluginData = array();
		if ( $query->have_posts() ) {
			$post = $query->next_post();

			static::$pluginDataPost = $post;

			$pluginData = json_decode( static::$pluginDataPost->post_content, true );
		}

		return $pluginData;
	}

	public function saveChanges() {
		$pluginData = static::getPluginData();

		$currentWidgetData = $this->getSourceData();
		if ( empty( $currentWidgetData ) || empty( $pluginData ) ) {
			return;
		}
		LodashBasic::set( $pluginData, $this->settingPath, $currentWidgetData );

		static::$pluginData = $pluginData;

		static::updatePluginDataContent( $pluginData );
	}

	/**
	 * @param $pluginData array
	 */
	public static function updatePluginDataContent( $plugin_data ) {
		$plugin_data_post = static::getPluginDataPost();

		$plugin_data_content_json = json_encode( $plugin_data );
		$new_plugin_data_post     = array(
			'ID'           => $plugin_data_post->ID,
			'post_content' => $plugin_data_content_json,
		);

		wp_update_post( $new_plugin_data_post );
	}
}
