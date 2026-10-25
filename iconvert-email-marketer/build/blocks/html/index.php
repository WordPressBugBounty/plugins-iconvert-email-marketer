<?php



namespace IconvertEmailMarketer\App\Core\Blocks;

use IconvertEmailMarketer\App\Core\Blocks\BaseBlock;
use IconvertEmailMarketer\App\Core\Blocks\BlockRegistry;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class HTMLBlock extends BaseBlock {

	public function html() {
		$allowed_tags = $this->getAllowedTags();
		ob_start();
		?>
		<?php echo wp_kses( $this->content, $allowed_tags ); ?>
		<?php

		return ob_get_clean();
	}

	public function getAllowedTags() {
		global $allowedposttags;
		$current_allowed_post_tags             = $allowedposttags;
		$allowed_atts                          = array(
			'align'      => array(),
			'class'      => array(),
			'type'       => array(),
			'id'         => array(),
			'dir'        => array(),
			'lang'       => array(),
			'style'      => array(),
			'xml:lang'   => array(),
			'src'        => array(),
			'alt'        => array(),
			'href'       => array(),
			'rel'        => array(),
			'rev'        => array(),
			'target'     => array(),
			'novalidate' => array(),
			'type'       => array(),
			'value'      => array(),
			'name'       => array(),
			'tabindex'   => array(),
			'action'     => array(),
			'method'     => array(),
			'for'        => array(),
			'width'      => array(),
			'height'     => array(),
			'data'       => array(),
			'title'      => array(),
		);
		$current_allowed_post_tags['form']     = $allowed_atts;
		$current_allowed_post_tags['label']    = $allowed_atts;
		$current_allowed_post_tags['input']    = $allowed_atts;
		$current_allowed_post_tags['textarea'] = $allowed_atts;
		$current_allowed_post_tags['iframe']   = $allowed_atts;
		$current_allowed_post_tags['script']   = $allowed_atts;
		$current_allowed_post_tags['style']    = $allowed_atts;
		$current_allowed_post_tags['strong']   = $allowed_atts;
		$current_allowed_post_tags['small']    = $allowed_atts;
		$current_allowed_post_tags['table']    = $allowed_atts;
		$current_allowed_post_tags['span']     = $allowed_atts;
		$current_allowed_post_tags['abbr']     = $allowed_atts;
		$current_allowed_post_tags['code']     = $allowed_atts;
		$current_allowed_post_tags['pre']      = $allowed_atts;
		$current_allowed_post_tags['div']      = $allowed_atts;
		$current_allowed_post_tags['img']      = $allowed_atts;
		$current_allowed_post_tags['h1']       = $allowed_atts;
		$current_allowed_post_tags['h2']       = $allowed_atts;
		$current_allowed_post_tags['h3']       = $allowed_atts;
		$current_allowed_post_tags['h4']       = $allowed_atts;
		$current_allowed_post_tags['h5']       = $allowed_atts;
		$current_allowed_post_tags['h6']       = $allowed_atts;
		$current_allowed_post_tags['ol']       = $allowed_atts;
		$current_allowed_post_tags['ul']       = $allowed_atts;
		$current_allowed_post_tags['li']       = $allowed_atts;
		$current_allowed_post_tags['em']       = $allowed_atts;
		$current_allowed_post_tags['hr']       = $allowed_atts;
		$current_allowed_post_tags['br']       = $allowed_atts;
		$current_allowed_post_tags['tr']       = $allowed_atts;
		$current_allowed_post_tags['td']       = $allowed_atts;
		$current_allowed_post_tags['p']        = $allowed_atts;
		$current_allowed_post_tags['a']        = $allowed_atts;
		$current_allowed_post_tags['b']        = $allowed_atts;
		$current_allowed_post_tags['i']        = $allowed_atts;

		return $current_allowed_post_tags;
	}
}

BlockRegistry::register( __DIR__, HTMLBlock::class );
