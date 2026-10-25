<?php
namespace  IconvertEmailMarketer\App\Core\Admin\Actions;

if ( ! defined( 'ABSPATH' ) ) {exit;}

class EditorActions {
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'iconvertem_register_template_mail_container_styles_meta' ) );
	}

	public function iconvertem_default_mail_container_style() {
		return (object) array(
			'backgroundColor' => null,
			'typography'      => array(
				'weight'        => null,
				'family'        => null,
				'transform'     => null,
				'style'         => null,
				'decoration'    => null,
				'lineHeight'    => array(
					'value' => null,
					'unit'  => '',
				),
				'letterSpacing' => array(
					'value' => null,
					'unit'  => 'px',
				),
				'size'          => array(
					'value' => null,
					'unit'  => 'px',
				),
				'color'         => '#111111',
			),
			'defaultRowWidth' => array(
				'value' => 600,
				'unit'  => 'px',
			),
		);
	}

	public function iconvertem_retrieve_mail_container_style_meta( $object ) {
		$post_id = is_object( $object ) ? $object->id : $object['id'];
		$style   = get_post_meta( $post_id, '_mail_container_style', true );
		if (
			! $style
		) {
			$style = $this->iconvertem_default_mail_container_style();
		}

		return $style;
	}

	public function iconvertem_update_mail_container_style_meta( $value, $object ) {
		$post_id  = $object->ID;
		$original = get_post_meta( $post_id, '_mail_container_style', true );
		update_post_meta( $post_id, '_mail_container_style', $value, $original );
	}

	public function iconvertem_register_template_mail_container_styles_meta() {
		register_rest_field(
			'icem-mail-tpl',
			'globalStyle',
			array(
				'get_callback'    => array( $this, 'iconvertem_retrieve_mail_container_style_meta' ),
				'update_callback' => array( $this, 'iconvertem_update_mail_container_style_meta' ),
			)
		);
	}


}
